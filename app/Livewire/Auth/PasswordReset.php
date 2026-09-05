<?php

namespace App\Livewire\Auth;

use App\Helpers\ActivityLogger; // <-- Correct import
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Auth\Events\PasswordReset as PasswordResetEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

#[Layout('layouts.auth')]
#[Title('Reset Password - Polysphere Tech')]
class PasswordReset extends Component
{
    public ?string $token = null;
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $showPassword = false;
    public bool $showConfirmation = false;
    public bool $loading = false;

    public bool $isValidToken = false;
    public ?int $expiresIn = null;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => [
                'required',
                'confirmed',
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
        'email.required' => 'Email address is required.',
        'email.email' => 'Please enter a valid email address.',
        'password.required' => 'Please enter your new password.',
        'password.confirmed' => 'Passwords do not match.',
        'password.uncompromised' => 'This password has appeared in a data breach. Choose a safer one.',
    ];

    public function mount(Request $request, string $token = null)
    {
        $this->token = $token;
        $this->email = (string) $request->query('email', '');

        if ($this->token && $this->email) {
            $record = DB::table('password_reset_tokens')
                ->where('email', $this->email)
                ->first();

            if ($record && Hash::check($this->token, $record->token)) {
                $this->isValidToken = true;

                $createdAt = Carbon::parse($record->created_at);
                $expiresAt = $createdAt->addMinutes(
                    config('auth.passwords.users.expire', 60)
                );

                $this->expiresIn = now()->diffInMinutes($expiresAt, false);
            }
        }

        $key = 'password-reset-view:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            abort(429);
        }

        RateLimiter::hit($key);
    }

    public function resetPassword()
    {
        $this->validate();
        $this->loading = true;

        $throttleKey = 'password-reset:' . $this->email . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $this->dispatch(
                'show-toast',
                type: 'error',
                message: 'Too many attempts. Please wait and try again.'
            );
            $this->loading = false;
            return;
        }

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordResetEvent($user));
                
                Auth::login($user);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            Log::info('Password reset successful', [
                'email' => $this->email,
                'ip' => request()->ip(),
            ]);

            // ✅ Log the successful reset
            ActivityLogger::log('Password reset successfully', [
                'email' => $this->email,
                'ip'    => request()->ip(),
            ], 'auth');

            session()->flash('status', 'Your password has been reset successfully.');
            $this->redirectRoute('dashboard', navigate: true);
            return;
        }

        RateLimiter::hit($throttleKey);

        $this->addError(
            'email',
            match ($status) {
                Password::INVALID_TOKEN => 'This reset link is invalid or expired.',
                Password::INVALID_USER => 'No account found for this email.',
                default => __($status),
            }
        );

        $this->loading = false;
    }

    public function togglePasswordVisibility(string $field = 'password')
    {
        $field === 'password'
            ? $this->showPassword = !$this->showPassword
            : $this->showConfirmation = !$this->showConfirmation;
    }

    public function redirectToLogin()
    {
        return redirect()->route('login');
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
        return view('livewire.auth.password-reset');
    }
}
