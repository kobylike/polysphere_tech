<?php

namespace App\Livewire\Admin\Users\Account\Tabs;

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
    public $filterStatus = '';
    public $filterDate = 'all';
    public $dateFrom = '';
    public $dateTo = '';
    public $search = '';
    public $perPage = 10;

    // Detail modal
    public $showDetailModal = false;
    public $selectedNotification = null;

    protected $queryString = [
        'filterStatus' => ['except' => ''],
        'filterDate' => ['except' => 'all'],
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'page' => ['except' => 1],
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

    public function getStatsProperty()
    {
        $query = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id);

        return [
            'total' => $query->count(),
            'unread' => $query->clone()->whereNull('read_at')->count(),
            'read' => $query->clone()->whereNotNull('read_at')->count(),
            'today' => $query->clone()->whereDate('created_at', Carbon::today())->count(),
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
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->filterDate === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year);
        } elseif ($this->filterDate === 'custom' && $this->dateFrom && $this->dateTo) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay()
            ]);
        }

        return $query->orderBy('created_at', 'desc')->paginate($this->perPage);
    }

    public function openDetailModal($id)
    {
        $this->selectedNotification = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->findOrFail($id);

        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedNotification = null;
    }

    public function toggleReadStatus()
    {
        if (!$this->selectedNotification) return;

        if ($this->selectedNotification->isRead()) {
            $this->selectedNotification->markAsUnread();
            $this->dispatch('notify', [
                'type' => 'info',
                'title' => 'Marked as unread',
                'message' => 'Notification marked as unread.',
            ]);
        } else {
            $this->selectedNotification->markAsRead();
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Marked as read',
                'message' => 'Notification marked as read.',
            ]);
        }

        // Refresh the selected notification
        $this->selectedNotification = $this->selectedNotification->fresh();
        $this->dispatch('notification-read');
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->findOrFail($id);

        $notification->markAsRead();

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Marked as read',
            'message' => 'Notification marked as read.',
        ]);

        $this->dispatch('notification-read');
    }

    public function markAllAsRead()
    {
        Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $this->user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'All marked as read',
            'message' => 'All notifications have been marked as read.',
        ]);

        $this->dispatch('notification-marked-all-read');
    }

    public function resetFilters()
    {
        $this->reset(['filterStatus', 'filterDate', 'dateFrom', 'dateTo', 'search', 'perPage']);
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.users.account.tabs.notification-tab', [
            'stats' => $this->stats,
            'notifications' => $this->notifications,
        ]);
    }
}
