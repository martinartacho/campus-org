<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CampusCourse;
use App\Models\CampusTeacher;

class CampusCourseTeacherCSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importación de Relaciones Curso-Profesor desde CSV ===');
        
        // Validar que existan cursos y profesores
        $coursesCount = CampusCourse::count();
        $teachersCount = CampusTeacher::count();
        
        if ($coursesCount === 0) {
            $this->command->error('❌ No hay cursos en la base de datos. Ejecuta CampusCoursesCSVSeeder primero.');
            return;
        }
        
        if ($teachersCount === 0) {
            $this->command->error('❌ No hay profesores en la base de datos. Ejecuta CampusTeachersCSVSeeder primero.');
            return;
        }
        
        $this->command->info("✅ Validación: $coursesCount cursos, $teachersCount profesores encontrados");
        
        // Leer archivo CSV
        $csvPath = storage_path('app/imports/campus_course_teacher.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("❌ Archivo no encontrado: $csvPath");
            return;
        }
        
        $this->command->info("📂 Leyendo archivo: $csvPath");
        
        $csvData = $this->parseCSV($csvPath);
        $totalRows = count($csvData);
        
        $this->command->info("📊 Total de filas en CSV: $totalRows");
        
        $importedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        
        foreach ($csvData as $index => $row) {
            try {
                // Saltar cabecera si existe
                if ($index === 0 && $row[0] === 'id') {
                    $this->command->info("📋 Cabecera detectada, saltando fila 0");
                    continue;
                }
                
                $relation = $this->parseRelationRow($row);
                
                if (!$relation) {
                    $skippedCount++;
                    continue;
                }
                
                // Validar que existan curso y profesor
                $courseExists = CampusCourse::find($relation['course_id']);
                $teacherExists = CampusTeacher::find($relation['teacher_id']);
                
                if (!$courseExists) {
                    $this->command->warn("⚠️ Curso {$relation['course_id']} no encontrado - Saltando relación");
                    $skippedCount++;
                    continue;
                }
                
                if (!$teacherExists) {
                    $this->command->warn("⚠️ Profesor {$relation['teacher_id']} no encontrado - Saltando relación");
                    $skippedCount++;
                    continue;
                }
                
                // Importar relación
                DB::table('campus_course_teacher')->updateOrInsert(
                    ['id' => $relation['id']],
                    $relation
                );
                
                $importedCount++;
                
                // Mostrar progreso cada 20 relaciones
                if ($importedCount % 20 === 0) {
                    $this->command->info("✅ Progreso: $importedCount relaciones importadas...");
                }
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->command->error("❌ Error en fila $index: " . $e->getMessage());
            }
        }
        
        $this->command->info("\n=== RESUMEN DE IMPORTACIÓN ===");
        $this->command->info("✅ Relaciones importadas: $importedCount");
        $this->command->info("⚠️ Relaciones omitidas: $skippedCount");
        $this->command->info("❌ Errores: $errorCount");
        
        // Verificar importación
        $totalRelations = DB::table('campus_course_teacher')->count();
        
        $this->command->info("📊 Total relaciones en base de datos: $totalRelations");
        
        // Mostrar estadísticas adicionales
        $this->showRelationStats();
    }
    
    /**
     * Parsear archivo CSV
     */
    private function parseCSV($filePath)
    {
        $csvData = [];
        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            throw new \Exception("No se puede abrir el archivo: $filePath");
        }
        
        while (($row = fgetcsv($handle, 0, ';', '"')) !== false) {
            $csvData[] = $row;
        }
        
        fclose($handle);
        return $csvData;
    }
    
    /**
     * Parsear fila de relación
     */
    private function parseRelationRow($row)
    {
        if (count($row) < 7) {
            return null;
        }
        
        // Mapear campos del CSV (ajustado según estructura real)
        $relation = [
            'id' => $this->parseValue($row[0]),
            'course_id' => $this->parseValue($row[1]),
            'teacher_id' => $this->parseValue($row[2]),
            'role' => $this->parseValue($row[3]) ?? 'professor',
            'hours_assigned' => $this->parseFloat($row[4]),
            'created_at' => $this->parseValue($row[5]) ?? now(),
            'updated_at' => $this->parseValue($row[6]) ?? now(),
        ];
        
        return $relation;
    }
    
    /**
     * Parsear valor flotante
     */
    private function parseFloat($value)
    {
        $parsed = $this->parseValue($value);
        if ($parsed === null) {
            return null;
        }
        
        return (float) str_replace(',', '.', $parsed);
    }
    
    /**
     * Parsear valor (limpiar comillas y espacios)
     */
    private function parseValue($value)
    {
        if ($value === null || $value === '\N' || $value === '') {
            return null;
        }
        
        return trim($value, '"');
    }
    
    /**
     * Mostrar estadísticas de relaciones
     */
    private function showRelationStats()
    {
        $this->command->info("\n📊 Estadísticas de Relaciones:");
        
        // Profesores con más cursos
        $teacherCounts = DB::table('campus_course_teacher')
            ->join('campus_teachers', 'campus_course_teacher.teacher_id', '=', 'campus_teachers.id')
            ->select('campus_teachers.first_name', 'campus_teachers.last_name', DB::raw('count(*) as course_count'))
            ->groupBy('campus_course_teacher.teacher_id', 'campus_teachers.first_name', 'campus_teachers.last_name')
            ->orderBy('course_count', 'desc')
            ->limit(5)
            ->get();
            
        $this->command->info("   👨‍🏫 Top 5 Profesores con más cursos:");
        foreach ($teacherCounts as $teacher) {
            $this->command->info("      • {$teacher->first_name} {$teacher->last_name}: {$teacher->course_count} cursos");
        }
        
        // Cursos con más profesores
        $courseCounts = DB::table('campus_course_teacher')
            ->join('campus_courses', 'campus_course_teacher.course_id', '=', 'campus_courses.id')
            ->select('campus_courses.title', DB::raw('count(*) as teacher_count'))
            ->groupBy('campus_course_teacher.course_id', 'campus_courses.title')
            ->orderBy('teacher_count', 'desc')
            ->limit(5)
            ->get();
            
        $this->command->info("\n   📚 Top 5 Cursos con más profesores:");
        foreach ($courseCounts as $course) {
            $this->command->info("      • {$course->title}: {$course->teacher_count} profesores");
        }
    }
}
