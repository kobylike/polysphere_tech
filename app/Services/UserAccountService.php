<?php

namespace App\Services;

use App\Mail\AccountCreatedMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserAccountService
{
    /**
     * Reserved words that must never become a username.
     * Prevents collisions with route segments like /user-management/roles
     * and other sensitive keywords.
     */
    protected array $reservedUsernames = [
        'admin',
        'administrator',
        'root',
        'system',
        'super',
        'superadmin',
        'roles',
        'role',
        'permissions',
        'permission',
        'create',
        'edit',
        'delete',
        'new',
        'add',
        'update',
        'profile',
        'profiles',
        'detail',
        'details',
        'view',
        'list',
        'user',
        'users',
        'user-management',
        'user_management',
        'auth',
        'login',
        'logout',
        'register',
        'signup',
        'signin',
        'api',
        'www',
        'support',
        'help',
        'info',
        'contact',
        'about',
        'account',
        'settings',
        'dashboard',
        'home',
    ];

    /**
     * Generate a unique, URL-safe username from first + last name.
     * Never returns a reserved word — appends '_user' if it would.
     */
    public function generateUsername(string $firstName, string $lastName): string
    {
        $base = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $firstName . $lastName));
        $base = $base !== '' ? $base : 'user';

        // Never allow a reserved word as the base
        if (in_array($base, $this->reservedUsernames, true)) {
            $base .= '_user';
        }

        $username = $base;
        $counter  = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Generate a strong, mixed-case, symbol-containing password.
     */
    public function generateSecurePassword(int $length = 12): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
        $charactersLength = strlen($characters);

        do {
            $password = '';
            for ($i = 0; $i < $length; $i++) {
                $password .= $characters[random_int(0, $charactersLength - 1)];
            }
        } while (!(
            preg_match('/[a-z]/', $password) &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[^A-Za-z0-9]/', $password)
        ));

        return $password;
    }

    /**
     * Create a new user account with a generated password.
     *
     * @return array{user: User, plain_password: string}
     */
    public function createWithGeneratedPassword(
        array $attributes,
        array $roles = ['User'],
        ?string $explicitPassword = null,
    ): array {
        $plainPassword = $explicitPassword ?: $this->generateSecurePassword();

        $user = User::create(array_merge($attributes, [
            'password'             => Hash::make($plainPassword),
            'must_change_password' => true,
            'email_verified_at'    => $attributes['email_verified_at'] ?? now(),
        ]));

        if (!empty($roles)) {
            $user->assignRole($roles);
        }

        return [
            'user'           => $user,
            'plain_password' => $plainPassword,
        ];
    }

    /**
     * Queue the welcome email containing login credentials.
     */
    public function sendWelcomeEmail(User $user, string $plainPassword, ?string $createdByName = null): bool
    {
        try {
            Mail::to($user->email)->queue(
                new AccountCreatedMail($user->fresh('roles'), $plainPassword, $createdByName)
            );
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }
}
