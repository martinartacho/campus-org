<?php

namespace App\Services;

use App\Models\CampusCourse;
use App\Models\CampusCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WoodComerceETLService
{
    /**
     * Procesar todos los cursos y generar CSV para WooCommerce
     */
    public function process(): string
    {
        Log::info('WoodComerce ETL: Iniciando procesamiento');
        
        // Obtener todos los cursos activos
        $courses = CampusCourse::where('is_active', 1)
            ->where('is_public', 1)
            ->with(['category', 'season'])
            ->orderBy('code')
            ->get();
        
        Log::info('WoodComerce ETL: Cursos encontrados', ['count' => $courses->count()]);
        
        // Separar productos variables y variaciones
        $variableProducts = $courses->where('parent_id', null);
        $variations = $courses->where('parent_id', '!=', null);
        
        // Procesar productos variables
        $csvData = [];
        foreach ($variableProducts as $course) {
            // Verificar si tiene hijos
            $children = $variations->where('parent_id', $course->id);
            
            if ($children->count() > 0) {
                // Producto variable con variaciones
                $csvData[] = $this->createVariableProduct($course, $children);
                
                // Agregar variaciones
                foreach ($children as $child) {
                    $csvData[] = $this->createVariation($course, $child);
                }
            } else {
                // Producto simple
                $csvData[] = $this->createSimpleProduct($course);
            }
        }
        
        // Generar CSV
        $csvContent = $this->generateCSV($csvData);
        
        Log::info('WoodComerce ETL: Procesamiento completado', ['products' => count($csvData)]);
        
        return $csvContent;
    }
    
    /**
     * Crear producto variable
     */
    protected function createVariableProduct($course, $children): array
    {
        $regularPrice = $children->max('price');
        
        return [
            'type' => 'variable',
            'sku' => $course->code,
            'name' => $course->title,
            'published' => 1,
            'description' => $course->description ?? '',
            'regular_price' => number_format($regularPrice, 2, '.', ''),
            'manage_stock' => 'yes',
            'stock_quantity' => $children->sum('max_students'),
            'categories' => $this->getCategoryPath($course),
            'attributes' => $this->getProductAttributes($children),
            'default_attributes' => $this->getDefaultAttributes($children),
        ];
    }
    
    /**
     * Crear variación
     */
    protected function createVariation($parent, $variation): array
    {
        return [
            'type' => 'variation',
            'sku' => $variation->code,
            'name' => $variation->title,
            'published' => 1,
            'description' => $variation->description ?? '',
            'regular_price' => number_format($variation->price, 2, '.', ''),
            'manage_stock' => 'yes',
            'stock_quantity' => $variation->max_students,
            'parent' => $parent->code, // SKU del parent
            'attribute_values' => $this->getVariationAttributes($variation),
        ];
    }
    
    /**
     * Crear producto simple
     */
    protected function createSimpleProduct($course): array
    {
        return [
            'type' => 'simple',
            'sku' => $course->code,
            'name' => $course->title,
            'published' => 1,
            'description' => $course->description ?? '',
            'regular_price' => number_format($course->price, 2, '.', ''),
            'manage_stock' => 'yes',
            'stock_quantity' => $course->max_students,
            'categories' => $this->getCategoryPath($course),
        ];
    }
    
    /**
     * Obtener ruta de categorías
     */
    private function getCategoryPath($course): string
    {
        if (!$course->category) {
            return 'Cursos';
        }
        
        $category = $course->category;
        $path = [$category->name];
        
        // Agregar categoría padre si existe
        if ($category->parent) {
            array_unshift($path, $category->parent->name);
        }
        
        return implode(' > ', $path);
    }
    
    /**
     * Obtener atributos del producto variable
     */
    private function getProductAttributes($children): array
    {
        $attributes = [];
        
        // Detectar atributos basados en las diferencias entre variaciones
        $formats = $children->pluck('format')->filter()->unique();
        if ($formats->count() > 1) {
            $attributes[] = [
                'name' => 'Format',
                'values' => $formats->implode(', '),
                'visible' => 1,
                'variation' => 1,
            ];
        }
        
        // Detectar diferencias en horarios (schedule)
        $schedules = $children->pluck('schedule')->filter()->unique();
        if ($schedules->count() > 1) {
            $attributes[] = [
                'name' => 'Horario',
                'values' => $schedules->implode(', '),
                'visible' => 1,
                'variation' => 1,
            ];
        }
        
        // Detectar diferencias en ubicación
        $locations = $children->pluck('location')->filter()->unique();
        if ($locations->count() > 1) {
            $attributes[] = [
                'name' => 'Ubicación',
                'values' => $locations->implode(', '),
                'visible' => 1,
                'variation' => 1,
            ];
        }
        
        return $attributes;
    }
    
    /**
     * Obtener atributos por defecto
     */
    private function getDefaultAttributes($children): array
    {
        $defaults = [];
        
        // Usar la primera variación como defecto
        $firstChild = $children->first();
        
        if ($firstChild->format) {
            $defaults[] = [
                'name' => 'Format',
                'value' => $firstChild->format,
            ];
        }
        
        if ($firstChild->schedule) {
            $defaults[] = [
                'name' => 'Horario',
                'value' => $firstChild->schedule,
            ];
        }
        
        if ($firstChild->location) {
            $defaults[] = [
                'name' => 'Ubicación',
                'value' => $firstChild->location,
            ];
        }
        
        return $defaults;
    }
    
    /**
     * Obtener atributos de variación
     */
    private function getVariationAttributes($variation): array
    {
        $attributes = [];
        
        if ($variation->format) {
            $attributes[] = [
                'name' => 'Format',
                'value' => $variation->format,
            ];
        }
        
        if ($variation->schedule) {
            $attributes[] = [
                'name' => 'Horario',
                'value' => $variation->schedule,
            ];
        }
        
        if ($variation->location) {
            $attributes[] = [
                'name' => 'Ubicación',
                'value' => $variation->location,
            ];
        }
        
        return $attributes;
    }
    
    /**
     * Generar contenido CSV
     */
    private function generateCSV(array $data): string
    {
        // Cabeceras WooCommerce
        $headers = [
            'type',
            'sku',
            'name',
            'published',
            'description',
            'regular_price',
            'manage_stock',
            'stock_quantity',
            'categories',
            'attributes',
            'default_attributes',
            'parent',
            'attribute_values',
        ];
        
        $csv = implode(',', array_map([$this, 'escapeCSV'], $headers)) . "\n";
        
        foreach ($data as $row) {
            $csvRow = [];
            foreach ($headers as $header) {
                $value = $row[$header] ?? '';
                
                // Formatear arrays como JSON
                if (is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                
                $csvRow[] = $this->escapeCSV($value);
            }
            $csv .= implode(',', $csvRow) . "\n";
        }
        
        return $csv;
    }
    
    /**
     * Escapar valor para CSV
     */
    private function escapeCSV($value): string
    {
        if (is_string($value) && (strpos($value, ',') !== false || strpos($value, '"') !== false || strpos($value, "\n") !== false)) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return (string) $value;
    }
    
    /**
     * Procesar cursos específicos para exportación seleccionada
     */
    public function processSpecific(array $courseIds): array
    {
        $courses = CampusCourse::whereIn('id', $courseIds)
            ->with(['category', 'season'])
            ->orderBy('code')
            ->get();
        
        $csvData = [];
        
        foreach ($courses as $course) {
            if ($course->parent_id) {
                // Es variación, buscar parent
                $parent = CampusCourse::find($course->parent_id);
                if ($parent) {
                    $csvData[] = $this->createVariation($parent, $course);
                }
            } else {
                // Es parent, verificar si tiene hijos
                $children = CampusCourse::where('parent_id', $course->id)->get();
                
                if ($children->count() > 0) {
                    $csvData[] = $this->createVariableProduct($course, $children);
                } else {
                    $csvData[] = $this->createSimpleProduct($course);
                }
            }
        }
        
        return $csvData;
    }
}
