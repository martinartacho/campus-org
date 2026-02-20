<?php

namespace App\Services;

use App\Models\CampusCategory;
use App\Models\CampusSeason;
use App\Models\CampusCourse;
use App\Models\CampusTeacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class CampusImportService
{
    private $incidencies = [];
    private $resum = [
        'teachers_creats' => 0,
        'courses_creats' => 0,
        'files_ignorats' => 0,
        'errors' => 0
    ];
    
    public function importFromCSV(UploadedFile $file, $seasonId = null)
    {
        $this->incidencies = [];
        $this->resum = [
            'teachers_creats' => 0,
            'courses_creats' => 0,
            'files_ignorats' => 0,
            'errors' => 0
        ];
        
        // 1. Verificar temporada
        $season = $this->getOrCreateSeason($seasonId);
        
        // 2. Procesar CSV
        $result = $this->processCSV($file, $season);
        
        return [
            'success' => $result,
            'resum' => $this->resum,
            'incidencies' => $this->incidencies
        ];
    }
    
    private function getOrCreateSeason($seasonId = null)
    {
        if ($seasonId) {
            $season = CampusSeason::find($seasonId);
            if ($season) {
                return $season;
            }
        }
        
        // Buscar temporada actual
        $season = CampusSeason::where('is_current', true)->first();
        if ($season) {
            return $season;
        }
        
        // Crear temporada por defecto
        return CampusSeason::firstOrCreate(
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
    }
    
    private function processCSV(UploadedFile $file, $season)
    {
        try {
            $content = file_get_contents($file->getPathname());
            $lines = explode("\n", $content);
            
            if (empty($lines) || count($lines) < 2) {
                $this->incidencies[] = "El fitxer CSV està buit o no té dades";
                return false;
            }
            
            // Procesar cabecera
            $header = str_getcsv($lines[0]);
            if (!$header) {
                $this->incidencies[] = "No s'ha pogut llegir la capçalera del CSV";
                return false;
            }
            
            // Procesar filas
            $rowCount = 0;
            for ($i = 1; $i < count($lines); $i++) {
                if (empty(trim($lines[$i]))) continue;
                
                $row = str_getcsv($lines[$i]);
                if ($row) {
                    $rowCount++;
                    $this->processRow($row, $header, $season, $rowCount);
                }
            }
            
            return $rowCount > 0;
            
        } catch (\Exception $e) {
            $this->incidencies[] = "Error processant el fitxer: " . $e->getMessage();
            return false;
        }
    }
    
    private function processRow($row, $header, $season, $rowNumber)
    {
        // Asociar columnas
        $data = array_combine($header, $row);
        
        try {
            // 1. Crear/obtener Teacher
            $teacher = $this->createTeacherFromCSV($data, $rowNumber);
            
            if ($teacher) {
                $this->resum['teachers_creats']++;
                
                // 2. Crear Course
                $course = $this->createCourseFromCSV($data, $season, $rowNumber);
                
                if ($course) {
                    $this->resum['courses_creats']++;
                    
                    // 3. Asignar course al teacher
                    $teacher->courses()->attach($course->id, [
                        'hours_assigned' => $this->extractSessions($data),
                        'role' => 'teacher',
                    ]);
                }
            }
            
        } catch (\Exception $e) {
            $this->resum['errors']++;
            $this->incidencies[] = "Error processant fila {$rowNumber}: " . $e->getMessage();
        }
    }
    
    private function createTeacherFromCSV($data, $rowNumber)
    {
        // Extraer datos del teacher
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
        
        // Validar campos obligatorios
        if (empty($firstName) || empty($lastName) || empty($email)) {
            $this->incidencies[] = "Fila {$rowNumber}: Camps obligatoris de teacher buits (Nom, Cognoms, Correu)";
            return null;
        }
        
        // Comprobar si el usuario ya existe
        if (User::where('email', $email)->exists()) {
            $this->incidencies[] = "Fila {$rowNumber}: L'usuari amb email '{$email}' ja existeix";
            return null;
        }
        
        // Generar teacher code único
        $teacherCode = 'PROF' . str_pad(CampusTeacher::count() + 1, 4, '0', STR_PAD_LEFT);
        
        try {
            // Crear User
            $user = User::create([
                'name' => "{$firstName} {$lastName}",
                'email' => $email,
                'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'password123')),
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
        // Extraer datos del course
        $courseTitle = trim($data['TÍTOL CURS'] ?? '');
        
        // Verificar si hay código en el CSV
        $csvCode = trim($data['CODI CURS'] ?? '');
        if (!empty($csvCode)) {
            $courseCode = $csvCode;
        } else {
            // Generar código automáticamente
            $courseCode = $this->generateCourseCode($courseTitle);
        }
        
        $sessions = $this->extractSessions($data);
        $price = $this->extractPrice($data);
        
        // Validar campos obligatorios
        if (empty($courseTitle)) {
            $this->incidencies[] = "Fila {$rowNumber}: Títol del curs buit";
            return null;
        }
        
        // Comprovar si el curs ya existe
        if (CampusCourse::where('code', $courseCode)->exists()) {
            $this->incidencies[] = "Fila {$rowNumber}: El curs amb codi '{$courseCode}' ja existeix";
            return null;
        }
        
        // Obtener o crear categoría
        $category = $this->getOrCreateCategory($data, $rowNumber);
        if (!$category) {
            return null;
        }
        
        try {
            // Crear CampusCourse
            $course = CampusCourse::create([
                'season_id' => $season->id,
                'category_id' => $category->id,
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
        // Extraer las primeras 3-4 letras del título (sin espacios ni caracteres especiales)
        $prefix = preg_replace('/[^a-zA-Z]/', '', $title);
        $prefix = strtoupper(substr($prefix, 0, 4));
        
        // Si el título es muy corto, usar al menos 3 caracteres
        if (strlen($prefix) < 3) {
            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $title), 0, 3));
        }
        
        // Obtener el último ID de curso y sumar 1
        $lastCourse = CampusCourse::orderBy('id', 'desc')->first();
        $nextId = $lastCourse ? $lastCourse->id + 1 : 1;
        $suffix = str_pad($nextId, 3, '0', STR_PAD_LEFT);
        
        // Generar código con formato: LETRAS + ID
        $code = $prefix . $suffix;
        
        // Asegurar que sea único
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
        // Extraer número de sesiones
        $sessionsField = trim($data['nombre sessions'] ?? '0');
        return (int) $sessionsField ?: 20; // Valor por defecto
    }
    
    private function extractPrice($data)
    {
        // Extraer precio (eliminar símbolos € y espacios)
        $priceField = trim($data['Preu/sessió'] ?? '0');
        $priceField = str_replace(['€', ' ', '.'], '', $priceField);
        $priceField = str_replace(',', '.', $priceField);
        
        return (float) $priceField ?: 50.0; // Valor por defecto
    }
    
    /**
     * Obtener o crear categoría automáticamente
     * Si la categoría no existe, crea una categoría temporal
     */
    private function getOrCreateCategory($data, $rowNumber)
    {
        // Extraer nombre de categoría del CSV
        $categoryName = trim($data['ÀREA FORMATIVA'] ?? '');
        
        if (empty($categoryName)) {
            // Usar categoría por defecto
            $category = CampusCategory::where('slug', 'formacio-continua')->first();
            if (!$category) {
                $category = CampusCategory::first();
            }
            return $category;
        }
        
        // Buscar categoría existente (busqueda flexible)
        $category = CampusCategory::where('name', 'LIKE', "%{$categoryName}%")
            ->orWhere('slug', 'LIKE', "%" . Str::slug($categoryName) . "%")
            ->first();
        
        if ($category) {
            return $category;
        }
        
        // Crear categoría temporal
        try {
            $categoryName = ucwords(strtolower($categoryName));
            $categorySlug = Str::slug($categoryName) . '-temp-' . time();
            
            $category = CampusCategory::create([
                'name' => $categoryName . ' (Temporal)',
                'slug' => $categorySlug,
                'description' => "Categoria temporal creada automàticament durant importació. Revisar i editar.",
                'is_active' => true,
                'sort_order' => 999, // Al final per facilitar revisió
            ]);
            
            $this->incidencies[] = "Fila {$rowNumber}: Creada categoria temporal '{$category->name}' per a '{$categoryName}'";
            
            return $category;
            
        } catch (\Exception $e) {
            $this->incidencies[] = "Fila {$rowNumber}: Error creant categoria temporal per a '{$categoryName}': " . $e->getMessage();
            
            // Usar categoria per defecte com a fallback
            $category = CampusCategory::where('slug', 'formacio-continua')->first();
            if (!$category) {
                $category = CampusCategory::first();
            }
            return $category;
        }
    }
}
