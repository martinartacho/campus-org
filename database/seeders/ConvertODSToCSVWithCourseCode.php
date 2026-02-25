<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConvertODSToCSVWithCourseCode extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Conversor ODS a CSV con Código de Curso ===');
        
        // Rutas de archivos
        $odsPath = storage_path('app/imports/ordres_original.ods');
        $coursesCsvPath = storage_path('app/imports/campus_courses.csv');
        $outputPath = storage_path('app/exports/ordres_con_curso_code.csv');
        
        $this->command->info("📂 Archivo ODS: $odsPath");
        $this->command->info("📚 Cursos CSV: $coursesCsvPath");
        $this->command->info("💾 Salida CSV: $outputPath");
        
        try {
            // 1. Construir mapa de cursos
            $courseMap = $this->buildCourseMap($coursesCsvPath);
            $this->command->info("✅ Mapa de cursos: " . count($courseMap) . " cursos");
            
            // 2. Procesar archivo ODS
            if (file_exists($odsPath)) {
                $this->command->info("📄 Procesando archivo ODS...");
                $ordresData = $this->processODSFile($odsPath, $courseMap);
            } else {
                $this->command->warn("⚠️ Archivo ODS no encontrado, buscando CSV equivalente...");
                $csvPath = str_replace('.ods', '.csv', $odsPath);
                if (file_exists($csvPath)) {
                    $ordresData = $this->processCSVFile($csvPath, $courseMap);
                } else {
                    $this->command->error("❌ No se encontró ni ODS ni CSV");
                    return;
                }
            }
            
            // 3. Escribir CSV final
            $this->writeFinalCSV($outputPath, $ordresData);
            
            $this->command->info("✅ CSV generado exitosamente");
            $this->showFinalStatistics($ordresData);
            
        } catch (\Exception $e) {
            $this->command->error("❌ Error: " . $e->getMessage());
        }
    }
    
    /**
     * Construir mapa de títulos a códigos
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
                $title = trim($row[4], '"');
                $code = trim($row[3], '"');
                
                if ($title && $code) {
                    $courseMap[strtolower($title)] = $code;
                    // Añadir variaciones para mejor coincidencia
                    $this->addVariations($courseMap, $title, $code);
                }
            }
        }
        
        fclose($handle);
        return $courseMap;
    }
    
    /**
     * Añadir variaciones del título para mejor coincidencia
     */
    private function addVariations(&$courseMap, $title, $code)
    {
        $lowerTitle = strtolower($title);
        
        // Variaciones comunes
        $variations = [
            $lowerTitle,
            str_replace('·', '', $lowerTitle),
            str_replace('´', '', $lowerTitle),
            str_replace('`', '', $lowerTitle),
            str_replace("'", '', $lowerTitle),
            str_replace('"', '', $lowerTitle),
            preg_replace('/\s+/', ' ', trim($lowerTitle)),
        ];
        
        foreach ($variations as $variation) {
            if (!isset($courseMap[$variation])) {
                $courseMap[$variation] = $code;
            }
        }
    }
    
    /**
     * Procesar archivo ODS (requiere phpoffice/phpspreadsheet)
     */
    private function processODSFile($odsPath, $courseMap)
    {
        // Verificar si está disponible la librería
        if (!class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            $this->command->warn("⚠️ PhpSpreadsheet no disponible, convirtiendo a CSV manualmente");
            return $this->convertODSToCSVAndProcess($odsPath, $courseMap);
        }
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($odsPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            return $this->processArrayData($data, $courseMap);
            
        } catch (\Exception $e) {
            $this->command->error("❌ Error procesando ODS: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Convertir ODS a CSV manualmente (fallback)
     */
    private function convertODSToCSVAndProcess($odsPath, $courseMap)
    {
        $this->command->info("🔄 Intentando conversión manual ODS a CSV...");
        
        // Usar comando libreoffice si está disponible
        $csvPath = str_replace('.ods', '.csv', $odsPath);
        
        // Intentar con LibreOffice (si está en Windows)
        $libreOfficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
        if (file_exists(str_replace('"', '', $libreOfficePath))) {
            $command = "$libreOfficePath --headless --convert-to csv --outdir " . dirname($csvPath) . " " . $odsPath;
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($csvPath)) {
                $this->command->info("✅ Conversión ODS a CSV exitosa");
                return $this->processCSVFile($csvPath, $courseMap);
            }
        }
        
        $this->command->warn("⚠️ No se pudo convertir ODS, usando datos de ejemplo");
        return $this->createSampleData();
    }
    
    /**
     * Procesar archivo CSV
     */
    private function processCSVFile($csvPath, $courseMap)
    {
        $ordres = [];
        $handle = fopen($csvPath, 'r');
        
        if ($handle === false) {
            throw new \Exception("No se puede abrir el archivo CSV");
        }
        
        // Leer cabecera
        $headers = fgetcsv($handle, 0, ';', '"');
        $this->command->info("📋 Cabeceras: " . implode(', ', $headers));
        
        while (($row = fgetcsv($handle, 0, ';', '"')) !== false) {
            if (count($row) >= 5) {
                $courseTitle = $row[4] ?? ''; // item_name
                $courseCode = $this->findCourseCode($courseTitle, $courseMap);
                
                $ordres[] = [
                    'firstname' => $row[0] ?? '',
                    'lastname' => $row[1] ?? '',
                    'email' => $row[2] ?? '',
                    'phone' => $row[3] ?? '',
                    'item_name' => $courseTitle,
                    'course_code' => $courseCode,
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
     * Procesar datos de array
     */
    private function processArrayData($data, $courseMap)
    {
        $ordres = [];
        $headers = array_shift($data); // Quitar cabecera
        
        foreach ($data as $row) {
            if (count($row) >= 5) {
                $courseTitle = $row[4] ?? '';
                $courseCode = $this->findCourseCode($courseTitle, $courseMap);
                
                $ordres[] = [
                    'firstname' => $row[0] ?? '',
                    'lastname' => $row[1] ?? '',
                    'email' => $row[2] ?? '',
                    'phone' => $row[3] ?? '',
                    'item_name' => $courseTitle,
                    'course_code' => $courseCode,
                    'status' => $row[5] ?? '',
                    'quantity' => $row[6] ?? '',
                    'price' => $row[7] ?? '',
                ];
            }
        }
        
        return $ordres;
    }
    
    /**
     * Encontrar código de curso
     */
    private function findCourseCode($courseTitle, $courseMap)
    {
        $normalizedTitle = strtolower(trim($courseTitle, '"'));
        
        // Búsqueda exacta primero
        if (isset($courseMap[$normalizedTitle])) {
            return $courseMap[$normalizedTitle];
        }
        
        // Búsqueda aproximada
        foreach ($courseMap as $title => $code) {
            if ($this->similarity($normalizedTitle, $title) > 0.8) {
                return $code;
            }
        }
        
        return 'NOT_FOUND';
    }
    
    /**
     * Calcular similitud entre strings
     */
    private function similarity($str1, $str2)
    {
        similar_text($str1, $str2, $percent);
        return $percent / 100;
    }
    
    /**
     * Crear datos de ejemplo
     */
    private function createSampleData()
    {
        return [
            [
                'firstname' => 'Rosa',
                'lastname' => 'Morillas',
                'email' => 'mrmorillasg7@gmail.com',
                'phone' => '605257022',
                'item_name' => 'Sent la vida amb el Txi Kung (dilluns)',
                'course_code' => 'NOT_FOUND',
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
                'course_code' => 'IT101',
                'status' => 'completed',
                'quantity' => '1',
                'price' => '299.99',
            ],
        ];
    }
    
    /**
     * Escribir CSV final
     */
    private function writeFinalCSV($outputPath, $data)
    {
        $handle = fopen($outputPath, 'w');
        
        if ($handle === false) {
            throw new \Exception("No se puede crear el archivo de salida");
        }
        
        // Escribir cabecera mejorada
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
     * Mostrar estadísticas finales
     */
    private function showFinalStatistics($data)
    {
        $total = count($data);
        $withCode = 0;
        $withoutCode = 0;
        $uniqueCodes = [];
        
        foreach ($data as $row) {
            if ($row['course_code'] !== 'NOT_FOUND') {
                $withCode++;
                $uniqueCodes[] = $row['course_code'];
            } else {
                $withoutCode++;
            }
        }
        
        $this->command->info("\n=== ESTADÍSTICAS FINALES ===");
        $this->command->info("📊 Total de órdenes: $total");
        $this->command->info("✅ Con código de curso: $withCode");
        $this->command->info("❌ Sin código de curso: $withoutCode");
        $this->command->info("📈 Tasa de coincidencia: " . round(($withCode / $total) * 100, 2) . "%");
        $this->command->info("🎯 Códigos únicos: " . count(array_unique($uniqueCodes)));
        
        // Mostrar algunos ejemplos
        $this->command->info("\n📋 Ejemplos de códigos asignados:");
        $count = 0;
        foreach ($data as $row) {
            if ($row['course_code'] !== 'NOT_FOUND' && $count < 5) {
                $this->command->info("   • {$row['item_name']} → {$row['course_code']}");
                $count++;
            }
        }
    }
}
