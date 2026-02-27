<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\CampusCourse;
use App\Models\CampusStudent;
use App\Models\CampusRegistration;
use App\Models\User;

class CampusRegistrationImportSeeder extends Seeder
{
    private $resum = [
        'matriculaciones_creadas' => 0,
        'usuaris_creats' => 0,
        'usuaris_existents' => 0,
        'alumnes_creats' => 0,
        'alumnes_existents' => 0,
        'ordres_procesades' => 0,
        'ordres_amb_curs_trobat' => 0,
        'ordres_sense_curs' => 0,
        'errors' => 0
    ];

    private $courseCache = [];
    private $userCache = [];
    private $studentCache = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importación Mejorada de Matrículas con Códigos de Curso ===');
        
        // Ruta del archivo CSV mejorado
        $csvPath = storage_path('app/imports/ordres_code.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("❌ Archivo no encontrado: $csvPath");
            return;
        }
        
        $this->command->info("📂 Procesando archivo: $csvPath");
        
        // Limpiar tablas si es necesario
        $this->prepareDatabase();
        
        // Cargar cursos en caché
        $this->loadCoursesCache();
        
        // Procesar CSV
        $this->processRegistrationsCSV($csvPath);
        
        // Mostrar resumen
        $this->showFinalSummary();
    }
    
    /**
     * Preparar base de datos
     */
    private function prepareDatabase()
    {
        $this->command->info('🔄 Preparando base de datos...');
        
        // Limpiar tablas de matrículas
        if (Schema::hasTable('campus_registrations')) {
            DB::table('campus_registrations')->truncate();
        }
        
        // No limpiar usuarios ni alumnos para preservar datos existentes
        $this->command->info('✅ Base de datos preparada');
    }
    
    /**
     * Cargar cursos en caché para mejor rendimiento
     */
    private function loadCoursesCache()
    {
        $this->command->info('📚 Cargando cursos en caché...');
        
        $courses = CampusCourse::all();
        foreach ($courses as $course) {
            $this->courseCache[$course->code] = $course;
        }
        
        $this->command->info("✅ " . count($this->courseCache) . " cursos cargados en caché");
    }
    
    /**
     * Procesar archivo CSV de matrículas
     */
    private function processRegistrationsCSV($csvPath)
    {
        $handle = fopen($csvPath, 'r');
        
        if ($handle === false) {
            throw new \Exception("No se puede abrir el archivo CSV");
        }
        
        // Leer cabecera
        $headers = fgetcsv($handle, 0, ';', '"');
        $this->command->info("📋 Cabeceras: " . implode(', ', array_slice($headers, 0, 5)));
        
        $rowNumber = 1;
        while (($row = fgetcsv($handle, 0, ';', '"')) !== false) {
            $rowNumber++;
            
            try {
                $this->processRegistrationRow($row, $rowNumber);
                
                // Mostrar progreso cada 50 registros
                if ($rowNumber % 50 === 0) {
                    $this->command->info("✅ Progreso: $rowNumber filas procesadas...");
                }
                
            } catch (\Exception $e) {
                $this->resum['errors']++;
                $this->command->error("❌ Error en fila $rowNumber: " . $e->getMessage());
            }
        }
        
        fclose($handle);
        $this->resum['ordres_procesades'] = $rowNumber - 1;
    }
    
    /**
     * Procesar fila de matrícula
     */
    private function processRegistrationRow($row, $rowNumber)
    {
        if (count($row) < 9) {
            return;
        }
        
        $firstName = $this->cleanField($row[0]);
        $lastName = $this->cleanField($row[1]);
        $email = $this->cleanField($row[2]);
        $phone = $this->cleanField($row[3]);
        $itemName = $this->cleanField($row[4]);
        $courseCode = $this->cleanField($row[5]);
        $status = $this->cleanField($row[6]) ?? 'pending';
        $quantity = $this->cleanField($row[7]) ?? 1;
        $price = $this->cleanField($row[8]);
        
        // Buscar curso por código
        $course = $this->findCourseByCode($courseCode, $itemName);
        
        if (!$course) {
            $this->resum['ordres_sense_curs']++;
            $this->command->warn("⚠️ Fila $rowNumber: Curso no encontrado - Código: $courseCode, Nombre: $itemName");
            return;
        }
        
        $this->resum['ordres_amb_curs_trobat']++;
        
        // Crear o obtener usuario
        $user = $this->createOrGetUser($firstName, $lastName, $email, $phone);
        
        // Crear o obtener alumno
        $student = $this->createOrGetStudent($user, $firstName, $lastName);
        
        // Crear matrícula
        $this->createRegistration($student, $course, $status, $quantity, $price);
    }
    
    /**
     * Buscar curso por código o nombre
     */
    private function findCourseByCode($courseCode, $itemName)
    {
        // Primero buscar por código exacto
        if (isset($this->courseCache[$courseCode]) && $courseCode !== 'NOT_FOUND') {
            return $this->courseCache[$courseCode];
        }
        
        // Si no hay código, buscar por nombre
        if ($courseCode === 'NOT_FOUND') {
            foreach ($this->courseCache as $course) {
                $similarity = $this->calculateSimilarity($itemName, $course->title);
                if ($similarity > 0.8) {
                    $this->command->info("🎯 Coincidencia por nombre: '$itemName' → {$course->title} ({$course->code})");
                    return $course;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Crear o obtener usuario
     */
    private function createOrGetUser($firstName, $lastName, $email, $phone)
    {
        $email = strtolower(trim($email));
        
        // Verificar caché primero
        if (isset($this->userCache[$email])) {
            $this->resum['usuaris_existents']++;
            return $this->userCache[$email];
        }
        
        // Buscar en base de datos
        $user = User::where('email', $email)->first();
        
        if ($user) {
            $this->userCache[$email] = $user;
            $this->resum['usuaris_existents']++;
            return $user;
        }
        
        // Crear nuevo usuario
        $user = User::create([
            'name' => trim("$firstName $lastName"),
            'email' => $email,
            'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD')),
            'locale' => 'ca',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        
        $this->userCache[$email] = $user;
        $this->resum['usuaris_creats']++;
        
        return $user;
    }
    
    /**
     * Crear o obtener alumno
     */
    private function createOrGetStudent($user, $firstName, $lastName)
    {
        $studentId = $user->id;
        
        // Verificar caché primero
        if (isset($this->studentCache[$studentId])) {
            $this->resum['alumnes_existents']++;
            return $this->studentCache[$studentId];
        }
        
        // Buscar en base de datos
        $student = CampusStudent::where('user_id', $studentId)->first();
        
        if ($student) {
            $this->studentCache[$studentId] = $student;
            $this->resum['alumnes_existents']++;
            return $student;
        }
        
        // Generar código de alumno único
        $studentCode = $this->generateStudentCode($firstName, $lastName);
        
        // Crear nuevo alumno
        $student = CampusStudent::create([
            'user_id' => $studentId,
            'student_code' => $studentCode,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $user->phone ?? null,
            'email' => $user->email,
            'status' => 'active',
            'enrollment_date' => now(),
        ]);
        
        $this->studentCache[$studentId] = $student;
        $this->resum['alumnes_creats']++;
        
        return $student;
    }
    
    /**
     * Generar código de alumno único
     */
    private function generateStudentCode($firstName, $lastName)
    {
        // Extraer iniciales
        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        
        // Generar número aleatorio
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $code = $initials . $random;
        
        // Verificar que no exista
        $exists = CampusStudent::where('student_code', $code)->exists();
        if ($exists) {
            // Si existe, generar otro
            return $this->generateStudentCode($firstName, $lastName);
        }
        
        return $code;
    }
    
    /**
     * Crear matrícula
     */
    private function createRegistration($student, $course, $status, $quantity, $price)
    {
        // Verificar si ya existe una matrícula similar
        $existing = CampusRegistration::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();
        
        if ($existing) {
            return; // No duplicar matrículas
        }
        
        // Generar código de matrícula único
        $registrationCode = $this->generateRegistrationCode();
        
        CampusRegistration::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'registration_code' => $registrationCode,
            'status' => $this->mapStatus($status),
            'registration_date' => now(),
            'amount' => $price ? (float) str_replace(',', '.', $price) : 0.00,
            'payment_status' => $status === 'completed' ? 'paid' : 'pending',
            'metadata' => json_encode([
                'imported_at' => now()->toISOString(),
                'source' => 'csv_import',
                'quantity' => $quantity
            ])
        ]);
        
        $this->resum['matriculaciones_creadas']++;
    }
    
    /**
     * Generar código de matrícula único
     */
    private function generateRegistrationCode()
    {
        do {
            $code = 'REG' . date('Y') . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $exists = CampusRegistration::where('registration_code', $code)->exists();
        } while ($exists);
        
        return $code;
    }
    
    /**
     * Mapear estado del CSV a estado del sistema
     */
    private function mapStatus($csvStatus)
    {
        $statusMap = [
            'completed' => 'confirmed',
            'pending' => 'pending',
            'processing' => 'pending',
            'cancelled' => 'cancelled',
            'refunded' => 'cancelled',
            '1' => 'confirmed',
            '0' => 'cancelled',
        ];
        
        return $statusMap[strtolower($csvStatus)] ?? 'pending';
    }
    
    /**
     * Limpiar campo
     */
    private function cleanField($field)
    {
        if ($field === null || $field === '\N' || $field === '') {
            return null;
        }
        
        return trim($field, '"');
    }
    
    /**
     * Calcular similitud
     */
    private function calculateSimilarity($str1, $str2)
    {
        similar_text(strtolower($str1), strtolower($str2), $percent);
        return $percent / 100;
    }
    
    /**
     * Mostrar resumen final
     */
    private function showFinalSummary()
    {
        $this->command->info("\n" . str_repeat("=", 60));
        $this->command->info("📊 RESUMEN FINAL DE IMPORTACIÓN");
        $this->command->info(str_repeat("=", 60));
        
        $this->command->info("📋 Órdenes procesadas: {$this->resum['ordres_procesades']}");
        $this->command->info("✅ Órdenes con curso encontrado: {$this->resum['ordres_amb_curs_trobat']}");
        $this->command->info("❌ Órdenes sin curso: {$this->resum['ordres_sense_curs']}");
        
        $this->command->info("\n👥 Usuarios:");
        $this->command->info("   🆕 Nuevos: {$this->resum['usuaris_creats']}");
        $this->command->info("   ✅ Existentes: {$this->resum['usuaris_existents']}");
        
        $this->command->info("\n🎓 Alumnos:");
        $this->command->info("   🆕 Nuevos: {$this->resum['alumnes_creats']}");
        $this->command->info("   ✅ Existentes: {$this->resum['alumnes_existents']}");
        
        $this->command->info("\n📝 Matrículas:");
        $this->command->info("   🆕 Creadas: {$this->resum['matriculaciones_creadas']}");
        
        if ($this->resum['errors'] > 0) {
            $this->command->info("\n❌ Errores: {$this->resum['errors']}");
        }
        
        // Calcular tasas
        $totalOrders = $this->resum['ordres_procesades'];
        if ($totalOrders > 0) {
            $courseMatchRate = round(($this->resum['ordres_amb_curs_trobat'] / $totalOrders) * 100, 2);
            $this->command->info("\n📈 Estadísticas:");
            $this->command->info("   🎯 Tasa de coincidencia de cursos: {$courseMatchRate}%");
        }
        
        $this->command->info(str_repeat("=", 60));
    }
}
