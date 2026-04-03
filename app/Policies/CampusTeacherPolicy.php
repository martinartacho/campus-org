<?php

namespace App\Policies;

use App\Models\CampusTeacher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CampusTeacherPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins i gestors poden veure teachers
        return $user->hasRole('admin') || 
               $user->hasRole('manager') ||
               $user->hasRole('treasury');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CampusTeacher $teacher): bool
    {
        // Admins, gestors, o el propi teacher poden veure
        return $user->hasRole('admin') || 
               $user->hasRole('manager') ||
               $user->hasRole('treasury') ||
               $user->teacherProfile?->id === $teacher->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || 
               $user->hasRole('manager') ||
               $user->hasRole('treasury');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CampusTeacher $teacher): bool
    {
        // Admins, gestors, o el propi teacher poden actualitzar
        return $user->hasRole('admin') || 
               $user->hasRole('manager') ||
               $user->hasRole('treasury') ||
               $user->teacherProfile?->id === $teacher->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CampusTeacher $teacher): bool
    {
        // Només admins i gestors poden eliminar
        return $user->hasRole('admin') || 
               $user->hasRole('manager') ||
               $user->hasRole('treasury');
    }

    /**
     * Determine whether the user can manage the model.
     */
    public function manage(User $user, CampusTeacher $teacher): bool
    {
        // Admins i gestors poden gestionar (eliminar PDFs, etc.)
        return $user->hasRole('admin') ||
               $user->hasRole('manager') ||
               $user->hasRole('treasury') ||
               $user->teacherProfile?->id === $teacher->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CampusTeacher $teacher): bool
    {
        return $user->hasRole('admin') ||
               $user->hasRole('manager') ||
               $user->hasRole('treasury');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CampusTeacher $teacher): bool
    {
        return $user->hasRole('admin') ||
               $user->hasRole('manager') ||
               $user->hasRole('treasury');
    }
}
