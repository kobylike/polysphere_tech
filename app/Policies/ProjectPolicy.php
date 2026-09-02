<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->can('View Projects');
    }

    public function view(User $user, Project $project): bool
    {
        return $user->can('View Projects');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Projects');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('Edit Projects');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->can('Delete Projects');
    }
}
