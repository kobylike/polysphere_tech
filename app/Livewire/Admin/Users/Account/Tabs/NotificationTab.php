<?php

namespace App\Livewire\Admin\Users\Account\Tabs;

use App\Helpers\ActivityLogger;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationTab extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $user;

    /* ──────────────────────────────────────────────────────────── */
    /*  Filters                                                    */
    /* ──────────────────────────────────────────────────────────── */

    public $filterStatus = '';
    public $filterDate   = 'all';
    public $dateFrom     = '';
    public $dateTo       = '';
    public $search       = '';
    public $perPage      = 10;

    /* ──────────────────────────────────────────────────────────── */
    /*  Bulk selection                                             */
    /* ──────────────────────────────────────────────────────────── */

    /** @var array<int|string> */
    public array $selectedIds = [];

    /* ──────────────────────────────────────────────────────────── */
    /*  Modals                                                     */
    /* ──────────────────────────────────────────────────────────── */

    public bool $showDetailModal  = false;
    public $selectedNotification  = null;

    public bool $showDeleteModal  = false;
    public ?int $deleteTargetId   = null;

    /**
     * Delete context.
     *   ''       → no pending delete
     *   'single' → delete one notification ($deleteTargetId)
     *   'bulk'   → delete all $selectedIds
     *   'all'    → delete every notification for this user
     *   'read'   → delete every READ notification for this user
     */
    public string $deleteMode = '';

    /** Counts cached for the modal copy. */
    public int $deleteCount = 0;

    /* ──────────────────────────────────────────────────────────── */
    /*  Query string                                               */
    /* ──────────────────────────────────────────────────────────── */

    protected $queryString = [
        'filterStatus' => ['except' => ''],
        'filterDate'   => ['except' => 'all'],
        'search'       => ['except' => ''],
        'perPage'      => ['except' => 10],
        'page'         => ['except' => 1],
    ];

    protected function getListeners()
    {
        return [
            'open-notification-detail' => 'openDetailModal',
        ];
    }

    public function mount()
    {
        $this->user = Auth::user();
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Stats & list accessors                                     */
    /* ──────────────────────────────────────────────────────────── */

    public function getStatsProperty()
    {
        $query = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id);

        return [
            'total'  => (clone $query)->count(),
            'unread' => (clone $query)->whereNull('read_at')->count(),
            'read'   => (clone $query)->whereNotNull('read_at')->count(),
            'today'  => (clone $query)->whereDate('created_at', Carbon::today())->count(),
        ];
    }

    public function getNotificationsProperty()
    {
        $query = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('body', 'like', '%' . $this->search . '%')
                    ->orWhere('type', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus === 'read') {
            $query->whereNotNull('read_at');
        } elseif ($this->filterStatus === 'unread') {
            $query->whereNull('read_at');
        }

        if ($this->filterDate === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($this->filterDate === 'week') {
            $query->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
        } elseif ($this->filterDate === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
        } elseif ($this->filterDate === 'custom' && $this->dateFrom && $this->dateTo) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ]);
        }

        return $query->orderByDesc('created_at')->paginate($this->perPage);
    }

    /**
     * Only the IDs currently visible on the page (used for select-all).
     */
    public function getVisibleIdsProperty(): array
    {
        return $this->notifications
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function getSelectedCountProperty(): int
    {
        return count($this->selectedIds);
    }

    public function getIsAllOnPageSelectedProperty(): bool
    {
        $visible = $this->visible_ids;

        if (empty($visible)) {
            return false;
        }

        return empty(array_diff($visible, $this->selectedIds));
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Bulk selection                                             */
    /* ──────────────────────────────────────────────────────────── */

    public function selectAllOnPage(): void
    {
        $this->selectedIds = $this->visible_ids;
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
    }

    public function toggleSelectAllOnPage(): void
    {
        if ($this->is_all_on_page_selected) {
            $this->clearSelection();
        } else {
            $this->selectAllOnPage();
        }
    }

    /**
     * Reset selection whenever filters change — otherwise a user
     * could have invisible items still selected.
     */
    public function updatingFilterStatus(): void
    {
        $this->clearSelection();
    }
    public function updatingFilterDate(): void
    {
        $this->clearSelection();
    }
    public function updatingSearch(): void
    {
        $this->clearSelection();
    }
    public function updatingDateFrom(): void
    {
        $this->clearSelection();
    }
    public function updatingDateTo(): void
    {
        $this->clearSelection();
    }
    public function updatingPerPage(): void
    {
        $this->clearSelection();
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Single-item actions                                        */
    /* ──────────────────────────────────────────────────────────── */

    public function openDetailModal($id)
    {
        $this->selectedNotification = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->findOrFail($id);

        $this->showDetailModal = true;

        ActivityLogger::log('Notification detail viewed', [
            'notification_id' => $this->selectedNotification->id,
            'title'           => $this->selectedNotification->title,
        ], 'notification');
    }

    public function closeDetailModal()
    {
        $this->showDetailModal       = false;
        $this->selectedNotification  = null;
    }

    public function toggleReadStatus()
    {
        if (! $this->selectedNotification) {
            return;
        }

        if ($this->selectedNotification->isRead()) {
            $this->selectedNotification->markAsUnread();

            ActivityLogger::log('Notification marked as unread', [
                'notification_id' => $this->selectedNotification->id,
                'title'           => $this->selectedNotification->title,
            ], 'notification');

            $this->dispatch('notify', [
                'type'    => 'info',
                'title'   => 'Marked as unread',
                'message' => 'Notification marked as unread.',
            ]);
        } else {
            $this->selectedNotification->markAsRead();

            ActivityLogger::log('Notification marked as read', [
                'notification_id' => $this->selectedNotification->id,
                'title'           => $this->selectedNotification->title,
            ], 'notification');

            $this->dispatch('notify', [
                'type'    => 'success',
                'title'   => 'Marked as read',
                'message' => 'Notification marked as read.',
            ]);
        }

        $this->selectedNotification = $this->selectedNotification->fresh();
        $this->dispatch('notification-read');
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->findOrFail($id);

        $notification->markAsRead();

        ActivityLogger::log('Notification marked as read (single)', [
            'notification_id' => $notification->id,
            'title'           => $notification->title,
        ], 'notification');

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Marked as read',
            'message' => 'Notification marked as read.',
        ]);

        $this->dispatch('notification-read');
    }

    public function markAllAsRead()
    {
        $count = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->whereNull('read_at')
            ->count();

        Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        ActivityLogger::log('All notifications marked as read', [
            'user_id' => $this->user->id,
            'count'   => $count,
        ], 'notification');

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'All marked as read',
            'message' => 'All notifications have been marked as read.',
        ]);

        $this->dispatch('notification-marked-all-read');
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Bulk actions                                               */
    /* ──────────────────────────────────────────────────────────── */

    public function bulkMarkAsRead(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $count = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->whereIn('id', $this->selectedIds)
            ->whereNull('read_at')
            ->count();

        Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->whereIn('id', $this->selectedIds)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        ActivityLogger::log('Notifications marked as read (bulk)', [
            'count' => $count,
        ], 'notification');

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Marked as read',
            'message' => "{$count} notification(s) marked as read.",
        ]);

        $this->clearSelection();
        $this->dispatch('notification-read');
    }

    public function bulkMarkAsUnread(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $count = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->whereIn('id', $this->selectedIds)
            ->whereNotNull('read_at')
            ->count();

        Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->whereIn('id', $this->selectedIds)
            ->whereNotNull('read_at')
            ->update(['read_at' => null]);

        ActivityLogger::log('Notifications marked as unread (bulk)', [
            'count' => $count,
        ], 'notification');

        $this->dispatch('notify', [
            'type'    => 'info',
            'title'   => 'Marked as unread',
            'message' => "{$count} notification(s) marked as unread.",
        ]);

        $this->clearSelection();
        $this->dispatch('notification-read');
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Delete — confirm + perform                                 */
    /* ──────────────────────────────────────────────────────────── */

    public function confirmDeleteSingle(int $id): void
    {
        // Must belong to this user
        $exists = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->where('id', $id)
            ->exists();

        if (! $exists) {
            return;
        }

        $this->deleteMode     = 'single';
        $this->deleteTargetId = $id;
        $this->deleteCount    = 1;
        $this->showDeleteModal = true;
    }

    public function confirmBulkDelete(): void
    {
        if (empty($this->selectedIds)) {
            return;
        }

        $this->deleteMode     = 'bulk';
        $this->deleteTargetId = null;
        $this->deleteCount    = count($this->selectedIds);
        $this->showDeleteModal = true;
    }

    public function confirmClearAll(): void
    {
        $count = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->count();

        if ($count === 0) {
            $this->dispatch('notify', [
                'type'    => 'info',
                'title'   => 'Nothing to clear',
                'message' => 'You have no notifications to delete.',
            ]);
            return;
        }

        $this->deleteMode     = 'all';
        $this->deleteTargetId = null;
        $this->deleteCount    = $count;
        $this->showDeleteModal = true;
    }

    public function confirmClearRead(): void
    {
        $count = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->whereNotNull('read_at')
            ->count();

        if ($count === 0) {
            $this->dispatch('notify', [
                'type'    => 'info',
                'title'   => 'Nothing to clear',
                'message' => 'You have no read notifications.',
            ]);
            return;
        }

        $this->deleteMode     = 'read';
        $this->deleteTargetId = null;
        $this->deleteCount    = $count;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteMode      = '';
        $this->deleteTargetId  = null;
        $this->deleteCount     = 0;
    }

    public function performDelete(): void
    {
        $base = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id);

        $deleted = 0;

        switch ($this->deleteMode) {
            case 'single':
                if ($this->deleteTargetId) {
                    $deleted = (clone $base)->where('id', $this->deleteTargetId)->delete();

                    // Close the detail modal if it was showing the deleted one
                    if ($this->selectedNotification && $this->selectedNotification->id === $this->deleteTargetId) {
                        $this->closeDetailModal();
                    }

                    // Clear from selection if present
                    $this->selectedIds = array_values(array_filter(
                        $this->selectedIds,
                        fn($id) => (string) $id !== (string) $this->deleteTargetId
                    ));
                }
                break;

            case 'bulk':
                if (! empty($this->selectedIds)) {
                    $deleted = (clone $base)->whereIn('id', $this->selectedIds)->delete();
                    $this->clearSelection();
                }
                break;

            case 'all':
                $deleted = (clone $base)->delete();
                $this->clearSelection();
                $this->resetPage();
                break;

            case 'read':
                $deleted = (clone $base)->whereNotNull('read_at')->delete();
                // Keep only unread selections
                $this->selectedIds = array_values(array_filter(
                    $this->selectedIds,
                    function ($id) {
                        return Notification::where('id', $id)
                            ->whereNull('read_at')
                            ->exists();
                    }
                ));
                break;
        }

        if ($deleted > 0) {
            ActivityLogger::log('Notifications deleted', [
                'mode'    => $this->deleteMode,
                'count'   => $deleted,
                'user_id' => $this->user->id,
            ], 'notification');

            $this->dispatch('notify', [
                'type'    => 'success',
                'title'   => 'Deleted',
                'message' => "{$deleted} notification(s) deleted.",
            ]);

            $this->dispatch('notification-read');

            // If we just emptied the current page, jump to page 1
            $this->ensureCurrentPageIsValid();
        }

        $this->closeDeleteModal();
    }

    /**
     * If the current page is now past the total pages, go back to page 1.
     */
    protected function ensureCurrentPageIsValid(): void
    {
        // Force recompute of notifications
        $notifications = $this->notifications;

        if ($notifications->total() === 0) {
            $this->resetPage();
            return;
        }

        if ($notifications->currentPage() > $notifications->lastPage()) {
            $this->resetPage();
        }
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Filters                                                    */
    /* ──────────────────────────────────────────────────────────── */

    public function resetFilters()
    {
        $this->reset(['filterStatus', 'filterDate', 'dateFrom', 'dateTo', 'search', 'perPage']);
        $this->clearSelection();
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.users.account.tabs.notification-tab', [
            'stats'         => $this->stats,
            'notifications' => $this->notifications,
        ]);
    }
}
