<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.users')]
class UserDetails extends Component
{
    public User $user;

    public function mount(string $identifier): void
    {
        $this->user = User::findByUsernameOrId($identifier);

        $this->authorize('view', $this->user);
    }

    public function getActivitiesProperty()
    {
        if (! class_exists(UserActivity::class)) {
            return collect();
        }

        return UserActivity::where('user_id', $this->user->id)
            ->latest()
            ->take(20)
            ->get();
    }

    /**
     * Full shareable URL for this user's profile.
     * Used by the Copy Link button.
     */
    public function getShareUrlProperty(): string
    {
        return route('users.profile', $this->user->route_identifier);
    }

    public function render()
    {
        return view('livewire.admin.users.user-details', [
            'activities' => $this->activities,
            'shareUrl'   => $this->shareUrl,
        ]);
    }
}
