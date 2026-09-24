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

class NewChatLeadNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Where new chat leads are sent.
     * Change here — no config dependency.
     */
    public const RECIPIENT = 'contact@polyspheretech.com';

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public function __construct(public ChatLead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            // Reply-To points at the visitor, so staff can hit "Reply" and
            // land directly in the visitor's inbox — same pattern as ContactMail.
            replyTo: [
                new Address($this->lead->email, $this->lead->name ?: 'Chat visitor'),
            ],
            subject: '🎯 New chat lead: ' . $this->lead->email,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.chat-lead',
            text: 'emails.chat-lead-plain',
            with: [
                'lead'          => $this->lead,
                'sentAt'        => now()->format('D, d M Y H:i T'),
                'pageUrl'       => $this->lead->page_url ?: config('app.url'),
                'recentTurns'   => array_slice($this->lead->conversation ?? [], -8),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
