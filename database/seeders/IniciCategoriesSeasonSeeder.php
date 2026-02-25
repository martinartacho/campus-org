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
        // 1. CREAR CATEGORIES
        $categories = [
            [
                'name' => 'Informàtica i Tecnologia',
                'slug' => 'informatica-tecnologia',
                'description' => 'Cursos de programació, desenvolupament web i tecnologies de la informació',
                'color' => '#3b82f6',
                'icon' => 'laptop-code',
                'order' => 1,
            ],
            [
                'name' => 'Idiomes',
                'slug' => 'idiomes',
                'description' => 'Cursos d\'idiomes estrangers per a tots els nivells',
                'color' => '#10b981',
                'icon' => 'language',
                'order' => 2,
            ],
            [
                'name' => 'Negocis i Administració',
                'slug' => 'negocis-administracio',
                'description' => 'Cursos de gestió empresarial, administració i finances',
                'color' => '#8b5cf6',
                'icon' => 'briefcase',
                'order' => 3,
            ],
            [
                'name' => 'Disseny Gràfic i Web',
                'slug' => 'disseny-grafic-web',
                'description' => 'Cursos de disseny gràfic, disseny web i multimèdia',
                'color' => '#f59e0b',
                'icon' => 'palette',
                'order' => 4,
            ],
            [
                'name' => 'Salut i Benestar',
                'slug' => 'salut-benestar',
                'description' => 'Cursos relacionats amb la salut, nutrició i benestar personal',
                'color' => '#ef4444',
                'icon' => 'heart',
                'order' => 5,
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
