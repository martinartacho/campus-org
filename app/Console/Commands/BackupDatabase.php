<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--environment=dev : Entorno (dev/prod)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realizar backup automático de la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $environment = $this->option('environment');
        
        // Convertir 'local' a 'dev' para compatibilidad
        if ($environment === 'local') {
            $environment = 'dev';
        }
        
        $this->info("🔄 Iniciando backup de base de datos - Entorno: {$environment}");
        
        try {
            // Ejecutar script de backup real
            $scriptPath = base_path('scripts/backup_database.sh');
            $env = $environment === 'prod' ? 'production' : 'development';
            
            // Configurar variables de entorno según el ambiente
            $dbConfig = $environment === 'prod' ? [
                'DB_DATABASE' => config('database.connections.mysql.database', 'campus_upg'),
                'LOG_PREFIX' => 'PROD'
            ] : [
                'DB_DATABASE' => config('database.connections.mysql.database', 'campus_dev'),
                'LOG_PREFIX' => 'DEV'
            ];
            
            // Ejecutar script bash real con variables de entorno
            $output = [];
            $returnCode = 0;
            
            // Preparar entorno para el script
            $envVars = [
                'DB_DATABASE' => $dbConfig['DB_DATABASE'],
                'DB_USER' => config('database.connections.mysql.username', 'artacho'),
                'DB_PASSWORD' => config('database.connections.mysql.password', 'M4rt1n.Ha'),
            ];
            
            // Construir comando con variables de entorno
            $envString = '';
            foreach ($envVars as $key => $value) {
                $envString .= "{$key}='{$value}' ";
            }
            
            $command = "env -i " . trim($envString) . " {$scriptPath}";
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                // Buscar el backup más reciente
                $backupFiles = glob('/var/www/backups/campus_dev_*.sql.gz');
                if (!empty($backupFiles)) {
                    $backupFile = end($backupFiles);
                    $this->info("✅ Backup completado: {$backupFile}");
                    $this->info("📊 Tamaño: " . $this->getBackupSize($backupFile));
                    
                    // Crear registro en base de datos
                    $this->createBackupRecord($environment, $backupFile);
                    
                    // Enviar notificación a admin
                    $this->notifyAdmin($environment, $backupFile);
                    
                    Log::info("Backup automático completado", [
                        'environment' => $environment,
                        'backup_file' => $backupFile,
                        'timestamp' => now()->toISOString()
                    ]);
                    
                    $this->info("🎉 Backup completado exitosamente");
                    
                    return Command::SUCCESS;
                } else {
                    throw new \Exception("No se encontró archivo de backup generado");
                }
            } else {
                throw new \Exception("Error ejecutando script de backup. Código: {$returnCode}");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error en backup: " . $e->getMessage());
            
            Log::error("Error en backup automático", [
                'environment' => $environment,
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ]);
            
            // Notificar error a admin
            $this->notifyAdminError($environment, $e->getMessage());
            
            return Command::FAILURE;
        }
    }
    
    /**
     * Obtener tamaño del backup
     */
    private function getBackupSize($filePath)
    {
        if (file_exists($filePath)) {
            $bytes = filesize($filePath);
            return $this->formatBytes($bytes);
        }
        return 'N/A';
    }
    
    /**
     * Formatear bytes a formato legible
     */
    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Crear registro del backup en la base de datos
     */
    private function createBackupRecord($environment, $backupFile)
    {
        // Crear tabla de backups si no existe
        DB::statement("
            CREATE TABLE IF NOT EXISTS backup_records (
                id BIGINT PRIMARY KEY AUTO_INCREMENT,
                environment VARCHAR(10) NOT NULL,
                filename VARCHAR(255) NOT NULL,
                file_size VARCHAR(20),
                status ENUM('success', 'error') DEFAULT 'success',
                error_message TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        // Insertar registro
        DB::table('backup_records')->insert([
            'environment' => $environment,
            'filename' => basename($backupFile),
            'file_size' => $this->getBackupSize($backupFile),
            'status' => 'success',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * Notificar al administrador
     */
    private function notifyAdmin($environment, $backupFile)
    {
        try {
            // Crear notificación en el sistema
            $adminUsers = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', ['super-admin', 'admin'])
                ->select('users.id', 'users.name', 'users.email')
                ->get();
            
            foreach ($adminUsers as $admin) {
                // Crear notificación en la base de datos
                DB::table('notifications')->insert([
                    'title' => '🔄 Backup Automático Completado',
                    'content' => "Backup de base de datos completado exitosamente en entorno {$environment}.",
                    'type' => 'info',
                    'sender_id' => 1,
                    'recipient_type' => 'specific',
                    'recipient_ids' => json_encode([$admin->id]),
                    'is_published' => 1,
                    'published_at' => now(),
                    'email_sent' => 0,
                    'web_sent' => 1,
                    'push_sent' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Asignar notificación al usuario
                $notificationId = DB::getPdo()->lastInsertId();
                DB::table('notification_user')->insert([
                    'notification_id' => $notificationId,
                    'user_id' => $admin->id,
                    'email_sent' => false,
                    'web_sent' => true,
                    'push_sent' => false,
                    'read' => false,
                    'created_at' => now()
                ]);
            }
            
            $this->info("📧 Notificaciones enviadas a " . $adminUsers->count() . " administradores");
            
        } catch (\Exception $e) {
            $this->warn("⚠️ No se pudieron enviar notificaciones: " . $e->getMessage());
        }
    }
    
    /**
     * Notificar error al administrador
     */
    private function notifyAdminError($environment, $errorMessage)
    {
        try {
            $adminUsers = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', ['super-admin', 'admin'])
                ->select('users.id', 'users.name', 'users.email')
                ->get();
            
            foreach ($adminUsers as $admin) {
                // Crear notificación de error
                DB::table('notifications')->insert([
                    'title' => '❌ Error en Backup Automático',
                    'content' => "Error en backup de base de datos en entorno {$environment}: {$errorMessage}",
                    'type' => 'error',
                    'sender_id' => 1,
                    'recipient_type' => 'specific',
                    'recipient_ids' => json_encode([$admin->id]),
                    'is_published' => 1,
                    'published_at' => now(),
                    'email_sent' => 0,
                    'web_sent' => 1,
                    'push_sent' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // Asignar notificación al usuario
                $notificationId = DB::getPdo()->lastInsertId();
                DB::table('notification_user')->insert([
                    'notification_id' => $notificationId,
                    'user_id' => $admin->id,
                    'email_sent' => false,
                    'web_sent' => true,
                    'push_sent' => false,
                    'read' => false,
                    'created_at' => now()
                ]);
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error crítico: No se pudo notificar a administradores: " . $e->getMessage());
        }
    }
}
