<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FCMService;

class FcmChannel
{
    protected $fcm;

    public function __construct(FCMService $fcm)
    {
        $this->fcm = $fcm;
    }

    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        $this->fcm->sendToUser($notifiable, $message['notification'], $message['data'] ?? []);
    }
}
