<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
        return false;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }
    public function view(User $user, Permission $permission): bool
    {
        return false;
    }
    public function create(User $user): bool
    {
        return false;
    }
    public function update(User $user, Permission $permission): bool
    {
        return false;
    }
    public function delete(User $user, Permission $permission): bool
    {
        return false;
    }
}
