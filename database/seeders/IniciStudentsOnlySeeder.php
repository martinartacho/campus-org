<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\CampusStudent;
use Spatie\Permission\Models\Role;

class IniciStudentsOnlySeeder extends Seeder
{
    /**
     * Run database seeds.
     */
    public function run(): void
    {
        $this->command->info('👨‍🎓 === CREACIÓ NOMÉS D\'ALUMNES (SIN MATRÍCULES) ===');
        
        $registrationPath = storage_path('app/imports/ordres_wp_registration.csv');
        
        if (!file_exists($registrationPath)) {
            $this->command->error('❌ Fitxer de registres no trobat: ' . $registrationPath);
            return;
        }
        
        // Llegir fitxer de registres
        $content = file_get_contents($registrationPath);
        $lines = explode(PHP_EOL, trim($content));
        array_shift($lines); // Saltar headers
        
        $this->command->info('📊 Processant ' . count($lines) . ' registres per crear estudiants...');
        
        $processed = 0;
        $studentsCreated = 0;
        $studentsUpdated = 0;
        $errors = [];
        $studentsMap = [];
        
        // Processar cada registre
        foreach ($lines as $lineIndex => $line) {
            $processed++;
            
            try {
                $parts = str_getcsv($line);
                
                if (count($parts) < 6) {
                    continue; // Saltar línies invàlides
                }
                
                $firstName = trim($parts[0]);
                $lastName = trim($parts[1]);
                $email = trim($parts[2]);
                $phone = trim($parts[3]);
                
                // Validar email - CONDICIÓ CLAU
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = [
                        'row' => $processed,
                        'error' => 'Email invàlid: ' . $email
                    ];
                    continue;
                }
                
                // Si ja hem processat aquest email, saltar (un estudiant pot tenir múltiples registres)
                if (isset($studentsMap[$email])) {
                    continue;
                }
                
                // Crear o actualitzar usuari
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $firstName . ' ' . $lastName,
                        'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD', 'Campus2026!')),
                        'email_verified_at' => now(),
                        'status' => 'active',
                    ]
                );
                
                // Assignar rol d'estudiant
                if (!$user->hasRole('student')) {
                    $studentRole = Role::where('name', 'student')->first();
                    if ($studentRole) {
                        $user->assignRole('student');
                    }
                }
                
                // Crear o actualitzar perfil d'estudiant
                $studentCode = 'STU' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
                $student = CampusStudent::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'student_code' => $studentCode,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'phone' => $phone,
                        'birth_date' => null,
                        'address' => null,
                        'email' => $email,
                        'emergency_contact' => null,
                        'emergency_phone' => null,
                        'status' => 'active',
                        'enrollment_date' => now(),
                    ]
                );
                
                $studentsMap[$email] = [
                    'user' => $user,
                    'student' => $student,
                    'was_created' => $user->wasRecentlyCreated,
                ];
                
                if ($user->wasRecentlyCreated) {
                    $studentsCreated++;
                } else {
                    $studentsUpdated++;
                }
                
                // Progress bar
                if ($processed % 100 === 0) {
                    $this->command->info('🔄 Processats ' . $processed . '/' . count($lines) . ' registres...');
                }
                
            } catch (\Exception $e) {
                $errors[] = [
                    'row' => $processed,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        // Report final
        $this->printFinalReport($studentsMap, $studentsCreated, $studentsUpdated, $errors);
    }
    
    /**
     * Report final
     */
    private function printFinalReport($studentsMap, $studentsCreated, $studentsUpdated, $errors)
    {
        $this->command->info('');
        $this->command->info('🎯 === REPORT FINAL - NOMÉS ESTUDIANTS ===');
        $this->command->info('');
        
        $this->command->info('👨‍🎓 ESTUDIANTS:');
        $this->command->info("   📝 Total processats: " . count($studentsMap));
        $this->command->info("   ✅ Nous creats: {$studentsCreated}");
        $this->command->info("   🔄 Actualitzats: {$studentsUpdated}");
        
        if (!empty($errors)) {
            $this->command->info('');
            $this->command->error('❌ Errors trobats: ' . count($errors));
            foreach (array_slice($errors, 0, 5) as $error) {
                $this->command->error("   Fila {$error['row']}: {$error['error']}");
            }
            if (count($errors) > 5) {
                $this->command->error("   ... i " . (count($errors) - 5) . " més");
            }
        }
        
        $this->command->info('');
        $this->command->info('🎉 === CREACIÓ D\'ESTUDIANTS COMPLETADA! ===');
        $this->command->info('👨‍🎓✨ Els alumnes estan al campus (sense matrícules)! ✨👨‍🎓');
    }
}
