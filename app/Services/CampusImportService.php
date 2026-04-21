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
    private $validationWarnings = [];
    
    /**
     * Validar archivo CSV antes de importar
     */
    public function validateCSV(UploadedFile $file)
    {
        $this->validationWarnings = [];
        
        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            return ['valid' => false, 'message' => 'No se puede leer el archivo'];
        }
        
        $header = fgetcsv($handle, 0, ',');
        $rowNumber = 2; // Empezar desde la fila 2 (después del header)
        
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $data = array_combine($header, $row);
            
            // Validar campos obligatorios de course (no de teacher)
            $missingFields = [];
            if (empty(trim($data['TÍTOL CURS'] ?? ''))) $missingFields[] = 'TÍTOL CURS';
            
            // Validar campos de teacher como advertencias (no críticos)
            $teacherMissingFields = [];
            if (empty(trim($data['Nom'] ?? ''))) $teacherMissingFields[] = 'Nom';
            if (empty(trim($data['Cognoms'] ?? ''))) $teacherMissingFields[] = 'Cognoms';
            if (empty(trim($data['Correu'] ?? ''))) $teacherMissingFields[] = 'Correu';
            
            if (!empty($missingFields)) {
                $this->validationWarnings[] = [
                    'row' => $rowNumber,
                    'type' => 'critical',
                    'fields' => $missingFields,
                    'message' => "Fila {$rowNumber}: Camps obligatoris buits: " . implode(', ', $missingFields)
                ];
            }
            
            if (!empty($teacherMissingFields)) {
                $this->validationWarnings[] = [
                    'row' => $rowNumber,
                    'type' => 'warning',
                    'fields' => $teacherMissingFields,
                    'message' => "Fila {$rowNumber}: Camps de professor buits: " . implode(', ', $teacherMissingFields) . " (Es crearà el curs sense professor)"
                ];
            }
            
            $rowNumber++;
        }
        
        fclose($handle);
        
        // Solo hay problemas críticos si faltan campos del curso
        $criticalIssues = array_filter($this->validationWarnings, fn($w) => $w['type'] === 'critical');
        
        return [
            'valid' => true,
            'warnings' => $this->validationWarnings,
            'has_critical_issues' => count($criticalIssues) > 0
        ];
    }
    
    public function importFromCSV(UploadedFile $file, $seasonId = null, $confirmResponsibility = false)
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
            // 1. Crear/obtener Teacher (opcional)
            $teacher = $this->createTeacherFromCSV($data, $rowNumber);
            
            if ($teacher) {
                $this->resum['teachers_creats']++;
            }
            
            // 2. Crear Course (siempre se intenta crear)
            $course = $this->createCourseFromCSV($data, $season, $rowNumber);
            
            if ($course) {
                $this->resum['courses_creats']++;
                
                // 3. Asignar course al teacher solo si ambos existen
                if ($teacher && $course) {
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
        // Extraer datos del teacher (soportar ambos idiomas)
        $firstName = trim($data['Nom'] ?? $data['first_name'] ?? $data['first_name'] ?? '');
        $lastName1 = trim($data['COGNOM 1'] ?? $data['last_name'] ?? '');
        $lastName2 = trim($data['COGNOM 2'] ?? '');
        $lastName = trim("{$lastName1} {$lastName2}");
        $email = trim($data['CORREU'] ?? $data['email'] ?? '');
        $phone = trim($data['TELÉFON'] ?? $data['phone'] ?? '');
        $nif = trim($data['NIF'] ?? $data['nif'] ?? '');
        $address = trim($data['ADREÇA'] ?? $data['address'] ?? '');
        $city = trim($data['POBLACIO'] ?? $data['city'] ?? '');
        $postalCode = trim($data['CP'] ?? $data['postal_code'] ?? '');
        $iban = trim($data['COMPTE IBAN'] ?? $data['iban'] ?? '');
        
        // Validar campos obligatorios de teacher (si no hay datos, no crear teacher pero continuar)
        if (empty($firstName) || empty($lastName) || empty($email)) {
            $this->incidencies[] = "Fila {$rowNumber}: Camps de teacher buits (Nom, Cognoms, Correu) - Es crearà el curs sense professor";
            return null; // No crear teacher, pero permitir continuar
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
        // Extraer datos del course (soportar ambos idiomas)
        $courseTitle = trim($data['TÍTOL CURS'] ?? $data['title'] ?? '');
        
        // Verificar si hay código en el CSV (soportar ambos idiomas)
        $csvCode = trim($data['CODI CURS'] ?? $data['code'] ?? '');
        if (!empty($csvCode)) {
            $courseCode = $csvCode;
        } else {
            // Generar código único por temporada
            $courseCode = $this->generateUniqueCourseCode($courseTitle, $season->id);
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
            // Crear CampusCourse con slug único por temporada
            $slug = $this->generateUniqueSlug($courseTitle, $season->id);
            
            $course = CampusCourse::create([
                'season_id' => $season->id,
                'category_id' => $category->id,
                'code' => $courseCode,
                'title' => $courseTitle,
                'slug' => $slug,
                'description' => "Curs importat des de CSV - {$courseTitle}",
                'credits' => 1,
                'hours' => $sessions,
                'max_students' => 30,
                'price' => $price,
                'level' => 'beginner',
                'start_date' => $season->season_start,
                'end_date' => $season->season_end,
                'is_active' => false,
                'is_public' => false,
            ]);
            
            return $course;
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejar errores de duplicados específicamente
            if ($e->getCode() == 23000) {
                $this->incidencies[] = "Fila {$rowNumber}: El curs amb títol '{$courseTitle}' o codi '{$courseCode}' ja existeix";
            } else {
                $this->incidencies[] = "Fila {$rowNumber}: Error creant course: " . $e->getMessage();
            }
            
            // Intentar crear el curso como inactivo/no público si falló por duplicado
            try {
                $slug = Str::slug($courseTitle) . '-' . uniqid() . '-error';
                CampusCourse::create([
                    'season_id' => $season->id,
                    'category_id' => $category->id,
                    'code' => $courseCode . '-DUPLICATED',
                    'title' => $courseTitle,
                    'slug' => $slug,
                    'description' => "Curs importat des de CSV amb errors - {$courseTitle}",
                    'credits' => 1,
                    'hours' => $sessions,
                    'max_students' => 30,
                    'price' => $price,
                    'level' => 'beginner',
                    'start_date' => $season->season_start,
                    'end_date' => $season->season_end,
                    'is_active' => false, // Inactivo por error
                    'is_public' => false, // No público por error
                ]);
                
                $this->incidencies[] = "Fila {$rowNumber}: Curs duplicat creat com a inactiu per revisió: {$courseTitle}";
            } catch (\Exception $retryException) {
                // Si ni siquiera se puede crear como inactivo, reportar error
                $this->incidencies[] = "Fila {$rowNumber}: Error creant course inactiu: " . $retryException->getMessage();
            }
            
            return null;
        } catch (\Exception $e) {
            $this->incidencies[] = "Fila {$rowNumber}: Error creant course: " . $e->getMessage();
            return null;
        }
    }
    
    /**
     * Generate unique slug for course by season
     * 
     * @param string $title
     * @param int $seasonId
     * @return string
     */
    private function generateUniqueSlug($title, $seasonId)
    {
        $baseSlug = Str::slug($title);
        $season = CampusSeason::find($seasonId);
        $seasonYear = $season ? substr($season->season_start->format('Y'), -2) : date('y');
        $uniqueSlug = "{$baseSlug}-{$seasonYear}";
        
        // Si existe, añadir sufijo numérico
        $counter = 1;
        while (CampusCourse::where('slug', $uniqueSlug)->where('season_id', $seasonId)->exists()) {
            $uniqueSlug = "{$baseSlug}-{$seasonYear}-{$counter}";
            $counter++;
        }
        
        return $uniqueSlug;
    }
    
    /**
     * Generate unique course code by season
     * 
     * @param string $title
     * @param int $seasonId
     * @return string
     */
    private function generateUniqueCourseCode($title, $seasonId)
    {
        // 1. Normalitzar text (accents i caràcters especials)
        $normalized = Str::ascii($title);
        $normalized = strtoupper($normalized);
        $normalized = preg_replace('/[^A-Z\s]/', '', $normalized);

        // 2. Separar paraulas
        $words = array_values(array_filter(explode(' ', $normalized)));
        $count = count($words);

        $base = '';

        if ($count == 1) {
            $base = substr($words[0], 0, 6);
        }
        elseif ($count == 2) {
            $base = substr($words[0], 0, 3) . substr($words[1], 0, 3);
        }
        elseif ($count == 3) {
            foreach ($words as $w) {
                $base .= substr($w, 0, 2);
            }
        }
        elseif ($count == 4) {
            $base = substr($words[0], 0, 3) . substr($words[1], 0, 1) . substr($words[2], 0, 1) . substr($words[3], 0, 1);
        }
        elseif ($count == 5) {
            $base = substr($words[0], 0, 2) . substr($words[1], 0, 2) . substr($words[2], 0, 1) . substr($words[3], 0, 1);
        }
        else { // 6 o més
            foreach ($words as $w) {
                $base .= substr($w, 0, 1);
                if (strlen($base) >= 6) break;
            }
        }

        // Assegurar 6 caràcters
        $base = substr(str_pad($base, 6, 'X'), 0, 6);

        // 3. Generar número incremental por temporada
        $counter = 1;
        do {
            $code = $base . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $exists = CampusCourse::where('code', $code)->where('season_id', $seasonId)->exists();
            $counter++;
        } while ($exists);

        return $code;
    }
    
    /**
     * Generate a unique course code from title (legacy method)
     * 
     * @param string $title
     * @return string
     */
    private function generateCourseCode($title)
    {
        // 1. Normalitzar text (accents i caràcters especials)
        $normalized = Str::ascii($title);
        $normalized = strtoupper($normalized);
        $normalized = preg_replace('/[^A-Z\s]/', '', $normalized);

        // 2. Separar paraules
        $words = array_values(array_filter(explode(' ', $normalized)));
        $count = count($words);

        $base = '';

        if ($count == 1) {
            $base = substr($words[0], 0, 6);
        }

        elseif ($count == 2) {
            $base =
                substr($words[0], 0, 3) .
                substr($words[1], 0, 3);
        }

        elseif ($count == 3) {
            foreach ($words as $w) {
                $base .= substr($w, 0, 2);
            }
        }

        elseif ($count == 4) {
            $base =
                substr($words[0], 0, 3) .
                substr($words[1], 0, 1) .
                substr($words[2], 0, 1) .
                substr($words[3], 0, 1);
        }

        elseif ($count == 5) {
            $base =
                substr($words[0], 0, 2) .
                substr($words[1], 0, 2) .
                substr($words[2], 0, 1) .
                substr($words[3], 0, 1);
        }

        else { // 6 o més
            foreach ($words as $w) {
                $base .= substr($w, 0, 1);
                if (strlen($base) >= 6) break;
            }
        }

        // Assegurar 6 caràcters
        $base = substr(str_pad($base, 6, 'X'), 0, 6);

        // 3. Generar número incremental
        $counter = 1;

        do {
            $code = $base . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $exists = CampusCourse::where('code', $code)->exists();
            $counter++;
        } while ($exists);

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
        // Extraer nombre de categoría del CSV (soportar ambos idiomas)
        $categoryName = trim($data['ÀREA FORMATIVA'] ?? $data['category'] ?? '');
        
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
