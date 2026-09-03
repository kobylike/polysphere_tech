<?php

namespace App\Helpers;

use App\Events\NewNotification;
use App\Models\Notification;
use App\Models\User;
use App\Models\Role;
use App\Models\UserProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    // ─── Existing methods ──────────────────────────────────────────────

    public static function sendToUser(User $user, array $data): void
    {
        self::sendToUsers(collect([$user]), $data);
    }

    public static function sendToUsers(Collection $users, array $data): void
    {
        foreach ($users as $user) {
            self::sendToSingleUser($user, $data);
        }
    }

    public static function sendToAll(array $data): void
    {
        $users = User::all();
        self::sendToUsers($users, $data);
    }

    public static function sendToRole(Role $role, array $data): void
    {
        $users = $role->users;
        self::sendToUsers($users, $data);
    }

    public static function sendToRoles(Collection $roles, array $data): void
    {
        $users = collect();
        foreach ($roles as $role) {
            $users = $users->merge($role->users);
        }
        self::sendToUsers($users->unique('id'), $data);
    }

    // ─── Employees ──────────────────────────────────────────────────

    public static function sendToEmployees(array $data): void
    {
        $users = User::whereHas('profile', function ($q) {
            $q->where('is_employee', true);
        })->get();
        self::sendToUsers($users, $data);
    }

    // ─── Positions ──────────────────────────────────────────────────

    public static function sendToPosition(string $position, array $data): void
    {
        $users = User::whereHas('profile', function ($q) use ($position) {
            $q->where('position', $position);
        })->get();
        self::sendToUsers($users, $data);
    }

    public static function sendToPositions(array $positions, array $data): void
    {
        $users = User::whereHas('profile', function ($q) use ($positions) {
            $q->whereIn('position', $positions);
        })->get();
        self::sendToUsers($users, $data);
    }

    // ─── Advanced: send to a mix of recipients ──────────────────────────

    public static function send(array $recipients, array $data): void
    {
        $users = collect();

        if (isset($recipients['all']) && $recipients['all'] === true) {
            self::sendToAll($data);
            return;
        }

        if (isset($recipients['employees']) && $recipients['employees'] === true) {
            $empUsers = User::whereHas('profile', function ($q) {
                $q->where('is_employee', true);
            })->get();
            $users = $users->merge($empUsers);
        }

        if (isset($recipients['users'])) {
            $userModels = User::whereIn('id', $recipients['users'])->get();
            $users = $users->merge($userModels);
        }

        if (isset($recipients['roles'])) {
            $roles = Role::whereIn('id', $recipients['roles'])->get();
            foreach ($roles as $role) {
                $users = $users->merge($role->users);
            }
        }

        if (isset($recipients['positions'])) {
            $users = $users->merge(
                User::whereHas('profile', function ($q) use ($recipients) {
                    $q->whereIn('position', $recipients['positions']);
                })->get()
            );
        }

        self::sendToUsers($users->unique('id'), $data);
    }

    // ─── Internal ──────────────────────────────────────────────────────────

    private static function sendToSingleUser(User $user, array $data): void
    {
        $notification = Notification::create([
            'type'            => $data['type'] ?? 'info',
            'title'           => $data['title'] ?? null,
            'body'            => $data['body'],
            'link'            => $data['link'] ?? null,
            'icon'            => $data['icon'] ?? null,
            'sender_name'     => $data['sender_name'] ?? 'System',
            'notifiable_type' => User::class,
            'notifiable_id'   => $user->id,
            'read_at'         => null,
        ]);

        try {
            Log::info('Broadcasting notification to user ' . $user->id);
            broadcast(new NewNotification($user->id, [
                'id'         => $notification->id,
                'title'      => $notification->title,
                'body'       => $notification->body,
                'type'       => $notification->type,
                'link'       => $notification->link,
                'icon'       => $notification->icon,
                'created_at' => $notification->created_at->diffForHumans(),
            ]));
        } catch (\Exception $e) {
            Log::error('Notification broadcast failed: ' . $e->getMessage());
        }
    }
}
