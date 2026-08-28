<?php
// app/Mail/CommentVerificationMail.php

namespace App\Mail;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommentVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Comment $comment,
        public string $verificationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Please verify your comment on ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.comment-verification',
            with: [
                'comment' => $this->comment,
                'url' => $this->verificationUrl,
            ],
        );
    }
}
