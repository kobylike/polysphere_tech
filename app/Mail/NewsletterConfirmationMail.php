<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public Subscriber $subscriber;
    public string $verificationUrl;

    public function __construct(Subscriber $subscriber, string $verificationUrl)
    {
        $this->subscriber = $subscriber;
        $this->verificationUrl = $verificationUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirm Your Newsletter Subscription | Polysphere Tech',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-confirmation',
            text: 'emails.newsletter-confirmation-plain',
            with: [
                'email' => $this->subscriber->email,
                'verificationUrl' => $this->verificationUrl,
            ],
        );
    }
}
