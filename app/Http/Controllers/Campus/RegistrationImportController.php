<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessRegistrationImportJob;
use App\Models\CampusCourse;
use App\Models\CampusSeason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class RegistrationImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:campus.registrations.import');
    }

    /**
     * Mostrar formulario de importación
     */
    public function showImportForm()
    {
        $seasons = CampusSeason::where('is_active', true)->orderBy('name')->get();
        return view('campus.registrations.import', compact('seasons'));
    }

    /**
     * Validar archivo CSV y mostrar preview
     */
    public function validateImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120', // 5MB max
            'season_id' => 'required|exists:campus_seasons,id'
        ]);

        $file = $request->file('csv_file');
        $season = CampusSeason::findOrFail($request->season_id);
        
        // Guardar archivo temporal
        $path = $file->storeAs('imports/temp', 'import_' . time() . '.csv');
        $fullPath = Storage::path($path);
        
        try {
            // Leer CSV
            $csvData = $this->readCsv($fullPath);
            
            if (empty($csvData)) {
                return back()->with('error', __('campus.import_empty_file'));
            }

            // Validar estructura
            $validation = $this->validateCsvStructure($csvData, $season);
            
            // Guardar en sesión siempre (incluso con errores)
            $finalPath = $file->storeAs('imports', 'validated_import_' . time() . '.csv');
            session(['validated_file_path' => $finalPath, 'validated_season_id' => $season->id]);
            
            if ($validation['has_errors']) {
                // No borramos el archivo para poder importar los registros válidos
                return view('campus.registrations.import-preview', [
                    'csvData' => $csvData,
                    'season' => $season,
                    'validation' => $validation,
                    'filePath' => $finalPath
                ]);
            }

        } catch (\Exception $e) {
            Storage::delete($path);
            return back()->with('error', __('campus.import_error', ['error' => $e->getMessage()]));
        }
    }

    /**
     * Procesar importación
     */
    public function processImport(Request $request)
    {
        // Log inmediato para saber si se llama al método
        file_put_contents('/var/www/dev.upg.cat/debug.log', 'processImport llamado: ' . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
        Log::info('=== INICIANDO PROCESS IMPORT ===');
        Log::info('Método request: ' . $request->method());
        Log::info('Todos los datos:', $request->all());
        
        try {
            $request->validate([
                'file_path' => 'required|string',
                'season_id' => 'required|exists:campus_seasons,id'
            ]);

            $filePath = $request->input('file_path');
            $season = CampusSeason::findOrFail($request->season_id);
            $userId = Auth::id();
            
            Log::info('Datos recibidos:', [
                'file_path' => $filePath,
                'season_id' => $season->id,
                'user_id' => $userId
            ]);

            if (!Storage::exists($filePath)) {
                Log::error('Archivo no encontrado: ' . $filePath);
                return back()->with('error', __('campus.import_file_not_found'));
            }

            // Leer y procesar CSV
            $fullPath = Storage::path($filePath);
            $csvData = $this->readCsv($fullPath);
            
            Log::info('CSV leído:', ['total_rows' => count($csvData)]);

            // Validación final
            $validation = $this->validateCsvStructure($csvData, $season);
            
            Log::info('Validación:', [
                'has_errors' => $validation['has_errors'],
                'valid_count' => $validation['valid_count'],
                'error_count' => $validation['error_count']
            ]);
            
            // IMPORTAR AUNQUE HAYA ERRORES (solo los válidos)
            $validRows = $validation['valid_rows'];
            
            if (empty($validRows)) {
                Log::warning('No hay filas válidas para importar');
                return back()->with('error', 'No hay registros válidos para importar');
            }

            // Procesar por lotes
            $batchSize = 100;
            $batches = array_chunk($validRows, $batchSize);
            
            Log::info('Creando batches:', [
                'total_valid_rows' => count($validRows),
                'batch_size' => $batchSize,
                'total_batches' => count($batches)
            ]);
            
            foreach ($batches as $index => $batch) {
                Log::info('Enviando batch ' . ($index + 1), ['rows' => count($batch)]);
                ProcessRegistrationImportJob::dispatch($batch, $season->id, $userId, $filePath)
                    ->onQueue('imports');
            }

            // Limpiar archivo temporal
            Storage::delete($filePath);

            return redirect()->route('campus.registrations.index')
                ->with('success', __('campus.import_started', [
                    'total' => count($validRows),
                    'batches' => count($batches)
                ]));
                
        } catch (\Exception $e) {
            Log::error('ERROR EN PROCESS IMPORT: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Leer archivo CSV
     */
    private function readCsv($filePath)
    {
        $csvData = [];
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Saltar header
            fgetcsv($handle, 1000, ',');
            
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($data) >= 7) {
                    $csvData[] = $data;
                }
            }
            fclose($handle);
        }
        
        return $csvData;
    }

    /**
     * Validar estructura del CSV
     */
    private function validateCsvStructure($csvData, $season)
    {
        $errors = [];
        $validRows = [];
        $totalRows = count($csvData);

        foreach ($csvData as $index => $row) {
            $rowNumber = $index + 2; // +2 porque saltamos header y empezamos en 1
            
            // Extraer campos
            $firstName = trim($row[0] ?? '');
            $email = trim($row[2] ?? '');
            $courseCode = trim($row[5] ?? '');
            $quantity = trim($row[6] ?? '');

            // PRIORIDAD 1: Validar código del curso PRIMERO
            if (empty($courseCode)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'course_code',
                    'error' => __('campus.import_required_field', ['field' => 'Código de curso']),
                    'data' => $courseCode
                ];
                continue; // Saltar resto de validaciones
            }

            // Verificar que el curso exista
            $course = CampusCourse::where('code', $courseCode)->first();
            if (!$course) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'course_code',
                    'error' => __('campus.import_course_not_found'),
                    'data' => $courseCode
                ];
                continue; // Saltar resto de validaciones
            }

            // PRIORIDAD 2: Validar campos obligatorios (solo si el código existe)
            if (empty($firstName)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'first_name',
                    'error' => __('campus.import_required_field', ['field' => 'Nombre']),
                    'data' => $firstName
                ];
                continue;
            }

            if (empty($email)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'email',
                    'error' => __('campus.import_required_field', ['field' => 'Email']),
                    'data' => $email
                ];
                continue;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'email',
                    'error' => __('campus.import_invalid_email'),
                    'data' => $email
                ];
                continue;
            }

            if (empty($quantity)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'field' => 'quantity',
                    'error' => __('campus.import_required_field', ['field' => 'Quantity']),
                    'data' => $quantity
                ];
                continue;
            }

            // Si pasa todas las validaciones
            $validRows[] = [
                'row_number' => $rowNumber,
                'first_name' => $firstName,
                'last_name' => trim($row[1] ?? ''),
                'email' => strtolower($email),
                'phone' => trim($row[3] ?? ''),
                'course_name' => trim($row[4] ?? ''),
                'course_code' => $courseCode,
                'course_id' => $course->id,
                'quantity' => $quantity,
                'amount' => $course->price ?? 0,
                'is_confirmed' => $quantity == 1,
                'is_paid' => $quantity == 1
            ];
        }

        return [
            'has_errors' => !empty($errors),
            'errors' => $errors,
            'valid_rows' => $validRows,
            'total_rows' => $totalRows,
            'valid_count' => count($validRows),
            'error_count' => count($errors),
            'invalid_courses' => $this->getInvalidCourses($errors)
        ];
    }

    /**
     * Obtener lista de códigos de curso inválidos únicos
     */
    private function getInvalidCourses($errors)
    {
        $invalidCourses = [];
        
        foreach ($errors as $error) {
            if ($error['field'] === 'course_code' && str_contains($error['error'], 'no encontrado')) {
                $code = $error['data'];
                if (!isset($invalidCourses[$code])) {
                    $invalidCourses[$code] = 0;
                }
                $invalidCourses[$code]++;
            }
        }
        
        return $invalidCourses;
    }

    /**
     * Iniciar worker de cua
     */
    public function startQueueWorker()
    {
        try {
            // Iniciar worker en background
            $command = 'php artisan queue:work --queue=imports,default --sleep=3 --timeout=60 --memory=256 > /dev/null 2>&1 &';
            exec($command);
            
            Log::info('Queue worker iniciado por usuario: ' . Auth::id());
            
            return back()->with('success', __('campus.queue_worker_started'));
            
        } catch (\Exception $e) {
            Log::error('Error iniciando queue worker: ' . $e->getMessage());
            return back()->with('error', __('campus.queue_worker_start_error'));
        }
    }

    /**
     * Aturar worker de cua
     */
    public function stopQueueWorker()
    {
        try {
            // Aturar tots els workers de queue
            $command = 'pkill -f "artisan queue:work"';
            exec($command);
            
            Log::info('Queue worker detenido por usuario: ' . Auth::id());
            
            return back()->with('success', __('campus.queue_worker_stopped'));
            
        } catch (\Exception $e) {
            Log::error('Error deteniendo queue worker: ' . $e->getMessage());
            return back()->with('error', __('campus.queue_worker_stop_error'));
        }
    }

    /**
     * Processar jobs de cua immediatament
     */
    public function processQueueNow()
    {
        try {
            // Processar jobs durant 5 minuts màxim
            Artisan::call('queue:work', [
                '--queue' => 'imports',
                '--timeout' => 60,
                '--max-time' => 300
            ]);
            
            $output = Artisan::output();
            
            Log::info('Queue procesado manualmente por usuario: ' . Auth::id(), ['output' => $output]);
            
            return back()->with('success', __('campus.queue_processed_manually'));
            
        } catch (\Exception $e) {
            Log::error('Error procesando queue manualmente: ' . $e->getMessage());
            return back()->with('error', __('campus.queue_process_error'));
        }
    }

    /**
     * Obtenir estat de la cua (AJAX)
     */
    public function getQueueStatus()
    {
        try {
            $queueSize = DB::table('jobs')->count();
            
            // Comprovar si hi ha workers actius (simple check)
            $workerRunning = false;
            $output = [];
            exec('ps aux | grep "artisan queue:work" | grep -v grep', $output);
            $workerRunning = !empty($output);
            
            return response()->json([
                'queue_size' => $queueSize,
                'worker_running' => $workerRunning,
                'last_check' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'queue_size' => 0,
                'worker_running' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
