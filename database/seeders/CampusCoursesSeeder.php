<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CampusCourse;
use App\Models\CampusCategory;
use App\Models\CampusSeason;

class CampusCoursesSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('=== Seeder de Cursos des de CSV ===');
        
        // Mostrar temporades disponibles
        $seasons = CampusSeason::where('is_active', true)->get();
        
        if ($seasons->isEmpty()) {
            $this->command->error("No hi ha temporades actives. Creant temporada per defecte...");
            $season = CampusSeason::create([
                'name' => '2025-26 - 2n Quadrimestre',
                'slug' => '2025-26-q2',
                'academic_year' => '2025-2026',
                'registration_start' => '2025-09-01',
                'registration_end' => '2026-06-30',
                'season_start' => '2026-02-16',
                'season_end' => '2026-06-30',
                'type' => 'quarter',
                'is_active' => true,
                'is_current' => true
            ]);
        } else {
            $this->command->info("\n=== TEMPORADES DISPONIBLES ===");
            foreach ($seasons as $season) {
                $current = $season->is_current ? ' (ACTUAL)' : '';
                $this->command->info("ID: {$season->id} - {$season->name}{$current}");
                $this->command->info("   Període: {$season->season_start} a {$season->season_end}");
            }
            
            // Seleccionar temporada correcta per 2025-26 Q2
            $season = CampusSeason::where('name', 'like', '%2025-26%')
                         ->where('is_current', true)
                         ->first();
            
            if (!$season) {
                // Si no n'hi ha cap marcada com actual, buscar la que correspongui
                $season = CampusSeason::where('name', 'like', '%2025-26%')
                             ->where('season_start', '>=', '2025-09-01')
                             ->first();
            }
            
            if (!$season) {
                $this->command->warn("No s'ha trobat temporada per 2025-26. Usant la primera activa...");
                $season = $seasons->first();
            }
        }
        
        $this->command->info("\n✅ Temporada seleccionada: {$season->name} (ID: {$season->id})");
        
        // Obtenir categoria per defecte
        $category = CampusCategory::where('is_active', true)->first();
        if (!$category) {
            $this->command->error('No hi ha categories. Creant categoria per defecte...');
            $category = CampusCategory::create([
                'name' => 'General',
                'slug' => 'general',
                'description' => 'Categoria general per a cursos',
                'color' => 'blue',
                'icon' => 'tag',
                'order' => 0,
                'is_active' => true,
                'is_featured' => false
            ]);
        }
        
        // Llegir arxiu CSV de cursos
        $csvFile = storage_path('app/imports/cursos_2025_26_Q2.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("No es troba l'arxiu: {$csvFile}");
            return;
        }
        
        $handle = fopen($csvFile, 'r');
        
        // Saltar cabecera
        fgetcsv($handle, 1000, ',');
        
        $cursosCreats = 0;
        $cursosExistent = 0;
        
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if (count($data) >= 2) {
                $title = trim($data[1]);
                $code = trim($data[0]);
                
                // Assignar codi si està buit
                if (empty($code) && !empty($title)) {
                    $code = $this->asignarCodigoACurso($title);
                }
                
                if (!empty($title) && !empty($code)) {
                    // Verificar si el curs ja existeix
                    $existingCourse = CampusCourse::where('code', $code)->first();
                    
                    if ($existingCourse) {
                        $this->command->info("✅ Curs existent: {$code} - {$title}");
                        $cursosExistent++;
                    } else {
                        // Crear nou curs
                        $course = CampusCourse::create([
                            'code' => $code,
                            'title' => $title,
                            'slug' => $this->createSlug($title),
                            'description' => "Curs: {$title}",
                            'credits' => 0,
                            'hours' => 18,
                            'max_students' => 25,
                            'price' => 0,
                            'level' => 'beginner',
                            'season_id' => $season->id,
                            'category_id' => $category->id,
                            'start_date' => '2025-09-15',
                            'end_date' => '2026-06-30',
                            'location' => 'Campus UPG',
                            'format' => 'Presencial',
                            'is_active' => true,
                            'is_public' => true,
                            'created_by' => 'CampusCoursesSeeder',
                            'source' => 'CSV: cursos_2025_26_Q2.csv',
                            'metadata' => json_encode([
                                'import_date' => now()->toISOString(),
                                'source_file' => 'cursos_2025_26_Q2.csv',
                                'auto_assigned_code' => empty(trim($data[0])),
                                'csv_row' => $data,
                                'seeder_version' => '1.0'
                            ])
                        ]);
                        
                        $this->command->info("📚 Curs creat: {$code} - {$title}");
                        $cursosCreats++;
                    }
                }
            }
        }
        
        fclose($handle);
        
        $this->command->info("\n=== RESUM DE CREACIÓ DE CURSOS ===");
        $this->command->info("📚 Cursos creats: {$cursosCreats}");
        $this->command->info("📚 Cursos existents: {$cursosExistent}");
        $this->command->info("📚 Total cursos: " . ($cursosCreats + $cursosExistent));
        $this->command->info("========================\n");
    }
    
    private function asignarCodigoACurso($title)
    {
        $codigosAsignados = [
            'Aula oberta al món digital - Presencial' => 'AOMD2',
            'De la psicodèlia dels 60 al moviment de Seattle' => 'PSY60',
            'La lletra a escena' => 'TEATRO'
        ];
        
        return $codigosAsignados[$title] ?? 'AUTO-' . substr(md5($title), 0, 6);
    }
    
    private function createSlug($title)
    {
        // Convertir a slug
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
}
