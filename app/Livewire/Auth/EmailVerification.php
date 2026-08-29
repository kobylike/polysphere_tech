<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class EmailVerification extends Component
{
    public string $status = '';
    public string $message = '';

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->hasVerifiedEmail()) {
            // Redirect to dashboard without full page reload
            $this->redirectRoute('dashboard', navigate: true);
        }
    }

    /**
     * Resend the verification email.
     */
    public function resendVerification(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            // Not logged in – redirect to login page (client‑side)
            $this->redirectRoute('login', navigate: true);
            return; // This return is optional because redirect throws an exception, but keeps static analysis happy
        }

        if ($user->hasVerifiedEmail()) {
            $this->status = 'info';
            $this->message = 'Your email is already verified.';
            return;
        }

        // Resend the notification
        $user->sendEmailVerificationNotification();

        $this->status = 'success';
        $this->message = 'A fresh verification link has been sent to your email address.';
    }

    /**
     * Logout the user using Livewire.
     */
    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        // Redirect to login with client‑side navigation
        $this->redirectRoute('login', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.email-verification');
    }
}
