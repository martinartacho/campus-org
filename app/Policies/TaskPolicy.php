<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Controlled by board visibility
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Creator can always view
        if ($task->created_by === $user->id) {
            return true;
        }

        // Assigned user can always view
        if ($task->assigned_to === $user->id) {
            return true;
        }

        // Check board access
        return $user->can('view', $task->board);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create tasks if they have board access
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Creator can always update
        if ($task->created_by === $user->id) {
            return true;
        }

        // Assigned user can update
        if ($task->assigned_to === $user->id) {
            return true;
        }

        // Admin can update all tasks
        if ($user->isBackoffice()) {
            return true;
        }

        // Board creator can update tasks in their board
        if ($task->board->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Creator can always delete
        if ($task->created_by === $user->id) {
            return true;
        }

        // Admin can delete all tasks
        if ($user->isBackoffice()) {
            return true;
        }

        // Board creator can delete tasks in their board
        if ($task->board->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $this->delete($user, $task);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can assign tasks.
     */
    public function assign(User $user, Task $task): bool
    {
        // Creator can assign
        if ($task->created_by === $user->id) {
            return true;
        }

        // Admin can assign
        if ($user->isBackoffice()) {
            return true;
        }

        // Board creator can assign
        if ($task->board->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can add comments.
     */
    public function comment(User $user, Task $task): bool
    {
        // Anyone who can view the task can comment
        return $this->view($user, $task);
    }

    /**
     * Determine whether the user can add attachments.
     */
    public function attach(User $user, Task $task): bool
    {
        // Anyone who can update the task can attach files
        return $this->update($user, $task);
    }
}
