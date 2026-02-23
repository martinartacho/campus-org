<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CampusUsersCSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importación de Usuarios desde CSV ===');
        
        // Leer archivo CSV
        $csvPath = storage_path('app/imports/campus_users.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error("❌ Archivo no encontrado: $csvPath");
            return;
        }
        
        $this->command->info("📂 Leyendo archivo: $csvPath");
        
        $csvData = $this->parseCSV($csvPath);
        $totalRows = count($csvData);
        
        $this->command->info("📊 Total de filas en CSV: $totalRows");
        
        $importedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        
        foreach ($csvData as $index => $row) {
            try {
                // Saltar cabecera si existe
                if ($index === 0 && $row[0] === 'id') {
                    $this->command->info("📋 Cabecera detectada, saltando fila 0");
                    continue;
                }
                
                $user = $this->parseUserRow($row);
                
                if (!$user) {
                    $skippedCount++;
                    continue;
                }
                
                // Importar usuario
                User::updateOrInsert(
                    ['id' => $user['id']],
                    $user
                );
                
                $importedCount++;
                
                // Mostrar progreso cada 20 usuarios
                if ($importedCount % 20 === 0) {
                    $this->command->info("✅ Progreso: $importedCount usuarios importados...");
                }
                
            } catch (\Exception $e) {
                $errorCount++;
                $this->command->error("❌ Error en fila $index: " . $e->getMessage());
            }
        }
        
        $this->command->info("\n=== RESUMEN DE IMPORTACIÓN ===");
        $this->command->info("✅ Usuarios importados: $importedCount");
        $this->command->info("⚠️ Usuarios omitidos: $skippedCount");
        $this->command->info("❌ Errores: $errorCount");
        
        // Verificar importación
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        
        $this->command->info("📊 Total usuarios en base de datos: $totalUsers");
        $this->command->info("📊 Usuarios activos: $activeUsers");
        
        // Mostrar distribución por rol
        $this->showUsersByRole();
    }
    
    /**
     * Parsear archivo CSV
     */
    private function parseCSV($filePath)
    {
        $csvData = [];
        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            throw new \Exception("No se puede abrir el archivo: $filePath");
        }
        
        while (($row = fgetcsv($handle, 0, ';', '"')) !== false) {
            $csvData[] = $row;
        }
        
        fclose($handle);
        return $csvData;
    }
    
    /**
     * Parsear fila de usuario
     */
    private function parseUserRow($row)
    {
        if (count($row) < 10) {
            return null;
        }
        
        // Mapear campos del CSV (ajustado según estructura real)
        $user = [
            'id' => $this->parseValue($row[0]),
            'name' => $this->parseValue($row[1]),
            'email' => $this->parseValue($row[2]),
            'email_verified_at' => $this->parseValue($row[3]),
            'password' => $this->parsePassword($row[4]),
            'status' => $this->parseValue($row[5]) ?? 'active',
            'remember_token' => $this->parseValue($row[6]),
            'locale' => $this->parseValue($row[7]) ?? 'ca',
            'created_at' => $this->parseValue($row[8]) ?? now(),
            'updated_at' => $this->parseValue($row[9]) ?? now(),
        ];
        
        return $user;
    }
    
    /**
     * Parsear valor (limpiar comillas y espacios)
     */
    private function parseValue($value)
    {
        if ($value === null || $value === '\N' || $value === '') {
            return null;
        }
        
        return trim($value, '"');
    }
    
    /**
     * Parsear contraseña (hashear si es necesario)
     */
    private function parsePassword($password)
    {
        $parsed = $this->parseValue($password);
        
        if ($parsed === null) {
            return Hash::make('Campus2026!'); // Contraseña por defecto
        }
        
        // Si ya está hasheada, devolverla tal cual
        if (strlen($parsed) > 50 && strpos($parsed, '$') === 0) {
            return $parsed;
        }
        
        // Si no está hasheada, hashearla
        return Hash::make($parsed);
    }
    
    /**
     * Mostrar distribución de usuarios por rol
     */
    private function showUsersByRole()
    {
        $this->command->info("\n📊 Distribución por Rol:");
        
        // Contar usuarios con roles asignados
        $roleCounts = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('count(*) as count'))
            ->groupBy('roles.name')
            ->get();
            
        foreach ($roleCounts as $role) {
            $this->command->info("   👤 {$role->name}: {$role->count} usuarios");
        }
        
        // Usuarios sin rol
        $usersWithoutRole = DB::table('users')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->whereNull('model_has_roles.role_id')
            ->count();
            
        if ($usersWithoutRole > 0) {
            $this->command->info("   ⚠️ Sin rol: $usersWithoutRole usuarios");
        }
    }
}
