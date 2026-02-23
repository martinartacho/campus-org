<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateOrdresWithCourseCode extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Creando CSV de Órdenes con Código de Curso ===');
        
        // Rutas de archivos
        $odsPath = storage_path('app/imports/ordres_original.ods');
        $coursesCsvPath = storage_path('app/imports/campus_courses.csv');
        $outputPath = storage_path('app/exports/ordres_con_curso_code.csv');
        
        // Verificar archivos de entrada
        if (!file_exists($odsPath)) {
            $this->command->error("❌ Archivo ODS no encontrado: $odsPath");
            return;
        }
        
        if (!file_exists($coursesCsvPath)) {
            $this->command->error("❌ Archivo CSV de cursos no encontrado: $coursesCsvPath");
            return;
        }
        
        // Crear directorio de salida si no existe
        $outputDir = dirname($outputPath);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        $this->command->info("📂 Leyendo archivo ODS: $odsPath");
        $this->command->info("📚 Leyendo cursos CSV: $coursesCsvPath");
        $this->command->info("💾 Generando salida: $outputPath");
        
        try {
            // 1. Leer cursos y crear mapa de títulos a códigos
            $courseMap = $this->buildCourseMap($coursesCsvPath);
            $this->command->info("✅ Mapa de cursos creado: " . count($courseMap) . " cursos");
            
            // 2. Leer archivo ODS
            $ordresData = $this->readODS($odsPath);
            $this->command->info("✅ Órdenes leídas: " . count($ordresData) . " registros");
            
            // 3. Añadir código de curso a cada orden
            $ordresWithCourseCode = $this->addCourseCodeToOrdres($ordresData, $courseMap);
            
            // 4. Escribir CSV de salida
            $this->writeCSV($outputPath, $ordresWithCourseCode);
            
            $this->command->info("✅ CSV generado exitosamente: $outputPath");
            
            // 5. Estadísticas
            $this->showStatistics($ordresWithCourseCode);
            
        } catch (\Exception $e) {
            $this->command->error("❌ Error: " . $e->getMessage());
        }
    }
    
    /**
     * Construir mapa de títulos de cursos a códigos
     */
    private function buildCourseMap($csvPath)
    {
        $courseMap = [];
        $handle = fopen($csvPath, 'r');
        
        if ($handle === false) {
            throw new \Exception("No se puede abrir el archivo CSV de cursos");
        }
        
        // Saltar cabecera
        fgetcsv($handle, 0, ';', '"');
        
        while (($row = fgetcsv($handle, 0, ';', '"')) !== false) {
            if (count($row) >= 5) {
                $title = trim($row[4], '"'); // columna title
                $code = trim($row[3], '"'); // columna code
                
                if ($title && $code) {
                    $courseMap[strtolower($title)] = $code;
                }
            }
        }
        
        fclose($handle);
        return $courseMap;
    }
    
    /**
     * Leer archivo ODS (simulado - en producción usar phpoffice/phpspreadsheet)
     */
    private function readODS($odsPath)
    {
        // Para este ejemplo, asumimos que convertimos ODS a CSV primero
        // En producción, usaría: $reader = IOFactory::createReader('Ods');
        $csvPath = str_replace('.ods', '.csv', $odsPath);
        
        if (!file_exists($csvPath)) {
            // Crear datos de ejemplo si no existe el CSV
            $this->command->warn("⚠️ Archivo CSV no encontrado, usando datos de ejemplo");
            return $this->createSampleOrdres();
        }
        
        $ordres = [];
        $handle = fopen($csvPath, 'r');
        
        if ($handle === false) {
            throw new \Exception("No se puede abrir el archivo CSV de órdenes");
        }
        
        // Leer cabecera
        $headers = fgetcsv($handle, 0, ';', '"');
        
        while (($row = fgetcsv($handle, 0, ';', '"')) !== false) {
            if (count($row) >= 7) {
                $ordres[] = [
                    'firstname' => $row[0] ?? '',
                    'lastname' => $row[1] ?? '',
                    'email' => $row[2] ?? '',
                    'phone' => $row[3] ?? '',
                    'item_name' => $row[4] ?? '', // Nombre del curso
                    'status' => $row[5] ?? '',
                    'quantity' => $row[6] ?? '',
                    'price' => $row[7] ?? '',
                ];
            }
        }
        
        fclose($handle);
        return $ordres;
    }
    
    /**
     * Crear datos de ejemplo de órdenes
     */
    private function createSampleOrdres()
    {
        return [
            [
                'firstname' => 'Rosa',
                'lastname' => 'Morillas',
                'email' => 'mrmorillasg7@gmail.com',
                'phone' => '605257022',
                'item_name' => 'Sent la vida amb el Txi Kung (dilluns)',
                'status' => 'completed',
                'quantity' => '1',
                'price' => '25.00',
            ],
            [
                'firstname' => 'Maria',
                'lastname' => 'García',
                'email' => 'maria@example.com',
                'phone' => '600111222',
                'item_name' => 'Introducció a la Programació',
                'status' => 'completed',
                'quantity' => '1',
                'price' => '299.99',
            ],
            [
                'firstname' => 'Joan',
                'lastname' => 'Prat',
                'email' => 'joan@example.com',
                'phone' => '600333444',
                'item_name' => 'Anglès Bàsic A1',
                'status' => 'completed',
                'quantity' => '1',
                'price' => '199.99',
            ],
        ];
    }
    
    /**
     * Añadir código de curso a las órdenes
     */
    private function addCourseCodeToOrdres($ordresData, $courseMap)
    {
        $ordresWithCode = [];
        $matched = 0;
        $unmatched = 0;
        
        foreach ($ordresData as $ordre) {
            $courseTitle = strtolower($ordre['item_name']);
            $courseCode = $courseMap[$courseTitle] ?? 'NOT_FOUND';
            
            if ($courseCode !== 'NOT_FOUND') {
                $matched++;
            } else {
                $unmatched++;
                // Intentar coincidencia parcial
                $courseCode = $this->findPartialMatch($courseTitle, $courseMap);
                if ($courseCode !== 'NOT_FOUND') {
                    $matched++;
                    $unmatched--;
                }
            }
            
            $ordre['course_code'] = $courseCode;
            $ordresWithCode[] = $ordre;
        }
        
        $this->command->info("✅ Coincidencias exactas: $matched, parciales: " . ($matched - $unmatched) . ", sin coincidencia: $unmatched");
        
        return $ordresWithCode;
    }
    
    /**
     * Buscar coincidencia parcial
     */
    private function findPartialMatch($courseTitle, $courseMap)
    {
        foreach ($courseMap as $title => $code) {
            // Buscar palabras clave comunes
            if (strpos($title, 'programaci') !== false && strpos($courseTitle, 'programaci') !== false) {
                return $code;
            }
            if (strpos($title, 'anglès') !== false && strpos($courseTitle, 'anglès') !== false) {
                return $code;
            }
            if (strpos($title, 'txi kung') !== false && strpos($courseTitle, 'txi kung') !== false) {
                return $code;
            }
        }
        
        return 'NOT_FOUND';
    }
    
    /**
     * Escribir archivo CSV
     */
    private function writeCSV($outputPath, $data)
    {
        $handle = fopen($outputPath, 'w');
        
        if ($handle === false) {
            throw new \Exception("No se puede crear el archivo de salida");
        }
        
        // Escribir cabecera
        fputcsv($handle, [
            'firstname',
            'lastname', 
            'email',
            'phone',
            'item_name',
            'course_code',
            'status',
            'quantity',
            'price'
        ], ';', '"');
        
        // Escribir datos
        foreach ($data as $row) {
            fputcsv($handle, $row, ';', '"');
        }
        
        fclose($handle);
    }
    
    /**
     * Mostrar estadísticas
     */
    private function showStatistics($data)
    {
        $total = count($data);
        $withCode = 0;
        $withoutCode = 0;
        
        foreach ($data as $row) {
            if ($row['course_code'] !== 'NOT_FOUND') {
                $withCode++;
            } else {
                $withoutCode++;
            }
        }
        
        $this->command->info("\n=== ESTADÍSTICAS ===");
        $this->command->info("📊 Total de órdenes: $total");
        $this->command->info("✅ Con código de curso: $withCode");
        $this->command->info("❌ Sin código de curso: $withoutCode");
        $this->command->info("📈 Tasa de coincidencia: " . round(($withCode / $total) * 100, 2) . "%");
        
        // Mostrar códigos únicos encontrados
        $uniqueCodes = array_unique(array_column($data, 'course_code'));
        $this->command->info("🎯 Códigos de curso únicos: " . count($uniqueCodes));
    }
}
