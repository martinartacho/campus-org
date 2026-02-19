<?php

namespace App\Observers;

use App\Models\Notification;
use App\Models\User;
use App\Services\FCMService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user)
    {
        if (!$user->fcmTokens()->exists()) {
            Log::info("🟡 Usuario creado pero sin FCM tokens: {$user->email}");
            return;
        }

        $title = __('notifications.welcome_title');
        $body = __('notifications.welcome_body');

        // Crear notificación personalizada
        $notification = \App\Models\Notification::create([
            'title' => $title,
            'content' => $body,
            'sender_id' => 1, // o el ID de un sistema/admin
            'recipient_type' => 'specific',
            'recipient_ids' => json_encode([$user->id]),
            'is_published' => true,
            'published_at' => now(),
            'push_sent' => true,
        ]);

        // Crear relación en notification_user
        $notification->users()->attach($user->id);

        // Enviar push
        app(FCMService::class)->sendToUser($user, [
            'title' => $title,
            'body' => $body,
        ], [
            'type' => 'welcome',
            'notification_id' => $notification->id,
        ]);

        Log::info("✅ Notificación de bienvenida enviada a {$user->email}");
    }
}

