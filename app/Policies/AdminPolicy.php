<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdminPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_SUPERADMIN, $admin->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Admin $admin): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function softList(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_SUPERADMIN, $admin->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_SUPERADMIN, $admin->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Admin $admin): Response
    {
        $ad = $user->userable;
        return $user->isGranted(User::ROLE_ADMIN, $ad->role) && $ad->id == $admin->id
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function softDestroy(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_SUPERADMIN, $admin->role)
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
    public function destroy(User $user): Response
    {
        $admin = $user->userable;
        return $user->isGranted(User::ROLE_SUPERADMIN, $admin->role)
            ? Response::allow()
            : Response::deny("You do not have the role allowed to perform this action");
    }
}
