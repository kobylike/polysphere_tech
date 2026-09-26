<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionToggled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $messageId,
        public int $userId,
        public int $targetUserId,
        public string $emoji
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('chat.' . $this->targetUserId)];
    }

    public function broadcastAs(): string
    {
        return 'reaction.toggled';
    }
}
