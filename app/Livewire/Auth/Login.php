<?php

namespace App\Livewire\Auth;

use App\Helpers\ActivityLogger;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Login - Polysphere Tech')]
class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required|min:6',
    ];

    public function mount()
    {
        if (session()->has('error')) {
            $error = session('error');
            $this->addError('email', $error);
            $this->dispatch('show-toast', [
                'type'     => 'error',
                'message'  => $error,
                'duration' => 6000,
            ]);
            session()->forget('error');
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function login()
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        // ─── Log attempt (even before validation) ──────────────────────────
        ActivityLogger::log('Login attempt', [
            'email' => $this->email,
            'ip'    => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], 'auth');

        // ─── Locked account check ────────────────────────────────────────────
        if ($user && $user->isLocked()) {
            $this->addError('email', 'Too many failed attempts. Please try again later.');
            ActivityLogger::log('Failed login attempt (account locked)', [
                'email' => $this->email,
                'ip'    => request()->ip(),
            ], 'auth');
            return;
        }

        // ─── Validate credentials ────────────────────────────────────────────
        if (!Auth::validate([
            'email'    => $this->email,
            'password' => $this->password,
        ])) {
            // Increment failed attempts if user exists
            if ($user) {
                $user->increment('failed_login_attempts');
                if ($user->failed_login_attempts >= 5) {
                    $user->lockAccount(15);
                }
                ActivityLogger::log('Failed login attempt (invalid credentials)', [
                    'email'    => $this->email,
                    'ip'       => request()->ip(),
                    'attempts' => $user->failed_login_attempts,
                ], 'auth');
            }

            $this->addError('email', 'Invalid email or password.');
            return;
        }

        // ─── Suspended account ────────────────────────────────────────────────
        if ($user->status === 'suspended') {
            $this->reset(['password']);
            $this->resetErrorBag();

            $this->addError('email', 'Your account has been suspended. Please contact support.');
            $this->dispatch('show-toast', [
                'type'     => 'error',
                'message'  => '🚫 Your account is suspended. Please reach out to our support team.',
                'duration' => 6000,
            ]);
            ActivityLogger::log('Suspended user attempted login', [
                'email' => $this->email,
                'ip'    => request()->ip(),
            ], 'auth');
            return;
        }

        // ─── Two-Factor Authentication ────────────────────────────────────────
        if ($user->hasTwoFactorEnabled()) {
            session()->put('login.id', $user->id);
            session()->put('login.remember', $this->remember);
            session()->put('login.time', now());
            session()->put('2fa_attempts', 5);

            ActivityLogger::log('2FA verification initiated', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'ip'      => request()->ip(),
            ], 'auth');

            return $this->redirect(route('two-factor.verification'));
        }

        // ─── Successful login ──────────────────────────────────────────────────
        Auth::login($user, $this->remember);
        session()->regenerate();

        $user->updateLastLogin();
        $user->resetFailedLoginAttempts();

        // ─── Log the successful login ────────────────────────────────────────
        ActivityLogger::log('User logged in successfully', [
            'user_id'    => $user->id,
            'email'      => $user->email,
            'name'       => $user->name,
            'ip'         => request()->ip(),
            'user_agent' => request()->userAgent(),
            'remember'   => $this->remember,
        ], 'auth');

        // ─── Force password change ────────────────────────────────────────────
        if ($user->must_change_password) {
            return $this->redirectRoute('password.change.force', navigate: true);
        }

        // ─── Redirect based on role ──────────────────────────────────────────
        $dashboardRoute = $user->hasRole(['Super Admin', 'Admin'])
            ? route('dashboard')
            : route('dashboard.user');
        return $this->redirect($dashboardRoute);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
