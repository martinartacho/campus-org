<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CampusSeason;
use App\Models\CampusCategory;

class IniciSetupProductionSeeder extends Seeder
{
    /**
     * Setup robusto para producción con validación de IDs
     */
    public function run(): void
    {
        $this->command->info('🔒 === SETUP PRODUCTION SEGURO ===');
        
        // 1. Validar y crear estructura base si no existe
        $this->ensureBaseStructure();
        
        // 2. Obtener IDs reales dinámicamente
        $seasonIds = $this->getSeasonIds();
        $categoryIds = $this->getCategoryIds();
        
        // 3. Ejecutar seeders con mapeo dinámico
        $this->callWithDependencies($seasonIds, $categoryIds);
        
        $this->command->info('✅ Setup production completado');
    }
    
    private function ensureBaseStructure(): void
    {
        $this->command->info('🏗️ Verificando estructura base...');
        
        // Asegurar temporadas base
        if (CampusSeason::count() === 0) {
            $this->command->call(IniciCategoriesSeasonSeeder::class);
            $this->command->call(IniciSeasonsUpdateSeeder::class);
        }
        
        $this->command->info('✅ Estructura base verificada');
    }
    
    private function getSeasonIds(): array
    {
        $seasons = CampusSeason::pluck('id', 'slug')->toArray();
        $this->command->info('📅 Temporadas encontradas: ' . implode(', ', array_keys($seasons)));
        return $seasons;
    }
    
    private function getCategoryIds(): array
    {
        $categories = CampusCategory::pluck('id', 'slug')->toArray();
        $this->command->info('📁 Categorías encontradas: ' . implode(', ', array_keys($categories)));
        return $categories;
    }
    
    private function callWithDependencies(array $seasonIds, array $categoryIds): void
    {
        // Pasar los IDs como configuración global para que otros seeders los usen
        config(['seeders.production.season_ids' => $seasonIds]);
        config(['seeders.production.category_ids' => $categoryIds]);
        
        // Ejecutar seeders principales
        $this->call([
            IniciTeachersCSVSeeder::class,
            IniciCoursesMapeadoCSVSeeder::class,
            IniciCourseTeacherSeeder::class,
            CampusSpaceSeeder::class,
            CampusTimeSlotSeeder::class,
            CampusCourseScheduleSeeder::class,
            IniciStudentsOnlySeeder::class,
        ]);
    }
}
