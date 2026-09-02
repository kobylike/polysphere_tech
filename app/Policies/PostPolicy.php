<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    // Super Admin bypass
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->can('View Posts');
    }

    public function view(User $user, Post $post): bool
    {
        return $user->can('View Posts');
    }

    public function create(User $user): bool
    {
        return $user->can('Create Posts');
    }

    public function update(User $user, Post $post): bool
    {
        // Optional: you can add ownership checks here if needed
        // e.g., only allow author to edit draft, but we keep it simple:
        return $user->can('Edit Posts');
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->can('Delete Posts');
    }

    // Optional – if you have approve/reject methods:
    public function approve(User $user, Post $post): bool
    {
        return $user->can('Approve Posts');
    }

    public function reject(User $user, Post $post): bool
    {
        return $user->can('Reject Posts');
    }
}
