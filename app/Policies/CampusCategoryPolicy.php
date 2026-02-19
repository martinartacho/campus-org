<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CampusCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampusCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('campus.categories.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CampusCategory $category): bool
    {
        return $user->can('campus.categories.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('campus.categories.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CampusCategory $category): bool
    {
        return $user->can('campus.categories.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CampusCategory $category): bool
    {
        return $user->can('campus.categories.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CampusCategory $category): bool
    {
        return $user->can('campus.categories.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CampusCategory $category): bool
    {
        return $user->can('campus.categories.delete');
    }
}