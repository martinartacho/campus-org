<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CampusTeacher;
use App\Models\User;

class CampusTeachersCSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importación de Profesores desde CSV ===');
        
        // Validar que existan usuarios
        $usersCount = User::count();
        
        if ($usersCount === 0) {
            $this->command->error('❌ No hay usuarios en la base de datos. Ejecuta CampusUsersCSVSeeder primero.');
            return;
        }
        
        $this->command->info("✅ Validación: $usersCount usuarios encontrados");
        
        // Leer archivo CSV
        $csvPath = storage_path('app/imports/campus_teacher.csv');
        
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
                
                $teacher = $this->parseTeacherRow($row);
                
                if (!$teacher) {
                    $skippedCount++;
                    continue;
                }
                
                // Validar que exista el usuario asociado
                $userExists = User::find($teacher['user_id']);
                
                if (!$userExists) {
                    $this->command->warn("⚠️ Usuario {$teacher['user_id']} no encontrado - Saltando profesor: {$teacher['teacher_code']}");
                    $skippedCount++;
                    continue;
                }
                
                // Importar profesor
                CampusTeacher::updateOrInsert(
                    ['id' => $teacher['id']],
                    $teacher
                );
                
                $importedCount++;
                
                // Mostrar progreso cada 10 profesores
                if ($importedCount % 10 === 0) {
                    $this->command->info("✅ Progreso: $importedCount profesores importados...");
                }
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->command->error("❌ Error en fila $index: " . $e->getMessage());
            }
        }
        
        $this->command->info("\n=== RESUMEN DE IMPORTACIÓN ===");
        $this->command->info("✅ Profesores importados: $importedCount");
        $this->command->info("⚠️ Profesores omitidos: $skippedCount");
        $this->command->info("❌ Errores: $errorCount");
        
        // Verificar importación
        $totalTeachers = CampusTeacher::count();
        $activeTeachers = CampusTeacher::where('status', 'active')->count();
        
        $this->command->info("📊 Total profesores en base de datos: $totalTeachers");
        $this->command->info("📊 Profesores activos: $activeTeachers");
        
        // Mostrar estadísticas adicionales
        $this->showTeacherStats();
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
     * Parsear fila de profesor
     */
    private function parseTeacherRow($row)
    {
        if (count($row) < 25) {
            return null;
        }
        
        // Mapear campos del CSV (ajustado según estructura real)
        $teacher = [
            'id' => $this->parseValue($row[0]),
            'user_id' => $this->parseValue($row[1]),
            'teacher_code' => $this->parseValue($row[2]),
            'first_name' => $this->parseValue($row[3]),
            'last_name' => $this->parseValue($row[4]),
            'dni' => $this->parseValue($row[5]),
            'email' => $this->parseValue($row[6]),
            'phone' => $this->parseValue($row[7]),
            'address' => $this->parseValue($row[8]),
            'postal_code' => $this->parseValue($row[9]),
            'city' => $this->parseValue($row[10]),
            'iban' => $this->parseValue($row[13]),
            'bank_titular' => $this->parseValue($row[14]),
            'specialization' => $this->parseValue($row[19]),
            'title' => $this->parseValue($row[20]),
            'areas' => $this->parseValue($row[21]),
            'status' => $this->parseValue($row[22]) ?? 'active',
            'hiring_date' => $this->parseValue($row[23]),
            'created_at' => $this->parseValue($row[24]) ?? now(),
            'updated_at' => $this->parseValue($row[25]) ?? now(),
        ];
        
        return $teacher;
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
     * Mostrar estadísticas de profesores
     */
    private function showTeacherStats()
    {
        $this->command->info("\n📊 Estadísticas de Profesores:");
        
        // Profesores por especialización
        $specializationCounts = CampusTeacher::select('specialization', DB::raw('count(*) as count'))
            ->whereNotNull('specialization')
            ->groupBy('specialization')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
            
        $this->command->info("   🎓 Top 5 Especializaciones:");
        foreach ($specializationCounts as $spec) {
            $this->command->info("      • {$spec->specialization}: {$spec->count} profesores");
        }
        
        // Profesores por título
        $titleCounts = CampusTeacher::select('title', DB::raw('count(*) as count'))
            ->whereNotNull('title')
            ->groupBy('title')
            ->orderBy('count', 'desc')
            ->get();
            
        $this->command->info("\n   📜 Distribución por Título:");
        foreach ($titleCounts as $title) {
            $this->command->info("      • {$title->title}: {$title->count} profesores");
        }
    }
}
