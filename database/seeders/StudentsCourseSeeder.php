<?php
// database/seeders/StudentsCourseSeeder.php

namespace Database\Seeders;

use App\Models\CampusCourse;
use App\Models\CampusStudent;
use App\Models\CampusRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class StudentsCourseSeeder extends Seeder
{
    private $courseId = null;
    private $cursoSeleccionado = null;
    private $incidencies = [];
    private $resum = [
        'matriculaciones_creadas' => 0,
        'alumnos_creados' => 0,
        'alumnos_existentes' => 0,
    ];

    public function run()
    {
        $this->command->info('=== Seeder de Matriculación de Alumnos en Cursos ===');
        
        // 1. OBTENER O SELECCIONAR CURSO
        $this->obtenerCurso();
        
        if (!$this->cursoSeleccionado) {
            $this->command->error('No se ha seleccionado ningún curso. Operación cancelada.');
            return;
        }
        
        $this->command->info("📚 Curso seleccionado: {$this->cursoSeleccionado->code} - {$this->cursoSeleccionado->title}");
        $this->command->info("   Precio: {$this->cursoSeleccionado->price}€");
        $this->command->info("   Plazas disponibles: {$this->cursoSeleccionado->max_students}");
        
        // 2. DETERMINAR NÚMERO DE ALUMNOS A MATRICULAR
        $numAlumnos = $this->determinarNumeroAlumnos();
        
        // 3. OBTENER O CREAR ALUMNOS
        $alumnos = $this->obtenerOCrearAlumnos($numAlumnos);
        
        // 4. MATRICULAR ALUMNOS EN EL CURSO
        $this->matricularAlumnos($alumnos);
        
        // 5. MOSTRAR RESUMEN
        $this->mostrarResumen();
    }
    
    /**
     * Obtiene el curso por ID o muestra lista para seleccionar
     */
    private function obtenerCurso()
    {
        // Opción 1: Verificar si se pasó un ID por argumento
        if (isset($_SERVER['argv'])) {
            $args = $_SERVER['argv'];
            foreach ($args as $arg) {
                if (str_contains($arg, '--course_id=')) {
                    $parts = explode('=', $arg);
                    if (count($parts) > 1) {
                        $this->courseId = intval($parts[1]);
                        break;
                    }
                }
            }
        }
        
        // Buscar curso por ID si se proporcionó
        if ($this->courseId) {
            $this->cursoSeleccionado = CampusCourse::find($this->courseId);
            
            if (!$this->cursoSeleccionado) {
                $this->command->error("❌ No se encontró el curso con ID: {$this->courseId}");
                $this->courseId = null;
            }
        }
        
        // Si no se proporcionó ID o no se encontró, mostrar lista
        if (!$this->cursoSeleccionado) {
            $this->mostrarListaCursos();
        }
    }
    
    /**
     * Muestra lista de cursos y permite seleccionar uno
     */
    private function mostrarListaCursos()
    {
        $this->command->info('📋 Listado de cursos disponibles (últimos 5 activos):');
        
        $cursos = CampusCourse::where('is_active', true)
            ->orderBy('created_at', 'desc')
           //  ->limit(5)
            ->get();
        
        if ($cursos->isEmpty()) {
            $this->command->error('No hay cursos activos disponibles.');
            return;
        }
        
        foreach ($cursos as $curso) {
            $matriculados = $curso->registrations()->count();
            $this->command->line("   [{$curso->id}] {$curso->code} - {$curso->title} (Plazas: {$curso->max_students}, Matriculados: {$matriculados})");
        }
        
        // Preguntar al usuario por el curso
        $cursoId = $this->command->ask('Introduce el ID del curso para matricular alumnos (o 0 para cancelar):');
        
        if ($cursoId == 0) {
            $this->command->info('Operación cancelada.');
            return;
        }
        
        $this->cursoSeleccionado = CampusCourse::find($cursoId);
        
        if (!$this->cursoSeleccionado) {
            $this->command->error('❌ ID de curso no válido.');
            $this->mostrarListaCursos();
        }
    }
    
    /**
     * Determina el número de alumnos a matricular (entre 50% y 100% del máximo)
     */
    private function determinarNumeroAlumnos()
    {
        $maxStudents = $this->cursoSeleccionado->max_students;
        
        if ($maxStudents <= 0) {
            $maxStudents = 30; // Valor por defecto si no está definido
        }
        
        // Calcular porcentaje aleatorio entre 50 y 100
        $porcentaje = rand(50, 100) / 100;
        
        $numAlumnos = (int) round($maxStudents * $porcentaje);
        
        // Asegurar al menos 1 alumno
        $numAlumnos = max(1, $numAlumnos);
        
        // Verificar cuántos ya están matriculados
        $matriculadosActuales = $this->cursoSeleccionado->registrations()->count();
        
        // Ajustar si ya hay matriculados
        $numAlumnos = min($numAlumnos, $maxStudents - $matriculadosActuales);
        
        // Asegurar que no sea negativo
        $numAlumnos = max(0, $numAlumnos);
        
        $this->command->info("📊 Se matricularán {$numAlumnos} alumnos ({$porcentaje}% de {$maxStudents})");
        
        if ($matriculadosActuales > 0) {
            $this->command->info("   Ya hay {$matriculadosActuales} alumnos matriculados.");
        }
        
        return $numAlumnos;
    }
    
    /**
     * Obtiene alumnos existentes o crea nuevos si es necesario
     */
    private function obtenerOCrearAlumnos($cantidad)
    {
        $alumnos = [];
        
        // Obtener alumnos existentes que NO estén ya matriculados en este curso
        $alumnosExistentes = CampusStudent::whereDoesntHave('registrations', function($query) {
            $query->where('course_id', $this->cursoSeleccionado->id);
        })
        ->inRandomOrder()
        ->limit($cantidad)
        ->get();
        
        $this->resum['alumnos_existentes'] = $alumnosExistentes->count();
        
        // Si no hay suficientes alumnos existentes, crear nuevos
        if ($alumnosExistentes->count() < $cantidad) {
            $necesarios = $cantidad - $alumnosExistentes->count();
            
            $this->command->info("📝 Creando {$necesarios} nuevos alumnos...");
            
            for ($i = 0; $i < $necesarios; $i++) {
                $alumno = $this->crearNuevoAlumno();
                if ($alumno) {
                    $alumnos[] = $alumno;
                    $this->resum['alumnos_creados']++;
                }
            }
        }
        
        // Combinar alumnos existentes y nuevos
        foreach ($alumnosExistentes as $alumno) {
            $alumnos[] = $alumno;
        }
        
        return $alumnos;
    }
    
    /**
     * Crea un nuevo alumno con datos aleatorios
     */
    private function crearNuevoAlumno()
    {
        try {
            // Generar datos aleatorios
            $nombres = ['Carlos', 'Ana', 'David', 'Elena', 'Javier', 'Laura', 'Miguel', 'Sofia', 'Pablo', 'Claudia'];
            $apellidos = ['García', 'Rodríguez', 'Martínez', 'López', 'Sánchez', 'Pérez', 'Gómez', 'Fernández', 'Díaz', 'Ruiz'];
            
            $nombre = $nombres[array_rand($nombres)];
            $apellido1 = $apellidos[array_rand($apellidos)];
            $apellido2 = $apellidos[array_rand($apellidos)];
            $nombreCompleto = "{$nombre} {$apellido1} {$apellido2}";
            
            // Generar código único
            $ultimoAlumno = CampusStudent::orderBy('id', 'desc')->first();
            $ultimoId = $ultimoAlumno ? intval(substr($ultimoAlumno->student_code, 3)) : 100;
            $nuevoCodigo = 'EST' . ($ultimoId + 1);
            
            // Generar email único
            $email = strtolower("{$nombre}.{$apellido1}." . uniqid() . "@upg.test");
            
            // Crear usuario
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $nombreCompleto,
                    'email' => $email,
                    'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'password123')),
                    'email_verified_at' => Carbon::now(),
                    'locale' => 'ca',
                ]
            );
            $user->assignRole('student');
            
            // Crear perfil de alumno
            $alumno = CampusStudent::create([
                'user_id' => $user->id,
                'student_code' => $nuevoCodigo,
                'first_name' => $nombre,
                'last_name' => "{$apellido1} {$apellido2}",
                'dni' => $this->generarDNI(),
                'birth_date' => $this->generarFechaNacimiento(),
                'phone' => '+34 6' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                'address' => $this->generarDireccion(),
                'email' => $email,
                'emergency_contact' => 'Contacto de emergencia',
                'emergency_phone' => '+34 900 123 456',
                'status' => 'active',
                'enrollment_date' => now()->format('Y-m-d'),
            ]);
            
            $this->command->info("   [+] Alumno creado: {$nuevoCodigo} - {$nombreCompleto}");
            
            return $alumno;
            
        } catch (\Exception $e) {
            $this->incidencies[] = "Error creando alumno: " . $e->getMessage();
            return null;
        }
    }
    
    /**
     * Matricula los alumnos en el curso
     */
    private function matricularAlumnos($alumnos)
    {
        $this->command->info('🎓 Matriculando alumnos...');
        
        // Obtener el precio del curso
        $precioCurso = $this->cursoSeleccionado->price;
        
        foreach ($alumnos as $alumno) {
            try {
                // Determinar estado de pago (80% pagado, 20% pendiente)
                $estaPagado = (rand(1, 100) <= 80);
                
                // Determinar estado (80% confirmado, 20% pendiente)
                $estaConfirmado = (rand(1, 100) <= 80);
                
                // Generar código de matrícula único
                $codigoMatricula = 'REG-' . strtoupper(Str::random(10));
                
                // Preparar historial de pago si está pagado
                $historialPago = null;
                if ($estaPagado) {
                    $historialPago = [
                        [
                            'date' => now()->format('Y-m-d'),
                            'amount' => $precioCurso,
                            'method' => $this->obtenerMetodoPagoAleatorio(),
                            'reference' => 'PAY-' . strtoupper(Str::random(10)),
                            'status' => 'paid'
                        ]
                    ];
                }
                
                $datosMatriculacion = [
                    'course_id' => $this->cursoSeleccionado->id,
                    'student_id' => $alumno->id,
                    'registration_code' => $codigoMatricula,
                    'registration_date' => now()->format('Y-m-d'),
                    'status' => $estaConfirmado ? 'confirmed' : 'pending',
                    'amount' => $precioCurso,
                    'payment_status' => $estaPagado ? 'paid' : 'pending',
                    'payment_due_date' => $estaPagado ? now()->addDays(30) : now()->addDays(14),
                    'payment_history' => $historialPago,
                    'payment_method' => $estaPagado ? $this->obtenerMetodoPagoAleatorio() : null,
                    'grade' => null,
                    'attendance_status' => null,
                    'notes' => 'Matrícula creada automáticamente por seeder',
                    'metadata' => [
                        'created_by_seeder' => true,
                        'seeder_date' => now()->format('Y-m-d H:i:s'),
                    ],
                ];
                
                // Verificar que no esté ya matriculado (por si acaso)
                $existe = CampusRegistration::where('course_id', $this->cursoSeleccionado->id)
                    ->where('student_id', $alumno->id)
                    ->exists();
                
                if (!$existe) {
                    CampusRegistration::create($datosMatriculacion);
                    $this->resum['matriculaciones_creadas']++;
                    
                    $estado = $estaConfirmado ? 'confirmada' : 'pendiente';
                    $pago = $estaPagado ? 'pagada' : 'pendiente';
                    $this->command->info("   ✓ {$alumno->student_code} - {$alumno->first_name} {$alumno->last_name} ({$estado}, {$pago})");
                } else {
                    $this->incidencies[] = "Alumno {$alumno->student_code} ya estaba matriculado";
                }
                
            } catch (\Exception $e) {
                $this->incidencies[] = "Error matriculando alumno {$alumno->student_code}: " . $e->getMessage();
                $this->incidencies[] = "Detalles: " . $e->getTraceAsString();
            }
        }
    }
    
    /**
     * Muestra el resumen de la operación
     */
    private function mostrarResumen()
    {
        $this->command->newLine();
        $this->command->info('========== RESUMEN ==========');
        $this->command->info("Curso: {$this->cursoSeleccionado->code} - {$this->cursoSeleccionado->title}");
        $this->command->info("Matriculaciones creadas: {$this->resum['matriculaciones_creadas']}");
        $this->command->info("Alumnos nuevos creados: {$this->resum['alumnos_creados']}");
        $this->command->info("Alumnos existentes utilizados: {$this->resum['alumnos_existentes']}");
        
        // Contar estadísticas de pago
        $matriculaciones = CampusRegistration::where('course_id', $this->cursoSeleccionado->id)->get();
        $total = $matriculaciones->count();
        $pagados = $matriculaciones->where('payment_status', 'paid')->count();
        $confirmados = $matriculaciones->where('status', 'confirmed')->count();
        
        if ($total > 0) {
            $porcentajePagados = round(($pagados / $total) * 100, 1);
            $porcentajeConfirmados = round(($confirmados / $total) * 100, 1);
            
            $this->command->info("📊 Estadísticas del curso:");
            $this->command->info("   Total matriculados: {$total}");
            $this->command->info("   Pagados: {$pagados} ({$porcentajePagados}%)");
            $this->command->info("   Confirmados: {$confirmados} ({$porcentajeConfirmados}%)");
        }
        
        if (!empty($this->incidencies)) {
            $this->command->newLine();
            $this->command->warn('========== INCIDENCIAS ==========');
            foreach ($this->incidencies as $incidencia) {
                $this->command->warn("• {$incidencia}");
            }
        }
        
        $this->command->newLine();
        $this->command->info('========== COMANDAS ==========');
        $this->command->line("• Matricular en curso específico: php artisan db:seed --class=StudentsCourseSeeder --course_id=1");
        $this->command->line("• Seleccionar de lista: php artisan db:seed --class=StudentsCourseSeeder");
    }
    
    // ============================================
    // FUNCIONES AUXILIARES
    // ============================================
    
    private function generarDNI()
    {
        $numero = rand(10000000, 99999999);
        $letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $letra = $letras[$numero % 23];
        return $numero . $letra;
    }
    
    private function generarFechaNacimiento()
    {
        // Edad entre 18 y 65 años
        $anios = rand(18, 65);
        $fecha = now()->subYears($anios);
        
        // Variar el día y mes
        $dias = rand(0, 365);
        return $fecha->subDays($dias)->format('Y-m-d');
    }
    
    private function generarDireccion()
    {
        $calles = ['Carrer Major', 'Avinguda Diagonal', 'Plaça Catalunya', 'Carrer Gran', 'Rambla', 'Passeig de Gràcia'];
        $numeros = rand(1, 200);
        $ciudades = ['Barcelona', 'Sant Cugat', 'Rubí', 'Terrassa', 'Sabadell', 'Mataró'];
        
        $calle = $calles[array_rand($calles)];
        $ciudad = $ciudades[array_rand($ciudades)];
        
        return "{$calle} {$numeros}, {$ciudad}";
    }
    
    private function obtenerMetodoPagoAleatorio()
    {
        $metodos = ['credit_card', 'bank_transfer', 'cash', 'paypal'];
        return $metodos[array_rand($metodos)];
    }
}