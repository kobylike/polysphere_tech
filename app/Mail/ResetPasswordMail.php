<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public string $recipientName;
    public string $recipientEmail;
    public string $resetUrl;
    public int $expiresInMinutes;

    public function __construct(
        string $name,
        string $email,
        string $resetUrl,
        int $expiresInMinutes = 60,
    ) {
        $this->recipientName    = $name;
        $this->recipientEmail   = $email;
        $this->resetUrl         = $resetUrl;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset your Polysphere Tech password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
            text: 'emails.reset-password-plain',
            with: [
                'recipientName'    => $this->recipientName,
                'recipientEmail'   => $this->recipientEmail,
                'resetUrl'         => $this->resetUrl,
                'expiresInMinutes' => $this->expiresInMinutes,
                'sentAt'           => now()->format('D, d M Y H:i T'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}