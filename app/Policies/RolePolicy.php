<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    // Super Admin bypass; everyone else is denied
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        return false;
    }

    // All methods return false – only Super Admin passes via `before`
    public function viewAny(User $user): bool
    {
        return false;
    }
    public function view(User $user, Role $role): bool
    {
        return false;
    }
    public function create(User $user): bool
    {
        return false;
    }
    public function update(User $user, Role $role): bool
    {
        return false;
    }
    public function delete(User $user, Role $role): bool
    {
        return false;
    }
    public function assignPermission(User $user, Role $targetRole): bool
    {
        return false;
    }
}
