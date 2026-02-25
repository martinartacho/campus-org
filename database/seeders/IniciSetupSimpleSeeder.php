<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class IniciSetupSimpleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 === CONFIGURACIÓN INICIAL COMPLETA DEL CAMPUS ===');
        $this->command->info('');
        
        // Lista de seeders en orden de ejecución
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
            
            if ($seeder['required']) {
                $this->command->warn("   ⚠️  Este seeder es REQUERIDO para el funcionamiento del sistema");
                $execute = true;
            } else {
                $execute = $this->command->confirm("   ¿Desea ejecutar este seeder? (y/n)");
            }

            if ($execute) {
                try {
                    $this->command->info("   🔄 Ejecutando {$seeder['class']}...");
                    
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
                    
                } catch (\Exception $e) {
                    $this->command->error("   ❌ Error en {$seeder['class']}: " . $e->getMessage());
                    $errors[] = [
                        'seeder' => $seeder['class'],
                        'name' => $seeder['name'],
                        'error' => $e->getMessage()
                    ];
                    
                    if ($seeder['required']) {
                        $this->command->error("   🚨 Error crítico en seeder requerido. Deteniendo ejecución.");
                        break;
                    } else {
                        $continue = $this->command->confirm("   ¿Desea continuar con los siguientes seeders? (y/n)");
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
            }
        }

        // Reporte final
        $this->printFinalReport($executed, $skipped, $errors);
    }
    
    private function printFinalReport($executed, $skipped, $errors)
    {
        $this->command->info('');
        $this->command->info('🎯 === REPORTE FINAL DE CONFIGURACIÓN ===');
        $this->command->info('');
        
        if (!empty($executed)) {
            $this->command->info('✅ SEEDERS EJECUTADOS:');
            foreach ($executed as $item) {
                $this->command->info("   - {$item['name']} - {$item['duration']}s");
            }
        }
        
        if (!empty($skipped)) {
            $this->command->info('');
            $this->command->info('⏭️  SEEDERS OMITIDOS:');
            foreach ($skipped as $item) {
                $this->command->info("   - {$item['name']}");
            }
        }
        
        if (!empty($errors)) {
            $this->command->info('');
            $this->command->error('❌ ERRORES ENCONTRADOS:');
            foreach ($errors as $item) {
                $this->command->error("   - {$item['name']}: {$item['error']}");
            }
        }
        
        // Resumen del estado actual
        $this->command->info('');
        $this->command->info('📊 ESTADO ACTUAL DEL SISTEMA:');
        $this->printSystemStatus();
        
        $this->command->info('');
        $this->command->info('🎉 === FIN DE LA CONFIGURACIÓN INICIAL ===');
    }
    
    private function printSystemStatus()
    {
        try {
            // Categorías
            $categories = \App\Models\CampusCategory::count();
            $this->command->info("   📂 Categorías: {$categories}");
            
            // Temporadas
            $seasons = \App\Models\CampusSeason::count();
            $this->command->info("   📅 Temporadas: {$seasons}");
            
            // Cursos
            $courses = \App\Models\CampusCourse::count();
            $this->command->info("   📚 Cursos: {$courses}");
            
            // Teachers
            $teachers = \App\Models\CampusTeacher::count();
            $teacherUsers = \App\Models\User::role('teacher')->count();
            $this->command->info("   👨‍🏫 Teachers: {$teachers} (usuarios: {$teacherUsers})");
            
            // Espacios
            $spaces = \App\Models\CampusSpace::count();
            $this->command->info("   🏫 Espacios: {$spaces}");
            
            // Franjas horarias
            $timeSlots = \App\Models\CampusTimeSlot::count();
            $this->command->info("   🕐 Franjas horarias: {$timeSlots}");
            
        } catch (\Exception $e) {
            $this->command->warn("   ⚠️  No se pudo obtener el estado del sistema: " . $e->getMessage());
        }
    }
}
