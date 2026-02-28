<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CampusCourse;
use App\Models\CampusSeason;
use App\Models\CampusCategory;
use Carbon\Carbon;

class IniciCoursesMapeadoCSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importación de Cursos desde CSV con Mapeo de IDs ===');
        
        $csvPath = storage_path('app/imports/campus_courses.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error('❌ Archivo CSV no encontrado: ' . $csvPath);
            return;
        }
        
        // Validar que existan temporadas y categorías
        $seasons = CampusSeason::pluck('slug', 'id')->toArray();
        $categories = CampusCategory::pluck('slug', 'id')->toArray();
        
        if (empty($seasons)) {
            $this->command->error('❌ No hay temporadas en la base de datos. Ejecuta IniciCategoriesSeasonSeeder primero.');
            return;
        }
        
        if (empty($categories)) {
            $this->command->error('❌ No hay categorías en la base de datos. Ejecuta IniciCategoriesSeasonSeeder primero.');
            return;
        }
        
        $this->command->info('✅ Temporadas encontradas: ' . implode(', ', $seasons));
        $this->command->info('✅ Categorías encontradas: ' . implode(', ', $categories));
        
        // Mapeo de IDs del CSV a nuestros IDs (dinámico para producción)
        $seasonMapping = $this->getSeasonMapping();
        $categoryMapping = $this->getCategoryMapping();
        
        // Leer CSV
        $csvData = $this->readCSV($csvPath);
        $headers = array_shift($csvData); // Primera fila son los headers
        
        $this->command->info('📊 Total registros en CSV: ' . count($csvData));
        
        $processed = 0;
        $errors = [];
        $created = 0;
        $updated = 0;
        
        foreach ($csvData as $rowIndex => $row) {
            $processed++;
            $rowData = array_combine($headers, $row);
            
            try {
                $result = $this->processCourse($rowData, $seasonMapping, $categoryMapping, $processed);
                
                if ($result['created']) {
                    $created++;
                } elseif ($result['updated']) {
                    $updated++;
                }
                
                if (!empty($result['errors'])) {
                    $errors = array_merge($errors, $result['errors']);
                }
                
            } catch (\Exception $e) {
                $errors[] = "Fila {$processed}: Error general - " . $e->getMessage();
                $this->command->error("❌ Error en fila {$processed}: " . $e->getMessage());
            }
        }
        
        // Reporte final
        $this->printReport($processed, $created, $updated, $errors);
    }
    
    private function readCSV($filePath)
    {
        $csv = [];
        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            throw new \Exception('No se puede abrir el archivo CSV');
        }
        
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $csv[] = $row;
        }
        
        fclose($handle);
        return $csv;
    }
    
    private function processCourse($rowData, $seasonMapping, $categoryMapping, $rowNumber)
    {
        $errors = [];
        $created = false;
        $updated = false;
        
        // Mapear IDs usando las tablas de mapeo
        $originalSeasonId = (int) $rowData['season_id'];
        $originalCategoryId = (int) $rowData['category_id'];
        
        $seasonId = $seasonMapping[$originalSeasonId] ?? 1;
        $categoryId = $categoryMapping[$originalCategoryId] ?? 1;
        
        // Validaciones y normalización
        $courseData = [
            'season_id' => $seasonId,
            'category_id' => $categoryId,
            'code' => $this->validateString($rowData['code'], 'code', $errors, $rowNumber, true),
            'title' => $this->validateString($rowData['title'], 'title', $errors, $rowNumber, true),
            'slug' => $this->validateString($rowData['slug'], 'slug', $errors, $rowNumber, true),
            'description' => $this->validateString($rowData['description'], 'description', $errors, $rowNumber),
            'credits' => $this->validateInteger($rowData['credits'], 'credits', $errors, $rowNumber, 1),
            'hours' => $this->validateInteger($rowData['hours'], 'hours', $errors, $rowNumber, 1),
            'sessions' => $this->validateInteger($rowData['sessions'] ?? null, 'sessions', $errors, $rowNumber, 1),
            'max_students' => $this->validateInteger($rowData['max_students'], 'max_students', $errors, $rowNumber, 1),
            'price' => $this->validateDecimal($rowData['price'], 'price', $errors, $rowNumber, 0.00),
            'level' => $this->validateLevel($rowData['level'], $errors, $rowNumber),
            'schedule' => $this->validateSchedule($rowData['schedule'], $errors, $rowNumber),
            'start_date' => $this->validateDate($rowData['start_date'], 'start_date', $errors, $rowNumber),
            'end_date' => $this->validateDate($rowData['end_date'], 'end_date', $errors, $rowNumber),
            'location' => $this->validateString($rowData['location'], 'location', $errors, $rowNumber),
            'format' => $this->validateFormat($rowData['format'], $errors, $rowNumber),
            'is_active' => $this->validateBoolean($rowData['is_active'], 'is_active', $errors, $rowNumber, true),
            'is_public' => $this->validateBoolean($rowData['is_public'], 'is_public', $errors, $rowNumber, true),
            'status' => 'draft', // Valor por defecto
            'created_by' => 1, // Admin user
            'source' => 'csv_import',
            'requirements' => $this->validateJSONToString($rowData['requirements'], 'requirements', $errors, $rowNumber),
            'objectives' => $this->validateJSONToString($rowData['objectives'], 'objectives', $errors, $rowNumber),
            'metadata' => $this->validateJSONToString($rowData['metadata'], 'metadata', $errors, $rowNumber),
        ];
        
        // Log de mapeo para depuración
        if ($originalSeasonId !== $seasonId || $originalCategoryId !== $categoryId) {
            $this->command->info("🔄 Fila {$rowNumber}: Mapeado season_id {$originalSeasonId}→{$seasonId}, category_id {$originalCategoryId}→{$categoryId}");
        }
        
        // Si hay errores críticos, no procesar
        if (!empty($errors)) {
            return ['created' => false, 'updated' => false, 'errors' => $errors];
        }
        
        // Crear o actualizar curso
        $existingCourse = CampusCourse::where('code', $courseData['code'])->first();
        
        if ($existingCourse) {
            $existingCourse->update($courseData);
            $updated = true;
        } else {
            CampusCourse::create($courseData);
            $created = true;
        }
        
        return ['created' => $created, 'updated' => $updated, 'errors' => $errors];
    }
    
    private function validateString($value, $field, &$errors, $rowNumber, $required = false)
    {
        $value = trim($value ?? '');
        
        if ($required && empty($value)) {
            $errors[] = "Fila {$rowNumber}: {$field} es requerido";
            return '';
        }
        
        return $value;
    }
    
    private function validateInteger($value, $field, &$errors, $rowNumber, $default = 0)
    {
        if ($value === null || $value === '' || $value === '\N') {
            return $default;
        }
        
        $intValue = (int) $value;
        if ($intValue < 0) {
            $errors[] = "Fila {$rowNumber}: {$field} debe ser positivo o cero";
            return $default;
        }
        
        return $intValue;
    }
    
    private function validateDecimal($value, $field, &$errors, $rowNumber, $default = 0.00)
    {
        if ($value === null || $value === '' || $value === '\N') {
            return $default;
        }
        
        // Limpiar formato (quitar comas, etc.)
        $cleanValue = str_replace(',', '.', preg_replace('/[^\d.,]/', '', $value));
        $decimalValue = (float) $cleanValue;
        
        if ($decimalValue < 0) {
            $errors[] = "Fila {$rowNumber}: {$field} debe ser positivo o cero";
            return $default;
        }
        
        return $decimalValue;
    }
    
    private function validateLevel($value, &$errors, $rowNumber)
    {
        $validLevels = ['beginner', 'intermediate', 'advanced'];
        $level = strtolower(trim($value ?? ''));
        
        if (empty($level)) {
            return 'beginner'; // Default
        }
        
        if (!in_array($level, $validLevels)) {
            $errors[] = "Fila {$rowNumber}: level '{$value}' no es válido. Valores válidos: " . implode(', ', $validLevels);
            return 'beginner';
        }
        
        return $level;
    }
    
    private function validateSchedule($value, &$errors, $rowNumber)
    {
        if (empty($value) || $value === '\N') {
            return null;
        }
        
        try {
            $schedule = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = "Fila {$rowNumber}: schedule JSON inválido: " . json_last_error_msg();
                return null;
            }
            return $schedule;
        } catch (\Exception $e) {
            $errors[] = "Fila {$rowNumber}: schedule error al procesar: " . $e->getMessage();
            return null;
        }
    }
    
    private function validateDate($value, $field, &$errors, $rowNumber)
    {
        if (empty($value) || $value === '\N') {
            return null;
        }
        
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            $errors[] = "Fila {$rowNumber}: {$field} fecha inválida: {$value}";
            return null;
        }
    }
    
    private function validateFormat($value, &$errors, $rowNumber)
    {
        $validFormats = ['Presencial', 'Online', 'Semipresencial', 'Híbrido'];
        $format = trim($value ?? '');
        
        if (empty($format) || $format === '\N') {
            return 'Presencial'; // Default
        }
        
        if (!in_array($format, $validFormats)) {
            $errors[] = "Fila {$rowNumber}: format '{$value}' no es válido. Valores válidos: " . implode(', ', $validFormats);
            return 'Presencial';
        }
        
        return $format;
    }
    
    private function validateBoolean($value, $field, &$errors, $rowNumber, $default = true)
    {
        if ($value === null || $value === '' || $value === '\N') {
            return $default;
        }
        
        return (bool) $value;
    }
    
    private function validateJSON($value, $field, &$errors, $rowNumber)
    {
        if (empty($value) || $value === '\N') {
            return null;
        }
        
        try {
            $json = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = "Fila {$rowNumber}: {$field} JSON inválido: " . json_last_error_msg();
                return null;
            }
            return $json;
        } catch (\Exception $e) {
            $errors[] = "Fila {$rowNumber}: {$field} error al procesar JSON: " . $e->getMessage();
            return null;
        }
    }
    
    private function validateJSONToString($value, $field, &$errors, $rowNumber)
    {
        if (empty($value) || $value === '\N') {
            return null;
        }
        
        try {
            $json = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = "Fila {$rowNumber}: {$field} JSON inválido: " . json_last_error_msg();
                return null;
            }
            return json_encode($json); // Convertir a string
        } catch (\Exception $e) {
            $errors[] = "Fila {$rowNumber}: {$field} error al procesar JSON: " . $e->getMessage();
            return null;
        }
    }
    
    private function printReport($processed, $created, $updated, $errors)
    {
        $this->command->info('');
        $this->command->info('=== REPORTE FINAL ===');
        $this->command->info("📊 Registros procesados: {$processed}");
        $this->command->info("✅ Cursos creados: {$created}");
        $this->command->info("🔄 Cursos actualizados: {$updated}");
        
        if (!empty($errors)) {
            $this->command->warn('');
            $this->command->warn('⚠️  ERRORES ENCONTRADOS (' . count($errors) . '):');
            foreach ($errors as $error) {
                $this->command->warn("   - {$error}");
            }
        } else {
            $this->command->info('');
            $this->command->info('🎉 No se encontraron errores');
        }
        
        $this->command->info('');
        $this->command->info('=== FIN DEL PROCESO ===');
    }
    
    /**
     * Obtener mapeo de temporadas dinámicamente
     */
    private function getSeasonMapping(): array
    {
        // Si hay configuración de producción, usarla
        if (config('seeders.production.season_ids')) {
            $seasonIds = config('seeders.production.season_ids');
            return [
                1 => $seasonIds['curs-2025-26'] ?? 1,      // 2024-25 -> temporada principal
                3 => $seasonIds['curs-2025-26-1q'] ?? 2,  // 2025-26 -> 1r trimestre
                4 => $seasonIds['curs-2025-26-1q'] ?? 2,  // 2025-26 -> 1r trimestre
            ];
        }
        
        // Mapeo por defecto (hardcoded para compatibilidad)
        return [
            1 => 1,  // 2024-25 -> ID: 1 (Curs 2025-26)
            3 => 2,  // 2025-26 -> ID: 2 (Curs 2025-26 - 1r Trimestre)
            4 => 2,  // 2025-26 -> ID: 2 (Curs 2025-26 - 1r Trimestre)
        ];
    }
    
    /**
     * Obtener mapeo de categorías dinámicamente
     */
    private function getCategoryMapping(): array
    {
        // Si hay configuración de producción, usarla
        if (config('seeders.production.category_ids')) {
            $categoryIds = config('seeders.production.category_ids');
            return [
                1 => $categoryIds['sense'] ?? 1,           // Sense Categoria
                2 => $categoryIds['salut-benestar'] ?? 2,  // Salut i Benestar
                3 => $categoryIds['educacio-pedagogia'] ?? 3, // Educació i Pedagogia
                4 => $categoryIds['ciencies-socials-humanitats'] ?? 4, // Ciències Socials i Humanitats
                5 => $categoryIds['tecnologia-informatica'] ?? 5, // Tecnologia i Informàtica
                6 => $categoryIds['gestio-administracio'] ?? 6, // Gestió i Administració
                7 => $categoryIds['llengues-idiomes'] ?? 7, // Idiomes i Llengües
                8 => $categoryIds['arts-disseny'] ?? 8,     // Arts i Disseny
                9 => $categoryIds['ciencies-investigacio'] ?? 9, // Ciències i Investigació
                10 => $categoryIds['esports-benestar'] ?? 10, // Esports i Benestar
                11 => $categoryIds['dret-seguretat'] ?? 11, // Dret i Seguretat
                12 => $categoryIds['formacio-continua'] ?? 12, // Formació Continua
                13 => $categoryIds['desenvolupament-personal'] ?? 13, // Desenvolupament Personal
                14 => $categoryIds['benestar-salut'] ?? 14, // Benestar i Salut
                16 => $categoryIds['sense'] ?? 1,          // CSV 16 -> Sense Categoria
            ];
        }
        
        // Mapeo por defecto (hardcoded para compatibilidad)
        return [
            1 => 1,   // Sense Categoria (ID: 1)
            2 => 2,   // Salut i Benestar (ID: 2)
            3 => 3,   // Educació i Pedagogia (ID: 3)
            4 => 4,   // Ciències Socials i Humanitats (ID: 4)
            5 => 5,   // Tecnologia i Informàtica (ID: 5)
            6 => 6,   // Gestió i Administració (ID: 6)
            7 => 7,   // Idiomes i Llengües (ID: 7)
            8 => 8,   // Arts i Disseny (ID: 8)
            9 => 9,   // Ciències i Investigació (ID: 9)
            10 => 10, // Esports i Benestar (ID: 10)
            11 => 11, // Dret i Seguretat (ID: 11)
            12 => 12, // Formació Continua (ID: 12)
            13 => 13, // Desenvolupament Personal (ID: 13)
            14 => 14, // Benestar i Salut (ID: 14)
            16 => 1,  // CSV 16 -> Sense Categoria (ID: 1)
        ];
    }
}
