<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class ModuleCreateCommand extends Command
{
    protected $signature = 'module:create {name}';
    protected $description = 'Crea estructura modular completa';
    
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }


   public function handle()
    {
        $moduleName = $this->argument('name');
        $studlyName = Str::studly($moduleName);
        $modulePath = "modules/$studlyName";

        // Verificar si el módulo ya existe
        if ($this->files->exists($modulePath)) {
            $this->warn("⚠️  El módulo <comment>$studlyName</comment> ya existe!");
            
            if (!$this->confirm('¿Desea reemplazarlo por uno nuevo? (Se eliminarán todos los archivos existentes)', false)) {
                $this->line('Operación cancelada');
                return;
            }
            
            $this->files->deleteDirectory($modulePath);
            $this->line('Módulo existente eliminado...');
        }
        // ... (código existente para crear directorios y archivos)

        $this->info("✅ Módulo <comment>$studlyName</comment> creado exitosamente!");
        $this->line("\n📂 Estructura generada:");
        
        $this->displayDirectoryTree($studlyName);
        
        $this->line("\n🔧 <fg=white;bg=blue> PASO FINAL REQUERIDO </>");
        $this->line("Registra el Service Provider en <comment>config/app.php</comment>:");

        $this->components->info("\n✅ Paso final requerido:");
        $this->components->warn("Agrega el Service Provider en `config/app.php`:\n");
        $this->line(<<<'EOT'
        'providers' => [
            // ...
            Modules\Example\Providers\ExampleServiceProvider::class,
        ],
        EOT);
    }

    protected function displayDirectoryTree($module)
    {
        $migrationFile = collect($this->files->glob("modules/$module/Database/Migrations/*.php"))
            ->first();
        
        $tree = [
            "modules/$module/" => [
                'Http/Controllers/' => ["{$module}Controller.php"],
                'Models/' => ["$module.php"],
                'Providers/' => ["{$module}ServiceProvider.php"],
                'Database/Migrations/' => [basename($migrationFile)],
                'Routes/' => ['web.php'],
                'Views/' => ['index.blade.php'],
            ],
            "lang/en/" => ["$module.php"]
        ];

        $this->renderTree($tree);
    }

    protected function renderTree($tree, $prefix = '')
    {
        foreach ($tree as $folder => $contents) {
            $this->line("<fg=blue>$folder</>");
            
            if (is_array($contents)) {
                $items = array_keys($contents);
                $lastItem = end($items);
                
                foreach ($contents as $subfolder => $files) {
                    $connector = $subfolder === $lastItem ? '└──' : '├──';
                    $this->line("$prefix$connector <fg=blue>$subfolder</>");
                    
                    if (is_array($files) && !empty($files)) {
                        $filePrefix = $subfolder === $lastItem ? '    ' : '│   ';
                        $lastFile = end($files);
                        
                        foreach ($files as $file) {
                            $fileConnector = $file === $lastFile ? '└──' : '├──';
                            $this->line("$prefix$filePrefix$fileConnector <fg=green>$file</>");
                        }
                    }
                }
            }
        }
    }

    protected function createDirectories($module)
    {
        $paths = [
            "modules/$module/Http/Controllers",
            "modules/$module/Models",
            "modules/$module/Providers",
            "modules/$module/Database/Migrations",
            "modules/$module/Routes",
            "modules/$module/Views",
            "lang/en",
        ];

        foreach ($paths as $path) {
            $this->files->ensureDirectoryExists($path, 0755, true);
        }
    }

    // Métodos para crear cada archivo
    protected function createMigration($module, $table)
    {
        $existingMigrations = collect($this->files->glob("modules/$module/Database/Migrations/*_create_{$table}_table.php"))
            ->map(fn($path) => basename($path))
            ->toArray();

        if (!empty($existingMigrations)) {
            $this->warn('¡Migración existente detectada!');
            $this->line(' Migraciones encontradas:');
            foreach ($existingMigrations as $migration) {
                $this->line("  - <fg=yellow>$migration</>");
            }
        }

        $timestamp = now()->format('Y_m_d_His');
        $migrationName = "{$timestamp}_create_{$table}_table.php";
        $stub = str_replace(
            ['{{ table }}'],
            [$table],
            $this->getStub('migration')
        );
        
        $this->files->put(
            "modules/$module/Database/Migrations/{$timestamp}_create_{$table}_table.php",
            $stub
        );
    }

    protected function createModel($module)
    {
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            ["Modules\\$module\Models", $module],
            $this->getStub('model')
        );
        
        $this->files->put("modules/$module/Models/$module.php", $stub);
    }

    protected function createController($module)
    {
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ view }}'],
            ["Modules\\$module\Http\Controllers", "{$module}Controller", Str::kebab($module)],
            $this->getStub('controller')
        );
        
        $this->files->put("modules/$module/Http/Controllers/{$module}Controller.php", $stub);
    }

    protected function createServiceProvider($module)
    {
        $stub = str_replace(
            ['{{ namespace }}', '{{ module }}', '{{ view }}'],
            ["Modules\\$module\Providers", $module, Str::kebab($module)],
            $this->getStub('provider')
        );
        
        $this->files->put("modules/$module/Providers/{$module}ServiceProvider.php", $stub);
    }

    protected function createRoutes($module, $route)
    {
        $stub = str_replace(
            ['{{ namespace }}', '{{ route }}', '{{ class }}'],
            ["Modules\\$module\Http\Controllers", $route, "{$module}Controller"],
            $this->getStub('routes')
        );
        
        $this->files->put("modules/$module/Routes/web.php", $stub);
    }

    protected function createViews($module)
    {
        $view = Str::kebab($module);
        $stub = str_replace(
            '{{ module }}',
            $module,
            $this->getStub('view-index')
        );
        
        $this->files->put("modules/$module/Views/index.blade.php", $stub);
    }

    protected function createLangFile($module)
    {
        $this->files->put("lang/en/$module.php", "<?php\n\nreturn [\n    // Traducciones\n];");
    }

    protected function getStub($type)
    {
        return $this->files->get(__DIR__."/stubs/$type.stub");
    }
}