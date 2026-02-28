<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CampusCourse;
use App\Models\CampusTeacher;
use App\Models\User;

class IniciCourseTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importación de Relaciones Curso-Teacher desde CSV ===');
        
        $csvPath = storage_path('app/imports/campus_course_teacher.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error('❌ Archivo CSV no encontrado: ' . $csvPath);
            return;
        }
        
        // Leer CSV (método simple para saltos de línea)
        $csvContent = file_get_contents($csvPath);
        $lines = explode(PHP_EOL, trim($csvContent));
        
        // Saltar headers
        array_shift($lines);
        
        $this->command->info('📊 Total registros en CSV: ' . count($lines));
        
        $processed = 0;
        $errors = [];
        $created = 0;
        $updated = 0;
        
        // Valores por defecto (Opción 2)
        $defaultCourseId = 1; // Primer curso
        $defaultTeacherId = 1; // Primer teacher
        $defaultRole = 'teacher'; // Rol por defecte
        
        foreach ($lines as $lineIndex => $line) {
            $processed++;
            
            try {
                // Parsear línea manualmente
                $parts = str_getcsv($line);
                
                if (count($parts) < 3) {
                    continue; // Saltar líneas vacías o inválidas
                }
                
                // Opción 2: Valores por defecte simples
                $courseId = $defaultCourseId;
                $teacherId = $defaultTeacherId;
                $role = $defaultRole;
                
                // Si hay valores válidos en el CSV, usarlos
                if (!empty($parts[1]) && is_numeric($parts[1])) {
                    $courseId = (int)$parts[1];
                }
                
                if (!empty($parts[2]) && is_numeric($parts[2])) {
                    $teacherId = (int)$parts[2];
                }
                
                if (!empty($parts[3])) {
                    $role = $parts[3];
                }
                
                // Verificar que el curso existe
                $course = CampusCourse::find($courseId);
                if (!$course) {
                    $errors[] = [
                        'row' => $processed,
                        'error' => "Course ID {$courseId} no encontrado"
                    ];
                    continue;
                }
                
                // Verificar que el teacher existe
                $teacher = CampusTeacher::find($teacherId);
                if (!$teacher) {
                    $errors[] = [
                        'row' => $processed,
                        'error' => "Teacher ID {$teacherId} no encontrado"
                    ];
                    continue;
                }
                
                // Crear o actualizar la relación en la tabla pivot
                $existingRelation = DB::table('campus_course_teacher')
                    ->where('course_id', $courseId)
                    ->where('teacher_id', $teacherId)
                    ->first();
                
                if ($existingRelation) {
                    // Actualizar relación existente
                    DB::table('campus_course_teacher')
                        ->where('id', $existingRelation->id)
                        ->update([
                            'role' => $role,
                            'hours_assigned' => $parts[4] ?? 0,
                            'assigned_at' => $parts[5] ?? now(),
                            'finished_at' => $parts[6] ?? null,
                            'metadata' => $parts[7] ?? null,
                            'updated_at' => now(),
                        ]);
                    $updated++;
                } else {
                    // Crear nueva relación
                    DB::table('campus_course_teacher')->insert([
                        'course_id' => $courseId,
                        'teacher_id' => $teacherId,
                        'role' => $role,
                        'hours_assigned' => $parts[4] ?? 0,
                        'assigned_at' => $parts[5] ?? now(),
                        'finished_at' => $parts[6] ?? null,
                        'metadata' => $parts[7] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $created++;
                }
                
            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $processed,
                    'error' => $e->getMessage()
                ];
            }
        }

        // Reporte final
        $this->printFinalReport($processed, $created, $updated, $errors);
    }
    
    private function readCSV($csvPath)
    {
        $csv = array_map('str_getcsv', file($csvPath));
        return $csv;
    }
    
    private function readCSVSafe($csvPath)
    {
        $csv = [];
        $handle = fopen($csvPath, 'r');
        
        if ($handle === false) {
            return [];
        }
        
        // Leer headers
        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);
            return [];
        }
        
        // Leer datos
        while (($row = fgetcsv($handle)) !== false) {
            // Asegurar que la fila tenga el mismo número de columnas que los headers
            if (count($row) === count($headers)) {
                $csv[] = $row;
            } else {
                // Si no tiene las mismas columnas, rellenar con valores vacíos
                $paddedRow = array_pad($row, count($headers), '');
                $csv[] = $paddedRow;
            }
        }
        
        fclose($handle);
        
        // Añadir headers al principio
        array_unshift($csv, $headers);
        
        return $csv;
    }
    
    private function printFinalReport($processed, $created, $updated, $errors)
    {
        $this->command->info('');
        $this->command->info('🎯 === REPORTE FINAL DE RELACIONES CURSO-TEACHER ===');
        $this->command->info('');
        
        $this->command->info('📊 Resumen del proceso:');
        $this->command->info("   📋 Registros procesados: {$processed}");
        $this->command->info("   ✅ Relaciones creadas: {$created}");
        $this->command->info("   🔄 Relaciones actualizadas: {$updated}");
        
        if (!empty($errors)) {
            $this->command->info('');
            $this->command->error('❌ ERRORES ENCONTRADOS:');
            foreach ($errors as $error) {
                $this->command->error("   - Fila {$error['row']}: {$error['error']}");
            }
        }
        
        $this->command->info('');
        $this->command->info('🎉 === FIN DEL PROCESO ===');
    }
}
