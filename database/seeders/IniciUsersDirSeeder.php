<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class IniciUsersDirSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('=== Creación de Usuarios Iniciales desde CSV ===');
        
        $csvPath = storage_path('app/imports/dir_upg_seeder.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error('❌ Archivo CSV no encontrado: ' . $csvPath);
            return;
        }
        
        $this->command->info('📁 Leyendo archivo: ' . basename($csvPath));
        
        // Leer el archivo CSV
        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command->error('❌ No se pudo abrir el archivo CSV');
            return;
        }
        
        // Leer cabeceras
        $headers = fgetcsv($handle, 1000, ',');
        $createdCount = 0;
        $updatedCount = 0;
        
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            try {
                // Mapear columnas según cabeceras
                $data = [];
                foreach ($headers as $index => $header) {
                    $cleanHeader = trim($header, '" ');
                    $data[$cleanHeader] = trim($row[$index] ?? '', '"');
                }
                
                // Validar datos mínimos
                if (empty($data['name']) || empty($data['email'])) {
                    $this->command->warn('⚠️ Fila incompleta, saltando: ' . json_encode($data));
                    continue;
                }
                
                $name = $data['name'];
                $role = $data['rol'] ?? 'user';
                
                // Buscar si el usuario ya existe
                $existingUser = User::where('email', $data['email'])->first();
                
                if ($existingUser) {
                    // Actualizar usuario existente
                    $existingUser->update([
                        'name' => $name,
                        'email' => $data['email'],
                        'password' => Hash::make($data['password'] ?? env('SEEDER_USER_PASSWORD')),
                        'email_verified_at' => Carbon::now(),
                        'locale' => 'ca',
                    ]);
                    
                    $existingUser->syncRoles([$role]);
                    $this->command->info('✅ Actualizado: ' . $data['email'] . ' (rol: ' . $role . ')');
                    $updatedCount++;
                } else {
                    // Crear nuevo usuario
                    $user = User::firstOrCreate([
                        'name' => $name,
                        'email' => $data['email'],
                        'password' => Hash::make($data['password'] ?? env('SEEDER_USER_PASSWORD')),
                        'email_verified_at' => Carbon::now(),
                        'locale' => 'ca',
                    ]);
                    
                    $user->assignRole($role);
                    $this->command->info('✅ Creado: ' . $data['email'] . ' (rol: ' . $role . ')');
                    $createdCount++;
                }
                
            } catch (\Exception $e) {
                $this->command->error('❌ Error procesando fila: ' . $e->getMessage());
            }
        }
        
        fclose($handle);
        
        $this->command->info('📈 Resumen:');
        $this->command->info('   📝 Usuarios creados: ' . $createdCount);
        $this->command->info('   🔄 Usuarios actualizados: ' . $updatedCount);
        $this->command->info('   ✅ Total procesados: ' . ($createdCount + $updatedCount));
    }
}
