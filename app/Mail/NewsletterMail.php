<?php

namespace App\Mail;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Subscriber $subscriber;
    public string $body;

    public function __construct(string $subject, string $body, Subscriber $subscriber)
    {
        $this->subject = $subject; // This is the parent property
        $this->body = $body;
        $this->subscriber = $subscriber;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            text: 'emails.newsletter-plain',
            with: [
                'body' => $this->body,
                'subscriber' => $this->subscriber,
                'subject' => $this->subject,
            ],
        );
    }
}
