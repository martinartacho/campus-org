<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CampusSeason;
use App\Models\CampusCourse;

class IniciSeasonsUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔄 === ACTUALITZACIÓ DE TEMPORADES (3 TEMPORADES) ===');
        
        // 1. Crear noves temporades
        $this->createNewSeasons();
        
        // 2. Actualitzar cursos a la nova temporada 2025-26-1Q
        $this->updateCoursesSeason();
        
        // 3. Report final
        $this->printFinalReport();
    }
    
    /**
     * Crear noves temporades
     */
    private function createNewSeasons()
    {
        $this->command->info('📅 Creant noves temporades...');
        
        // Temporada 1: 2025-26
        $season1 = CampusSeason::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Curs 2025-26',
                'slug' => 'curs-2025-26',
                'academic_year' => '2025-26',
                'registration_start' => '2025-06-01',
                'registration_end' => '2025-09-15',
                'season_start' => '2025-09-16',
                'season_end' => '2026-06-30',
                'type' => 'annual',
                'status' => 'active',
                'is_active' => true,
                'is_current' => true,
                'periods' => json_encode(['1Q', '2Q']),
            ]
        );
        
        // Temporada 2: 2025-26-1Q
        $season2 = CampusSeason::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Curs 2025-26 - 1r Trimestre',
                'slug' => 'curs-2025-26-1q',
                'academic_year' => '2025-26',
                'registration_start' => '2025-06-01',
                'registration_end' => '2025-09-15',
                'season_start' => '2025-09-16',
                'season_end' => '2025-12-20',
                'type' => 'quarter',
                'status' => 'active',
                'is_active' => true,
                'is_current' => false,
                'periods' => json_encode(['1Q']),
            ]
        );
        
        // Temporada 3: 2025-26-2Q
        $season3 = CampusSeason::updateOrCreate(
            ['id' => 3],
            [
                'name' => 'Curs 2025-26 - 2n Trimestre',
                'slug' => 'curs-2025-26-2q',
                'academic_year' => '2025-26',
                'registration_start' => '2025-11-01',
                'registration_end' => '2026-01-07',
                'season_start' => '2026-01-08',
                'season_end' => '2026-03-28',
                'type' => 'quarter',
                'status' => 'active',
                'is_active' => true,
                'is_current' => false,
                'periods' => json_encode(['2Q']),
            ]
        );
        
        $this->command->info('✅ Temporades creades/actualitzades:');
        $this->command->info('   ID 1: ' . $season1->name);
        $this->command->info('   ID 2: ' . $season2->name);
        $this->command->info('   ID 3: ' . $season3->name);
    }
    
    /**
     * Actualitzar cursos a la nova temporada 2025-26-1Q
     */
    private function updateCoursesSeason()
    {
        $this->command->info('📚 Actualitzant cursos a temporada 2025-26-1Q...');
        
        // Actualitzar tots els cursos (excepte el curs d'incidències) a la temporada 2 (2025-26-1Q)
        $updatedCount = CampusCourse::where('code', '!=', 'DRAF')
            ->update(['season_id' => 2]);
        
        // El curs d'incidències es queda a la temporada 1 (general)
        $incidentCourse = CampusCourse::where('code', 'DRAF')->first();
        if ($incidentCourse) {
            $incidentCourse->update(['season_id' => 1]);
            $this->command->info('🔧 Curs d\'incidències mantingut a temporada 1');
        }
        
        $this->command->info('✅ Cursos actualitzats: ' . $updatedCount);
    }
    
    /**
     * Report final
     */
    private function printFinalReport()
    {
        $this->command->info('');
        $this->command->info('🎯 === REPORT FINAL - TEMPORADES ACTUALITZADES ===');
        $this->command->info('');
        
        // Mostrar temporades
        $this->command->info('📅 Temporades:');
        $seasons = CampusSeason::orderBy('id')->get();
        foreach ($seasons as $season) {
            $current = $season->is_current ? ' (ACTUAL)' : '';
            $this->command->info('   ID ' . $season->id . ': ' . $season->name . $current);
        }
        
        // Mostrar distribució de cursos
        $this->command->info('');
        $this->command->info('📚 Distribució de cursos:');
        $courses = CampusCourse::all();
        $seasonCounts = [];
        foreach ($courses as $course) {
            $seasonId = $course->season_id;
            if (!isset($seasonCounts[$seasonId])) {
                $seasonCounts[$seasonId] = 0;
            }
            $seasonCounts[$seasonId]++;
        }
        
        foreach ($seasonCounts as $seasonId => $count) {
            $season = CampusSeason::find($seasonId);
            $this->command->info('   Temporada ' . $seasonId . ' (' . $season->name . '): ' . $count . ' cursos');
        }
        
        $this->command->info('');
        $this->command->info('🎉 === TEMPORADES ACTUALITZADES CORRECTAMENT! ===');
        $this->command->info('📅✨ El sistema té 3 temporades amb cursos distribuïts! ✨📅');
    }
}
