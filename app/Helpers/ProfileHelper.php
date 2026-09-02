<?php

namespace App\Helpers;

use App\Events\ProfileUpdated;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileHelper
{
    /**
     * Broadcast the full profile of a user.
     */
    public static function broadcast(User $user): void
    {
        $profile = $user->profile;

        broadcast(new ProfileUpdated(
            $user,
            [
                'name'       => $user->name,
                'email'      => $user->email,
                'phone'      => $user->phone,
                'avatar_url' => $user->avatar_url,
                'initials'   => $user->initials,
            ],
            $profile ? [
                'about_me'                => $profile->about_me,
                'skills'                 => $profile->skills,
                'education'              => $profile->education,
                'social_links'           => $profile->social_links,
                'position'               => $profile->position,
                'gender'                 => $profile->gender,
                'date_of_birth'          => $profile->date_of_birth?->toDateString(),
                'country_code'           => $profile->country_code,
                'city'                   => $profile->city,
                'emergency_contact_name' => $profile->emergency_contact_name,
                'emergency_contact_phone' => $profile->emergency_contact_phone,
                'is_employee'            => $profile->is_employee,
                // add any other fields you want to sync
            ] : null
        ));
    }

    /**
     * Broadcast for a specific user ID (convenience).
     */
    public static function broadcastForId(int $userId): void
    {
        $user = User::find($userId);
        if ($user) {
            self::broadcast($user);
        }
    }

    /**
     * Resolve the public URL for a user's avatar, or null.
     */
    public static function avatarUrl(User $user): ?string
    {
        if (empty($user->avatar)) {
            return null;
        }

        $disk = Storage::disk('public');
        if ($disk->exists($user->avatar)) {
            return asset('storage/' . $user->avatar) . '?v=' . time();
        }

        return null;
    }
}
