<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Services\FCMService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SendPendingPushNotifications extends Command
{
    //protected $signature = 'notifications:send-push';
    protected $signature = 'notifications:send-pending-push';
    protected $description = 'Envía notificaciones push pendientes a usuarios';

    public function handle(): int
    {
        $now = now()->format('Y-m-d H:i:s');
        $filename = 'push-' . now()->format('Y-m-d') . '.log';
        $logPath = storage_path("logs/{$filename}");

        // Crea el logger personalizado
        $logger = new class($logPath) {
            protected string $path;
            protected $handle;

            public function __construct(string $path)
            {
                $this->path = $path;
                $this->handle = fopen($path, 'a');
            }

            public function __destruct()
            {
                if ($this->handle) {
                    fclose($this->handle);
                }
            }

            public function info(string $message): void
            {
                fwrite($this->handle, "[INFO] $message\n");
            }

            public function warning(string $message): void
            {
                fwrite($this->handle, "[WARN] $message\n");
            }
        };

        // Inicializa buffer y contador
        $logBuffer = [];
        $hasActivity = false;
        $totalSentUsers = 0;
        $processedNotifications = 0;

        $log = function ($line, $level = 'info') use (&$logBuffer, &$hasActivity) {
            $logBuffer[] = [$level, $line];
            if (str_contains($line, '✅') || str_contains($line, '⚠️')) {
                $hasActivity = true;
            }
        };

        $log("[$now] 🔍 Inicio del proceso automático de envío push");

        $notifications = Notification::where('is_published', true)
            ->where('push_sent', false)
            ->get();

        if ($notifications->isEmpty()) {
            $this->info("ℹ️ No hay notificaciones pendientes de enviar.");
            $log("[$now] ℹ️ No hay notificaciones pendientes.");
        } else {
            $fcm = new FCMService();

            foreach ($notifications as $notification) {
                $processedNotifications++;
                $this->info("📤 Enviando notificación ID {$notification->id}: '{$notification->title}'");
                $log("[$now] 📤 Notificación ID {$notification->id}: '{$notification->title}'");

                $users = $notification->users;
                $sent = 0;

                foreach ($users as $user) {
                    $result = $fcm->sendToUser($user, $notification->title, $notification->content);
                    if ($result && $result['sent'] > 0) {
                        $sent++;
                    }
                }

                if ($sent > 0) {
                    $notification->push_sent = true;
                    $notification->save();
                    $this->info("✅ Push enviado a $sent usuarios.");
                    $log("[$now] ✅ Push enviado a $sent usuarios.");
                    $totalSentUsers += $sent;
                } else {
                    $this->warn("⚠️ No se pudo enviar push a ningún usuario.");
                    $log("[$now] ⚠️ Push fallido. Cero usuarios recibieron.", 'warning');
                }
            }
        }

        $log("[$now] 🏁 Fin del proceso.");
        $log("[$now] 🧾 Resumen: {$processedNotifications} notificaciones procesadas, {$totalSentUsers} usuarios alcanzados.");

        // Escribir log si hubo actividad
        if ($hasActivity) {
            foreach ($logBuffer as [$level, $line]) {
                $logger->$level($line);
            }
        }

        return 0;
    }
}
