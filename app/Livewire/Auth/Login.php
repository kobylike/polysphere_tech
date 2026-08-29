<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
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

        if ($user && $user->isLocked()) {
            $this->addError('email', 'Too many failed attempts. Please try again later.');
            return;
        }

        // Validate credentials without logging the user in yet — needed
        // because 2FA users shouldn't get an authenticated session until
        // they've passed the verification step.
        if (!Auth::validate([
            'email'    => $this->email,
            'password' => $this->password,
        ])) {
            $this->addError('email', 'Invalid email or password.');
            return;
        }

        if ($user->status === 'suspended') {
            $this->reset(['password']);
            $this->resetErrorBag();

            $this->addError('email', 'Your account has been suspended. Please contact support.');
            $this->dispatch('show-toast', [
                'type'     => 'error',
                'message'  => '🚫 Your account is suspended. Please reach out to our support team.',
                'duration' => 6000,
            ]);
            return;
        }

        if ($user->hasTwoFactorEnabled()) {
            session()->put('login.id', $user->id);
            session()->put('login.remember', $this->remember);
            session()->put('login.time', now());
            session()->put('2fa_attempts', 5);

            return $this->redirect(route('two-factor.verification'));
        }

        Auth::login($user, $this->remember);
        session()->regenerate();

        $user->updateLastLogin();
        $user->resetFailedLoginAttempts();

        return $this->redirect(route('dashboard'));
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
