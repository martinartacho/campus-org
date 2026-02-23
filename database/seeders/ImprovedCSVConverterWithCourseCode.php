<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ImprovedCSVConverterWithCourseCode extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Conversor Mejorado CSV con Código de Curso ===');
        
        // Rutas de archivos
        $inputCsvPath = storage_path('app/imports/ordres_original.csv');
        $coursesCsvPath = storage_path('app/imports/campus_courses.csv');
        $outputPath = storage_path('app/exports/ordres_mejorado_con_curso_code.csv');
        
        $this->command->info("📂 CSV de entrada: $inputCsvPath");
        $this->command->info("📚 Cursos CSV: $coursesCsvPath");
        $this->command->info("💾 Salida CSV: $outputPath");
        
        try {
            // 1. Construir mapa de cursos mejorado
            $courseMap = $this->buildEnhancedCourseMap($coursesCsvPath);
            $this->command->info("✅ Mapa de cursos mejorado: " . count($courseMap) . " cursos");
            
            // 2. Procesar CSV con mejor manejo de comillas
            $ordresData = $this->processEnhancedCSV($inputCsvPath, $courseMap);
            $this->command->info("✅ Órdenes procesadas: " . count($ordresData) . " registros");
            
            // 3. Escribir CSV mejorado
            $this->writeEnhancedCSV($outputPath, $ordresData);
            
            $this->command->info("✅ CSV mejorado generado exitosamente");
            $this->showEnhancedStatistics($ordresData);
            
        } catch (\Exception $e) {
            $this->command->error("❌ Error: " . $e->getMessage());
        }
    }
    
    /**
     * Construir mapa de cursos mejorado con múltiples variaciones
     */
    private function buildEnhancedCourseMap($csvPath)
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
                    $this->addAllVariations($courseMap, $title, $code);
                }
            }
        }
        
        fclose($handle);
        return $courseMap;
    }
    
    /**
     * Añadir todas las variaciones posibles para mejorar coincidencias
     */
    private function addAllVariations(&$courseMap, $title, $code)
    {
        $baseTitle = strtolower($title);
        
        // Variaciones de limpieza
        $variations = [
            $baseTitle,
            str_replace(['·', '´', '`', "'", '"'], '', $baseTitle),
            str_replace(['(', ')'], ' ', $baseTitle),
            preg_replace('/\s+/', ' ', trim($baseTitle)),
            preg_replace('/\s+/', ' ', trim(str_replace(['·', '´', '`', "'", '"', '(', ')'], '', $baseTitle))),
            trim($baseTitle),
            trim($baseTitle, ' '),
            trim($baseTitle, '()'),
        ];
        
        // Añadir palabras clave para coincidencias parciales
        $keywords = $this->extractKeywords($title);
        foreach ($keywords as $keyword) {
            $variations[] = strtolower($keyword);
        }
        
        foreach ($variations as $variation) {
            if (!empty($variation) && !isset($courseMap[$variation])) {
                $courseMap[$variation] = $code;
            }
        }
    }
    
    /**
     * Extraer palabras clave del título
     */
    private function extractKeywords($title)
    {
        $keywords = [];
        
        // Palabras clave comunes en catalán/castellano
        $commonWords = ['de', 'la', 'el', 'los', 'las', 'del', 'en', 'con', 'por', 'para', 'y', 'o', 'a', 'al', 'un', 'una', 'dels', 'dela', 'dels', 'amb', 'per', 'i', 'o', 'a', 'al', 'un', 'una'];
        
        // Dividir en palabras
        $words = preg_split('/[\s\(\)\-\,\;\:\.]+/', $title);
        
        foreach ($words as $word) {
            $cleanWord = strtolower(trim($word));
            if (strlen($cleanWord) > 2 && !in_array($cleanWord, $commonWords)) {
                $keywords[] = $cleanWord;
            }
        }
        
        return array_unique($keywords);
    }
    
    /**
     * Procesar CSV con mejor manejo de comillas
     */
    private function processEnhancedCSV($csvPath, $courseMap)
    {
        $ordres = [];
        $handle = fopen($csvPath, 'r');
        
        if ($handle === false) {
            throw new \Exception("No se puede abrir el archivo CSV de órdenes");
        }
        
        // Leer cabecera
        $headers = fgetcsv($handle, 0, ',', '"'); // Usar coma como delimitador
        $this->command->info("📋 Cabeceras detectadas: " . implode(', ', $headers));
        
        while (($row = fgetcsv($handle, 0, ',', '"')) !== false) {
            if (count($row) >= 5) {
                $firstName = $row[0] ?? '';
                $lastName = $row[1] ?? '';
                $email = $row[2] ?? '';
                $phone = $row[3] ?? '';
                $itemName = $row[4] ?? '';
                $status = $row[5] ?? '';
                $quantity = $row[6] ?? '1';
                $price = $row[7] ?? '';
                
                // Buscar código de curso con múltiples estrategias
                $courseCode = $this->findBestCourseCode($itemName, $courseMap);
                
                $ordres[] = [
                    'firstname' => $firstName,
                    'lastname' => $lastName,
                    'email' => $email,
                    'phone' => $phone,
                    'item_name' => $itemName,
                    'course_code' => $courseCode,
                    'status' => $status,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            }
        }
        
        fclose($handle);
        return $ordres;
    }
    
    /**
     * Encontrar el mejor código de curso usando múltiples estrategias
     */
    private function findBestCourseCode($itemName, $courseMap)
    {
        $normalizedItem = strtolower(trim($itemName, '"'));
        
        // 1. Búsqueda exacta
        if (isset($courseMap[$normalizedItem])) {
            return $courseMap[$normalizedItem];
        }
        
        // 2. Búsqueda sin caracteres especiales
        $cleanItem = preg_replace('/[^a-z0-9\s]/', ' ', $normalizedItem);
        $cleanItem = preg_replace('/\s+/', ' ', trim($cleanItem));
        if (isset($courseMap[$cleanItem])) {
            return $courseMap[$cleanItem];
        }
        
        // 3. Búsqueda por palabras clave
        $keywords = $this->extractKeywords($itemName);
        foreach ($keywords as $keyword) {
            if (isset($courseMap[$keyword])) {
                return $courseMap[$keyword];
            }
        }
        
        // 4. Búsqueda por similitud
        $bestMatch = '';
        $bestScore = 0;
        foreach ($courseMap as $title => $code) {
            $similarity = $this->calculateSimilarity($normalizedItem, $title);
            if ($similarity > $bestScore && $similarity > 0.7) {
                $bestScore = $similarity;
                $bestMatch = $code;
            }
        }
        
        if ($bestMatch) {
            return $bestMatch;
        }
        
        // 5. Búsqueda por coincidencia parcial
        foreach ($courseMap as $title => $code) {
            if (strpos($title, $cleanItem) !== false || strpos($cleanItem, $title) !== false) {
                return $code;
            }
        }
        
        return 'NOT_FOUND';
    }
    
    /**
     * Calcular similitud mejorada
     */
    private function calculateSimilarity($str1, $str2)
    {
        similar_text($str1, $str2, $percent);
        return $percent / 100;
    }
    
    /**
     * Escribir CSV mejorado
     */
    private function writeEnhancedCSV($outputPath, $data)
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
     * Mostrar estadísticas mejoradas
     */
    private function showEnhancedStatistics($data)
    {
        $total = count($data);
        $withCode = 0;
        $withoutCode = 0;
        $uniqueCodes = [];
        $codeDistribution = [];
        
        foreach ($data as $row) {
            if ($row['course_code'] !== 'NOT_FOUND') {
                $withCode++;
                $uniqueCodes[] = $row['course_code'];
                $codeDistribution[$row['course_code']] = ($codeDistribution[$row['course_code']] ?? 0) + 1;
            } else {
                $withoutCode++;
            }
        }
        
        $this->command->info("\n=== ESTADÍSTICAS MEJORADAS ===");
        $this->command->info("📊 Total de órdenes: $total");
        $this->command->info("✅ Con código de curso: $withCode");
        $this->command->info("❌ Sin código de curso: $withoutCode");
        $this->command->info("📈 Tasa de coincidencia: " . round(($withCode / $total) * 100, 2) . "%");
        $this->command->info("🎯 Códigos únicos: " . count(array_unique($uniqueCodes)));
        
        // Mostrar distribución de códigos más usados
        $this->command->info("\n📈 Códigos más usados:");
        arsort($codeDistribution);
        $count = 0;
        foreach ($codeDistribution as $code => $frequency) {
            if ($count >= 5) break;
            $this->command->info("   • $code: $frequency órdenes");
            $count++;
        }
        
        // Mostrar ejemplos de coincidencias
        $this->command->info("\n🎯 Ejemplos de coincidencias exitosas:");
        $count = 0;
        foreach ($data as $row) {
            if ($row['course_code'] !== 'NOT_FOUND' && $count < 5) {
                $this->command->info("   • '{$row['item_name']}' → {$row['course_code']}");
                $count++;
            }
        }
    }
}
