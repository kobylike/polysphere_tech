<?php

namespace App\Livewire\Admin\Leads;

use App\Helpers\ActivityLogger;
use App\Helpers\NotificationHelper;
use App\Mail\NewChatLeadNotification;
use App\Models\ChatLead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.users')]
class LeadManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /* ──────────────────────────────────────────────────────────── */
    /*  Filters                                                    */
    /* ──────────────────────────────────────────────────────────── */

    public string $search        = '';
    public string $statusFilter  = '';
    public string $sourceFilter  = '';
    public string $scoreFilter   = '';
    public string $starredFilter = '';
    public string $hasFilter     = '';
    public string $spamFilter    = '';
    public string $dateRange     = '';
    public string $dateFrom      = '';
    public string $dateTo        = '';
    public string $sortBy        = 'created_at';
    public string $sortDir       = 'desc';
    public int    $perPage       = 20;

    /* ──────────────────────────────────────────────────────────── */
    /*  Real-time polling                                          */
    /* ──────────────────────────────────────────────────────────── */

    /** User-toggleable pause. Persists in the component state. */
    public bool $pollingPaused = false;

    /** Bumped on every poll — lets the view show "updated Xs ago". */
    public ?string $lastPolledAt = null;

    /* ──────────────────────────────────────────────────────────── */
    /*  Modals                                                     */
    /* ──────────────────────────────────────────────────────────── */

    public bool $showViewModal       = false;
    public bool $showDeleteModal     = false;
    public bool $showBulkDeleteModal = false;
    public bool $showStatusModal     = false;
    public bool $showNoteModal       = false;
    public bool $showTagModal        = false;
    public bool $showExportModal     = false;
    public bool $showReplyModal      = false;

    /* ──────────────────────────────────────────────────────────── */
    /*  Selected / form state                                      */
    /* ──────────────────────────────────────────────────────────── */

    public ?int      $selectedLeadId = null;
    public ?ChatLead $viewingLead    = null;
    public array     $selectedLeads  = [];
    public bool      $selectAll      = false;

    public string $newStatus   = '';
    public string $statusNote  = '';
    public string $noteText    = '';
    public string $tagInput    = '';
    public string $exportScope = 'filtered';

    /** Reply modal state */
    public ?ChatLead $replyLead    = null;
    public string    $replySubject = '';
    public string    $replyBody    = '';

    /* ──────────────────────────────────────────────────────────── */
    /*  Query string                                               */
    /* ──────────────────────────────────────────────────────────── */

    protected $queryString = [
        'search'        => ['except' => ''],
        'statusFilter'  => ['except' => ''],
        'sourceFilter'  => ['except' => ''],
        'scoreFilter'   => ['except' => ''],
        'starredFilter' => ['except' => ''],
        'hasFilter'     => ['except' => ''],
        'spamFilter'    => ['except' => ''],
        'dateRange'     => ['except' => ''],
        'dateFrom'      => ['except' => ''],
        'dateTo'        => ['except' => ''],
        'sortBy'        => ['except' => 'created_at'],
        'sortDir'       => ['except' => 'desc'],
        'perPage'       => ['except' => 20],
    ];

    /* ──────────────────────────────────────────────────────────── */
    /*  Lifecycle                                                  */
    /* ──────────────────────────────────────────────────────────── */

    public function mount(): void
    {
        $this->authorize('viewAny', ChatLead::class);

        if ($this->spamFilter === '') {
            $this->spamFilter = 'exclude';
        }
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Polling logic                                              */
    /* ──────────────────────────────────────────────────────────── */

    /**
     * Called by wire:poll every N seconds.
     *
     * When a modal is open (or the user has paused polling), skip the
     * render entirely so form input is never clobbered mid-typing.
     */
    public function pollTick(): void
    {
        if (! $this->shouldPoll) {
            $this->skipRender();
            return;
        }

        $this->lastPolledAt = now()->toIso8601String();
    }

    public function getShouldPollProperty(): bool
    {
        return ! $this->pollingPaused && ! $this->isAnyModalOpen();
    }

    public function isAnyModalOpen(): bool
    {
        return $this->showViewModal
            || $this->showDeleteModal
            || $this->showBulkDeleteModal
            || $this->showStatusModal
            || $this->showNoteModal
            || $this->showTagModal
            || $this->showExportModal
            || $this->showReplyModal;
    }

    public function togglePolling(): void
    {
        $this->pollingPaused = ! $this->pollingPaused;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Filter hooks — reset pagination                            */
    /* ──────────────────────────────────────────────────────────── */

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }
    public function updatingSourceFilter(): void
    {
        $this->resetPage();
    }
    public function updatingScoreFilter(): void
    {
        $this->resetPage();
    }
    public function updatingStarredFilter(): void
    {
        $this->resetPage();
    }
    public function updatingHasFilter(): void
    {
        $this->resetPage();
    }
    public function updatingSpamFilter(): void
    {
        $this->resetPage();
    }
    public function updatingDateFrom(): void
    {
        $this->resetPage();
    }
    public function updatingDateTo(): void
    {
        $this->resetPage();
    }

    public function updatingDateRange(): void
    {
        $this->applyDateRangePreset();
        $this->resetPage();
    }

    protected function applyDateRangePreset(): void
    {
        match ($this->dateRange) {
            'today' => [$this->dateFrom, $this->dateTo] = [today()->toDateString(), today()->toDateString()],
            'week'  => [$this->dateFrom, $this->dateTo] = [now()->subDays(7)->toDateString(), today()->toDateString()],
            'month' => [$this->dateFrom, $this->dateTo] = [now()->subDays(30)->toDateString(), today()->toDateString()],
            'custom' => null,
            default => [$this->dateFrom, $this->dateTo] = ['', ''],
        };
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'statusFilter',
            'sourceFilter',
            'scoreFilter',
            'starredFilter',
            'hasFilter',
            'dateRange',
            'dateFrom',
            'dateTo',
        ]);
        $this->spamFilter = 'exclude';
        $this->sortBy  = 'created_at';
        $this->sortDir = 'desc';
        $this->perPage = 20;
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Bulk selection                                             */
    /* ──────────────────────────────────────────────────────────── */

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedLeads = $value
            ? $this->baseQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Per-row actions                                            */
    /* ──────────────────────────────────────────────────────────── */

    public function viewLead(int $id): void
    {
        $lead = ChatLead::with('contactedBy')->findOrFail($id);
        $this->authorize('view', $lead);

        $this->viewingLead    = $lead;
        $this->selectedLeadId = $id;
        $this->statusNote     = '';
        $this->noteText       = $lead->notes ?? '';
        $this->showViewModal  = true;
    }

    public function toggleStar(int $id): void
    {
        $lead = ChatLead::findOrFail($id);
        $this->authorize('star', $lead);

        $lead->is_starred = ! $lead->is_starred;
        $lead->save();

        if ($this->viewingLead && $this->viewingLead->id === $id) {
            $this->viewingLead = $lead->fresh('contactedBy');
        }
    }

    public function toggleSpam(int $id): void
    {
        $lead = ChatLead::findOrFail($id);
        $this->authorize('markSpam', $lead);

        $lead->is_spam = ! $lead->is_spam;
        if ($lead->is_spam) {
            $lead->status = 'spam';
        } elseif ($lead->status === 'spam') {
            $lead->status = 'new';
        }
        $lead->save();

        if ($this->viewingLead && $this->viewingLead->id === $id) {
            $this->viewingLead = $lead->fresh('contactedBy');
        }

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => $lead->is_spam ? 'Marked as spam' : 'Unmarked spam',
            'message' => $lead->email,
        ]);
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Reply — modal is side-effect-free until send              */
    /* ──────────────────────────────────────────────────────────── */

    public function openReply(int $id): void
    {
        $lead = ChatLead::findOrFail($id);
        $this->authorize('view', $lead);

        $this->replyLead    = $lead;
        $this->replySubject = 'Re: Your enquiry to Polysphere Tech';

        $name   = $lead->name ?: 'there';
        $author = Auth::user()?->name ?? 'The Polysphere Tech Team';

        $this->replyBody = "Hi {$name},\n\n"
            . "Thanks for reaching out to Polysphere Tech. I've received your message and "
            . "wanted to follow up personally.\n\n"
            . "Would you be open to a quick call this week to talk through what you're looking for?\n\n"
            . "Best regards,\n"
            . $author
            . "\nPolysphere Tech";

        $this->showReplyModal = true;

        // No side effects here. The lead is only marked as contacted
        // once the reply is actually sent (in sendReply()).
    }

    public function sendReply(): void
    {
        if (! $this->replyLead) {
            return;
        }

        $this->authorize('view', $this->replyLead);

        // Commit the lead as contacted now that the admin has actually
        // decided to send — not when the modal was opened.
        $lead = $this->replyLead;

        if ($lead->status === 'new') {
            $lead->status       = 'contacted';
            $lead->contacted_at = now();
            $lead->contacted_by = Auth::id();

            $stamp      = now()->format('M j, Y g:i A');
            $authorName = Auth::user()?->name ?? 'Admin';
            $note       = "[{$stamp} · {$authorName}] Reply email opened from admin.";
            $lead->notes = trim(($lead->notes ? $lead->notes . "\n\n" : '') . $note);

            $lead->save();

            Cache::forget('sidebar.new_lead_count');

            if (class_exists(ActivityLogger::class)) {
                ActivityLogger::log('Lead reply sent', [
                    'lead_id'  => $lead->id,
                    'email'    => $lead->email,
                    'sent_by'  => Auth::id(),
                ], 'lead');
            }
        }

        $url = 'mailto:' . $lead->email
            . '?subject=' . rawurlencode($this->replySubject)
            . '&body='    . rawurlencode($this->replyBody);

        $this->showReplyModal = false;

        $this->dispatch('open-mailto', url: $url);

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Reply ready',
            'message' => 'Your email client should open. If not, copy the address from the lead row.',
        ]);
    }

    public function closeReply(): void
    {
        $this->showReplyModal = false;
        $this->replyLead      = null;
        $this->replySubject   = '';
        $this->replyBody      = '';
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Status change                                              */
    /* ──────────────────────────────────────────────────────────── */

    public function openStatusModal(int $id): void
    {
        $lead = ChatLead::findOrFail($id);
        $this->authorize('changeStatus', $lead);

        $this->selectedLeadId  = $id;
        $this->newStatus       = $lead->status;
        $this->statusNote      = '';
        $this->showStatusModal = true;
    }

    public function saveStatus(): void
    {
        $this->validate([
            'newStatus' => 'required|in:' . implode(',', array_keys(ChatLead::STATUSES)),
        ]);

        if (! $this->selectedLeadId) {
            return;
        }

        $lead = ChatLead::findOrFail($this->selectedLeadId);
        $this->authorize('changeStatus', $lead);

        $oldStatus = $lead->status;
        $lead->status = $this->newStatus;

        if ($this->newStatus === 'contacted' && ! $lead->contacted_at) {
            $lead->contacted_at = now();
            $lead->contacted_by = Auth::id();
        }

        if ($this->newStatus === 'spam') {
            $lead->is_spam = true;
        } elseif ($oldStatus === 'spam') {
            $lead->is_spam = false;
        }

        if (trim($this->statusNote) !== '') {
            $stamp    = now()->format('M j, Y g:i A');
            $author   = Auth::user()?->name ?? 'Admin';
            $addition = "[{$stamp} · {$author}] {$this->statusNote}";
            $lead->notes = trim(($lead->notes ? $lead->notes . "\n\n" : '') . $addition);
        }

        $lead->save();

        if (class_exists(ActivityLogger::class)) {
            ActivityLogger::log('Lead status changed', [
                'lead_id'    => $lead->id,
                'email'      => $lead->email,
                'from'       => $oldStatus,
                'to'         => $this->newStatus,
                'changed_by' => Auth::id(),
            ], 'lead');
        }

        Cache::forget('sidebar.new_lead_count');

        $this->notifyTeamOfConversion($lead, $oldStatus);

        $this->showStatusModal = false;
        $this->selectedLeadId  = null;
        $this->newStatus       = '';
        $this->statusNote      = '';

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Status updated',
            'message' => "Lead marked as " . ($lead->status_label) . ".",
        ]);
    }

    protected function notifyTeamOfConversion(ChatLead $lead, string $oldStatus): void
    {
        if ($lead->status !== 'converted' || $oldStatus === 'converted') {
            return;
        }

        if (! class_exists(NotificationHelper::class)) {
            return;
        }

        try {
            $recipients = User::permission('View Chat Leads')->get();
        } catch (\Throwable $e) {
            report($e);
            return;
        }

        if ($recipients->isEmpty()) {
            return;
        }

        $actingUserId = Auth::id();
        $who          = $lead->name ?: $lead->email;

        foreach ($recipients as $user) {
            if ($actingUserId && $user->id === $actingUserId) {
                continue;
            }

            try {
                NotificationHelper::sendToUser($user, [
                    'title' => '🏆 Lead converted: ' . $who,
                    'body'  => "A chat lead just converted into a client. Nice work! ({$lead->email})",
                    'type'  => 'success',
                    'icon'  => 'fa-trophy',
                    'link'  => route('admin.leads', ['statusFilter' => 'converted']),
                ]);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Notes                                                      */
    /* ──────────────────────────────────────────────────────────── */

    public function openNoteModal(int $id): void
    {
        $lead = ChatLead::findOrFail($id);
        $this->authorize('manageNotes', $lead);

        $this->selectedLeadId = $id;
        $this->noteText       = $lead->notes ?? '';
        $this->showNoteModal  = true;
    }

    public function saveNote(): void
    {
        if (! $this->selectedLeadId) {
            return;
        }

        $lead = ChatLead::findOrFail($this->selectedLeadId);
        $this->authorize('manageNotes', $lead);

        $lead->notes = trim($this->noteText);
        $lead->save();

        $this->showNoteModal  = false;
        $this->selectedLeadId = null;
        $this->noteText       = '';

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Note saved',
            'message' => 'Internal note updated.',
        ]);
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Tags                                                       */
    /* ──────────────────────────────────────────────────────────── */

    public function openTagModal(int $id): void
    {
        $lead = ChatLead::findOrFail($id);
        $this->authorize('manageTags', $lead);

        $this->selectedLeadId = $id;
        $this->tagInput       = '';
        $this->showTagModal   = true;
    }

    public function addTag(): void
    {
        $tag = trim(strtolower($this->tagInput));
        $tag = preg_replace('/[^a-z0-9\-_]/', '', $tag) ?? '';

        if ($tag === '' || ! $this->selectedLeadId) {
            return;
        }

        $lead = ChatLead::findOrFail($this->selectedLeadId);
        $this->authorize('manageTags', $lead);

        $tags = $lead->tags ?? [];
        if (! in_array($tag, $tags, true)) {
            $tags[] = $tag;
            $lead->tags = array_values($tags);
            $lead->save();
        }
        $this->tagInput = '';
    }

    public function removeTag(string $tag): void
    {
        if (! $this->selectedLeadId) {
            return;
        }

        $lead = ChatLead::findOrFail($this->selectedLeadId);
        $this->authorize('manageTags', $lead);

        $tags = $lead->tags ?? [];
        $tags = array_values(array_filter($tags, fn($t) => $t !== $tag));
        $lead->tags = $tags;
        $lead->save();
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Delete                                                     */
    /* ──────────────────────────────────────────────────────────── */

    public function confirmDelete(int $id): void
    {
        $lead = ChatLead::findOrFail($id);
        $this->authorize('delete', $lead);

        $this->selectedLeadId  = $id;
        $this->showDeleteModal = true;
    }

    public function deleteLead(): void
    {
        if (! $this->selectedLeadId) {
            return;
        }

        $lead = ChatLead::findOrFail($this->selectedLeadId);
        $this->authorize('delete', $lead);

        $lead->delete();

        Cache::forget('sidebar.new_lead_count');

        $this->showDeleteModal = false;
        $this->selectedLeadId  = null;

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Lead deleted',
            'message' => 'The lead has been removed.',
        ]);
    }

    public function confirmBulkDelete(): void
    {
        if (empty($this->selectedLeads)) {
            return;
        }
        $this->authorize('deleteAny', ChatLead::class);
        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete(): void
    {
        $this->authorize('deleteAny', ChatLead::class);

        $count = count($this->selectedLeads);
        ChatLead::whereIn('id', $this->selectedLeads)->delete();

        Cache::forget('sidebar.new_lead_count');

        $this->selectedLeads = [];
        $this->selectAll     = false;
        $this->showBulkDeleteModal = false;

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Deleted',
            'message' => "{$count} lead(s) deleted.",
        ]);
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Bulk actions                                               */
    /* ──────────────────────────────────────────────────────────── */

    public function bulkMarkContacted(): void
    {
        if (empty($this->selectedLeads)) {
            return;
        }
        $this->authorize('update', ChatLead::class);

        ChatLead::whereIn('id', $this->selectedLeads)->update([
            'status'       => 'contacted',
            'contacted_at' => now(),
            'contacted_by' => Auth::id(),
        ]);

        Cache::forget('sidebar.new_lead_count');

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Updated',
            'message' => count($this->selectedLeads) . ' lead(s) marked contacted.',
        ]);
        $this->selectedLeads = [];
        $this->selectAll     = false;
    }

    public function bulkMarkSpam(): void
    {
        if (empty($this->selectedLeads)) {
            return;
        }
        $this->authorize('update', ChatLead::class);

        ChatLead::whereIn('id', $this->selectedLeads)->update([
            'status'  => 'spam',
            'is_spam' => true,
        ]);

        Cache::forget('sidebar.new_lead_count');

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Spam',
            'message' => count($this->selectedLeads) . ' lead(s) marked as spam.',
        ]);
        $this->selectedLeads = [];
        $this->selectAll     = false;
    }

    public function bulkStar(): void
    {
        if (empty($this->selectedLeads)) {
            return;
        }
        $this->authorize('update', ChatLead::class);

        ChatLead::whereIn('id', $this->selectedLeads)->update(['is_starred' => true]);

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Starred',
            'message' => count($this->selectedLeads) . ' lead(s) starred.',
        ]);
        $this->selectedLeads = [];
        $this->selectAll     = false;
    }

    public function bulkResendNotification(): void
    {
        if (empty($this->selectedLeads)) {
            return;
        }
        $this->authorize('update', ChatLead::class);

        $leads = ChatLead::whereIn('id', $this->selectedLeads)->get();
        $sent  = 0;

        foreach ($leads as $lead) {
            try {
                Mail::to(NewChatLeadNotification::RECIPIENT)
                    ->queue(new NewChatLeadNotification($lead));
                $lead->update(['notified_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Resent',
            'message' => "{$sent} notification email(s) queued.",
        ]);
        $this->selectedLeads = [];
        $this->selectAll     = false;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Export                                                     */
    /* ──────────────────────────────────────────────────────────── */

    public function openExportModal(): void
    {
        $this->authorize('export', ChatLead::class);
        $this->exportScope     = 'filtered';
        $this->showExportModal = true;
    }

    public function exportCsv()
    {
        $this->authorize('export', ChatLead::class);

        $query = match ($this->exportScope) {
            'selected' => ChatLead::whereIn('id', $this->selectedLeads),
            'filtered' => $this->baseQuery(),
            default    => ChatLead::query(),
        };

        $leads = $query->orderByDesc('created_at')->get();

        $filename = 'polysphere-leads-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($leads) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'ID',
                'Created',
                'Status',
                'Score',
                'Starred',
                'Spam',
                'Email',
                'Name',
                'Phone',
                'Company',
                'Source',
                'Intent',
                'Message',
                'Page',
                'IP',
                'Notified At',
                'Contacted At',
            ]);

            foreach ($leads as $lead) {
                fputcsv($out, [
                    $lead->id,
                    optional($lead->created_at)->format('Y-m-d H:i:s'),
                    $lead->status,
                    $lead->score,
                    $lead->is_starred ? 'yes' : 'no',
                    $lead->is_spam ? 'yes' : 'no',
                    $lead->email,
                    $lead->name,
                    $lead->phone,
                    $lead->company,
                    $lead->source,
                    $lead->intent,
                    $lead->message,
                    $lead->page_url,
                    $lead->ip_address,
                    optional($lead->notified_at)->format('Y-m-d H:i:s'),
                    optional($lead->contacted_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Query building                                             */
    /* ──────────────────────────────────────────────────────────── */

    protected function baseQuery()
    {
        return ChatLead::query()
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('email', 'like', $term)
                        ->orWhere('name', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('company', 'like', $term)
                        ->orWhere('message', 'like', $term)
                        ->orWhere('intent', 'like', $term)
                        ->orWhere('ip_address', 'like', $term);
                });
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->sourceFilter, fn($q) => $q->where('source', $this->sourceFilter))
            ->when($this->scoreFilter === 'hot',  fn($q) => $q->where('score', '>=', 70))
            ->when($this->scoreFilter === 'warm', fn($q) => $q->whereBetween('score', [40, 69]))
            ->when($this->scoreFilter === 'cold', fn($q) => $q->where('score', '<', 40))
            ->when($this->starredFilter === 'starred', fn($q) => $q->where('is_starred', true))
            ->when($this->hasFilter === 'has-phone',   fn($q) => $q->whereNotNull('phone')->where('phone', '!=', ''))
            ->when($this->hasFilter === 'has-company', fn($q) => $q->whereNotNull('company')->where('company', '!=', ''))
            ->when($this->hasFilter === 'has-name',    fn($q) => $q->whereNotNull('name')->where('name', '!=', ''))
            ->when($this->spamFilter === 'exclude', fn($q) => $q->where('is_spam', false))
            ->when($this->spamFilter === 'only',    fn($q) => $q->where('is_spam', true))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortBy, $this->sortDir);
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Stats                                                      */
    /* ──────────────────────────────────────────────────────────── */

    protected function buildStats(): array
    {
        return [
            'total'     => ChatLead::notSpam()->count(),
            'new'       => ChatLead::notSpam()->where('status', 'new')->count(),
            'today'     => ChatLead::notSpam()->whereDate('created_at', today())->count(),
            'week'      => ChatLead::notSpam()->where('created_at', '>=', now()->subDays(7))->count(),
            'contacted' => ChatLead::notSpam()->whereIn('status', ['contacted', 'qualified', 'converted'])->count(),
            'converted' => ChatLead::notSpam()->where('status', 'converted')->count(),
            'hot'       => ChatLead::notSpam()->where('score', '>=', 70)->count(),
            'starred'   => ChatLead::starred()->notSpam()->count(),
            'spam'      => ChatLead::onlySpam()->count(),
        ];
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Render                                                     */
    /* ──────────────────────────────────────────────────────────── */

    public function render()
    {
        $this->authorize('viewAny', ChatLead::class);

        return view('livewire.admin.leads.lead-management', [
            'leads'    => $this->baseQuery()->paginate($this->perPage),
            'stats'    => $this->buildStats(),
            'statuses' => ChatLead::STATUSES,
            'sources'  => ChatLead::SOURCES,
        ]);
    }
}
