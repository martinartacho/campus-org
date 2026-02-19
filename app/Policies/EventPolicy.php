<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('events.view');
    }

    public function view(User $user, Event $event)
    {
        return $user->can('events.view');
    }

    public function create(User $user)
    {
        return $user->can('events.create');
    }

    public function update(User $user, Event $event)
    {
        return $user->can('events.edit');
    }

    public function delete(User $user, Event $event)
    {
        return $user->can('events.delete');
    }
}