<?php

namespace App\Policies;

use App\Models\Qualification;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QualificationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Qualification $qualificationController): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        $teacher = $user->userable;
        return $user->isGranted(User::ROLE_TEACHER, $teacher->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function udpdate(User $user, Qualification $qualificationController): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Qualification $qualificationController): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Qualification $qualificationController): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Qualification $qualificationController): bool
    {
        return false;
    }
}
