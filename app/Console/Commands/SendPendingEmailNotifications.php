<?php

namespace App\Console\Commands;

use App\Mail\NotificationMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPendingEmailNotifications extends Command
{
    protected $signature = 'notifications:send-pending-email';
    protected $description = 'Envía notificaciones por email pendientes a usuarios';

    public function handle(): int
    {
        $now = now()->format('Y-m-d H:i:s');
        $filename = 'email-' . now()->format('Y-m-d') . '.log';
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

            public function error(string $message): void
            {
                fwrite($this->handle, "[ERROR] $message\n");
            }
        };

        // Inicializa buffer y contador
        $logBuffer = [];
        $hasActivity = false;
        $totalSentEmails = 0;
        $processedNotifications = 0;
        $failedEmails = 0;

        $log = function ($line, $level = 'info') use (&$logBuffer, &$hasActivity) {
            $logBuffer[] = [$level, $line];
            if (str_contains($line, '✅') || str_contains($line, '⚠️') || str_contains($line, '❌')) {
                $hasActivity = true;
            }
        };

        $log("[$now] 🔍 Inicio del proceso automático de envío de emails");

        $notifications = Notification::where('is_published', true)
            ->where('email_sent', false)
            ->get();

        if ($notifications->isEmpty()) {
            $this->info("ℹ️ No hay notificaciones pendientes de enviar por email.");
            $log("[$now] ℹ️ No hay notificaciones pendientes de email.");
        } else {
            foreach ($notifications as $notification) {
                $processedNotifications++;
                $this->info("📧 Enviando notificación ID {$notification->id}: '{$notification->title}'");
                $log("[$now] 📧 Notificación ID {$notification->id}: '{$notification->title}'");

                // Obtener destinatarios según el tipo
                $recipients = $this->getRecipients($notification);
                $sent = 0;

                foreach ($recipients as $user) {
                    try {
                        // Usar la clase Mailable
                        $mail = new NotificationMail($notification, $user);
                        
                        Mail::to($user->email)->send($mail);
                        
                        // Marcar como enviado en la tabla pivot
                        $notification->recipients()->updateExistingPivot($user->id, [
                            'email_sent' => true,
                            'email_sent_at' => now(),
                        ]);

                        $sent++;
                        $this->info("✅ Email enviado a {$user->email}");
                        $log("[$now] ✅ Email enviado a {$user->email}");

                    } catch (\Exception $e) {
                        $failedEmails++;
                        $this->error("❌ Error enviando email a {$user->email}: " . $e->getMessage());
                        $log("[$now] ❌ Error email a {$user->email}: " . $e->getMessage(), 'error');
                    }
                }

                if ($sent > 0) {
                    $notification->email_sent = true;
                    $notification->save();
                    $this->info("✅ Emails enviados a $sent usuarios.");
                    $log("[$now] ✅ Emails enviados a $sent usuarios.");
                    $totalSentEmails += $sent;
                } else {
                    $this->warn("⚠️ No se pudo enviar email a ningún usuario.");
                    $log("[$now] ⚠️ Email fallido. Cero usuarios recibieron.", 'warning');
                }
            }
        }

        $log("[$now] 🏁 Fin del proceso.");
        $log("[$now] 🧾 Resumen: {$processedNotifications} notificaciones procesadas, {$totalSentEmails} emails enviados, {$failedEmails} fallidos.");

        // Escribir log si hubo actividad
        if ($hasActivity) {
            foreach ($logBuffer as [$level, $line]) {
                $logger->$level($line);
            }
        }

        $this->info("📊 Proceso completado: {$totalSentEmails} emails enviados, {$failedEmails} fallidos.");

        return 0;
    }

    /**
     * Obtener destinatarios según el tipo de notificación
     */
    private function getRecipients($notification)
    {
        switch ($notification->recipient_type) {
            case 'all':
                return User::where('email', '!=', null)->get();
                
            case 'role':
                return User::role($notification->recipient_role)->where('email', '!=', null)->get();
                
            case 'specific':
                $userIds = $notification->recipient_ids ?? [];
                return User::whereIn('id', $userIds)->where('email', '!=', null)->get();
                
            default:
                return collect();
        }
    }
}
