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

class ApplicationReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application->loadMissing('vacancy.department');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Envelope — sends from the system mailer; reply goes to careers inbox
    // ─────────────────────────────────────────────────────────────────────────

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address(
                    'careers@polyspheretech.com',
                    'Polysphere Tech Hiring',
                ),
            ],
            subject: 'We received your application — ' . $this->application->vacancy->title,
        );
    }
    // ─────────────────────────────────────────────────────────────────────────
    // Content — HTML view + plain-text fallback
    // ─────────────────────────────────────────────────────────────────────────

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-received',
            text: 'emails.application-received-plain',
            with: [
                'application' => $this->application,
                'vacancy'     => $this->application->vacancy,
                'department'  => $this->application->vacancy->department?->name ?? 'General',
                'statusUrl'   => $this->application->statusUrl(),
                'reference'   => strtoupper(substr($this->application->tracking_token, 0, 12)),
                'sentAt'      => now()->format('D, d M Y H:i T'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
