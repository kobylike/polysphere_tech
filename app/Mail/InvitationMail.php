<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public Invitation $invitation;
    public string $registrationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->registrationUrl = route('register', ['token' => $invitation->token]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're invited to join Polysphere Tech",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
            text: 'emails.invitation-plain',
            with: [
                'invitation'        => $this->invitation,
                'registrationUrl'   => $this->registrationUrl,
                'invitedByName'     => $this->invitation->invitedBy->name,
                'roleName'          => $this->invitation->role?->name ?? 'User',
                'expiryDate'        => $this->invitation->expires_at?->format('F j, Y g:i A') ?? 'Never expires',
                'companyName'       => config('app.name', 'Polysphere Tech'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
