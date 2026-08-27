<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public string $senderName;
    public string $senderEmail;
    public string $contactSubject;
    public string $contactMessage;
    public string $category;

    public function __construct(
        string $name,
        string $email,
        string $subject,
        string $message,
        string $category = 'General',
    ) {
        $this->senderName     = $name;
        $this->senderEmail    = $email;
        $this->contactSubject = $subject;
        $this->contactMessage = $message;
        $this->category       = $category;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Envelope — sets Reply-To and Subject header
    // ─────────────────────────────────────────────────────────────────────────

    public function envelope(): Envelope
    {
        return new Envelope(
            // Send FROM the system mailer address so DMARC/SPF always pass.
            // Reply-To means staff can hit "Reply" and reach the visitor directly.
            replyTo: [
                new Address($this->senderEmail, $this->senderName),
            ],
            subject: "[{$this->category}] Contact: {$this->contactSubject}",
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Content — HTML view + plain-text fallback
    // ─────────────────────────────────────────────────────────────────────────

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            text: 'emails.contact-plain',
            with: [
                'senderName'     => $this->senderName,
                'senderEmail'    => $this->senderEmail,
                'messageSubject' => $this->contactSubject,
                'messageBody'    => $this->contactMessage,
                'category'       => $this->category,
                'sentAt'         => now()->format('D, d M Y H:i T'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
