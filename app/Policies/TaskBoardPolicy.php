<?php

namespace App\Policies;

use App\Models\TaskBoard;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskBoardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view boards they have access to
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskBoard $board): bool
    {
        // Creator can always view
        if ($board->created_by === $user->id) {
            return true;
        }

        // Public boards are visible to everyone
        if ($board->visibility === 'public') {
            return true;
        }

        // Team boards are visible to team members
        if ($board->visibility === 'team') {
            return $this->isTeamMember($user, $board);
        }

        // Course boards are visible to course participants
        if ($board->type === 'course' && $board->entity_id) {
            return $this->isCourseParticipant($user, $board->entity_id);
        }

        // Admin can view all boards
        return $user->isBackoffice();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create boards
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskBoard $board): bool
    {
        // Creator can always update
        if ($board->created_by === $user->id) {
            return true;
        }

        // Admin can update all boards
        return $user->isBackoffice();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskBoard $board): bool
    {
        // Creator can always delete
        if ($board->created_by === $user->id) {
            return true;
        }

        // Admin can delete all boards
        return $user->isBackoffice();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskBoard $board): bool
    {
        return $this->delete($user, $board);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskBoard $board): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Check if user is a team member for the board.
     */
    private function isTeamMember(User $user, TaskBoard $board): bool
    {
        // TODO: Implement team membership logic
        // For now, all authenticated users are considered team members
        return true;
    }

    /**
     * Check if user is a course participant.
     */
    private function isCourseParticipant(User $user, int $courseId): bool
    {
        // Check if user is teacher of the course
        if ($user->isTeacher()) {
            return $user->teacherProfile()
                ->courses()
                ->where('id', $courseId)
                ->exists();
        }

        // Check if user is student of the course
        if ($user->student) {
            return $user->student
                ->registrations()
                ->where('course_id', $courseId)
                ->where('status', 'confirmed')
                ->exists();
        }

        return false;
    }
}
