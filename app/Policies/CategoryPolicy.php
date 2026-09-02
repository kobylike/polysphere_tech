<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
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
        return $user->can('View Categories');
    }

    public function view(User $user, Category $category): bool
    {
        return $user->can('View Categories');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Categories');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can('Edit Categories');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can('Delete Categories');
    }
}
