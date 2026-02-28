<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CampusCategory;
use App\Models\CampusSeason;

class IniciCategoriesSeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. CREAR CATEGORIES (des de CSV)
        $categories = [
            [
                'name' => 'Sense Categoria',
                'slug' => 'sense',
                'description' => 'Sense categoria definida',
                'color' => '#3b82f6',
                'icon' => 'tag',
                'order' => 1,
            ],
            [
                'name' => 'Salut i Benestar',
                'slug' => 'salut-benestar',
                'description' => 'Cursos relacionats amb la salut, nutrició i benestar personal',
                'color' => '#10b981',
                'icon' => 'heart',
                'order' => 2,
            ],
            [
                'name' => 'Educació i Pedagogia',
                'slug' => 'educacio-pedagogia',
                'description' => 'Categoria creada automàticament',
                'color' => '#8b5cf6',
                'icon' => 'circle',
                'order' => 3,
            ],
            [
                'name' => 'Ciències Socials i Humanitats',
                'slug' => 'ciencies-socials-humanitats',
                'description' => 'Categoria creada automàticament',
                'color' => '#f59e0b',
                'icon' => 'circle',
                'order' => 4,
            ],
            [
                'name' => 'Tecnologia i Informàtica',
                'slug' => 'tecnologia-informatica',
                'description' => 'Categoria creada automàticament',
                'color' => '#ef4444',
                'icon' => 'laptop-code',
                'order' => 5,
            ],
            [
                'name' => 'Gestió i Administració',
                'slug' => 'gestio-administracio',
                'description' => 'Categoria creada automàticament',
                'color' => '#8b5cf6',
                'icon' => 'briefcase',
                'order' => 6,
            ],
            [
                'name' => 'Idiomes i Llengües',
                'slug' => 'llengues-idiomes',
                'description' => 'Categoria creada automàticament',
                'color' => '#3b82f6',
                'icon' => 'language',
                'order' => 7,
            ],
            [
                'name' => 'Arts i Disseny',
                'slug' => 'arts-disseny',
                'description' => 'Categoria creada automàticament',
                'color' => '#ef4444',
                'icon' => 'palette',
                'order' => 8,
            ],
            [
                'name' => 'Ciències i Investigació',
                'slug' => 'ciencies-investigacio',
                'description' => 'Categoria creada automàticament',
                'color' => '#06b6d4',
                'icon' => 'circle',
                'order' => 9,
            ],
            [
                'name' => 'Esports i Benestar',
                'slug' => 'esports-benestar',
                'description' => 'Categoria creada automàticament',
                'color' => '#10b981',
                'icon' => 'circle',
                'order' => 10,
            ],
            [
                'name' => 'Dret i Seguretat',
                'slug' => 'dret-seguretat',
                'description' => 'Categoria creada automàticament',
                'color' => '#ef4444',
                'icon' => 'circle',
                'order' => 11,
            ],
            [
                'name' => 'Formació Continua',
                'slug' => 'formacio-continua',
                'description' => 'Cursos de formació continua',
                'color' => '#06b6d4',
                'icon' => 'circle',
                'order' => 12,
            ],
            [
                'name' => 'Desenvolupament Personal',
                'slug' => 'desenvolupament-personal',
                'description' => 'Cursos de desenvolupament personal',
                'color' => '#84cc16',
                'icon' => 'circle',
                'order' => 13,
            ],
            [
                'name' => 'Benestar i Salut',
                'slug' => 'benestar-salut',
                'description' => 'Cursos de benestar i salut',
                'color' => '#8b5cf6',
                'icon' => 'circle',
                'order' => 14,
            ],
        ];

        foreach ($categories as $categoryData) {
            CampusCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('✅ Categories creadas correctamente');

        // 2. CREAR TEMPORADES
        $seasons = [
            [
                'name' => 'Curs 2024-25',
                'slug' => '2024-25',
                'academic_year' => '2024-2025',
                'registration_start' => '2024-08-01',
                'registration_end' => '2024-09-30',
                'season_start' => '2024-09-16',
                'season_end' => '2025-06-30',
                'type' => 'annual',
                'is_current' => true,
                'is_active' => true,
                'periods' => [
                    ['name' => 'Primer Quadrimestre', 'start' => '2024-09-16', 'end' => '2025-01-31'],
                    ['name' => 'Segon Quadrimestre', 'start' => '2025-02-01', 'end' => '2025-06-30'],
                ],
            ],
            [
                'name' => 'Curs 2025-26',
                'slug' => '2025-26',
                'academic_year' => '2025-2026',
                'registration_start' => '2025-08-01',
                'registration_end' => '2025-09-30',
                'season_start' => '2025-09-15',
                'season_end' => '2026-06-30',
                'type' => 'annual',
                'is_current' => false,
                'is_active' => true,
            ],
        ];

        foreach ($seasons as $seasonData) {
            CampusSeason::firstOrCreate(
                ['slug' => $seasonData['slug']],
                $seasonData
            );
        }

        $this->command->info('✅ Temporadas creadas correctamente');
        $this->command->info('🎉 Seeder IniciCategoriesSeason completado');
    }
}
