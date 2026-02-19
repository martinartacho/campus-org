<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\FCMService;

class SendTestFCM extends Command
{
    protected $signature = 'fcm:send-test {userId} {--title=Hola 👋} {--body=¡Te damos la bienvenida al sistema!}';

    protected $description = 'Envía una notificación FCM de prueba a un usuario específico.';

    public function handle(FCMService $fcmService): int
    {
        $userId = $this->argument('userId');
        $title = $this->option('title');
        $body = $this->option('body');

        $user = User::find($userId);

        if (!$user) {
            $this->error("❌ Usuario con ID {$userId} no encontrado.");
            return self::FAILURE;
        }

        $this->info("📨 Enviando notificación a usuario #{$user->id} ({$user->name})...");

        $result = $fcmService->sendToUser($user, $title, $body);

        if (!$result) {
            $this->error("❌ Fallo al enviar notificación.");
            return self::FAILURE;
        }

        $this->info("✅ Notificación enviada correctamente.");
        return self::SUCCESS;
    }
}
