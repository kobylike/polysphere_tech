<?php

namespace App\Livewire\Admin\Partials;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

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

        $this->refreshNotifications();
    }

    /**
     * Fires when App\Events\NewNotification broadcasts on
     * PrivateChannel('notifications.' . $userId), event ".new.notification".
     * Parameter name must match the key from broadcastWith(): ['notification' => [...]]
     */
    #[On('echo-private:notifications.{userId},.new.notification')]
    public function handleNewNotification($notification)
    {
        logger('NotificationBell received live event for user: ' . $this->userId);

        $this->refreshNotifications();
        $this->dispatch('notification-received'); // optional, for a pop animation
    }

    public function refreshNotifications()
    {
        /** @var User $user */
        $user = Auth::user();

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
        $notification = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        $this->dispatch('notification-read');
        $this->refreshNotifications();
    }

    public function markAllRead()
    {
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
        $this->refreshNotifications();
    }

    public function render()
    {
        return view('livewire.admin.partials.notification-bell');
    }
}
