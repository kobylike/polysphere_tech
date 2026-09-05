<?php

namespace App\Livewire\Auth;

use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Set a New Password - Polysphere Tech')]
class ForcePasswordChange extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $showCurrentPassword = false;
    public bool $showPassword = false;
    public bool $showConfirmation = false;
    public bool $loading = false;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // If user doesn't need to change password, redirect to appropriate dashboard
        if (!$user->must_change_password) {
            $route = $user->hasRole(['Super Admin', 'Admin'])
                ? route('dashboard')
                : route('dashboard.user');
            return redirect()->to($route);
        }
    }

    protected function rules(): array
    {
        return [
            'current_password' => ['required'],
            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                PasswordRule::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ];
    }

    protected $messages = [
        'current_password.required' => 'Please enter the temporary password you were given.',
        'password.required' => 'Please choose a new password.',
        'password.confirmed' => 'Passwords do not match.',
        'password.different' => 'Your new password must be different from your temporary one.',
        'password.uncompromised' => 'This password has appeared in a data breach. Choose a safer one.',
    ];

    public function updatePassword()
    {
        $this->validate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'That temporary password is incorrect.');
            return;
        }

        $user->forceFill([
            'password' => Hash::make($this->password),
            'must_change_password' => false,
        ])->save();

        // ✅ Log the successful change
        ActivityLogger::log('Forced password change completed', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'ip'      => request()->ip(),
        ], 'auth');

        session()->flash('status', 'Your password has been updated. Welcome aboard!');

        // ✅ Redirect based on role
        $route = $user->hasRole(['Super Admin', 'Admin'])
            ? route('dashboard')
            : route('dashboard.user');
        return redirect()->to($route);
    }

    public function togglePasswordVisibility(string $field = 'password')
    {
        match ($field) {
            'current' => $this->showCurrentPassword = !$this->showCurrentPassword,
            'confirmation' => $this->showConfirmation = !$this->showConfirmation,
            default => $this->showPassword = !$this->showPassword,
        };
    }

    // ─── Password strength helpers ─────────────────────────────────────

    public function hasMinLength(): bool
    {
        return strlen($this->password) >= 8;
    }

    public function hasUppercase(): bool
    {
        return preg_match('/[A-Z]/', $this->password) === 1;
    }

    public function hasLowercase(): bool
    {
        return preg_match('/[a-z]/', $this->password) === 1;
    }

    public function hasNumber(): bool
    {
        return preg_match('/[0-9]/', $this->password) === 1;
    }

    public function hasSpecialChar(): bool
    {
        return preg_match('/[^A-Za-z0-9]/', $this->password) === 1;
    }

    public function getPasswordStrength(): int
    {
        if (!$this->password) {
            return 0;
        }

        $score = 0;
        $score += $this->hasMinLength() ? 20 : 0;
        $score += $this->hasUppercase() ? 20 : 0;
        $score += $this->hasLowercase() ? 20 : 0;
        $score += $this->hasNumber() ? 20 : 0;
        $score += $this->hasSpecialChar() ? 20 : 0;

        return $score;
    }

    public function getPasswordStrengthLabel(): string
    {
        return match (true) {
            $this->getPasswordStrength() >= 100 => 'Very Strong',
            $this->getPasswordStrength() >= 80  => 'Strong',
            $this->getPasswordStrength() >= 60  => 'Good',
            $this->getPasswordStrength() >= 40  => 'Weak',
            default => 'Very Weak',
        };
    }

    public function getPasswordStrengthClass(): string
    {
        return match (true) {
            $this->getPasswordStrength() >= 100 => 'bg-green-500',
            $this->getPasswordStrength() >= 80  => 'bg-green-400',
            $this->getPasswordStrength() >= 60  => 'bg-yellow-400',
            $this->getPasswordStrength() >= 40  => 'bg-orange-400',
            default => 'bg-red-400',
        };
    }

    public function getPasswordStrengthColor(): string
    {
        return match (true) {
            $this->getPasswordStrength() >= 100 => 'text-green-600',
            $this->getPasswordStrength() >= 80  => 'text-green-600',
            $this->getPasswordStrength() >= 60  => 'text-yellow-600',
            $this->getPasswordStrength() >= 40  => 'text-orange-600',
            default => 'text-red-600',
        };
    }

    public function render()
    {
        return view('livewire.auth.force-password-change');
    }
}
