<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanPushLogs extends Command
{
    protected $signature = 'logs:clean-push';
    protected $description = 'Eliminar logs de push que tengan más de 7 días';

    public function handle(): void
    {
        $path = storage_path('logs/push.log');

        if (File::exists($path)) {
            $lastModified = File::lastModified($path);
            $expired = now()->subDays(7)->timestamp;

            if ($lastModified < $expired) {
                File::delete($path);
                $this->info('🗑️ Log de push eliminado por antigüedad');
            } else {
                $this->info('📁 Log de push aún dentro del rango permitido.');
            }
        } else {
            $this->info('📂 No se encontró archivo de log de push.');
        }
    }
}
