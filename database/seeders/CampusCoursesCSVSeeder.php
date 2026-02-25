<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CampusSeason;
use App\Models\CampusCategory;

class CampusCoursesCSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importación Completa de Cursos desde CSV ===');
        
        // Validar que existan temporadas y categorías
        $seasonsCount = CampusSeason::count();
        $categoriesCount = CampusCategory::count();
        
        if ($seasonsCount === 0) {
            $this->command->error('❌ No hay temporadas en la base de datos. Ejecuta CampusSeeder primero.');
            return;
        }
        
        if ($categoriesCount === 0) {
            $this->command->error('❌ No hay categorías en la base de datos. Ejecuta CampusCategoriesCSVSeeder primero.');
            return;
        }
        
        $this->command->info("✅ Validación: $seasonsCount temporadas, $categoriesCount categorías encontradas");
        
        // Leer archivo CSV
        $csvPath = storage_path('app/imports/campus_courses.csv');
        
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
                
                $course = $this->parseCourseRow($row);
                
                if (!$course) {
                    $skippedCount++;
                    continue;
                }
                
                // Validar que existan temporada y categoría
                $seasonExists = CampusSeason::find($course['season_id']);
                $categoryExists = CampusCategory::find($course['category_id']);
                
                if (!$seasonExists) {
                    $this->command->warn("⚠️ Temporada {$course['season_id']} no encontrada - Saltando: {$course['code']}");
                    $skippedCount++;
                    continue;
                }
                
                if (!$categoryExists) {
                    $this->command->warn("⚠️ Categoría {$course['category_id']} no encontrada - Saltando: {$course['code']}");
                    $skippedCount++;
                    continue;
                }
                
                // Importar curso
                DB::table('campus_courses')->updateOrInsert(
                    ['id' => $course['id']],
                    $course
                );
                
                $importedCount++;
                
                // Mostrar progreso cada 10 cursos
                if ($importedCount % 10 === 0) {
                    $this->command->info("✅ Progreso: $importedCount cursos importados...");
                }
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->command->error("❌ Error en fila $index: " . $e->getMessage());
            }
        }
        
        $this->command->info("\n=== RESUMEN DE IMPORTACIÓN ===");
        $this->command->info("✅ Cursos importados: $importedCount");
        $this->command->info("⚠️ Cursos omitidos: $skippedCount");
        $this->command->info("❌ Errores: $errorCount");
        
        // Verificar importación
        $totalCourses = DB::table('campus_courses')->count();
        $activeCourses = DB::table('campus_courses')->where('is_active', 1)->count();
        
        $this->command->info("📊 Total cursos en base de datos: $totalCourses");
        $this->command->info("📊 Cursos activos: $activeCourses");
        
        // Mostrar distribución por temporada
        $this->showCoursesBySeason();
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
     * Parsear fila de curso
     */
    private function parseCourseRow($row)
    {
        if (count($row) < 22) {
            return null;
        }
        
        // Mapear campos del CSV
        $course = [
            'id' => $this->parseValue($row[0]),
            'season_id' => $this->parseValue($row[1]),
            'category_id' => $this->parseValue($row[2]),
            'code' => $this->parseValue($row[3]),
            'title' => $this->parseValue($row[4]),
            'slug' => $this->parseValue($row[5]),
            'description' => $this->parseValue($row[6]),
            'credits' => $this->parseValue($row[7]),
            'hours' => $this->parseValue($row[8]),
            'max_students' => $this->parseValue($row[9]),
            'price' => $this->parsePrice($row[10]),
            'level' => $this->parseValue($row[11]),
            'schedule' => $this->parseValue($row[12]),
            'start_date' => $this->parseValue($row[13]),
            'end_date' => $this->parseValue($row[14]),
            'location' => $this->parseNull($row[15]),
            'format' => $this->parseNull($row[16]),
            'is_active' => $this->parseBoolean($row[17]),
            'is_public' => $this->parseBoolean($row[18]),
            'requirements' => $this->parseValue($row[19]),
            'objectives' => $this->parseValue($row[20]),
            'metadata' => $this->parseNull($row[21]),
            'created_at' => $this->parseValue($row[22]) ?? now(),
            'updated_at' => $this->parseValue($row[23]) ?? now(),
        ];
        
        return $course;
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
     * Parsear valor nulo
     */
    private function parseNull($value)
    {
        $parsed = $this->parseValue($value);
        return $parsed === '\N' || $parsed === '' ? null : $parsed;
    }
    
    /**
     * Parsear precio (reemplazar coma por punto)
     */
    private function parsePrice($price)
    {
        $parsed = $this->parseValue($price);
        if ($parsed === null) {
            return 0.0;
        }
        
        return (float) str_replace(',', '.', $parsed);
    }
    
    /**
     * Parsear booleano
     */
    private function parseBoolean($value)
    {
        $parsed = $this->parseValue($value);
        return $parsed === '1' || $parsed === 1 || $parsed === true;
    }
    
    /**
     * Mostrar distribución de cursos por temporada
     */
    private function showCoursesBySeason()
    {
        $this->command->info("\n📊 Distribución por Temporada:");
        
        $seasons = CampusSeason::withCount('courses')->get();
        
        foreach ($seasons as $season) {
            $this->command->info("   📅 {$season->name}: {$season->courses_count} cursos");
        }
    }
}
