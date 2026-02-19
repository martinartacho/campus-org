<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;
use App\Models\User;

class SendWebNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notification;
    public $user;

    public function __construct(Notification $notification, User $user)
    {
        $this->notification = $notification;
        $this->user = $user;
    }

    public function handle()
    {
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
    }
}