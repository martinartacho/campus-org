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

class RegistrationImportSeeder extends Seeder
{
    private $resum = [
        'matriculaciones_creadas' => 0,
        'usuaris_creats' => 0,
        'usuaris_existents' => 0,
        'alumnes_creats' => 0,
        'alumnes_existents' => 0,
        'ordres_procesades' => 0,
        'ordres_sense_email' => 0,
        'ordres_sense_curs' => 0,
        'matriculaciones_posibles' => 0,
        'matriculaciones_imposibles' => 0,
    ];
    
    private $resultadosCSV = [];

    public function run()
    {
        $this->command->info('=== Seeder de Importación de Matrículas desde CSV ===');
        
        // 1. LEER ARCHIVOS CSV
        $cursosFile = storage_path('app/imports/cursos_upg.csv');
        $ordresFile = storage_path('app/imports/ordres_2025-26-Q2-v2.csv');
        
        if (!file_exists($cursosFile) || !file_exists($ordresFile)) {
            $this->command->error('No se encuentran los archivos CSV necesarios.');
            return;
        }
        
        $this->command->info("📁 Leyendo archivos:");
        $this->command->info("   Cursos: {$cursosFile}");
        $this->command->info("   Órdenes: {$ordresFile}");
        
        // 2. OBTENER CURSOS DISPONIBLES
        $cursos = $this->leerCursosCSV($cursosFile);
        $this->command->info("📚 Cursos encontrados: " . count($cursos));
        
        // 3. PROCESAR ÓRDENES DE PAGO
        $ordres = $this->leerOrdresCSV($ordresFile);
        $this->command->info("💳 Órdenes encontradas: " . count($ordres));
        
        // 4. PROCESAR CADA ORDEN
        foreach ($ordres as $orden) {
            $this->procesarOrden($orden, $cursos);
        }
        
        // 5. MOSTRAR RESUMEN
        $this->mostrarResumen();
        
        // 6. GUARDAR RESULTADOS EN CSV
        $this->guardarResultadosCSV();
        
        $this->command->info('✅ Proceso completado.');
    }
    
    private function leerCursosCSV($file)
    {
        $cursos = [];
        $handle = fopen($file, 'r');
        
        // Saltar cabecera
        fgetcsv($handle, 1000, ',');
        
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if (count($data) >= 2) {
                $code = trim($data[0] ?? '');      // Columna 0 = code
                $title = trim($data[1] ?? '');     // Columna 1 = title
                
                // Solo procesar si tenemos código y título válidos
                if (!empty($title) && !empty($code)) {
                    $cursos[$title] = [
                        'code' => $code,
                        'title' => $title
                    ];
                }
            }
        }
        fclose($handle);
        
        return $cursos;
    }
    
    private function leerOrdresCSV($file)
    {
        $ordres = [];
        $handle = fopen($file, 'r');
        
        // Leer primera línea para detectar formato
        $firstLine = fgets($handle);
        $hasQuotes = strpos($firstLine, '"') !== false;
        
        // Volver al inicio
        rewind($handle);
        
        // Saltar cabecera
        fgetcsv($handle, 1000, ',');
        
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if (count($data) >= 5) {
                // Validar que los campos numéricos no tengan comillas
                $importe = is_numeric($data[6]) ? floatval($data[6]) : floatval(str_replace(['"', ','], ['', '.'], $data[6]));
                
                $ordres[] = [
                    'fecha' => trim($data[0] ?? ''),
                    'concepto' => trim($data[4] ?? ''),  // Columna 4 = Item Name
                    'nif' => trim($data[8] ?? ''),      // Columna 8 = NIF
                    'nombre' => trim($data[0]) . ' ' . trim($data[1]),  // First + Last Name
                    'importe' => $importe,
                    'email' => trim($data[2] ?? ''),
                    'telefono' => trim($data[3] ?? ''),
                    'status' => trim($data[5] ?? '')  // Columna 5 = Status (nou)
                ];
            }
        }
        
        fclose($handle);
        return $ordres;
    }
    
    private function procesarOrden($orden, $cursos)
    {
        $this->resum['ordres_procesades']++;
        
        // 1. VALIDAR EMAIL OBLIGATORI
        if (empty($orden['email']) || !filter_var($orden['email'], FILTER_VALIDATE_EMAIL)) {
            $this->resum['ordres_sense_email']++;
            $this->command->warn("⚠️  Ordre SENSE EMAIL: {$orden['nombre']} - {$orden['concepto']}");
            
            // Agregar a CSV sense email
            $this->resultadosCSV[] = [
                $orden['nombre'],
                $orden['email'],
                $orden['telefono'],
                '', // code buit
                $orden['concepto'],
                $orden['importe'],
                'SENSE EMAIL - NO VÀLID'
            ];
            return;
        }
        
        // 2. BUSCAR CURSO PER TÍTOL
        $curso = $this->buscarCursoPorTitulo($orden['concepto'], $cursos);
        
        if (!$curso) {
            $this->resum['ordres_sense_curs']++;
            $this->command->warn("⚠️  Ordre sense curs: {$orden['concepto']} - {$orden['nombre']} (Email: {$orden['email']})");
            
            // Agregar a CSV sense curs
            $this->resultadosCSV[] = [
                $orden['nombre'],
                $orden['email'],
                $orden['telefono'],
                '', // code buit
                $orden['concepto'],
                $orden['importe'],
                'SENSE CURS - TÍTOL NO TROBAT: ' . $orden['concepto']
            ];
            return;
        }
        
        // 3. GESTIONAR USUARI I ALUMNES
        $usuari = $this->buscarOCrearUsuari($orden);
        $alumne = $this->buscarOCrearAlumne($orden, $usuari);
        
        // 4. BUSCAR O CREAR CURS A BD
        $cursBD = $this->buscarOCrearCurs($curso);
        
        // 5. CREAR MATRÍCULA
        $this->crearMatricula($alumne, $cursBD, $orden, $curso['code']);
    }
    
    private function buscarCursoPorTitulo($concepto, $cursos)
    {
        // Búsqueda exacta primero
        foreach ($cursos as $curso) {
            if (strcasecmp($curso['title'], $concepto) === 0) {
                return $curso;
            }
        }
        
        // Búsqueda parcial
        foreach ($cursos as $curso) {
            if (stripos($concepto, $curso['title']) !== false || 
                stripos($curso['title'], $concepto) !== false) {
                return $curso;
            }
        }
        
        return null;
    }
    
    private function buscarOCrearUsuari($orden)
    {
        $usuari = User::where('email', $orden['email'])->first();
        
        if ($usuari) {
            $this->resum['usuaris_existents']++;
            $this->command->info("✅ Usuari existent: {$orden['email']}");
        } else {
            // Crear nou usuari
            $usuari = User::create([
                'name' => $orden['nombre'],
                'email' => $orden['email'],
                'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
                'email_verified_at' => now()
            ]);
            
            // Assignar rol segons estat de pagament
            $rol = $this->determinarRolPerPagament($orden);
            $usuari->assignRole($rol);
            
            $this->resum['usuaris_creats']++;
            $this->command->info("👤 Usuari creat: {$orden['email']} (rol: {$rol})");
        }
        
        return $usuari;
    }
    
    private function determinarRolPerPagament($orden)
    {
        // Si l'import és > 0, considerem pagament completat
        if ($orden['importe'] > 0) {
            return 'student';
        } else {
            return 'invited';
        }
    }
    
    private function buscarOCrearAlumne($orden, $usuari)
    {
        $alumne = CampusStudent::where('user_id', $usuari->id)->first();
        
        if ($alumne) {
            $this->resum['alumnes_existents']++;
            $this->command->info("✅ Alumne existent: {$orden['nombre']} ({$orden['email']})");
        } else {
            // Crear nou alumne amb format STD-000
            $alumne = CampusStudent::create([
                'user_id' => $usuari->id,
                'student_code' => 'STD-' . str_pad($usuari->id, 3, '0', STR_PAD_LEFT),
                'first_name' => $orden['nombre'],
                'last_name' => '',
                'dni' => $orden['nif'] ?? '',
                'birth_date' => null,
                'phone' => $orden['telefono'],
                'address' => '',
                'email' => $orden['email'],
                'emergency_contact' => '',
                'emergency_phone' => '',
                'status' => 'active',
                'enrollment_date' => now()->toDateString(),
                'academic_record' => null,
                'metadata' => null
            ]);
            
            $this->resum['alumnes_creats']++;
            $this->command->info("🎓 Alumne creat: {$orden['nombre']} (STD-" . str_pad($usuari->id, 3, '0', STR_PAD_LEFT) . ")");
        }
        
        return $alumne;
    }
    
    private function buscarOCrearCurs($curso)
    {
        // Només buscar, no crear
        $cursBD = CampusCourse::where('code', $curso['code'])->first();
        
        if (!$cursBD) {
            $this->command->warn("📚 Curs NO trobat: {$curso['code']} - {$curso['title']}");
        } else {
            $this->command->info("📚 Curs trobat: {$curso['code']} - {$curso['title']}");
        }
        
        return $cursBD;
    }
    
    private function crearMatricula($alumne, $curs, $orden, $courseCode)
    {
        // Verificar si alumne i curs existeixen
        if (!$alumne) {
            $this->resum['matriculaciones_imposibles']++;
            $this->command->warn("❌ Matrícula IMPOSSIBLE: {$orden['nombre']} - {$orden['concepto']} (Alumne: NO)");
            
            // Agregar a CSV sense alumne
            $this->resultadosCSV[] = [
                $orden['nombre'],
                $orden['email'],
                $orden['telefono'],
                $courseCode,
                $orden['concepto'],
                $orden['importe'],
                'SENSE ALUMNE'
            ];
            return;
        }
        
        if (!$curs) {
            $this->resum['matriculaciones_imposibles']++;
            $this->command->warn("❌ Matrícula IMPOSSIBLE: {$orden['nombre']} - {$orden['concepto']} (Curs: NO)");
            
            // Agregar a CSV sense curs
            $this->resultadosCSV[] = [
                $orden['nombre'],
                $orden['email'],
                $orden['telefono'],
                $courseCode,
                $orden['concepto'],
                $orden['importe'],
                'SENSE CURS'
            ];
            return;
        }
        
        $existe = CampusRegistration::where('student_id', $alumne->id)
            ->where('course_id', $curs->id)
            ->first();
            
        if ($existe) {
            $this->command->warn("⚠️  Matrícula ja existeix: {$alumne->first_name} - {$curs->title}");
            
            // Agregar a CSV ja existeix
            $this->resultadosCSV[] = [
                $orden['nombre'],
                $orden['email'],
                $orden['telefono'],
                $courseCode,
                $orden['concepto'],
                $orden['importe'],
                'JA EXISTEIX'
            ];
            return;
        }
        
        // Crear matrícula
        $matricula = CampusRegistration::create([
            'student_id' => $alumne->id,
            'course_id' => $curs->id,
            'registration_code' => 'REG-' . date('YmdHis') . '-' . $alumne->id . '-' . $curs->id,
            'registration_date' => $this->parseDate($orden['fecha']),
            'status' => $this->determinarEstatMatricula($orden),
            'amount' => $orden['importe'],
            'payment_status' => $this->determinarPaymentStatus($orden),
            'payment_method' => $orden['status'] === '1' ? 'wordpress' : 'import',
            'notes' => 'Importat des de CSV d\'ordres de pagament'
        ]);
        
        $this->resum['matriculaciones_creadas']++;
        $this->command->info("✅ Matrícula creada: {$alumne->first_name} - {$curs->title} (€{$orden['importe']})");
        
        // Agregar a CSV exitós
        $this->resultadosCSV[] = [
            $orden['nombre'],
            $orden['email'],
            $orden['telefono'],
            $courseCode,
            $orden['concepto'],
            $orden['importe'],
            'MATRÍCULA CREADA'
        ];
    }
    
    private function determinarEstatMatricula($orden)
    {
        // Determinar estat segons columna Status del CSV
        if (isset($orden['status'])) {
            // Si Status = 1, matrícula confirmada
            return $orden['status'] === '1' ? 'confirmed' : 'pending';
        } else {
            // Si no hi ha columna Status, usar import de pagament
            return $orden['importe'] > 0 ? 'confirmed' : 'pending';
        }
    }
    
    private function determinarPaymentStatus($orden)
    {
        // Determinar estat de pagament segons columna Status del CSV
        if (isset($orden['status'])) {
            // Si Status = 1, pagament confirmat
            return $orden['status'] === '1' ? 'paid' : 'pending';
        } else {
            // Si no hi ha columna Status, usar import de pagament
            return $orden['importe'] > 0 ? 'paid' : 'pending';
        }
    }
    
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return now()->toDateString();
        }
        
        try {
            // Intentar parsear com a data vàlida
            $date = \Carbon\Carbon::parse($dateString);
            return $date->toDateString();
        } catch (\Exception $e) {
            // Si falla, usar data actual
            return now()->toDateString();
        }
    }
    
    private function mostrarResumen()
    {
        $this->command->info("\n=== RESUM DEL PROCÉS ===");
        $this->command->info("📊 Ordres processades: {$this->resum['ordres_procesades']}");
        $this->command->info("👤 Usuaris creats: {$this->resum['usuaris_creats']}");
        $this->command->info("👥 Usuaris existents: {$this->resum['usuaris_existents']}");
        $this->command->info("🎓 Alumnes creats: {$this->resum['alumnes_creats']}");
        $this->command->info("🎓 Alumnes existents: {$this->resum['alumnes_existents']}");
        $this->command->info("✅ Matrícules creades: {$this->resum['matriculaciones_creadas']}");
        $this->command->info("❌ Matrícules impossibles: {$this->resum['matriculaciones_imposibles']}");
        $this->command->info("⚠️  Ordres sense email: {$this->resum['ordres_sense_email']}");
        $this->command->info("⚠️  Ordres sense curs: {$this->resum['ordres_sense_curs']}");
        $this->command->info("========================\n");
    }
    
    private function guardarResultadosCSV()
    {
        // 1. Crear CSV detallado de resultados
        $filename = storage_path('app/imports/resultados_importacion_' . date('Ymd_His') . '.csv');
        $handle = fopen($filename, 'w');
        
        // Cabecera del CSV detallado
        fputcsv($handle, [
            'Nombre',
            'Email',
            'Telefono',
            'Code Curso',
            'Titulo Curso',
            'Importe',
            'Estado'
        ], ';');
        
        // Escribir resultados
        foreach ($this->resultadosCSV as $resultado) {
            fputcsv($handle, $resultado, ';');
        }
        
        fclose($handle);
        
        // 2. Crear CSV de resumen
        $summaryFilename = storage_path('app/imports/resumen_importacion_' . date('Ymd_His') . '.csv');
        $summaryHandle = fopen($summaryFilename, 'w');
        
        // Cabecera del resumen
        fputcsv($summaryHandle, [
            'timestamp',
            'accion',
            'tipo',
            'codigo',
            'descripcion',
            'resultado'
        ], ';');
        
        // Escribir resumen
        fputcsv($summaryHandle, [
            date('Y-m-d H:i:s'),
            'RESUMEN',
            'PROCESO',
            'IMPORTACION',
            'COMPLETADO',
            json_encode($this->resum)
        ], ';');
        
        fclose($summaryHandle);
        
        $this->command->info("📄 Resultados detallados guardados en: {$filename}");
        $this->command->info("📊 Resumen guardado en: {$summaryFilename}");
    }
}
