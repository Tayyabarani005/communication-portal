<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Message $message)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel.' . $this->message->channel_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        return [
            'message_id'  => $this->message->message_id,
            'channel_id'  => $this->message->channel_id,
            'sender_id'   => $this->message->sender_id,
            'parent_id'   => $this->message->parent_id,
            'msg_body'    => $this->message->msg_body,
            'msg_type'    => $this->message->msg_type,
            'sent_at'     => $this->message->sent_at?->toIso8601String(),
            'sender'      => [
                'user_id'    => $this->message->sender->user_id,
                'username'   => $this->message->sender->username,
                'avatar_url' => $this->message->sender->avatar_url,
            ],
        ];
    }
}
