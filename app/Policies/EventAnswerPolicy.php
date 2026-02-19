<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EventAnswer;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventAnswerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('event_answers.view');
    }

    public function view(User $user, EventAnswer $answer)
    {
        return $user->can('event_answers.view');
    }

    public function create(User $user)
    {
        return $user->can('event_answers.create');
    }

    public function update(User $user, EventAnswer $answer)
    {
        return $user->can('event_answers.edit');
    }

    public function delete(User $user, EventAnswer $answer)
    {
        return $user->can('event_answers.delete');
    }
}