<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApplicationPolicy
{
    use HandlesAuthorization;

    // ─── Super Admin bypass ────────────────────────────────────────────────
    public function before(User $user, string $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }

    // ─── View ──────────────────────────────────────────────────────────────
    public function viewAny(User $user): bool
    {
        return $user->can('View Applications');
    }

    public function view(User $user, Application $application): bool
    {
        return $user->can('View Applications');
    }

    // ─── Update (status changes, notes, reviewer assignment) ───────────────
    public function update(User $user, Application $application): bool
    {
        return $user->can('Edit Applications');
    }

    // ─── Delete ────────────────────────────────────────────────────────────
    public function delete(User $user, Application $application): bool
    {
        return $user->can('Delete Applications');
    }
}
