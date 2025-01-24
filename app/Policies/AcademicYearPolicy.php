<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AcademicYearPolicy
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
    public function view(User $user, AcademicYear $academicYear): bool
    {
        return false;
    }


    /**
     * Determine whether the user can view the model.
     */
    public function soft_view(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_SUPERADMIN, $admin->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_ADMIN, $admin->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_ADMIN, $admin->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function soft_delete(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_ADMIN, $admin->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_SUPERADMIN, $admin->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_SUPERADMIN, $admin->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }
}