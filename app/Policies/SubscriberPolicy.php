<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Subscriber;

class SubscriberPolicy
{
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }
    }
    /**
     * Determine if the user can view any subscribers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('View Newsletter Subscribers');
    }

    /**
     * Determine if the user can update the subscriber (e.g. mark active/unsubscribed).
     */
    public function update(User $user, Subscriber $subscriber): bool
    {
        return $user->can('Edit Newsletter Subscribers');
    }

    /**
     * Determine if the user can delete the subscriber.
     */
    public function delete(User $user, Subscriber $subscriber): bool
    {
        return $user->can('Delete Newsletter Subscribers');
    }
}
