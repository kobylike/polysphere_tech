<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        $ids = [$this->message->sender_id, $this->message->receiver_id];
        sort($ids);

        return [
            new PrivateChannel('chat.' . implode('.', $ids)),
            new PrivateChannel('notifications.' . $this->message->receiver_id), // ← add this
        ];
    }
    /**
     * Custom event name that your Echo listener will subscribe to.
     * Must match exactly: .message.sent
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'sender_id'   => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
        ];
    }
}
