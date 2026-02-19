<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotificationPolicy
{
    public function view(User $user, Notification $notification): bool
{
    return true;
}

public function update(User $user, Notification $notification): bool
{
    return $user->hasRole(['admin', 'gestor', 'treasury', 'editor']);
}

public function delete(User $user, Notification $notification): bool
{
    return $user->hasRole(['admin', 'gestor']);
}

}
