<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Output\ConsoleOutput;

class SetupPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:permissions {--fix : Fix permission issues automatically}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify and fix Laravel file permissions';

    /**
     * Array of permission requirements
     *
     * @var array
     */
    protected $permissions = [
        'storage' => [
            'path' => 'storage',
            'permissions' => 0775,
            'recursive' => true,
            'type' => 'directory'
        ],
        'bootstrap/cache' => [
            'path' => 'bootstrap/cache',
            'permissions' => 0775,
            'recursive' => true,
            'type' => 'directory'
        ],
        'public' => [
            'path' => 'public',
            'permissions' => 0755,
            'recursive' => true,
            'type' => 'directory'
        ],
        '.env' => [
            'path' => '.env',
            'permissions' => 0644,
            'recursive' => false,
            'type' => 'file'
        ],
        'artisan' => [
            'path' => 'artisan',
            'permissions' => 0755,
            'recursive' => false,
            'type' => 'file'
        ]
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando permisos de Laravel...');
        $this->line('');

        $issues = [];
        $fixed = [];

        foreach ($this->permissions as $name => $config) {
            $path = base_path($config['path']);
            
            if (!file_exists($path)) {
                $this->warn("⚠️  {$config['path']} no existe");
                continue;
            }

            $currentPermissions = $this->getCurrentPermissions($path);
            $expectedPermissions = $config['permissions'];
            
            $status = $this->checkPermissions($currentPermissions, $expectedPermissions);
            
            if ($status['correct']) {
                $this->info("✅ {$name}: {$currentPermissions} (correcto)");
            } else {
                $issues[$name] = [
                    'path' => $path,
                    'current' => $currentPermissions,
                    'expected' => $expectedPermissions,
                    'config' => $config
                ];
                
                $this->error("❌ {$name}: {$currentPermissions} (debe ser {$expectedPermissions})");
                
                if ($this->option('fix')) {
                    if ($this->fixPermissions($path, $config)) {
                        $fixed[] = $name;
                        $newPermissions = $this->getCurrentPermissions($path);
                        $this->info("🔧 {$name}: Corregido a {$newPermissions}");
                    } else {
                        $this->error("❌ {$name}: No se pudo corregir");
                    }
                }
            }
        }

        $this->line('');

        // Resumen
        if (empty($issues)) {
            $this->info('🎉 Todos los permisos son correctos');
        } else {
            $totalIssues = count($issues);
            $totalFixed = count($fixed);
            
            if ($this->option('fix')) {
                if ($totalFixed === $totalIssues) {
                    $this->info("🎉 Se corrigieron {$totalFixed} problemas de permisos");
                } else {
                    $this->warn("⚠️  Se corrigieron {$totalFixed} de {$totalIssues} problemas");
                }
            } else {
                $this->warn("⚠️  Se encontraron {$totalIssues} problemas de permisos");
                $this->line('');
                $this->info('💡 Ejecuta "php artisan setup:permissions --fix" para corregir automáticamente');
            }
        }

        // Verificación adicional para directorios críticos
        $this->checkCriticalDirectories();

        return empty($issues) ? 0 : 1;
    }

    /**
     * Get current permissions in octal format
     */
    protected function getCurrentPermissions($path)
    {
        $permissions = fileperms($path);
        return substr(sprintf('%o', $permissions), -4);
    }

    /**
     * Check if permissions are correct
     */
    protected function checkPermissions($current, $expected)
    {
        return [
            'correct' => octdec($current) === $expected,
            'current' => $current,
            'expected' => sprintf('%04o', $expected)
        ];
    }

    /**
     * Fix permissions for a path
     */
    protected function fixPermissions($path, $config)
    {
        try {
            if ($config['recursive']) {
                return chmod($path, $config['permissions']) && 
                       $this->chmodRecursive($path, $config['permissions'], $config['type']);
            } else {
                return chmod($path, $config['permissions']);
            }
        } catch (\Exception $e) {
            $this->error("Error al corregir permisos para {$path}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Recursively change permissions
     */
    protected function chmodRecursive($path, $permissions, $type)
    {
        if ($type === 'directory') {
            $items = glob($path . '/*');
            foreach ($items as $item) {
                if (is_dir($item)) {
                    if (!chmod($item, $permissions) || !$this->chmodRecursive($item, $permissions, $type)) {
                        return false;
                    }
                } else {
                    if (!chmod($item, $permissions)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * Check critical directories for writability
     */
    protected function checkCriticalDirectories()
    {
        $this->line('');
        $this->info('🔍 Verificando directorios críticos...');
        
        $criticalPaths = [
            'storage/logs' => 'Escritura de logs',
            'storage/framework/cache' => 'Cache',
            'storage/framework/sessions' => 'Sesiones',
            'storage/framework/views' => 'Vistas compiladas',
            'bootstrap/cache' => 'Cache de bootstrap'
        ];

        foreach ($criticalPaths as $path => $description) {
            $fullPath = base_path($path);
            
            if (!is_dir($fullPath)) {
                $this->warn("⚠️  {$path}: Directorio no existe");
                continue;
            }

            if (is_writable($fullPath)) {
                $this->info("✅ {$path}: {$description} - writable");
            } else {
                $this->error("❌ {$path}: {$description} - NOT writable");
                
                if ($this->option('fix')) {
                    if (chmod($fullPath, 0775)) {
                        $this->info("🔧 {$path}: Corregido");
                    }
                }
            }
        }
    }
}
