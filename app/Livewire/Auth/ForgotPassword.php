<?php

namespace App\Livewire\Auth;

use App\Helpers\ActivityLogger; // <-- Added
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.auth')]
#[Title('Reset Password - Polysphere Tech')]
class ForgotPassword extends Component
{
    public string $email = '';
    public bool $loading = false;
    public bool $resetSent = false;
    public bool $canResend = true;
    public int $remainingSeconds = 0;
    public bool $showManualInstructions = false;
    public int $redirectCountdown = 5;
    public int $redirectProgress = 0;
    public bool $checking = false;
    public int $checkProgress = 0;
    public string $rateLimitType = '';
    private string $ipKey;
    private string $emailKey;

    protected $rules = [
        'email' => 'required|email|exists:users,email',
    ];

    protected $messages = [
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
        'email.exists' => 'We couldn\'t find an account with this email address.',
    ];

    protected $listeners = [
        'startCountdown',
        'resetSent',
        'rateLimitReset',
        'showNotification'
    ];

    public function mount(Request $request)
    {
        if (Auth::check()) {
            // Redirect to appropriate dashboard

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $route = $user->hasRole(['Super Admin', 'Admin']) ? 'dashboard' : 'dashboard.user';
            return redirect()->route($route);
        }

        $this->checkRateLimit();

        // Auto-show manual instructions after delay
        $this->dispatch('showManualInstructionsAfterDelay');
    }

    protected function checkRateLimit()
    {
        $this->ipKey = 'reset-ip:' . request()->ip();
        $this->emailKey = 'reset-email:' . Str::lower($this->email);

        $ipLimit = Cache::get($this->ipKey);
        $emailLimit = Cache::get($this->emailKey);

        if ($ipLimit && $ipLimit > time()) {
            $this->canResend = false;
            $this->remainingSeconds = $ipLimit - time();
            $this->rateLimitType = 'ip';
        } elseif ($emailLimit && $emailLimit > time()) {
            $this->canResend = false;
            $this->remainingSeconds = $emailLimit - time();
            $this->rateLimitType = 'email';
        } else {
            $this->canResend = true;
            $this->remainingSeconds = 0;
        }
    }

    public function sendResetLink()
    {
        $this->validate();
        $this->checkRateLimit();

        if (!$this->canResend) {
            $message = $this->rateLimitType === 'ip'
                ? 'Too many requests from your network. Please wait ' . $this->remainingSeconds . ' seconds.'
                : 'A reset link was already sent recently. Please wait ' . $this->remainingSeconds . ' seconds.';

            // 🔥 Log rate limit hit
            ActivityLogger::log('Password reset rate limit hit', [
                'email' => $this->email,
                'ip'    => request()->ip(),
                'type'  => $this->rateLimitType,
                'remaining_seconds' => $this->remainingSeconds,
            ], 'auth');

            $this->dispatch('showNotification', [
                'message' => $message,
                'type' => 'error'
            ]);
            return;
        }

        $this->loading = true;
        $this->checkProgress = 0;

        // Set rate limits
        $waitSeconds = 60;
        Cache::put($this->ipKey, time() + $waitSeconds, $waitSeconds);
        Cache::put($this->emailKey, time() + $waitSeconds, $waitSeconds);

        // Simulate progress animation
        $this->dispatch('startProgressAnimation');

        try {
            $status = Password::sendResetLink([
                'email' => $this->email,
            ]);

            if ($status === Password::RESET_LINK_SENT) {
                // 🔥 Log successful reset link request
                ActivityLogger::log('Password reset link sent', [
                    'email' => $this->email,
                    'ip'    => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ], 'auth');

                $this->resetSent = true;
                $this->canResend = false;
                $this->remainingSeconds = $waitSeconds;

                $this->dispatch('resetSent');
                $this->dispatch('startCountdown', ['seconds' => $waitSeconds]);

                // Start redirect countdown
                $this->startRedirectCountdown();
            } else {
                // 🔥 Log failed attempt (invalid email or other)
                ActivityLogger::log('Password reset request failed', [
                    'email'  => $this->email,
                    'ip'     => request()->ip(),
                    'reason' => __($status),
                ], 'auth');

                $this->addError('email', __($status));
                $this->dispatch('showNotification', [
                    'message' => __($status),
                    'type' => 'error'
                ]);
            }
        } catch (\Exception $e) {
            // 🔥 Log exception
            Log::error('Password reset exception: ' . $e->getMessage(), [
                'email' => $this->email,
                'ip'    => request()->ip(),
                'trace' => $e->getTraceAsString(),
            ]);
            ActivityLogger::log('Password reset exception', [
                'email' => $this->email,
                'ip'    => request()->ip(),
                'error' => $e->getMessage(),
            ], 'auth');

            $this->addError('email', 'An error occurred. Please try again.');
            $this->dispatch('showNotification', [
                'message' => 'An error occurred. Please try again.',
                'type' => 'error'
            ]);
        }

        $this->loading = false;
        $this->checkProgress = 100;
        $this->dispatch('stopProgressAnimation');
    }

    public function resetForm()
    {
        $this->resetSent = false;
        $this->loading = false;
        $this->email = '';
        $this->dispatch('formReset');
    }

    public function redirectToLogin()
    {
        return $this->redirect(route('login'));
    }

    public function clearRateLimits()
    {
        Cache::forget($this->ipKey);
        Cache::forget($this->emailKey);
        $this->checkRateLimit();

        // 🔥 Log manual reset of rate limits
        ActivityLogger::log('Password reset rate limits manually cleared', [
            'ip' => request()->ip(),
        ], 'auth');

        $this->dispatch('showNotification', [
            'message' => 'Rate limits cleared. You can request a new reset link.',
            'type' => 'success'
        ]);
    }

    protected function startRedirectCountdown()
    {
        $this->redirectProgress = 0;
        $this->redirectCountdown = 5;

        $interval = $this->redirectCountdown * 1000 / 100;

        $this->dispatch('startRedirectCountdown');
    }

    public function showManualInstructionsAfterDelay()
    {
        if (!$this->resetSent) {
            $this->showManualInstructions = true;
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
