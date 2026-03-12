<?php

namespace App\Jobs;

use App\Models\CampusCourse;
use App\Models\CampusCourseStudent;
use App\Models\CampusRegistration;
use App\Models\CampusSeason;
use App\Models\CampusStudent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessRegistrationImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 3;

    private $batch;
    private $seasonId;
    private $userId;
    private $filePath;

    /**
     * Create a new job instance.
     */
    public function __construct($batch, $seasonId, $userId, $filePath)
    {
        $this->batch = $batch;
        $this->seasonId = $seasonId;
        $this->userId = $userId;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $season = CampusSeason::findOrFail($this->seasonId);
            $processedCount = 0;
            $errors = [];

            foreach ($this->batch as $row) {
                try {
                    $this->processRow($row, $season);
                    $processedCount++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'row' => $row['row_number'],
                        'error' => $e->getMessage(),
                        'data' => $row['email']
                    ];
                    Log::error("Import error row {$row['row_number']}: " . $e->getMessage());
                }
            }

            // Guardar log de importación
            $this->saveImportLog($processedCount, $errors);

            DB::commit();

            Log::info("Import batch processed: {$processedCount} rows, " . count($errors) . " errors");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Import batch failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Procesar una fila individual
     */
    private function processRow($row, $season)
    {
        // Buscar o crear estudiante
        $student = $this->findOrCreateStudent($row);
        
        // Verificar si ya existe una matrícula para este estudiante en este curso y temporada
        $existingRegistration = CampusRegistration::where('student_id', $student->id)
            ->where('course_id', $row['course_id'])
            ->where('season_id', $season->id)
            ->first();

        if ($existingRegistration) {
            // Actualizar matrícula existente si es necesario
            $this->updateExistingRegistration($existingRegistration, $row);
        } else {
            // Crear nueva matrícula
            $this->createNewRegistration($student, $row, $season);
        }

        // Crear o actualizar relación curso-estudiante
        $this->createOrUpdateCourseStudent($student, $row, $season);
    }

    /**
     * Buscar o crear estudiante
     */
    private function findOrCreateStudent($row)
    {
        $student = CampusStudent::where('email', $row['email'])->first();

        if (!$student) {
            // Crear usuario primero
            $user = User::create([
                'name' => trim($row['first_name'] . ' ' . $row['last_name']),
                'email' => $row['email'],
                'password' => Hash::make(Str::random(10)),
                'email_verified_at' => now()
            ]);

            // Asignar rol de estudiante
            $user->assignRole('student');

            // Crear estudiante
            $student = CampusStudent::create([
                'user_id' => $user->id,
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'dni' => null, // No viene en el CSV
                'enrollment_date' => now(),
                'is_active' => true
            ]);
        }

        return $student;
    }

    /**
     * Crear nueva matrícula
     */
    private function createNewRegistration($student, $row, $season)
    {
        $registration = CampusRegistration::create([
            'student_id' => $student->id,
            'course_id' => $row['course_id'],
            'season_id' => $season->id,
            'registration_code' => 'REG-' . strtoupper(uniqid()),
            'registration_date' => now(),
            'status' => $row['is_confirmed'] ? 'confirmed' : 'pending',
            'amount' => $row['amount'],
            'payment_status' => $row['is_paid'] ? 'paid' : 'pending',
            'payment_method' => 'importacion',
            'payment_history' => $row['is_paid'] ? json_encode([[
                'date' => now()->toISOString(),
                'amount' => $row['amount'],
                'method' => 'importacion',
                'reference' => 'ordres-wp-import'
            ]]) : null,
            'metadata' => json_encode([
                'source' => 'ordres-wp',
                'imported_by' => $this->userId,
                'import_date' => now()->toISOString(),
                'file_path' => $this->filePath,
                'row_number' => $row['row_number'],
                'original_data' => $row
            ])
        ]);

        return $registration;
    }

    /**
     * Actualizar matrícula existente
     */
    private function updateExistingRegistration($registration, $row)
    {
        // Solo actualizar si el nuevo estado es más alto
        if ($row['is_confirmed'] && $registration->status !== 'confirmed') {
            $registration->status = 'confirmed';
        }
        
        if ($row['is_paid'] && $registration->payment_status !== 'paid') {
            $registration->payment_status = 'paid';
            $registration->payment_history = json_encode([[
                'date' => now()->toISOString(),
                'amount' => $row['amount'],
                'method' => 'importacion',
                'reference' => 'ordres-wp-import-update'
            ]]);
        }

        $metadata = json_decode($registration->metadata ?? '{}', true);
        $metadata['last_import'] = [
            'import_date' => now()->toISOString(),
            'imported_by' => $this->userId,
            'row_number' => $row['row_number']
        ];
        
        $registration->metadata = json_encode($metadata);
        $registration->save();
    }

    /**
     * Crear o actualizar relación curso-estudiante
     */
    private function createOrUpdateCourseStudent($student, $row, $season)
    {
        $courseStudent = CampusCourseStudent::where('student_id', $student->id)
            ->where('course_id', $row['course_id'])
            ->where('season_id', $season->id)
            ->first();

        if (!$courseStudent) {
            CampusCourseStudent::create([
                'student_id' => $student->id,
                'course_id' => $row['course_id'],
                'season_id' => $season->id,
                'enrollment_date' => now(),
                'academic_status' => 'enrolled',
                'start_date' => now(),
                'metadata' => json_encode([
                    'source' => 'ordres-wp-import',
                    'import_date' => now()->toISOString(),
                    'imported_by' => $this->userId
                ])
            ]);
        }
    }

    /**
     * Guardar log de importación
     */
    private function saveImportLog($processedCount, $errors)
    {
        $logData = [
            'batch_size' => count($this->batch),
            'processed_count' => $processedCount,
            'error_count' => count($errors),
            'errors' => $errors,
            'season_id' => $this->seasonId,
            'user_id' => $this->userId,
            'file_path' => $this->filePath,
            'processed_at' => now()->toISOString()
        ];

        // Guardar en storage/logs
        $logFileName = 'import_log_' . date('Y-m-d_H-i-s') . '_' . $this->userId . '.json';
        \Storage::disk('local')->put('import_logs/' . $logFileName, json_encode($logData, JSON_PRETTY_PRINT));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Import job failed for user {$this->userId}: " . $exception->getMessage());
    }
}
