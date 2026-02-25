<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class IniciSetupWithLogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 === CONFIGURACIÓN INICIAL COMPLETA DEL CAMPUS (CON LOGS) ===');
        $this->command->info('');
        
        // Crear archivo de log específico para este setup
        $logFile = 'inici_setup_' . date('Y-m-d_H-i-s') . '.log';
        $logPath = storage_path("logs/{$logFile}");
        
        $this->command->info("📝 Log guardado en: {$logFile}");
        $this->writeLog($logPath, "=== INICIO DE CONFIGURACIÓN DEL CAMPUS ===");
        $this->writeLog($logPath, "Fecha: " . Carbon::now()->format('Y-m-d H:i:s'));
        $this->writeLog($logPath, "");
        
        $seeders = [
            [
                'class' => 'Database\\Seeders\\IniciCategoriesSeasonSeeder',
                'name' => 'Categorías y Temporadas',
                'description' => 'Crear 5 categorías principales y 2 temporadas académicas',
                'required' => true,
            ],
            [
                'class' => 'Database\\Seeders\\IniciCoursesMapeadoCSVSeeder',
                'name' => 'Cursos desde CSV',
                'description' => 'Importar 56 cursos desde archivo CSV con mapeo de IDs',
                'required' => false,
            ],
            [
                'class' => 'Database\\Seeders\\IniciTeachersCSVSeeder',
                'name' => 'Teachers y Usuarios desde CSV',
                'description' => 'Importar 55 teachers y crear usuarios con rol teacher',
                'required' => false,
            ],
            [
                'class' => 'Database\\Seeders\\IniciSpaceSeeder',
                'name' => 'Espacios/Aulas',
                'description' => 'Crear 8 espacios físicos para impartir clases',
                'required' => false,
            ],
            [
                'class' => 'Database\\Seeders\\IniciTimeSlotSeeder',
                'name' => 'Franjas Horarias',
                'description' => 'Crear 15 franjas horarias (3 por día, lunes a viernes)',
                'required' => false,
            ],
        ];

        $executed = [];
        $skipped = [];
        $errors = [];

        foreach ($seeders as $seeder) {
            $this->command->info('');
            $this->command->info("📋 {$seeder['name']}");
            $this->command->info("   {$seeder['description']}");
            
            $this->writeLog($logPath, "Procesando: {$seeder['name']}");
            $this->writeLog($logPath, "Descripción: {$seeder['description']}");
            
            if ($seeder['required']) {
                $this->command->warn("   ⚠️  Este seeder es REQUERIDO para el funcionamiento del sistema");
                $this->writeLog($logPath, "Estado: REQUERIDO - Ejecutando automáticamente");
                $execute = true;
            } else {
                $execute = $this->command->confirm("   ¿Desea ejecutar este seeder? (y/n)");
                $this->writeLog($logPath, "Estado: OPCIONAL - Usuario eligió: " . ($execute ? 'EJECUTAR' : 'OMITIR'));
            }

            if ($execute) {
                try {
                    $this->command->info("   🔄 Ejecutando {$seeder['class']}...");
                    $this->writeLog($logPath, "Ejecutando seeder: {$seeder['class']}");
                    
                    $startTime = microtime(true);
                    $this->call($seeder['class']);
                    $endTime = microtime(true);
                    
                    $duration = round(($endTime - $startTime), 2);
                    $executed[] = [
                        'seeder' => $seeder['class'],
                        'name' => $seeder['name'],
                        'duration' => $duration
                    ];
                    
                    $this->command->info("   ✅ Completado en {$duration} segundos");
                    $this->writeLog($logPath, "✅ ÉXITO: Completado en {$duration} segundos");
                    
                } catch (\Exception $e) {
                    $errorMsg = "❌ Error en {$seeder['class']}: " . $e->getMessage();
                    $this->command->error("   {$errorMsg}");
                    
                    $errorData = [
                        'seeder' => $seeder['class'],
                        'name' => $seeder['name'],
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ];
                    $errors[] = $errorData;
                    
                    // Guardar error detallado en log
                    $this->writeLog($logPath, "❌ ERROR: {$e->getMessage()}");
                    $this->writeLog($logPath, "Stack trace: " . $e->getTraceAsString());
                    
                    // También guardar en log principal de Laravel
                    Log::error("Seeder Error: {$seeder['name']}", [
                        'class' => $seeder['class'],
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    if ($seeder['required']) {
                        $this->command->error("   🚨 Error crítico en seeder requerido. Deteniendo ejecución.");
                        $this->writeLog($logPath, "🚨 ERROR CRÍTICO: Deteniendo ejecución por seeder requerido");
                        break;
                    } else {
                        $continue = $this->command->confirm("   ¿Desea continuar con los siguientes seeders? (y/n)");
                        $this->writeLog($logPath, "Usuario eligió continuar: " . ($continue ? 'SÍ' : 'NO'));
                        if (!$continue) {
                            break;
                        }
                    }
                }
            } else {
                $skipped[] = [
                    'seeder' => $seeder['class'],
                    'name' => $seeder['name']
                ];
                $this->command->info("   ⏭️  Omitido");
                $this->writeLog($logPath, "⏭️ OMITIDO por usuario");
            }
            
            $this->writeLog($logPath, ""); // Línea en blanco
        }

        // Reporte final
        $this->printFinalReport($executed, $skipped, $errors, $logPath);
    }
    
    private function writeLog($logPath, $message)
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
        file_put_contents($logPath, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    private function printFinalReport($executed, $skipped, $errors, $logPath)
    {
        $this->command->info('');
        $this->command->info('🎯 === REPORTE FINAL DE CONFIGURACIÓN ===');
        $this->command->info('');
        
        // Guardar reporte en log
        $this->writeLog($logPath, "=== REPORTE FINAL ===");
        
        if (!empty($executed)) {
            $this->command->info('✅ SEEDERS EJECUTADOS:');
            $this->writeLog($logPath, "SEEDERS EJECUTADOS:");
            foreach ($executed as $item) {
                $this->command->info("   - {$item['name']} - {$item['duration']}s");
                $this->writeLog($logPath, "  - {$item['name']}: {$item['duration']}s");
            }
        }
        
        if (!empty($skipped)) {
            $this->command->info('');
            $this->command->info('⏭️  SEEDERS OMITIDOS:');
            $this->writeLog($logPath, "SEEDERS OMITIDOS:");
            foreach ($skipped as $item) {
                $this->command->info("   - {$item['name']}");
                $this->writeLog($logPath, "  - {$item['name']}");
            }
        }
        
        if (!empty($errors)) {
            $this->command->info('');
            $this->command->error('❌ ERRORES ENCONTRADOS:');
            $this->writeLog($logPath, "ERRORES ENCONTRADOS:");
            foreach ($errors as $item) {
                $this->command->error("   - {$item['name']}: {$item['error']}");
                $this->writeLog($logPath, "  - {$item['name']}: {$item['error']}");
            }
        }
        
        // Resumen del estado actual
        $this->command->info('');
        $this->command->info('📊 ESTADO ACTUAL DEL SISTEMA:');
        $this->writeLog($logPath, "ESTADO ACTUAL DEL SISTEMA:");
        $this->printSystemStatus($logPath);
        
        $this->command->info('');
        $this->command->info("📝 Log completo guardado en: {$logPath}");
        $this->command->info('🎉 === FIN DE LA CONFIGURACIÓN INICIAL ===');
        
        $this->writeLog($logPath, "=== FIN DE LA CONFIGURACIÓN ===");
        $this->writeLog($logPath, "");
    }
    
    private function printSystemStatus($logPath)
    {
        try {
            // Categorías
            $categories = \App\Models\CampusCategory::count();
            $this->command->info("   📂 Categorías: {$categories}");
            $this->writeLog($logPath, "  📂 Categorías: {$categories}");
            
            // Temporadas
            $seasons = \App\Models\CampusSeason::count();
            $this->command->info("   📅 Temporadas: {$seasons}");
            $this->writeLog($logPath, "  📅 Temporadas: {$seasons}");
            
            // Cursos
            $courses = \App\Models\CampusCourse::count();
            $this->command->info("   📚 Cursos: {$courses}");
            $this->writeLog($logPath, "  📚 Cursos: {$courses}");
            
            // Teachers
            $teachers = \App\Models\CampusTeacher::count();
            $teacherUsers = \App\Models\User::role('teacher')->count();
            $this->command->info("   👨‍🏫 Teachers: {$teachers} (usuarios: {$teacherUsers})");
            $this->writeLog($logPath, "  👨‍🏫 Teachers: {$teachers} (usuarios: {$teacherUsers})");
            
            // Espacios
            $spaces = \App\Models\CampusSpace::count();
            $this->command->info("   🏫 Espacios: {$spaces}");
            $this->writeLog($logPath, "  🏫 Espacios: {$spaces}");
            
            // Franjas horarias
            $timeSlots = \App\Models\CampusTimeSlot::count();
            $this->command->info("   🕐 Franjas horarias: {$timeSlots}");
            $this->writeLog($logPath, "  🕐 Franjas horarias: {$timeSlots}");
            
        } catch (\Exception $e) {
            $errorMsg = "No se pudo obtener el estado del sistema: " . $e->getMessage();
            $this->command->warn("   ⚠️  {$errorMsg}");
            $this->writeLog($logPath, "  ⚠️  {$errorMsg}");
        }
    }
}
