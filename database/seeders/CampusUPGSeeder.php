<?php

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

class CampusUPGSeeder extends Seeder
{
    private $incidencies = [];
    private $resum = [
        'teachers_creats' => 0,
        'courses_creats' => 0,
        'files_ignorats' => 0,
        'errors' => 0
    ];
    
    public function run()
    {
        $this->command->info('========== INICIANT CAMPUS UPG SEEDER ==========');
        
        // 1. CREAR CATEGORIES BASE
        $this->createCategories();
        
        // 2. CREAR TEMPORADA 2025-2026 1r Quadrimestre
        $season = $this->createSeason();
        
        // 3. PROCESSAR CSV I CREAR TEACHERS I CURSOS
        $this->processCSV($season);
        
        // 4. MOSTRAR RESUM
        $this->mostrarResum();
        
        $this->command->info('CampusUPGSeeder finalitzat.');
    }
    
    private function createCategories()
    {
        $categories = [
            ['name' => 'Formació Continua', 'slug' => 'formacio-continua', 'description' => 'Cursos de formació continua'],
            ['name' => 'Desenvolupament Personal', 'slug' => 'desenvolupament-personal', 'description' => 'Cursos de desenvolupament personal'],
            ['name' => 'Benestar i Salut', 'slug' => 'benestar-salut', 'description' => 'Cursos de benestar i salut'],
        ];
        
        foreach ($categories as $cat) {
            CampusCategory::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
        
        $this->command->info('Categories base creades.');
    }
    
    private function createSeason()
    {
        try {
            $season = CampusSeason::firstOrCreate(
                ['slug' => '2025-26-1q'],
                [
                    'name' => 'Curs 2025-26 - 1r Quadrimestre',
                    'slug' => '2025-26-1q',
                    'academic_year' => '2025-2026',
                    'registration_start' => '2025-09-01',
                    'registration_end' => '2025-09-15',
                    'season_start' => '2025-09-16',
                    'season_end' => '2026-01-31',
                    'type' => 'quarter',
                    'is_current' => true,
                    'is_active' => true,
                    'periods' => [
                        ['name' => '1r Quadrimestre', 'start' => '2025-09-16', 'end' => '2026-01-31']
                    ],
                ]
            );
            
            $this->command->info('Temporada 2025-26 1r Quadrimestre creada/verificada.');
            return $season;
            
        } catch (\Exception $e) {
            $this->incidencies[] = "Error creant temporada: " . $e->getMessage();
            
            $existingSeason = CampusSeason::where('is_current', true)->first();
            if ($existingSeason) {
                $this->command->info("Utilitzant temporada existent: {$existingSeason->name}");
                return $existingSeason;
            }
            
            throw $e;
        }
    }
    
    private function processCSV($season)
    {
        $csvPath = storage_path('app/imports/2025-2026 1Q.csv');
        
        if (!file_exists($csvPath)) {
            $this->incidencies[] = "Arxiu CSV no trobat: {$csvPath}";
            return;
        }
        
        $this->command->info("Processant CSV: {$csvPath}");
        
        // Obrir i llegir CSV
        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            $this->incidencies[] = "No s'ha pogut obrir l'arxiu CSV";
            return;
        }
        
        // Llegir capçalera
        $header = fgetcsv($handle);
        if (!$header) {
            $this->incidencies[] = "No s'ha pogut llegir la capçalera del CSV";
            fclose($handle);
            return;
        }
        
        $rowCount =0; // Comença a la fila n
        $maxRows = 40; // Limitar a 2 registres 
        
        while (($row = fgetcsv($handle)) !== false && $rowCount < $maxRows) {
            $rowCount++;
            $this->processRow($row, $header, $season, $rowCount);
        }
        
        fclose($handle);
        $this->command->info("Processats {$rowCount} registres (limitat a {$maxRows})");
    }
    
    private function processRow($row, $header, $season, $rowNumber)
    {
        // Associar columnes
        $data = array_combine($header, $row);
        
        try {
            // 1. Crear/obtenir Teacher
            $teacher = $this->createTeacherFromCSV($data, $rowNumber);
            
            if ($teacher) {
                $this->resum['teachers_creats']++;
                
                // 2. Crear Course
                $course = $this->createCourseFromCSV($data, $season, $rowNumber);
                
                if ($course) {
                    $this->resum['courses_creats']++;
                    
                    // 3. Assignar course al teacher
                    $teacher->courses()->attach($course->id, [
                        'hours_assigned' => $this->extractSessions($data),
                        'role' => 'teacher',
                    ]);
                    
                    $this->command->info("✅ Fila {$rowNumber}: Teacher '{$teacher->first_name} {$teacher->last_name}' i Course '{$course->title}' creats");
                }
            }
            
        } catch (\Exception $e) {
            $this->resum['errors']++;
            $this->incidencies[] = "Error processant fila {$rowNumber}: " . $e->getMessage();
            $this->command->error("❌ Fila {$rowNumber}: " . $e->getMessage());
        }
    }
    
    private function createTeacherFromCSV($data, $rowNumber)
    {
        // Extreure dades del teacher
        $firstName = trim($data['Nom'] ?? '');
        $lastName1 = trim($data['COGNOM 1'] ?? '');
        $lastName2 = trim($data['COGNOM 2'] ?? '');
        $lastName = trim("{$lastName1} {$lastName2}");
        $email = trim($data['CORREU'] ?? '');
        $phone = trim($data['TELÉFON'] ?? '');
        $nif = trim($data['NIF'] ?? '');
        $address = trim($data['ADREÇA'] ?? '');
        $city = trim($data['POBLACIO'] ?? '');
        $postalCode = trim($data['CP'] ?? '');
        $iban = trim($data['COMPTE IBAN'] ?? '');
        
        // Validar camps obligatoris
        if (empty($firstName) || empty($lastName) || empty($email)) {
            $this->incidencies[] = "Fila {$rowNumber}: Camps obligatoris de teacher buits (Nom, Cognoms, Correu)";
            return null;
        }
        
        // Comprovar si l'usuari ja existeix
        if (User::where('email', $email)->exists()) {
            $this->incidencies[] = "Fila {$rowNumber}: L'usuari amb email '{$email}' ja existeix";
            return null;
        }
        
        // Generar teacher code únic
        $teacherCode = 'PROF' . str_pad(CampusTeacher::count() + 1, 4, '0', STR_PAD_LEFT);
        
        try {
            // Crear User
            $user = User::create([
                'name' => "{$firstName} {$lastName}",
                'email' => $email,
                'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD')),
                'email_verified_at' => Carbon::now(),
                'locale' => 'ca',
            ]);
            $user->assignRole('teacher');
            
            // Crear CampusTeacher
            $teacher = CampusTeacher::create([
                'user_id' => $user->id,
                'teacher_code' => $teacherCode,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone ?: null,
                'dni' => $nif ?: null,
                'address' => $address ?: null,
                'postal_code' => $postalCode ?: null,
                'city' => $city ?: null,
                'iban' => $iban ?: null,
                'hiring_date' => now()->format('Y-m-d'),
                'status' => 'active',
            ]);
            
            return $teacher;
            
        } catch (\Exception $e) {
            $this->incidencies[] = "Fila {$rowNumber}: Error creant teacher: " . $e->getMessage();
            return null;
        }
    }
    
    private function createCourseFromCSV($data, $season, $rowNumber)
    {
        // Extreure dades del course
        $courseTitle = trim($data['TÍTOL CURS'] ?? '');
        $courseCode = $this->generateCourseCode($courseTitle);
        $sessions = $this->extractSessions($data);
        $price = $this->extractPrice($data);
        
        // Validar camps obligatoris
        if (empty($courseTitle)) {
            $this->incidencies[] = "Fila {$rowNumber}: Títol del curs buit";
            return null;
        }
        
        // Comprovar si el curs ja existeix
        if (CampusCourse::where('code', $courseCode)->exists()) {
            $this->incidencies[] = "Fila {$rowNumber}: El curs amb codi '{$courseCode}' ja existeix";
            return null;
        }
        
        try {
            // Obtenir categoria per defecte
            $category = CampusCategory::where('slug', 'formacio-continua')->first();
            
            // Crear CampusCourse
            $course = CampusCourse::create([
                'season_id' => $season->id,
                'category_id' => $category?->id,
                'code' => $courseCode,
                'title' => $courseTitle,
                'slug' => Str::slug($courseTitle) . '-' . time(),
                'description' => "Curs importat des de CSV - {$courseTitle}",
                'credits' => 1,
                'hours' => $sessions,
                'max_students' => 30,
                'price' => $price,
                'level' => 'beginner',
                'start_date' => $season->season_start,
                'end_date' => $season->season_end,
                'is_active' => true,
                'is_public' => true,
            ]);
            
            return $course;
            
        } catch (\Exception $e) {
            $this->incidencies[] = "Fila {$rowNumber}: Error creant course: " . $e->getMessage();
            return null;
        }
    }
    
    private function generateCourseCode($title)
    {
        // Generar codi a partir del títol
        $code = strtoupper(substr(Str::slug($title), 0, 8));
        $code = preg_replace('/[^A-Z0-9]/', '', $code);
        
        // Assegurar que sigui únic
        $originalCode = $code;
        $counter = 1;
        
        while (CampusCourse::where('code', $code)->exists()) {
            $code = $originalCode . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }
        
        return $code;
    }
    
    private function extractSessions($data)
    {
        // Extreure nombre de sessions
        $sessionsField = trim($data['nombre sessions'] ?? '0');
        return (int) $sessionsField ?: 20; // Valor per defecte
    }
    
    private function extractPrice($data)
    {
        // Extreure preu (eliminar símbols € i espais)
        $priceField = trim($data['Preu/sessió'] ?? '0');
        $priceField = str_replace(['€', ' ', '.'], '', $priceField);
        $priceField = str_replace(',', '.', $priceField);
        
        return (float) $priceField ?: 50.0; // Valor per defecte
    }
    
    private function mostrarResum()
    {
        $this->command->newLine();
        $this->command->info('========== RESUM CAMPUS UPG SEEDER ==========');
        
        $this->command->info("📊 Teachers creats: " . $this->resum['teachers_creats']);
        $this->command->info("📚 Courses creats: " . $this->resum['courses_creats']);
        $this->command->info("📁 Fitxers processats: 2 (limitat)");
        $this->command->info("❌ Errors: " . $this->resum['errors']);
        
        if (!empty($this->incidencies)) {
            $this->command->newLine();
            $this->command->warn('========== INCIDÈNCIES ==========');
            foreach ($this->incidencies as $incidencia) {
                $this->command->warn("• {$incidencia}");
            }
        }
        
        $this->command->newLine();
        $this->command->info('========== COMANDES ==========');
        $this->command->line("• Executar seeder: php artisan db:seed --class=CampusUPGSeeder");
        $this->command->line("• Per processar tot el CSV: Modificar \$maxRows aquest seeder");
    }
}
