<?php

namespace App\Mail;

use App\Models\ChatLead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitorLeadAcknowledgement extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public function __construct(public ChatLead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            // Replies from the visitor route straight back to the human inbox.
            replyTo: [
                new Address('contact@polyspheretech.com', 'Polysphere Tech'),
            ],
            subject: 'We got your message — Polysphere Tech',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.visitor-lead-acknowledgement',
            text: 'emails.visitor-lead-acknowledgement-plain',
            with: [
                'lead'         => $this->lead,
                'firstName'    => $this->firstName(),
                'receivedAt'   => now()->format('D, d M Y H:i T'),
                'contactEmail' => 'contact@polyspheretech.com',
                'contactPhone' => '+233 (59) 756-3427',
                'siteUrl'      => rtrim(config('app.url'), '/'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    /**
     * Just the first word of the visitor's name, so the greeting
     * reads naturally — "Hi Ama" not "Hi Ama Mensah Boateng".
     */
    protected function firstName(): ?string
    {
        $name = trim((string) $this->lead->name);

        if ($name === '') {
            return null;
        }

        $parts = preg_split('/\s+/', $name);
        return $parts[0] ?? null;
    }
}
