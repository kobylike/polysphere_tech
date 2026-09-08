<?php

namespace App\Livewire\Admin\Newsletter;

use App\Jobs\SendNewsletterJob;
use App\Mail\NewsletterConfirmationMail;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.users')]
class SubscriberManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ─── Filters ──────────────────────────────────────────────────────────────
    public $search = '';
    public $statusFilter = '';

    // ─── Bulk actions ─────────────────────────────────────────────────────────
    public $selectedIds = [];
    public $selectAll = false;

    // ─── Newsletter modal ────────────────────────────────────────────────────
    public $showNewsletterModal = false;
    public $newsletterSubject = '';
    public $newsletterBody = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->authorize('View Newsletter Subscribers');
    }

    // ─── Real‑time search ────────────────────────────────────────────────────
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    // ─── Get subscribers query ──────────────────────────────────────────────
    public function getSubscribersProperty()
    {
        return Subscriber::query()
            ->when($this->search, function ($query) {
                $query->where('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    // ─── Toggle select all ──────────────────────────────────────────────────
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->subscribers->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    // ─── Resend verification ─────────────────────────────────────────────────
    public function resendVerification($id)
    {
        $subscriber = Subscriber::findOrFail($id);

        if ($subscriber->status !== 'pending') {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'Already Verified',
                'message' => 'This subscriber is already active or unsubscribed.',
            ]);
            return;
        }

        $subscriber->verification_token = Str::random(60);
        $subscriber->save();

        $verificationUrl = route('newsletter.verify', [
            'token' => $subscriber->verification_token,
            'email' => $subscriber->email,
        ]);

        Mail::to($subscriber->email)->queue(
            new NewsletterConfirmationMail($subscriber, $verificationUrl)
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Verification Sent',
            'message' => "Verification email resent to {$subscriber->email}.",
        ]);
    }

    // ─── Mark as active ──────────────────────────────────────────────────────
    public function markActive($id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->markAsActive();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Updated',
            'message' => "{$subscriber->email} is now active.",
        ]);
    }

    // ─── Mark as unsubscribed ──────────────────────────────────────────────
    public function markUnsubscribed($id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->markAsUnsubscribed();

        $this->dispatch('notify', [
            'type' => 'info',
            'title' => 'Unsubscribed',
            'message' => "{$subscriber->email} has been unsubscribed.",
        ]);
    }

    // ─── Delete subscriber ──────────────────────────────────────────────────
    public function delete($id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $email = $subscriber->email;
        $subscriber->delete();

        $this->dispatch('notify', [
            'type' => 'danger',
            'title' => 'Deleted',
            'message' => "Subscriber {$email} removed.",
        ]);
    }

    // ─── Bulk delete ─────────────────────────────────────────────────────────
    public function bulkDelete()
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Selection',
                'message' => 'Please select at least one subscriber.',
            ]);
            return;
        }

        $count = Subscriber::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;

        $this->dispatch('notify', [
            'type' => 'danger',
            'title' => 'Deleted',
            'message' => "{$count} subscribers removed permanently.",
        ]);
    }

    // ─── Export to CSV ───────────────────────────────────────────────────────
    public function exportCsv()
    {
        $subscribers = Subscriber::query()
            ->when($this->search, function ($query) {
                $query->where('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'subscribers_' . now()->format('Y-m-d_Hi') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($subscribers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Email', 'Status', 'Subscribed At', 'Unsubscribed At', 'IP Address']);

            foreach ($subscribers as $sub) {
                fputcsv($file, [
                    $sub->id,
                    $sub->email,
                    $sub->status,
                    $sub->subscribed_at?->format('Y-m-d H:i'),
                    $sub->unsubscribed_at?->format('Y-m-d H:i'),
                    $sub->subscribed_ip,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    // ─── Open newsletter modal ──────────────────────────────────────────────
    public function openNewsletterModal()
    {
        $this->showNewsletterModal = true;
        $this->newsletterSubject = '';
        $this->newsletterBody = '';
    }

    // ─── Send newsletter ─────────────────────────────────────────────────────
    public function sendNewsletter()
    {
        $this->validate([
            'newsletterSubject' => 'required|string|max:255',
            'newsletterBody' => 'required|string',
        ]);

        $subscribers = Subscriber::where('status', 'active')->get();
        if ($subscribers->isEmpty()) {
            $this->dispatch('notify', [
                'type' => 'warning',
                'title' => 'No Subscribers',
                'message' => 'No active subscribers to send to.',
            ]);
            return;
        }

        foreach ($subscribers as $sub) {
            dispatch(new SendNewsletterJob($sub, $this->newsletterSubject, $this->newsletterBody));
        }

        $this->showNewsletterModal = false;
        $this->newsletterSubject = '';
        $this->newsletterBody = '';

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Newsletter Sent!',
            'message' => "Queued newsletter to {$subscribers->count()} subscribers.",
        ]);
    }

    public function render()
    {
        return view('livewire.admin.newsletter.subscriber-management', [
            'subscribers' => $this->subscribers,
        ]);
    }
}
