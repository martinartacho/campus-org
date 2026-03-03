<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CampusSeason;
use Illuminate\Support\Facades\DB;

class TestSeasonHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Creant Jerarquia de Temporades de Prova ===');
        
        // Desactivar totes les temporades actuals
        CampusSeason::query()->update(['is_current' => false]);
        
        // 1. Crear Any Acadèmic 2026-27
        $academicYear = CampusSeason::create([
            'name' => 'Curs 2026-27',
            'slug' => 'curs-2026-27',
            'academic_year' => '2026-27',
            'parent_id' => null,
            'type' => 'annual',
            'semester_number' => null,
            'registration_start' => now()->subMonths(2),
            'registration_end' => now()->addMonths(1),
            'season_start' => now()->subMonth(),
            'season_end' => now()->addMonths(10),
            'status' => 'planning',
            'is_active' => true,
            'is_current' => true,
        ]);
        
        $this->command->info("✅ Any Acadèmic creat: {$academicYear->name} (ID: {$academicYear->id})");
        
        // 2. Crear Quadrimestre 1
        $semester1 = CampusSeason::create([
            'name' => 'Curs 2026-27 - 1r Quadrimestre',
            'slug' => 'curs-2026-27-q1',
            'academic_year' => '2026-27',
            'parent_id' => $academicYear->id,
            'type' => 'semester',
            'semester_number' => 1,
            'registration_start' => now()->subMonths(2),
            'registration_end' => now()->addMonths(1),
            'season_start' => now()->subMonth(),
            'season_end' => now()->addMonths(5),
            'status' => 'planning',
            'is_active' => true,
            'is_current' => false,
        ]);
        
        $this->command->info("✅ Q1 creat: {$semester1->name} (ID: {$semester1->id})");
        
        // 3. Crear Quadrimestre 2
        $semester2 = CampusSeason::create([
            'name' => 'Curs 2026-27 - 2n Quadrimestre',
            'slug' => 'curs-2026-27-q2',
            'academic_year' => '2026-27',
            'parent_id' => $academicYear->id,
            'type' => 'semester',
            'semester_number' => 2,
            'registration_start' => now()->addMonths(3),
            'registration_end' => now()->addMonths(6),
            'season_start' => now()->addMonths(4),
            'season_end' => now()->addMonths(10),
            'status' => 'draft',
            'is_active' => true,
            'is_current' => false,
        ]);
        
        $this->command->info("✅ Q2 creat: {$semester2->name} (ID: {$semester2->id})");
        
        // 4. Validar relacions
        $this->command->info(PHP_EOL . "=== Validant Relacions ===");
        
        // Validar parent
        $parent1 = $semester1->parent;
        $this->command->info("Q1 Parent: {$parent1->name} (✓)");
        
        $parent2 = $semester2->parent;
        $this->command->info("Q2 Parent: {$parent2->name} (✓)");
        
        // Validar children
        $children = $academicYear->children;
        $this->command->info("Any Acadèmic Children: {$children->count()} quadrimestres");
        
        foreach ($children as $child) {
            $this->command->info("  - {$child->name} (Semester {$child->semester_number})");
        }
        
        // 5. Provar mètodes del model
        $this->command->info(PHP_EOL . "=== Provant Mètodes del Model ===");
        
        $this->command->info("És Any Acadèmic? " . ($academicYear->isAcademicYear() ? '✓ Sí' : '✗ No'));
        $this->command->info("És Semestre? " . ($academicYear->isSemester() ? '✗ Sí' : '✓ No'));
        $this->command->info("És Any Acadèmic? " . ($semester1->isAcademicYear() ? '✗ Sí' : '✓ No'));
        $this->command->info("És Semestre? " . ($semester1->isSemester() ? '✓ Sí' : '✗ No'));
        
        $firstSemester = $academicYear->firstSemester();
        $this->command->info("Primer Quadrimestre: " . ($firstSemester ? $firstSemester->name : 'No trobat'));
        
        $secondSemester = $academicYear->secondSemester();
        $this->command->info("Segon Quadrimestre: " . ($secondSemester ? $secondSemester->name : 'No trobat'));
        
        $this->command->info(PHP_EOL . "=== Jerarquia Creada Correctament! ===");
    }
}
