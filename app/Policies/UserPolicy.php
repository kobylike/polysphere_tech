<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    // ─── Super Admin bypass ────────────────────────────────────────────────
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }

    // ─── View Users ────────────────────────────────────────────────────────
    public function viewAny(User $user): bool
    {
        return $user->can('View Users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('View Users');
    }

    // ─── Create User ──────────────────────────────────────────────────────
    public function create(User $user): bool
    {
        return $user->can('Create User');
    }

    // ─── Edit User ────────────────────────────────────────────────────────
    public function update(User $user, User $targetUser): bool
    {
        // Prevent Admin from editing another Admin
        if ($user->hasRole('Admin') && $targetUser->hasRole('Admin')) {
            return false;
        }
        // Prevent editing Super Admin (even if someone had permission)
        if ($targetUser->hasRole('Super Admin')) {
            return false;
        }
        return $user->can('Edit User');
    }

    // ─── Delete User ──────────────────────────────────────────────────────
    public function delete(User $user, User $targetUser): bool
    {
        if ($targetUser->hasRole('Super Admin')) {
            return false;
        }
        if ($user->hasRole('Admin') && $targetUser->hasRole('Admin')) {
            return false;
        }
        return $user->can('Delete User');
    }

    // ─── Toggle Status ────────────────────────────────────────────────────
    public function toggleStatus(User $user, User $targetUser): bool
    {
        if ($targetUser->hasRole('Super Admin')) {
            return false;
        }
        if ($user->hasRole('Admin') && $targetUser->hasRole('Admin')) {
            return false;
        }
        return $user->can('toggleStatus');
    }

    // ─── Assign Role ──────────────────────────────────────────────────────
    public function assignRole(User $user, User $targetUser): bool
    {
        if ($targetUser->hasRole('Super Admin')) {
            return false;
        }
        if ($user->hasRole('Admin') && $targetUser->hasRole('Admin')) {
            return false;
        }
        return $user->can('Assign Role');
    }

    // ─── Extra: view the "terminate" button (optional) ────────────────────
    public function viewTerminateButton(User $authUser, User $targetUser): bool
    {
        return $authUser->hasRole('Admin') || $authUser->hasRole('Super Admin');
    }
}
