<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database', 'fcm'];
    }

    public function toFcm($notifiable)
    {
        // Selecciona idioma del usuario si lo tienes guardado, aquí solo español/catalán por ejemplo
        $locale = $notifiable->locale ?? 'es';
        app()->setLocale($locale);

        return [
            'notification' => [
                'title' => __('notifications.welcome_title'),
                'body' => __('notifications.welcome_body'),
            ],
            'data' => [
                'type' => 'welcome',
            ],
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => __('notifications.welcome_title'),
            'body' => __('notifications.welcome_body'),
        ];
    }
}
