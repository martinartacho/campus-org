<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class TeacherDocumentCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener categoría principal "Documentació"
        $parentCategory = DocumentCategory::where('slug', 'documentacio')->first();
        
        if (!$parentCategory) {
            $this->command->error('Categoría "Documentació" no encontrada. Ejecuta primero las migraciones y seeders básicos.');
            return;
        }

        // Crear categorías específicas para profesores
        $categories = [
            [
                'name' => 'Material Docente',
                'slug' => 'material-docente',
                'description' => 'Documentos y materiales educativos para profesores',
                'sort_order' => 10,
            ],
            [
                'name' => 'Tareas y Ejercicios',
                'slug' => 'tareas-ejercicios',
                'description' => 'Tareas, ejercicios y actividades para estudiantes',
                'sort_order' => 20,
            ],
            [
                'name' => 'Evaluaciones',
                'slug' => 'evaluaciones',
                'description' => 'Exámenes, controles y herramientas de evaluación',
                'sort_order' => 30,
            ],
            [
                'name' => 'Recursos Educativos',
                'slug' => 'recursos-educativos',
                'description' => 'Material complementario y recursos adicionales',
                'sort_order' => 40,
            ],
        ];

        foreach ($categories as $categoryData) {
            DocumentCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                array_merge($categoryData, [
                    'parent_id' => $parentCategory->id,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Categorías de documentos para profesores creadas exitosamente.');
    }
}
