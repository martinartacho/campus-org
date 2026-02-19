<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EventType;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('event_types.view');
    }

    public function view(User $user, EventType $eventType)
    {
        return $user->can('event_types.view');
    }

    public function create(User $user)
    {
        return $user->can('event_types.create');
    }

    public function update(User $user, EventType $eventType)
    {
        return $user->can('event_types.edit');
    }

    public function delete(User $user, EventType $eventType)
    {
        return $user->can('event_types.delete');
    }
}