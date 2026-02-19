<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EventQuestionTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventQuestionTemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->can('event_question_templates.view');
    }

    public function view(User $user, EventQuestionTemplate $template)
    {
        return $user->can('event_question_templates.view');
    }

    public function create(User $user)
    {
        return $user->can('event_question_templates.create');
    }

    public function update(User $user, EventQuestionTemplate $template)
    {
        return $user->can('event_question_templates.edit');
    }

    public function delete(User $user, EventQuestionTemplate $template)
    {
        return $user->can('event_question_templates.delete');
    }
}