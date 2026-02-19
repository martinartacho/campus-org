<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EventQuestion;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventQuestionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('event_questions.view');
    }

    public function view(User $user, EventQuestion $question)
    {
        return $user->can('event_questions.view');
    }

    public function create(User $user)
    {
        return $user->can('event_questions.create');
    }

    public function update(User $user, EventQuestion $question)
    {
        return $user->can('event_questions.edit');
    }

    public function delete(User $user, EventQuestion $question)
    {
        return $user->can('event_questions.delete');
    }
}