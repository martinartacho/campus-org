<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScheduleEmailNotifications extends Command
{
    protected $signature = 'notifications:schedule-email {--minutes=5 : Minutes to wait before sending}';
    protected $description = 'Programa el envío automático de emails para notificaciones publicadas';

    public function handle(): int
    {
        $minutes = $this->option('minutes');
        
        $this->info("🔍 Buscando notificaciones publicadas para programar envío de emails...");
        
        $notifications = Notification::where('is_published', true)
            ->where('email_sent', false)
            ->whereNull('published_at')
            ->get();

        if ($notifications->isEmpty()) {
            $this->info("ℹ️ No hay notificaciones pendientes de programar.");
            return 0;
        }

        $scheduled = 0;
        
        foreach ($notifications as $notification) {
            // Programar para dentro de los minutos especificados
            $publishAt = now()->addMinutes($minutes);
            
            $notification->published_at = $publishAt;
            $notification->save();
            
            $this->info("📧 Notificación ID {$notification->id} programada para envío a las {$publishAt->format('H:i')}");
            $scheduled++;
            
            Log::info("Notificación {$notification->id} programada para email", [
                'notification_id' => $notification->id,
                'title' => $notification->title,
                'scheduled_for' => $publishAt,
            ]);
        }

        $this->info("✅ {$scheduled} notificaciones programadas para envío automático.");
        
        // Mostrar resumen
        $this->table(
            ['ID', 'Título', 'Programada para'],
            $notifications->map(function ($notification) {
                return [
                    $notification->id,
                    substr($notification->title, 0, 30) . (strlen($notification->title) > 30 ? '...' : ''),
                    now()->addMinutes($this->option('minutes'))->format('H:i')
                ];
            })->toArray()
        );

        return 0;
    }
}
