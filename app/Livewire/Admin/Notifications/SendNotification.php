<?php

namespace App\Livewire\Admin\Notifications;

use App\Helpers\ActivityLogger;
use App\Helpers\NotificationHelper;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.users')]
class SendNotification extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ─── Form fields ──────────────────────────────────────────────
    public $recipientType = 'all';
    public $selectedRoles = [];
    public $selectedUsers = [];
    public $selectedPositions = [];
    public $title = '';
    public $body = '';
    public $type = 'info';
    public $link = '';
    public $icon = 'fa-bell';

    // ─── Filters for history ──────────────────────────────────────
    public $search = '';
    public $statusFilter = '';

    protected $rules = [
        'recipientType' => 'required|in:all,employees,roles,positions,users,custom',
        'selectedRoles' => 'required_if:recipientType,roles|required_if:recipientType,custom|array',
        'selectedPositions' => 'required_if:recipientType,positions|required_if:recipientType,custom|array',
        'selectedUsers' => 'required_if:recipientType,users|required_if:recipientType,custom|array',
        'title'         => 'nullable|string|max:255',
        'body'          => 'required|string|max:5000',
        'type'          => 'required|in:info,success,warning,danger',
        'link'          => 'nullable|url|max:500',
        'icon'          => 'nullable|string|max:100',
    ];

    // ─── Query string for filters ─────────────────────────────────
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('Send Notifications');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function send()
    {
        $this->validate();

        $data = [
            'title'       => $this->title,
            'body'        => $this->body,
            'type'        => $this->type,
            'link'        => $this->link,
            'icon'        => $this->icon,
            'sender_name' => Auth::user()->name,
        ];

        $recipientDescription = '';

        switch ($this->recipientType) {
            case 'all':
                NotificationHelper::sendToAll($data);
                $message = 'Notification sent to all users.';
                $recipientDescription = 'All users';
                break;

            case 'employees':
                NotificationHelper::sendToEmployees($data);
                $message = 'Notification sent to all employees.';
                $recipientDescription = 'All employees';
                break;

            case 'roles':
                $roles = Role::whereIn('id', $this->selectedRoles)->get();
                $roleNames = $roles->pluck('name')->toArray();
                NotificationHelper::sendToRoles($roles, $data);
                $message = 'Notification sent to selected roles.';
                $recipientDescription = 'Roles: ' . implode(', ', $roleNames);
                break;

            case 'positions':
                NotificationHelper::sendToPositions($this->selectedPositions, $data);
                $message = 'Notification sent to selected positions.';
                $recipientDescription = 'Positions: ' . implode(', ', $this->selectedPositions);
                break;

            case 'users':
                $users = User::whereIn('id', $this->selectedUsers)->get();
                $userNames = $users->pluck('name')->toArray();
                NotificationHelper::sendToUsers($users, $data);
                $message = 'Notification sent to selected users.';
                $recipientDescription = 'Users: ' . implode(', ', $userNames);
                break;

            case 'custom':
                $recipients = [
                    'users'     => $this->selectedUsers,
                    'roles'     => $this->selectedRoles,
                    'positions' => $this->selectedPositions,
                ];
                NotificationHelper::send($recipients, $data);
                $message = 'Notification sent to custom recipients.';
                $recipientDescription = 'Custom recipients';
                break;
        }

        // ─── Log notification ───────────────────────────────────────────────────
        ActivityLogger::log('System notification sent', [
            'title' => $this->title ?: 'Untitled',
            'body' => $this->body,
            'type' => $this->type,
            'recipient_type' => $this->recipientType,
            'recipients' => $recipientDescription,
            'sender_name' => Auth::user()->name,
            'link' => $this->link,
            'icon' => $this->icon,
        ], 'notification');

        $this->reset(['title', 'body', 'link', 'icon']);

        // Dispatch toast
        $this->dispatch('notify', [
            'type' => 'success',
            'title' => 'Sent!',
            'message' => $message,
        ]);
    }

    // ─── Computed properties ──────────────────────────────────────

    public function getRolesProperty()
    {
        return Role::orderBy('name')->get();
    }

    public function getUsersProperty()
    {
        return User::orderBy('name')->get();
    }

    public function getPositionsListProperty()
    {
        return UserProfile::whereNotNull('position')
            ->distinct()
            ->pluck('position')
            ->filter()
            ->values()
            ->toArray();
    }

    public function getSentNotificationsProperty()
    {
        $query = \App\Models\Notification::with('notifiable')
            ->where('sender_name', Auth::user()->name)
            ->orWhere('sender_name', 'System');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('body', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter === 'read') {
            $query->whereNotNull('read_at');
        } elseif ($this->statusFilter === 'unread') {
            $query->whereNull('read_at');
        }

        return $query->latest()->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.notifications.send-notification', [
            'sentNotifications' => $this->sentNotifications,
        ]);
    }
}
