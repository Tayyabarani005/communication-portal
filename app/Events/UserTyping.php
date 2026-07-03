<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $username,
        public readonly string $channelType, // 'channel' or 'dm'
        public readonly int $channelId,
    ) {
    }

    public function broadcastOn(): array
    {
        $channelName = $this->channelType === 'dm'
            ? 'dm.' . $this->channelId
            : 'channel.' . $this->channelId;

        return [new PrivateChannel($channelName)];
    }

    public function broadcastAs(): string
    {
        return 'UserTyping';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'  => $this->userId,
            'username' => $this->username,
        ];
    }
}
