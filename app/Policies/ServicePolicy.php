<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
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
        return $user->can('View Services');
    }

    public function view(User $user, Service $service): bool
    {
        return $user->can('View Services');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Services');
    }

    public function update(User $user, Service $service): bool
    {
        return $user->can('Edit Services');
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->can('Delete Services');
    }
}
