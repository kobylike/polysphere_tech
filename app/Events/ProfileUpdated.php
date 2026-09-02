<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfileUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $user,
        public array $profile,
        public ?array $profileData = null // extra fields from UserProfile
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . $this->user->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'profile.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'user' => [
                'id'         => $this->user->id,
                'name'       => $this->user->name,
                'email'      => $this->user->email,
                'phone'      => $this->user->phone,
                'avatar_url' => $this->user->avatar_url,
                'initials'   => $this->user->initials,
            ],
            'profile' => $this->profile, // basic fields
            'profile_data' => $this->profileData, // extended fields (about_me, skills, etc.)
        ];
    }
}
