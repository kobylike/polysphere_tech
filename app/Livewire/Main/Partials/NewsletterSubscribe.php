<?php

namespace App\Livewire\Main\Partials;

use App\Models\Subscriber;
use App\Mail\NewsletterConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Component;

class NewsletterSubscribe extends Component
{
    public $email = '';
    public $showSuccess = false;
    public $errorMessage = '';

    protected $rules = [
        'email' => 'required|email|max:255',
    ];

    protected $messages = [
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
        'email.max' => 'Email address is too long.',
    ];

    public function subscribe()
    {
        $this->validate();

        // ─── SPAM PREVENTION: Rate limiting ────────────────────────────────
        $key = 'newsletter-subscribe:' . request()->ip();
        $maxAttempts = 5;
        $decayMinutes = 60;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $this->errorMessage = 'Too many subscription attempts. Please try again later.';
            return;
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        // ─── Check for existing subscriber ──────────────────────────────────
        $existing = Subscriber::where('email', $this->email)->first();

        if ($existing) {
            if ($existing->isActive()) {
                $this->errorMessage = 'This email is already subscribed.';
                return;
            }

            if ($existing->isPending()) {
                $this->errorMessage = 'Please check your email to confirm your subscription.';
                return;
            }

            if ($existing->isUnsubscribed()) {
                // Reactivate
                $existing->update([
                    'status' => 'pending',
                    'verification_token' => Str::random(60),
                    'subscribed_ip' => request()->ip(),
                    'subscribed_at' => null,
                    'unsubscribed_at' => null,
                ]);

                $this->sendConfirmationEmail($existing);
                $this->showSuccess = true;
                $this->email = '';
                return;
            }
        }

        // ─── Create new subscriber ──────────────────────────────────────────
        $subscriber = Subscriber::create([
            'email' => $this->email,
            'status' => 'pending',
            'verification_token' => Str::random(60),
            'subscribed_ip' => request()->ip(),
        ]);

        // ─── Send confirmation email via queue ─────────────────────────────
        $this->sendConfirmationEmail($subscriber);

        $this->showSuccess = true;
        $this->email = '';
    }

    protected function sendConfirmationEmail(Subscriber $subscriber): void
    {
        $verificationUrl = route('newsletter.verify', [
            'token' => $subscriber->verification_token,
            'email' => $subscriber->email,
        ]);

        Mail::to($subscriber->email)->queue(
            new NewsletterConfirmationMail($subscriber, $verificationUrl)
        );
    }

    public function render()
    {
        return view('livewire.main.partials.newsletter-subscribe');
    }
}
