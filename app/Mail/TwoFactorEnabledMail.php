<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorEnabledMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public User $user;
    public string $changedAt;
    public string $ipAddress;
    public string $userAgent;
    public string $securityUrl;

    public function __construct(User $user, ?string $ipAddress = null, ?string $userAgent = null)
    {
        $this->user       = $user;
        $this->changedAt  = now()->format('F j, Y \a\t g:i A T');
        $this->ipAddress  = $ipAddress  ?? request()->ip()       ?? 'Unknown';
        $this->userAgent  = $userAgent  ?? request()->userAgent() ?? 'Unknown device';
        $this->securityUrl = route('account', ['tab' => 'security']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Two-Factor Authentication Enabled on Your Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.two-factor-enabled',
            text: 'emails.two-factor-enabled-plain',
            with: [
                'user'        => $this->user,
                'changedAt'   => $this->changedAt,
                'ipAddress'   => $this->ipAddress,
                'userAgent'   => $this->userAgent,
                'securityUrl' => $this->securityUrl,
                'companyName' => config('app.name', 'Polysphere Tech'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
