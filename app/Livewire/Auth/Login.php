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

        if (!Auth::attempt([
            'email'    => $this->email,
            'password' => $this->password,
        ], $this->remember)) {
            $this->addError('email', 'Invalid email or password.');
            return;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->status === 'suspended') {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

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

        session()->regenerate();
        return $this->redirect('dashboard');
        // return $this->redirectRoute('dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
