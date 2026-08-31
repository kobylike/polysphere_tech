<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallSignal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int    $fromUserId,
        public int    $toUserId,
        public string $type,    // offer | answer | ice-candidate | call-request | call-accepted | call-declined | call-ended | call-busy
        public array  $payload = [],
    ) {}

    public function broadcastOn(): array
    {
        $ids = [$this->fromUserId, $this->toUserId];
        sort($ids);

        return [
            new PrivateChannel('call.' . implode('.', $ids)),
        ];
    }

    public function broadcastAs(): string
    {
        return 'call.signal';
    }

    public function broadcastWith(): array
    {
        return [
            'from'    => $this->fromUserId,
            'to'      => $this->toUserId,
            'type'    => $this->type,
            'payload' => $this->payload,
        ];
    }
}
