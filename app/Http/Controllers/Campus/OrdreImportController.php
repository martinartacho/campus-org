<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use App\Models\CampusOrdreTemp;
use App\Models\CampusCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class OrdreImportController extends Controller
{
    /**
     * Mostrar vista principal d'importació.
     */
    public function index()
    {
        // Estadístiques
        $stats = [
            'total_ordres' => CampusOrdreTemp::count(),
            'pending' => CampusOrdreTemp::pending()->count(),
            'matched' => CampusOrdreTemp::matched()->count(),
            'manual' => CampusOrdreTemp::manual()->count(),
            'error' => CampusOrdreTemp::error()->count(),
        ];

        // Últimes importacions
        $recentImports = CampusOrdreTemp::latest('imported_at')
            ->take(10)
            ->get();

        return view('campus.ordres.import', compact('stats', 'recentImports'));
    }

    /**
     * Processar importació CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->store('imports', 'local');
            $fullPath = Storage::disk('local')->path($path);

            // Netejar taula temporal
            CampusOrdreTemp::truncate();

            // Processar CSV
            $this->processCSV($fullPath);

            return redirect()
                ->route('campus.ordres.import')
                ->with('success', 'CSV importat correctament. ' . CampusOrdreTemp::count() . ' ordres processades.');

        } catch (\Exception $e) {
            return redirect()
                ->route('campus.ordres.import')
                ->with('error', 'Error en importar CSV: ' . $e->getMessage());
        }
    }

    /**
     * Validar ordres amb cerca i filtres.
     */
    public function validateOrdres(Request $request)
    {
        // Obtenir paràmetres de cerca
        $search = $request->get('search', '');
        $codeFilter = $request->get('code_filter', '');
        
        // Construir query base - Mostrar pending, matched (NO processed)
        $query = CampusOrdreTemp::with('course')
            ->whereIn('validation_status', ['pending', 'matched']);
        
        // Aplicar cerca global
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('wp_first_name', 'LIKE', "%{$search}%")
                  ->orWhere('wp_last_name', 'LIKE', "%{$search}%")
                  ->orWhere('wp_email', 'LIKE', "%{$search}%")
                  ->orWhere('wp_item_name', 'LIKE', "%{$search}%")
                  ->orWhere('wp_code', 'LIKE', "%{$search}%");
            });
        }
        
        // Aplicar filtre per codi
        if (!empty($codeFilter)) {
            $query->where('wp_code', 'LIKE', "%{$codeFilter}%");
        }
        
        // Paginar resultats
        $pendingOrdres = $query->paginate(20);
        
        $manualOrdres = CampusOrdreTemp::with('course')
            ->manual()
            ->paginate(20);

        $errorOrdres = CampusOrdreTemp::with('course')
            ->error()
            ->paginate(20);

        return view('campus.ordres.validate', compact(
            'pendingOrdres',
            'manualOrdres', 
            'errorOrdres'
        ));
    }

    /**
     * Auto-matching massiu.
     */
    public function autoMatch()
    {
        try {
            $ordres = CampusOrdreTemp::pending()->get();
            $matched = 0;
            $manual = 0;
            $errors = 0;

            foreach ($ordres as $ordre) {
                try {
                    if ($ordre->autoMatchCourse()) {
                        $matched++;
                    } elseif ($ordre->matchByTitle()) {
                        $matched++;
                    } else {
                        $ordre->validation_status = 'manual';
                        $ordre->validation_notes = 'No s\'ha trobat coincidència automàtica. Revisar manualment.';
                        $ordre->save();
                        $manual++;
                    }
                } catch (\Exception $e) {
                    $ordre->validation_status = 'error';
                    $ordre->validation_notes = $e->getMessage();
                    $ordre->save();
                    $errors++;
                }
            }

            $message = "Auto-matching completat: {$matched} coincidències, {$manual} revisió manual, {$errors} errors.";
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'matched' => $matched,
                'manual' => $manual,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            \Log::error('Auto-match error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error en auto-matching: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Processar ordres validades a campus_course_student.
     */
    public function process(Request $request)
    {
        $request->validate([
            'ordre_ids' => 'required|array',
            'ordre_ids.*' => 'integer|exists:campus_ordres_temp,id',
            'season_id' => 'required|integer|exists:campus_seasons,id',
        ]);

        try {
            $processed = 0;
            $duplicates = 0;
            $errors = 0;
            $errorDetails = [];

            DB::beginTransaction();

            foreach ($request->ordre_ids as $ordreId) {
                try {
                    // Processar ordres amb course_id assignat
                    $ordre = CampusOrdreTemp::whereIn('validation_status', ['matched', 'pending'])
                        ->whereIn('id', $request->ordre_ids)
                        ->whereNotNull('course_id')
                        ->findOrFail($ordreId);
                    
                    if (!$ordre->course_id) {
                        throw new \Exception("L'ordre no té un curs assignat");
                    }

                    // Find or create student
                    $student = \App\Models\CampusStudent::firstOrCreate(
                        ['email' => $ordre->wp_email],
                        [
                            'first_name' => $ordre->wp_first_name,
                            'last_name' => $ordre->wp_last_name,
                            'phone' => $ordre->wp_phone ?? null,
                            'created_by' => auth()->id(),
                        ]
                    );

                    // Check for duplicate enrollment in campus_registrations
                    if (\App\Models\CampusRegistration::where('student_id', $student->id)
                        ->where('course_id', $ordre->course_id)
                        ->where('season_id', $request->season_id)
                        ->exists()) {
                        $duplicates++;
                        
                        // ACTUALITZAR ESTAT FINS I SI ÉS DUPLICAT
                        $ordre->validation_status = 'processed';
                        $ordre->validation_notes = 'Ja existia a campus_registrations';
                        $ordre->save();
                        continue;
                    }

                    // Check for duplicate enrollment in campus_course_student
                    if (\App\Models\CampusCourseStudent::where('student_id', $student->id)
                        ->where('course_id', $ordre->course_id)
                        ->where('season_id', $request->season_id)
                        ->exists()) {
                        $duplicates++;
                        
                        // ACTUALITZAR ESTAT FINS I SI ÉS DUPLICAT
                        $ordre->validation_status = 'processed';
                        $ordre->validation_notes = 'Ja existia a campus_course_student';
                        $ordre->save();
                        continue;
                    }

                    // Create campus registration (definitive)
                    \App\Models\CampusRegistration::createFromOrdreTemp($ordre, $student->id, $request->season_id);

                    // Create course student enrollment
                    \App\Models\CampusCourseStudent::createFromOrdreTemp($ordre, $student->id, $request->season_id);

                    // Update ordre status - MOGUT DINS DEL TRY
                    $ordre->validation_status = 'processed';
                    $ordre->validation_notes = 'Processat correctament';
                    $ordre->save();

                    $processed++;

                } catch (\Exception $e) {
                    $errors++;
                    $errorDetails[] = "Ordre {$ordreId}: " . $e->getMessage();
                    \Log::error("Process ordre {$ordreId} error: " . $e->getMessage());
                    
                    // NO actualitzar estat si hi ha error
                    // L'ordre roman a 'matched' o 'pending' per reintentar
                }
            }

            DB::commit();

            $message = "Processament completat: {$processed} processats, {$duplicates} duplicats, {$errors} errors.";
            
            if ($errors > 0) {
                $message .= " Errors: " . implode('; ', array_slice($errorDetails, 0, 3));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'processed' => $processed,
                'duplicates' => $duplicates,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bulk process error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processant ordres: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate student code.
     */
    private function generateStudentCode(string $firstName, string $lastName): string
    {
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $code = $initials . $random;

        // Ensure uniqueness
        while (\App\Models\CampusStudent::where('student_code', $code)->exists()) {
            $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $code = $initials . $random;
        }

        return $code;
    }

    /**
     * Processar CSV.
     */
    private function processCSV($filePath)
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new \Exception("No es pot obrir el fitxer CSV");
        }

        // Llegir capçalera i crear mapeig
        $headers = fgetcsv($handle, 0, ',', '"');
        $fieldMap = $this->processCSVHeader($headers);
        
        $rowNumber = 1;
        while (($row = fgetcsv($handle, 0, ',', '"')) !== false) {
            $rowNumber++;
            
            try {
                $this->processCSVRow($row, $rowNumber, $fieldMap);
            } catch (\Exception $e) {
                \Log::error("Error en fila {$rowNumber}: " . $e->getMessage());
            }
        }

        fclose($handle);
    }

    /**
     * Processar capçalera CSV.
     */
    private function processCSVHeader($headers)
    {
        // Mapeig flexible de camps independentment del nom
        $fieldMap = [];
        
        foreach ($headers as $index => $header) {
            $cleanHeader = strtolower(trim(str_replace(['"', ' ', '(', ')', 'Billing'], '', $header)));
            
            switch ($cleanHeader) {
                case 'firstname':
                case 'first_name':
                    $fieldMap['first_name'] = $index;
                    break;
                case 'lastname':
                case 'last_name':
                    $fieldMap['last_name'] = $index;
                    break;
                case 'email':
                case 'emailbilling':
                    $fieldMap['email'] = $index;
                    break;
                case 'phone':
                case 'phonebilling':
                    $fieldMap['phone'] = $index;
                    break;
                case 'itemname':
                case 'item_name':
                    $fieldMap['item_name'] = $index;
                    break;
                case 'codi':
                case 'code':
                    $fieldMap['code'] = $index;
                    break;
                case 'quantity':
                    $fieldMap['quantity'] = $index;
                    break;
                case 'price':
                    $fieldMap['price'] = $index;
                    break;
            }
        }
        
        // Debug: mostrar mapeig de camps
        \Log::info('CSV Field Map: ' . json_encode($fieldMap));
        
        return $fieldMap;
    }

    /**
     * Processar fila CSV.
     */
    private function processCSVRow($row, $rowNumber, $fieldMap)
    {
        // Validar que hi hagin dades suficients
        if (count($row) < 6 || empty(trim($row[0] ?? ''))) {
            \Log::warning("Ometent fila {$rowNumber}: dades insuficients o buides");
            return;
        }

        // Debug: mostrar fila processada
        \Log::info("Processing row {$rowNumber}: " . json_encode($row));

        // Obtenir codi amb validació
        $code = $this->cleanField($row[$fieldMap['code'] ?? 5] ?? '');
        if (empty($code)) {
            \Log::warning("Fila {$rowNumber}: codi buit, assignant 'UNKNOWN'");
            $code = 'UNKNOWN';
        }

        // Usar mapeig de camps flexible amb valors per defecte segurs
        CampusOrdreTemp::create([
            'wp_first_name' => $this->cleanField($row[$fieldMap['first_name'] ?? 0] ?? ''),
            'wp_last_name' => $this->cleanField($row[$fieldMap['last_name'] ?? 1] ?? ''),
            'wp_email' => $this->cleanField($row[$fieldMap['email'] ?? 2] ?? ''),
            'wp_phone' => $this->cleanField($row[$fieldMap['phone'] ?? 3] ?? ''),
            'wp_item_name' => $this->cleanField($row[$fieldMap['item_name'] ?? 4] ?? ''),
            'wp_code' => $code, // CORREGIT: validat i amb valor per defecte
            'wp_quantity' => $this->cleanField($row[$fieldMap['quantity'] ?? 6] ?? 0),
            'wp_status' => $this->deriveStatusFromQuantity($this->cleanField($row[$fieldMap['quantity'] ?? 6] ?? 0)),
            'wp_price' => $this->cleanField($row[$fieldMap['price'] ?? 7] ?? 0.00), // Per defecte 0.00 si no existeix
            'validation_status' => 'pending',
            'metadata' => [
                'csv_row' => $rowNumber,
                'imported_at' => now()->toISOString(),
                'original_quantity' => $this->cleanField($row[$fieldMap['quantity'] ?? 6] ?? 0),
                'field_map' => $fieldMap,
                'raw_row' => $row,
            ]
        ]);
    }

    /**
     * Netejar camp.
     */
    private function cleanField($field)
    {
        if ($field === null || $field === '\N' || $field === '') {
            return null;
        }
        
        return trim(preg_replace('/^\s*"\s*|\s*"\s*$/', '', $field));
    }

    /**
     * Derivar estat a partir de Quantity.
     * Quantity = 1 -> "completed" (pagat)
     * Quantity != 1 -> "pending" (pendent de pagament)
     */
    private function deriveStatusFromQuantity($quantity)
    {
        $qty = (int) $quantity;
        return $qty === 1 ? 'completed' : 'pending';
    }

    /**
     * Assignar codi massivament.
     */
    public function bulkAssignCode(Request $request)
    {
        $request->validate([
            'ordre_ids' => 'required|array',
            'ordre_ids.*' => 'integer|exists:campus_ordres_temp,id',
            'code' => 'required|string|max:50',
        ]);

        try {
            $updated = CampusOrdreTemp::whereIn('id', $request->ordre_ids)
                ->update([
                    'wp_code' => $request->code,
                    'validation_status' => 'pending',
                    'validation_notes' => null,
                ]);

            return response()->json([
                'success' => true,
                'message' => "Codi '{$request->code}' assignat a {$updated} ordres",
                'updated' => $updated
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk assign code error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error assignant codi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Canviar estat de pagament massivament.
     */
    public function bulkChangePayment(Request $request)
    {
        $request->validate([
            'ordre_ids' => 'required|array',
            'ordre_ids.*' => 'integer|exists:campus_ordres_temp,id',
            'payment_status' => 'required|string|in:paid,pending',
        ]);

        try {
            $updated = CampusOrdreTemp::whereIn('id', $request->ordre_ids)
                ->update([
                    'wp_quantity' => $request->payment_status === 'paid' ? 1 : 0,
                    'wp_status' => $this->deriveStatusFromQuantity($request->payment_status === 'paid' ? 1 : 0),
                ]);

            $statusLabel = $request->payment_status === 'paid' ? 'Pagat' : 'Pendent';
            return response()->json([
                'success' => true,
                'message' => "Estat de pagament canviat a '{$statusLabel}' per {$updated} ordres",
                'updated' => $updated
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk change payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error canviant pagament: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar ordres massivament.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ordre_ids' => 'required|array',
            'ordre_ids.*' => 'integer|exists:campus_ordres_temp,id',
        ]);

        try {
            $deleted = CampusOrdreTemp::whereIn('id', $request->ordre_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => "{$deleted} ordres eliminades correctament",
                'deleted' => $deleted
            ]);

        } catch (\Exception $e) {
            \Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error eliminant ordres: ' . $e->getMessage()
            ], 500);
        }
    }
}
