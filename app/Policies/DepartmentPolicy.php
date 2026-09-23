<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->can('View Departments');
    }

    public function view(User $user, Department $department): bool
    {
        return $user->can('View Departments');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Department');
    }

    public function update(User $user, Department $department): bool
    {
        return $user->can('Edit Department');
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->can('Delete Department');
    }

    public function archive(User $user, Department $department): bool
    {
        return $user->can('Edit Department');
    }
}
