<?php
// database/seeders/CampusExempleSeeder.php

namespace Database\Seeders;

use App\Models\CampusCategory;
use App\Models\CampusSeason;
use App\Models\CampusCourse;
use App\Models\CampusTeacher;
use App\Models\CampusStudent;
use App\Models\CampusRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CampusExempleSeeder extends Seeder
{
    private $incidencies = [];
    private $resum = [];
    private $importarCSV = false; // Per defecte NO importar
    
    public function run()
    {
        // 0. COMPROVAR SI ES VOL IMPORTAR CSV
        $this->detectarImportacio();
        
        // 1. CREAR CATEGORIES EXEMPLE
        $this->createCategories();
        
        // 2. CREAR TEMPORADA 2025-2026 2n Quadrimestre
        $currentSeason = $this->createSeason();
        
        // 3. CREAR PROFESSORS EXEMPLE
        $teachers = $this->createTeachers();
        
        // 4. CREAR ALUMNES EXEMPLE
        $students = $this->createStudents();
        
        // 5. CREAR CURSOS EXEMPLE (només si NO s'importa CSV)
        if (!$this->importarCSV) {
            $this->createCourses($currentSeason, $teachers);
            
            // 6. CREAR MATRICULACIONS EXEMPLE
            $this->createRegistrations($students);
        }
        
        // 7. IMPORTAR DES DE CSV (si s'ha indicat)
        if ($this->importarCSV) {
            $this->importFromCSV($currentSeason);
        }
        
        // 8. MOSTRAR RESUM
        $this->mostrarResum();
        
        $this->command->info('Seeder CampusExemple finalitzat.');
    }
    
    /**
     * Detecta si s'ha de fer importació CSV basant-se en arguments de consola
     */
    private function detectarImportacio()
    {
        // Opció 1: Mirar si l'argument --import està present
        if (isset($_SERVER['argv'])) {
            $args = $_SERVER['argv'];
            foreach ($args as $arg) {
                if (str_contains($arg, '--import') || str_contains($arg, '-import')) {
                    $this->importarCSV = true;
                    $this->command->info('✅ Mode IMPORTACIÓ CSV activat.');
                    break;
                }
            }
        }
        
        // Opció 2: Si ja s'ha detectat, sortir
        if ($this->importarCSV) {
            return;
        }
        
        // Opció 3: Preguntar interactivament
        if ($this->command && method_exists($this->command, 'confirm')) {
            $this->importarCSV = $this->command->confirm('Vols importar cursos des del fitxer CSV?', false);
            if ($this->importarCSV) {
                $this->command->info('✅ Important des de CSV...');
            }
        }
    }
    
    private function createCategories()
    {
        // ... (codi existent sense canvis) ...
    }
    
    private function createSeason()
    {
        try {
            $season = CampusSeason::firstOrCreate(
                ['slug' => '2025-26-2q'],
                [
                    'name' => 'Curs 2025-26 - 2n Quadrimestre',
                    'slug' => '2025-26-2q',
                    'academic_year' => '2025-2026',
                    'registration_start' => '2026-01-02',
                    'registration_end' => '2026-01-15',
                    'season_start' => '2026-02-16',
                    'season_end' => '2026-06-30',
                    'type' => 'quarter',
                    'is_current' => true,
                    'is_active' => true,
                    'periods' => [
                        ['name' => '2n Quadrimestre', 'start' => '2026-02-16', 'end' => '2026-06-30']
                    ],
                ]
            );
            
            $this->command->info('Temporada 2025-26 2n Quadrimestre creada/verificada.');
            return $season;
            
        } catch (\Exception $e) {
            $this->incidencies[] = "Error creant temporada: " . $e->getMessage();
            
            // Intentar obtenir qualsevol temporada existent
            $existingSeason = CampusSeason::first();
            if ($existingSeason) {
                $this->command->info("Utilitzant temporada existent: {$existingSeason->name}");
                return $existingSeason;
            }
            
            throw $e;
        }
    }
    
    private function createTeachers()
    {
        $teachers = [];
        
        // SOLS crear professors d'exemple si NO s'importa CSV
        if (!$this->importarCSV) {
            // Professor 1
            $user1 = User::firstOrCreate(
                ['email' => 'anna.estape@' . env('SEEDER_EMAIL_DOMAIN', 'test.local')],
                [
                    'name' => 'Anna Estapé',
                    'email' => 'anna.estape@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
                    'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
                    'email_verified_at' => Carbon::now(),
                    'locale' => 'ca',
                ]
            );
            $user1->assignRole('teacher');
            
            $teacher1 = CampusTeacher::firstOrCreate(
                ['teacher_code' => 'PROF101'],
                [
                    'user_id' => $user1->id,
                    'teacher_code' => 'PROF101',
                    'first_name' => 'Anna',
                    'last_name' => 'Estapé',
                    'dni' => '44555666E',
                    'email' => 'anna.estape@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
                    'phone' => '+34 600 123 456',
                    'specialization' => 'Pediatria',
                    'title' => 'Dra.',
                    'areas' => ['Pediatria', 'Salut Infantil'],
                    'status' => 'active',
                    'hiring_date' => '2020-09-01',
                ]
            );
            $teachers[] = $teacher1;
            
            // Professor 2
            $user2 = User::firstOrCreate(
                ['email' => 'marta.soler@' . env('SEEDER_EMAIL_DOMAIN', 'test.local')],
                [
                    'name' => 'Marta Soler',
                    'email' => 'marta.soler@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
                    'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
                    'email_verified_at' => Carbon::now(),
                    'locale' => 'ca',
                ]
            );
            $user2->assignRole('teacher');
            
            $teacher2 = CampusTeacher::firstOrCreate(
                ['teacher_code' => 'PROF102'],
                [
                    'user_id' => $user2->id,
                    'teacher_code' => 'PROF102',
                    'first_name' => 'Marta',
                    'last_name' => 'Soler',
                    'dni' => '55667788F',
                    'email' => 'marta.soler@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
                    'phone' => '+34 600 234 567',
                    'specialization' => 'Educació Especial',
                    'title' => 'Dra.',
                    'areas' => ['TDAH', 'Educació Inclusiva'],
                    'status' => 'active',
                    'hiring_date' => '2019-09-01',
                ]
            );
            $teachers[] = $teacher2;
            
            $this->command->info('Professors exemple creats.');
        }
        
        return $teachers;
    }
    
    private function createStudents()
    {
        $students = [];
        
        // SOLS crear alumnes d'exemple si NO s'importa CSV
        if (!$this->importarCSV) {
            // Estudiant 1
            $user1 = User::firstOrCreate(
                ['email' => 'estudiant1@' . env('SEEDER_EMAIL_DOMAIN', 'test.local')],
                [
                    'name' => 'Laura García i Martí',
                    'email' => 'estudiant1@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
                    'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
                    'email_verified_at' => Carbon::now(),
                    'locale' => 'ca',
                ]
            );
            $user1->assignRole('student');
            
            $student1 = CampusStudent::firstOrCreate(
                ['student_code' => 'EST101'],
                [
                    'user_id' => $user1->id,
                    'student_code' => 'EST101',
                    'first_name' => 'Laura',
                    'last_name' => 'García i Martí',
                    'dni' => '99887766G',
                    'birth_date' => '1995-08-12',
                    'phone' => '+34 600 345 678',
                    'address' => 'Carrer Major 45, Sant Cugat',
                    'email' => 'estudiant1@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
                    'emergency_contact' => 'Pare - Joan García',
                    'emergency_phone' => '+34 600 456 789',
                    'status' => 'active',
                    'enrollment_date' => '2026-01-03',
                ]
            );
            $students[] = $student1;
            
            // Estudiant 2
            $user2 = User::firstOrCreate(
                ['email' => 'estudiant2@' . env('SEEDER_EMAIL_DOMAIN', 'test.local')],
                [
                    'name' => 'Marc Rodríguez i López',
                    'email' => 'estudiant2@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
                    'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
                    'email_verified_at' => Carbon::now(),
                    'locale' => 'ca',
                ]
            );
            $user2->assignRole('student');
            
            $student2 = CampusStudent::firstOrCreate(
                ['student_code' => 'EST102'],
                [
                    'user_id' => $user2->id,
                    'student_code' => 'EST102',
                    'first_name' => 'Marc',
                    'last_name' => 'Rodríguez i López',
                    'dni' => '88776655H',
                    'birth_date' => '1998-03-25',
                    'phone' => '+34 600 567 890',
                    'address' => 'Avinguda dels Pins 12, Rubí',
                    'email' => 'estudiant2@' . env('SEEDER_EMAIL_DOMAIN', 'test.local'),
                    'emergency_contact' => 'Mare - Anna López',
                    'emergency_phone' => '+34 600 678 901',
                    'status' => 'active',
                    'enrollment_date' => '2026-01-05',
                ]
            );
            $students[] = $student2;
            
            $this->command->info('Alumnes exemple creats.');
        }
        
        return $students;
    }
    
    private function createCourses($season, $teachers)
    {
        // SOLS executar si NO s'importa CSV
        if ($this->importarCSV) {
            return;
        }
        
        // ... (codi existent per crear cursos d'exemple) ...
    }
    
    private function createRegistrations($students)
    {
        // SOLS executar si NO s'importa CSV
        if ($this->importarCSV) {
            return;
        }
        
        // ... (codi existent per crear matriculacions) ...
    }
    
    /**
     * Funció per importar cursos des d'un arxiu CSV
     */
    private function importFromCSV($season)
    {
        $csvPath = storage_path('app/imports/cursos_upg.csv');
        
        $this->command->info("📁 Buscant fitxer CSV a: {$csvPath}");
        
        if (!file_exists($csvPath)) {
            $this->incidencies[] = "No s'ha trobat el fitxer CSV: {$csvPath}";
            $this->command->error("❌ ERROR: No s'ha trobat el fitxer CSV.");
            $this->command->line("   El fitxer ha d'estar a: storage/app/imports/cursos_upg.csv");
            $this->command->line("   Pots crear-lo amb el CSV de la Fase 2.");
            return;
        }
        
        $this->command->info('✅ Fitxer CSV trobat. Iniciant importació...');
        
        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            $this->incidencies[] = "No s'ha pogut obrir el fitxer CSV";
            return;
        }
        
        $header = fgetcsv($handle);
        if (!$header) {
            $this->incidencies[] = "El fitxer CSV està buit";
            fclose($handle);
            return;
        }
        
        $rowNumber = 1;
        $imported = 0;
        $skipped = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            if (count($row) !== count($header)) {
                $this->incidencies[] = "Fila {$rowNumber}: Columnes incorrectes";
                $skipped++;
                continue;
            }
            
            $data = array_combine($header, $row);
            
            // Processar la fila
            $result = $this->processarFilaCSV($data, $season, $rowNumber);
            
            if ($result) {
                $imported++;
            } else {
                $skipped++;
            }
        }
        
        fclose($handle);
        
        $this->resum['cursos_importats'] = $imported;
        $this->resum['cursos_saltats'] = $skipped;
        
        $this->command->info("✅ Importació completada: {$imported} cursos importats, {$skipped} saltats.");
    }
    
    private function processarFilaCSV($data, $season, $rowNumber)
    {
        try {
            // 1. Buscar o crear categoria
            $category = $this->obtenirOCrearCategoria($data, $rowNumber);
            if (!$category) return false;
            
            // 2. Crear/actualitzar curs
            $course = $this->crearOActualitzarCurs($data, $season, $category, $rowNumber);
            if (!$course) return false;
            
            // 3. Buscar/crear i assignar professor
            if (!empty($data['professor'])) {
                $this->assignarProfessorAlCurs($course, $data['professor'], $data['hours'] ?? 0, $rowNumber);
            }
            
            return true;
            
        } catch (\Exception $e) {
            $this->incidencies[] = "Fila {$rowNumber}: Error processant '{$data['title']}': " . $e->getMessage();
            return false;
        }
    }
    
    private function obtenirOCrearCategoria($data, $rowNumber)
    {
        if (empty($data['category'])) {
            $this->incidencies[] = "Fila {$rowNumber}: Falta categoria";
            return null;
        }
        
        $category = CampusCategory::where('name', $data['category'])->first();
        
        if (!$category) {
            try {
                $category = CampusCategory::create([
                    'name' => $data['category'],
                    'slug' => $this->crearSlug($data['category']),
                    'description' => 'Categoria creada automàticament durant importació CSV',
                    'color' => $this->generarColorAleatori(),
                    'icon' => 'circle',
                    'order' => 99,
                ]);
                
                $this->command->info("   [+] Categoria creada: {$data['category']}");
                
            } catch (\Exception $e) {
                $this->incidencies[] = "Fila {$rowNumber}: Error creant categoria '{$data['category']}': " . $e->getMessage();
                return null;
            }
        }
        
        return $category;
    }
    
    private function crearOActualitzarCurs($data, $season, $category, $rowNumber)
    {
        if (empty($data['code']) || empty($data['title'])) {
            $this->incidencies[] = "Fila {$rowNumber}: Falta codi o títol";
            return null;
        }
        
        try {
            $courseData = [
                'season_id' => $season->id,
                'category_id' => $category->id,
                'title' => $data['title'],
                'slug' => $data['slug'] ?? $this->crearSlug($data['title']),
                'description' => $data['description'] ?? '',
                'credits' => (int)($data['credits'] ?? 0),
                'hours' => (int)($data['hours'] ?? 0),
                'max_students' => (int)($data['max_students'] ?? 0),
                'price' => (float)($data['price'] ?? 0),
                'level' => $data['level'] ?? 'beginner',
                'schedule' => $this->parsearHorariCSV($data['schedule_days'] ?? '', $data['schedule_times'] ?? ''),
                'start_date' => !empty($data['start_date']) ? 
                    Carbon::createFromFormat('Y-m-d', $data['start_date']) : 
                    $season->season_start,
                'end_date' => !empty($data['end_date']) ? 
                    Carbon::createFromFormat('Y-m-d', $data['end_date']) : 
                    $season->season_end,
                'requirements' => !empty($data['requirements']) ? 
                    explode(', ', $data['requirements']) : [],
                'objectives' => !empty($data['objectives']) ? 
                    explode(', ', $data['objectives']) : [],
                'location' => $data['location'] ?? null,
                'format' => $data['format'] ?? 'Presencial',
                'is_public' => false, // Per defecte no públic
                'is_active' => true,
            ];
            
            $course = CampusCourse::updateOrCreate(
                ['code' => $data['code']],
                $courseData
            );
            
            $accio = $course->wasRecentlyCreated ? 'creat' : 'actualitzat';
            $this->command->info("   [+] Curs {$accio}: {$data['code']} - {$data['title']}");
            
            return $course;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    private function assignarProfessorAlCurs($course, $professorName, $hours, $rowNumber)
    {
        try {
            // Buscar professor existent
            $teacher = CampusTeacher::where('last_name', 'LIKE', '%' . $professorName . '%')
                ->orWhere('first_name', 'LIKE', '%' . $professorName . '%')
                ->first();
            
            // Si no existeix, crear-lo
            if (!$teacher) {
                // Generar email temporal
                $email = $this->generarEmailTemporal($professorName);
                
                // Crear usuari
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $professorName,
                        'email' => $email,
                        'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
                        'email_verified_at' => Carbon::now(),
                        'locale' => 'ca',
                    ]
                );
                $user->assignRole('teacher');
                
                // Crear perfil de professor
                $teacher = CampusTeacher::create([
                    'user_id' => $user->id,
                    'teacher_code' => 'TEMP-' . strtoupper(uniqid()),
                    'first_name' => $this->extreureNom($professorName),
                    'last_name' => $this->extreureCognom($professorName),
                    'dni' => 'TEMP-' . strtoupper(uniqid()),
                    'email' => $email,
                    'phone' => '',
                    'specialization' => 'Especialització pendent',
                    'title' => '',
                    'areas' => [],
                    'status' => 'inactive',
                    'hiring_date' => Carbon::now()->format('Y-m-d'),
                ]);
                
                $this->incidencies[] = "Fila {$rowNumber}: Professor temporal creat: {$professorName} ({$email})";
            }
            
            // Assignar professor al curs
            $course->teachers()->syncWithoutDetaching([$teacher->id => [
                'role' => 'teacher',
                'hours_assigned' => (int)$hours,
                'assigned_at' => now(),
            ]]);
            
        } catch (\Exception $e) {
            $this->incidencies[] = "Fila {$rowNumber}: Error assignant professor '{$professorName}': " . $e->getMessage();
        }
    }
    
    // Funcions auxiliars (mantenir les que ja tens)
    private function parsearHorariCSV($days, $times)
    {
        $horari = ['horaris' => []];
        
        if (!empty($days) && !empty($times)) {
            $diesArray = array_map('trim', explode(',', $days));
            $horesArray = explode('-', $times);
            
            foreach ($diesArray as $dia) {
                if (!empty($dia)) {
                    $horari['horaris'][] = [
                        'day' => trim($dia),
                        'start' => trim($horesArray[0] ?? '09:00'),
                        'end' => trim($horesArray[1] ?? '13:00'),
                    ];
                }
            }
        }
        
        return $horari;
    }
    
    private function crearSlug($text)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }
    
    private function generarColorAleatori()
    {
        $colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#06b6d4', '#ec4899', '#84cc16'];
        return $colors[array_rand($colors)];
    }
    
    private function generarEmailTemporal($nomComplet)
    {
        $nomNet = preg_replace('/[^a-zA-Z0-9]/', '', $nomComplet);
        $nomNet = strtolower($nomNet);
        $id = substr(uniqid(), -6);
        return "temp.{$nomNet}.{$id}@upg.test";
    }
    
    private function extreureNom($nomComplet)
    {
        $parts = explode(' ', $nomComplet);
        return $parts[0] ?? 'Nom';
    }
    
    private function extreureCognom($nomComplet)
    {
        $parts = explode(' ', $nomComplet);
        if (count($parts) > 1) {
            return implode(' ', array_slice($parts, 1));
        }
        return 'Cognom';
    }
    
    private function mostrarResum()
    {
        $this->command->newLine();
        $this->command->info('========== RESUM ==========');
        
        if ($this->importarCSV) {
            $this->command->info("📊 Mode: IMPORTACIÓ CSV");
            $this->command->info("Cursos importats: " . ($this->resum['cursos_importats'] ?? 0));
            $this->command->info("Cursos saltats: " . ($this->resum['cursos_saltats'] ?? 0));
        } else {
            $this->command->info("📊 Mode: EXEMPLE BASE");
            $this->command->info("Cursos exemple creats: 2");
            $this->command->info("Professors exemple creats: 2");
            $this->command->info("Alumnes exemple creats: 2");
        }
        
        if (!empty($this->incidencies)) {
            $this->command->newLine();
            $this->command->warn('========== INCIDÈNCIES ==========');
            foreach ($this->incidencies as $incidencia) {
                $this->command->warn("• {$incidencia}");
            }
        }
        
        $this->command->newLine();
        $this->command->info('========== COMANDES ==========');
        $this->command->line("• Executar sense importar: php artisan db:seed --class=CampusExempleSeeder");
        $this->command->line("• Executar AMB importació: php artisan db:seed --class=CampusExempleSeeder --import");
    }
}