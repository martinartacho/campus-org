<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\User;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Probando sistema de notificaciones ===');
        
        // Mostrar resumen
        $this->info("\nResumen de notificaciones:");
        $this->table(
            ['ID', 'Título', 'Remitente', 'Tipo', 'Publicada'],
            Notification::all()->map(function($n) {
                return [
                    $n->id,
                    $n->title,
                    $n->sender->name,
                    $n->recipient_type,
                    $n->is_published ? 'Sí' : 'No'
                ];
            })
        );

        // Probar acceso
        $this->info("\nProbando acceso:");
        $users = User::with('roles')->get();
        
        foreach ($users as $user) {
            $this->info("\nUsuario: {$user->name} (Roles: ".$user->getRoleNames()->implode(', ').")");
            
            $notifications = Notification::accessibleBy($user)->get();
            
            if ($notifications->isEmpty()) {
                $this->line('  No tiene acceso a ninguna notificación');
                continue;
            }
            
            foreach ($notifications as $n) {
                $this->line("  - Puede ver notificación #{$n->id}: {$n->title}");
            }
        }
    }
}

