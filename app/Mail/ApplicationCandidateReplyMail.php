<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationCandidateReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public function __construct(
        public Application $application,
        public string $subjectLine,
        public string $body,
        public string $senderName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address('careers@polyspheretech.com', 'Polysphere Tech Hiring'),
            ],
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-candidate-reply',
            text: 'emails.application-candidate-reply-plain',
            with: [
                'application' => $this->application,
                'vacancy'     => $this->application->vacancy,
                'bodyHtml'    => nl2br(e($this->body)),
                'bodyPlain'   => $this->body,
                'senderName'  => $this->senderName,
                'sentAt'      => now()->format('D, d M Y H:i T'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
