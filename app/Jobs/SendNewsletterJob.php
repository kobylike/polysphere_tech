<?php

namespace App\Jobs;

use App\Models\Subscriber;
use App\Mail\NewsletterMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNewsletterJob implements ShouldQueue
{
    use Queueable;

    public Subscriber $subscriber;
    public string $subject;
    public string $body;

    public function __construct(Subscriber $subscriber, string $subject, string $body)
    {
        $this->subscriber = $subscriber;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle(): void
    {
        try {
            Mail::to($this->subscriber->email)
                ->send(new NewsletterMail($this->subject, $this->body, $this->subscriber));
        } catch (\Exception $e) {
            Log::error('Newsletter send failed for ' . $this->subscriber->email . ': ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e; // Re-throw to mark job as failed
        }
    }
}
