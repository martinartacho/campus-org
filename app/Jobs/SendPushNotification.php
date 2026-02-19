<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;
use App\Models\User;
use App\Services\FCMService;

class SendPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notification;
    public $user;

    public function __construct(Notification $notification, User $user)
    {
        $this->notification = $notification;
        $this->user = $user;
    }

    public function handle(FCMService $fcmService)
    {
        $fcmService->sendToUser($this->user, $this->notification->title, $this->notification->content);
        
        $this->notification->recipients()->updateExistingPivot($this->user->id, [
            'push_sent' => true,
            'push_sent_at' => now()
        ]);
    }
}