<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\DirectMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DirectMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly DirectMessage $message)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('dm.' . $this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'DirectMessageSent';
    }

    public function broadcastWith(): array
    {
        return [
            'dm_message_id'   => $this->message->dm_message_id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id'       => $this->message->sender_id,
            'parent_id'       => $this->message->parent_id,
            'msg_body'        => $this->message->msg_body,
            'msg_type'        => $this->message->msg_type,
            'sent_at'         => $this->message->sent_at?->toIso8601String(),
            'sender'          => [
                'user_id'    => $this->message->sender->user_id,
                'username'   => $this->message->sender->username,
                'avatar_url' => $this->message->sender->avatar_url,
            ],
        ];
    }
}
