<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // Number of times Laravel will attempt this job before giving up
    // and moving it to failed_jobs permanently.
    public int $tries = 3;

    // Seconds to wait before each retry attempt (index 0 = wait before
    // 1st retry, index 1 = wait before 2nd retry, etc). Using increasing
    // delays gives transient issues (like an SMTP provider's per-second
    // rate limit) time to clear before trying again.
    public array $backoff = [5, 15, 30];

    public string $recipientName;
    public string $originalSubject;

    public function __construct(string $name, string $subject)
    {
        $this->recipientName  = $name;
        $this->originalSubject = $subject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "We've received your message",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-confirmation',
            text: 'emails.contact-confirmation-plain',
            with: [
                'recipientName'   => $this->recipientName,
                'originalSubject' => $this->originalSubject,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
