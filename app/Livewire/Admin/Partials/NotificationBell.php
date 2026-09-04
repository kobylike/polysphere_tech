<?php

namespace App\Livewire\Admin\Partials;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

#[On('echo-private:notifications.{userId},.new.notification')]
class NotificationBell extends Component
{
    public $userId;
    public $unreadCount = 0;
    public $notifications = [];
    public $limit = 5;

    protected function getListeners()
    {
        return [
            'notification-received'        => 'refreshNotifications',
            'notification-read'            => 'refreshNotifications',
            'notification-marked-all-read' => 'refreshNotifications',
            '$refresh'                     => '$refresh',
        ];
    }

    public function mount()
    {
        // Must be set BEFORE render so #[On] below can interpolate {userId}
        $this->userId = Auth::id();

        logger('NotificationBell mounted for user: ' . $this->userId);

        // Guard: this component can mount on a guest page (e.g. right after
        // logout/account deactivation redirects to "/"), in which case
        // there's no authenticated user to load notifications for.
        if (Auth::check()) {
            $this->refreshNotifications();
        }
    }

    /**
     * Fires when App\Events\NewNotification broadcasts on
     * PrivateChannel('notifications.' . $userId), event ".new.notification".
     * Parameter name must match the key from broadcastWith(): ['notification' => [...]]
     */
    #[On('echo-private:notifications.{userId},.new.notification')]
    public function handleNewNotification($notification)
    {
        if (! Auth::check()) {
            return;
        }

        logger('NotificationBell received live event for user: ' . $this->userId);

        $this->refreshNotifications();
        $this->dispatch('notification-received'); // optional, for a pop animation
    }

    public function refreshNotifications()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            $this->unreadCount   = 0;
            $this->notifications = [];
            return;
        }

        $this->unreadCount = $user->notifications()->unread()->count();

        $this->notifications = $user->notifications()
            ->latest()
            ->limit($this->limit)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'body'       => $n->body,
                'type'       => $n->type,
                'link'       => $n->link,
                'icon'       => $n->icon,
                'read_at'    => $n->read_at,
                'is_read'    => $n->isRead(),
                'created_at' => $n->formatted_created_at,
            ])
            ->toArray();
    }

    public function markAsRead($id)
    {
        if (! Auth::check()) {
            return;
        }

        $notification = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        $this->dispatch('notification-read');
        $this->refreshNotifications();
    }

    public function markAllRead()
    {
        if (! Auth::check()) {
            return;
        }

        Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        $this->dispatch('notification-marked-all-read');
        $this->refreshNotifications();
    }

    public function loadMore()
    {
        $this->limit += 5;

        if (Auth::check()) {
            $this->refreshNotifications();
        }
    }

    public function render()
    {
        return view('livewire.admin.partials.notification-bell');
    }
}
