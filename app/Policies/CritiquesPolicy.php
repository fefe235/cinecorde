<?php

namespace App\Policies;

use App\Models\Critiques;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CritiquesPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Critiques $critiques): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,Critiques $critiques): bool
    {
        return $user->user_id === $critiques->id_user;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Critiques $critiques): bool
    {
        return $user->is_admin || $user->user_id === $critiques->id_user;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Critiques $critiques): bool
    {
        return $user->is_admin || $user->user_id === $critiques->id_user;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Critiques $critiques): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Critiques $critiques): bool
    {
        return $user->is_admin || $user->user_id === $critiques->id_user;
    }
}
