<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public User $user;
    public string $temporaryPassword;
    public ?string $createdByName;
    public string $loginUrl;

    public function __construct(User $user, string $temporaryPassword, ?string $createdByName = null)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
        $this->createdByName = $createdByName;
        $this->loginUrl = route('login');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your account has been created — " . config('app.name', 'Polysphere Tech'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.account-created',
            text: 'emails.account-created-plain',
            with: [
                'user'              => $this->user,
                'temporaryPassword' => $this->temporaryPassword,
                'createdByName'     => $this->createdByName,
                'loginUrl'          => $this->loginUrl,
                'roleName'          => $this->user->roles->first()?->name ?? 'User',
                'positionName'      => $this->user->position,
                'companyName'       => config('app.name', 'Polysphere Tech'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
