<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ApplicationSubmittedAdminMail extends Mailable implements ShouldQueue
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
    // Envelope — sends to hiring inbox; Reply-To reaches the candidate directly
    // ─────────────────────────────────────────────────────────────────────────

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address($this->application->email, $this->application->name),
            ],
            subject: sprintf(
                '[New Application] %s — %s',
                $this->application->vacancy->title,
                $this->application->name,
            ),
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Content — HTML view + plain-text fallback
    // ─────────────────────────────────────────────────────────────────────────

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-submitted-admin',
            text: 'emails.application-submitted-admin-plain',
            with: [
                'application' => $this->application,
                'vacancy'     => $this->application->vacancy,
                'department'  => $this->application->vacancy->department?->name ?? 'General',
                'reviewUrl'   => url('/applications?search=' . urlencode($this->application->email)),
                'sentAt'      => now()->format('D, d M Y H:i T'),
            ],
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Attachments — the CV, pulled straight from the private disk
    // ─────────────────────────────────────────────────────────────────────────

    public function attachments(): array
    {
        if (! Storage::disk('private')->exists($this->application->cv_path)) {
            return [];
        }

        return [
            Attachment::fromStorageDisk('private', $this->application->cv_path)
                ->as($this->application->cv_original_name ?: ('cv-' . $this->application->id . '.pdf')),
        ];
    }
}
