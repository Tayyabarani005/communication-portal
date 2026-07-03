<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Task $task)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel.' . $this->task->channel_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'TaskUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'task_id'     => $this->task->task_id,
            'channel_id'  => $this->task->channel_id,
            'title'       => $this->task->title,
            'status'      => $this->task->status->value,
            'assigned_to' => $this->task->assigned_to,
            'due_date'    => $this->task->due_date?->toDateString(),
        ];
    }
}
