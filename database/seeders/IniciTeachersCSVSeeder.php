<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\CampusTeacher;
use App\Models\User;
use Carbon\Carbon;

class IniciTeachersCSVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importación de Teachers desde CSV ===');
        
        $csvPath = storage_path('app/imports/campus_teacher.csv');
        
        if (!file_exists($csvPath)) {
            $this->command->error('❌ Archivo CSV no encontrado: ' . $csvPath);
            return;
        }
        
        // Leer CSV
        $csvData = $this->readCSV($csvPath);
        $headers = array_shift($csvData); // Primera fila son los headers
        
        $this->command->info('📊 Total registros en CSV: ' . count($csvData));
        
        $processed = 0;
        $errors = [];
        $created = 0;
        $updated = 0;
        $usersCreated = 0;
        
        foreach ($csvData as $rowIndex => $row) {
            $processed++;
            $rowData = array_combine($headers, $row);
            
            try {
                $result = $this->processTeacher($rowData, $processed);
                
                if ($result['created']) {
                    $created++;
                } elseif ($result['updated']) {
                    $updated++;
                }
                
                if ($result['userCreated']) {
                    $usersCreated++;
                }
                
                if (!empty($result['errors'])) {
                    $errors = array_merge($errors, $result['errors']);
                }
                
            } catch (\Exception $e) {
                $errors[] = "Fila {$processed}: Error general - " . $e->getMessage();
                $this->command->error("❌ Error en fila {$processed}: " . $e->getMessage());
            }
        }
        
        // Reporte final
        $this->printReport($processed, $created, $updated, $usersCreated, $errors);
    }
    
    private function readCSV($filePath)
    {
        $csv = [];
        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            throw new \Exception('No se puede abrir el archivo CSV');
        }
        
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $csv[] = $row;
        }
        
        fclose($handle);
        return $csv;
    }
    
    private function processTeacher($rowData, $rowNumber)
    {
        $errors = [];
        $created = false;
        $updated = false;
        $userCreated = false;
        
        // Validaciones y normalización
        $teacherCode = $this->validateString($rowData['teacher_code'], 'teacher_code', $errors, $rowNumber, true);
        $userId = $this->validateInteger($rowData['user_id'], 'user_id', $errors, $rowNumber);
        $email = $this->validateEmail($rowData['email'], 'email', $errors, $rowNumber, true);
        $firstName = $this->validateString($rowData['first_name'], 'first_name', $errors, $rowNumber, true);
        $lastName = $this->validateString($rowData['last_name'], 'last_name', $errors, $rowNumber, true);
        
        // Si hay errores críticos, no procesar
        if (!empty($errors)) {
            return ['created' => false, 'updated' => false, 'userCreated' => false, 'errors' => $errors];
        }
        
        // 1. Obtener o crear usuario del sistema basado en user_id del CSV
        $user = $this->getOrCreateUser($userId, $email, $firstName, $lastName, $rowNumber, $errors);
        
        if (!$user) {
            return ['created' => false, 'updated' => false, 'userCreated' => false, 'errors' => $errors];
        }
        
        if (!$user->wasRecentlyCreated) {
            // Usuario existente, no contar como creado
            $userCreated = false;
        } else {
            $userCreated = true;
            $this->command->info("👤 Usuario creado: {$user->email} (ID: {$user->id})");
        }
        
        // 2. Preparar datos del teacher
        $teacherData = [
            'user_id' => $user->id, // Usar el ID real del usuario
            'teacher_code' => $teacherCode,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dni' => $this->validateString($rowData['dni'], 'dni', $errors, $rowNumber),
            'email' => $email,
            'phone' => $this->validateString($rowData['phone'], 'phone', $errors, $rowNumber),
            'address' => $this->validateString($rowData['address'], 'address', $errors, $rowNumber),
            'postal_code' => $this->validateString($rowData['postal_code'], 'postal_code', $errors, $rowNumber),
            'city' => $this->validateString($rowData['city'], 'city', $errors, $rowNumber),
            'observacions' => $this->validateString($rowData['observacions'], 'observacions', $errors, $rowNumber),
            'iban' => $this->validateString($rowData['iban'], 'iban', $errors, $rowNumber),
            'bank_titular' => $this->validateString($rowData['bank_titular'], 'bank_titular', $errors, $rowNumber),
            'fiscal_id' => $this->validateString($rowData['fiscal_id'], 'fiscal_id', $errors, $rowNumber),
            'fiscal_situation' => $this->validateString($rowData['fiscal_situation'], 'fiscal_situation', $errors, $rowNumber),
            'needs_payment' => $this->validateBoolean($rowData['needs_payment'], 'needs_payment', $errors, $rowNumber, false),
            'invoice' => $this->validateBoolean($rowData['invoice'], 'invoice', $errors, $rowNumber, false),
            'degree' => $this->validateString($rowData['degree'], 'degree', $errors, $rowNumber),
            'specialization' => $this->validateString($rowData['specialization'], 'specialization', $errors, $rowNumber),
            'title' => $this->validateString($rowData['title'], 'title', $errors, $rowNumber),
            'areas' => $this->validateJSONToString($rowData['areas'], 'areas', $errors, $rowNumber),
            'status' => $this->validateStatus($rowData['status'], $errors, $rowNumber),
            'hiring_date' => $this->validateDate($rowData['hiring_date'], 'hiring_date', $errors, $rowNumber),
            'metadata' => $this->validateJSONToString($rowData['metadata'], 'metadata', $errors, $rowNumber),
        ];
        
        // 3. Crear o actualizar teacher
        $existingTeacher = CampusTeacher::where('teacher_code', $teacherCode)->first();
        
        if ($existingTeacher) {
            $existingTeacher->update($teacherData);
            $updated = true;
            $this->command->info("🔄 Teacher actualizado: {$teacherCode} (User ID: {$user->id})");
        } else {
            CampusTeacher::create($teacherData);
            $created = true;
            $this->command->info("✅ Teacher creado: {$teacherCode} (User ID: {$user->id})");
        }
        
        return ['created' => $created, 'updated' => $updated, 'userCreated' => $userCreated, 'errors' => $errors];
    }
    
    private function getOrCreateUser($userId, $email, $firstName, $lastName, $rowNumber, &$errors)
    {
        try {
            // Primero buscar por ID (prioridad según CSV)
            $user = User::find($userId);
            
            if ($user) {
                // Usuario encontrado por ID, verificar y actualizar si es necesario
                $this->command->info("🔍 Usuario encontrado por ID: {$userId} -> {$user->email}");
                
                // Asegurar que tenga rol teacher
                if (!$user->hasRole('teacher')) {
                    $user->assignRole('teacher');
                    $this->command->info("📋 Rol teacher asignado a usuario ID: {$userId}");
                }
                
                // Actualizar nombre si es necesario
                $newName = trim($firstName . ' ' . $lastName);
                if ($user->name !== $newName) {
                    $user->update(['name' => $newName]);
                    $this->command->info("✏️ Nombre actualizado para usuario ID: {$userId}");
                }
                
                return $user;
            }
            
            // Si no existe por ID, buscar por email
            $user = User::where('email', $email)->first();
            
            if ($user) {
                // Usuario encontrado por email, pero con ID diferente
                $this->command->warn("⚠️ Usuario encontrado por email pero ID no coincide: Email {$email} (CSV ID: {$userId}, BD ID: {$user->id})");
                
                // Asegurar rol teacher
                if (!$user->hasRole('teacher')) {
                    $user->assignRole('teacher');
                }
                
                // Actualizar nombre
                $newName = trim($firstName . ' ' . $lastName);
                if ($user->name !== $newName) {
                    $user->update(['name' => $newName]);
                }
                
                return $user;
            }
            
            // Crear nuevo usuario con el ID especificado del CSV
            $user = User::create([
                'id' => $userId, // Forzar ID del CSV
                'name' => trim($firstName . ' ' . $lastName),
                'email' => $email,
                'password' => Hash::make(env('SEEDER_DEFAULT_PASSWORD')), // Contraseña por defecto
                'email_verified_at' => Carbon::now(),
                'locale' => 'ca',
            ]);
            
            // Asignar rol de teacher
            $user->assignRole('teacher');
            
            $this->command->info("👤 Nuevo usuario creado con ID: {$userId}, Email: {$email}");
            
            return $user;
            
        } catch (\Exception $e) {
            $errors[] = "Fila {$rowNumber}: Error al crear/obtener usuario (ID: {$userId}, Email: {$email}): " . $e->getMessage();
            return null;
        }
    }
    
    private function validateInteger($value, $field, &$errors, $rowNumber, $default = 0)
    {
        if ($value === null || $value === '' || $value === '\N') {
            return $default;
        }
        
        $intValue = (int) $value;
        if ($intValue < 0) {
            $errors[] = "Fila {$rowNumber}: {$field} debe ser positivo o cero";
            return $default;
        }
        
        return $intValue;
    }
    
    private function validateString($value, $field, &$errors, $rowNumber, $required = false)
    {
        $value = trim($value ?? '');
        
        if ($required && empty($value)) {
            $errors[] = "Fila {$rowNumber}: {$field} es requerido";
            return '';
        }
        
        return $value;
    }
    
    private function validateEmail($value, $field, &$errors, $rowNumber, $required = false)
    {
        $value = trim($value ?? '');
        
        if ($required && empty($value)) {
            $errors[] = "Fila {$rowNumber}: {$field} es requerido";
            return '';
        }
        
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Fila {$rowNumber}: {$field} '{$value}' no es un email válido";
            return '';
        }
        
        return $value;
    }
    
    private function validateBoolean($value, $field, &$errors, $rowNumber, $default = false)
    {
        if ($value === null || $value === '' || $value === '\N') {
            return $default;
        }
        
        return (bool) $value;
    }
    
    private function validateStatus($value, &$errors, $rowNumber)
    {
        $validStatuses = ['active', 'inactive', 'pending', 'suspended'];
        $status = strtolower(trim($value ?? ''));
        
        if (empty($status)) {
            return 'active'; // Default
        }
        
        if (!in_array($status, $validStatuses)) {
            $errors[] = "Fila {$rowNumber}: status '{$value}' no es válido. Valores válidos: " . implode(', ', $validStatuses);
            return 'active';
        }
        
        return $status;
    }
    
    private function validateDate($value, $field, &$errors, $rowNumber)
    {
        if (empty($value) || $value === '\N') {
            return null;
        }
        
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            $errors[] = "Fila {$rowNumber}: {$field} fecha inválida: {$value}";
            return null;
        }
    }
    
    private function validateJSONToString($value, $field, &$errors, $rowNumber)
    {
        if (empty($value) || $value === '\N') {
            return null;
        }
        
        try {
            $json = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = "Fila {$rowNumber}: {$field} JSON inválido: " . json_last_error_msg();
                return null;
            }
            return json_encode($json); // Convertir a string
        } catch (\Exception $e) {
            $errors[] = "Fila {$rowNumber}: {$field} error al procesar JSON: " . $e->getMessage();
            return null;
        }
    }
    
    private function printReport($processed, $created, $updated, $usersCreated, $errors)
    {
        $this->command->info('');
        $this->command->info('=== REPORTE FINAL ===');
        $this->command->info("📊 Registros procesados: {$processed}");
        $this->command->info("✅ Teachers creados: {$created}");
        $this->command->info("🔄 Teachers actualizados: {$updated}");
        $this->command->info("👤 Usuarios creados: {$usersCreated}");
        
        if (!empty($errors)) {
            $this->command->warn('');
            $this->command->warn('⚠️  ERRORES ENCONTRADOS (' . count($errors) . '):');
            foreach ($errors as $error) {
                $this->command->warn("   - {$error}");
            }
        } else {
            $this->command->info('');
            $this->command->info('🎉 No se encontraron errores');
        }
        
        $this->command->info('');
        $this->command->info('=== FIN DEL PROCESO ===');
    }
}
