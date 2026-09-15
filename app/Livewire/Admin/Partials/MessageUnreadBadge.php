<?php

namespace App\Livewire\Admin\Partials;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class MessageUnreadBadge extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    /**
     * ChatMessengerComponent already calls:
     *   $this->dispatch('unread-count-updated', count: $this->totalReceivedMessages);
     * every time it loads friends (on send, receive, open conversation, etc).
     * Livewire delivers dispatched events to every mounted component on the
     * page via its own event bus — this works regardless of @persist and
     * regardless of wire:navigate, because it doesn't depend on this
     * component re-rendering or the DOM being touched at all.
     */
    #[On('unread-count-updated')]
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * Safety net: recompute from the DB directly. Useful if you ever want
     * to force a refresh from elsewhere (e.g. after marking messages read
     * from an email link, another tab, etc).
     */
    #[On('message-notification-updated')]
    public function refreshCount(): void
    {
        if (! Auth::check()) {
            $this->count = 0;
            return;
        }

        $this->count = Message::query()
            ->where('receiver_id', Auth::id())
            ->where('read', false)
            ->count();
    }

    public function render()
    {
        return view('livewire.admin.partials.message-unread-badge');
    }
}
