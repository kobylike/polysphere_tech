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

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->registrationUrl = route('register', ['token' => $invitation->token]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're invited to join Polysphere Tech",
        );
    }

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
                'positionName'      => $this->invitation->position,
                'expiryDate'        => $this->invitation->expires_at?->format('F j, Y g:i A') ?? 'Never expires',
                'companyName'       => config('app.name', 'Polysphere Tech'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
