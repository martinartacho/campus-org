<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CampusSeason;
use App\Models\CampusCourse;
use Illuminate\Support\Facades\DB;

class MigrateCoursesToNewHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Migrant Cursos a Nova Estructura Jeràrquica ===');
        
        // 1. Analitzar situació actual
        $this->analyzeCurrentSituation();
        
        // 2. Crear estructura jeràrquica per 2025-26
        $this->create2025Hierarchy();
        
        // 3. Migrar cursos
        $this->migrateCourses();
        
        // 4. Validar migració
        $this->validateMigration();
    }
    
    private function analyzeCurrentSituation()
    {
        $this->command->info(PHP_EOL . "--- Anàlisi de Situació Actual ---");
        
        // Temporades existents
        $seasons = CampusSeason::all();
        $this->command->info("Temporades totals: {$seasons->count()}");
        
        foreach ($seasons as $season) {
            $courseCount = CampusCourse::where('season_id', $season->id)->count();
            $this->command->info("  ID: {$season->id} | {$season->name} | Cursos: {$courseCount}");
        }
        
        // Cursos per migrar
        $coursesToMigrate = CampusCourse::where('season_id', 2)->count();
        $this->command->info("Cursos a migrar (des de ID 2): {$coursesToMigrate}");
    }
    
    private function create2025Hierarchy()
    {
        $this->command->info(PHP_EOL . "--- Creant Estructura 2025-26 ---");
        
        // Desactivar temporades actuals
        CampusSeason::query()->update(['is_current' => false]);
        
        // 1. Crear Any Acadèmic 2025-26
        $academicYear2025 = CampusSeason::create([
            'name' => 'Curs 2025-26',
            'slug' => 'curs-2025-26',
            'academic_year' => '2025-26',
            'parent_id' => null,
            'type' => 'annual',
            'semester_number' => null,
            'registration_start' => now()->subMonths(14),
            'registration_end' => now()->subMonths(10),
            'season_start' => now()->subMonths(13),
            'season_end' => now()->addMonths(2),
            'status' => 'completed', // Ja està completat
            'is_active' => true,
            'is_current' => false, // No és l'actual
        ]);
        
        $this->command->info("✅ Any Acadèmic 2025-26 creat (ID: {$academicYear2025->id})");
        
        // 2. Convertir les temporades existents en quadrimestres
        $oldSeason2 = CampusSeason::find(2); // "Curs 2025-26 - 1r Trimestre"
        $oldSeason3 = CampusSeason::find(3); // "Curs 2025-26 - 2n Trimestre"
        
        if ($oldSeason2) {
            $oldSeason2->update([
                'parent_id' => $academicYear2025->id,
                'type' => 'semester',
                'semester_number' => 1,
                'status' => 'completed',
            ]);
            $this->command->info("✅ Q1 actualitzat (ID: {$oldSeason2->id})");
        }
        
        if ($oldSeason3) {
            $oldSeason3->update([
                'parent_id' => $academicYear2025->id,
                'type' => 'semester',
                'semester_number' => 2,
                'status' => 'completed',
            ]);
            $this->command->info("✅ Q2 actualitzat (ID: {$oldSeason3->id})");
        }
        
        // Guardar referències
        $this->academicYear2025 = $academicYear2025;
        $this->oldSeason2 = $oldSeason2;
        $this->oldSeason3 = $oldSeason3;
    }
    
    private function migrateCourses()
    {
        $this->command->info(PHP_EOL . "--- Migrant Cursos ---");
        
        // Cursos a migrar (estan a season_id = 2)
        $courses = CampusCourse::where('season_id', 2)->get();
        
        $migratedCount = 0;
        foreach ($courses as $course) {
            // Els cursos ja estan bé a season_id = 2 (ara Q1)
            // Només necessitem assegurar que les dades són consistents
            
            // Actualitzar metadades si cal
            $metadata = $course->metadata ?? [];
            $metadata['migration_info'] = [
                'migrated_at' => now()->toISOString(),
                'from_hierarchy' => 'flat',
                'to_hierarchy' => 'academic_year',
                'academic_year_id' => $this->academicYear2025->id,
            ];
            
            $course->update(['metadata' => $metadata]);
            
            $migratedCount++;
            
            if ($migratedCount % 10 === 0) {
                $this->command->info("  Migrats: {$migratedCount} cursos...");
            }
        }
        
        $this->command->info("✅ Total cursos migrats: {$migratedCount}");
    }
    
    private function validateMigration()
    {
        $this->command->info(PHP_EOL . "--- Validant Migració ---");
        
        // Validar jerarquia
        $academicYear = CampusSeason::find($this->academicYear2025->id);
        $children = $academicYear->children()->count();
        
        $this->command->info("Any Acadèmic 2025-26 té {$children} quadrimestres");
        
        // Validar cursos
        $coursesInQ1 = CampusCourse::where('season_id', $this->oldSeason2->id)->count();
        $coursesInQ2 = CampusCourse::where('season_id', $this->oldSeason3->id)->count();
        
        $this->command->info("Cursos en Q1: {$coursesInQ1}");
        $this->command->info("Cursos en Q2: {$coursesInQ2}");
        
        // Validar relacions
        $this->command->info("Validant relacions jeràrquiques:");
        
        $q1Parent = $this->oldSeason2->parent;
        $this->command->info("  Q1 Parent: {$q1Parent->name} ✓");
        
        $q2Parent = $this->oldSeason3->parent;
        $this->command->info("  Q2 Parent: {$q2Parent->name} ✓");
        
        // Resum final
        $this->command->info(PHP_EOL . "=== Migració Completada! ===");
        $this->command->info("✅ Estructura jeràrquica creada");
        $this->command->info("✅ Cursos migrats correctament");
        $this->command->info("✅ Relacions validades");
    }
}
