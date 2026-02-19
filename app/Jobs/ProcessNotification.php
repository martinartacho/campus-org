<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationEmail;
use App\Services\FCMService;

class ProcessNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notification;
    public $user;
    public $type;

    public function __construct(Notification $notification, User $user, string $type)
    {
        $this->notification = $notification;
        $this->user = $user;
        $this->type = $type;
    }

    public function handle()
    {
        switch ($this->type) {
            case 'email':
                Mail::to($this->user->email)->send(new NotificationEmail($this->notification));
                $this->notification->recipients()->updateExistingPivot($this->user->id, [
                    'email_sent' => true,
                    'email_sent_at' => now()
                ]);
                break;
                
            case 'web':
                $this->user->notifications()->create([
                    'title' => $this->notification->title,
                    'content' => $this->notification->content,
                    'type' => $this->notification->type,
                    'read_at' => null,
                ]);
                $this->notification->recipients()->updateExistingPivot($this->user->id, [
                    'web_sent' => true,
                    'web_sent_at' => now()
                ]);
                break;
                
            case 'push':
                $fcmService = app(FCMService::class);
                $fcmService->sendToUser($this->user, $this->notification->title, $this->notification->content);
                $this->notification->recipients()->updateExistingPivot($this->user->id, [
                    'push_sent' => true,
                    'push_sent_at' => now()
                ]);
                break;
        }
    }
}