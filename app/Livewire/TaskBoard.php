<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Events\TaskUpdated;
use App\Models\Channel;
use App\Models\Task;
use App\Enums\TaskStatus;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class TaskBoard extends Component
{
    public Channel $channel;

    /** @var array<string, array<int, array<string, mixed>>> */
    public array $columns = [];

    public function mount(Channel $channel): void
    {
        $this->channel = $channel;
        $this->loadTasks();
    }

    public function loadTasks(): void
    {
        $tasks = Task::where('channel_id', $this->channel->channel_id)
            ->with(['creator', 'assignee'])
            ->get();

        $this->columns = [
            TaskStatus::PENDING->value    => [],
            TaskStatus::IN_PROGRESS->value => [],
            TaskStatus::DONE->value       => [],
        ];

        foreach ($tasks as $task) {
            $this->columns[$task->status->value][] = $task->toArray();
        }
    }

    public function updateStatus(int $taskId, string $newStatus): void
    {
        $task = Task::findOrFail($taskId);
        $this->authorize('update', $task);

        $task->update(['status' => TaskStatus::from($newStatus)]);

        broadcast(new TaskUpdated($task))->toOthers();

        $this->loadTasks();
    }

    #[On('echo-private:channel.{channel.channel_id},TaskUpdated')]
    public function onTaskUpdated(array $data): void
    {
        $this->loadTasks();
    }

    public function render(): View
    {
        return view('livewire.task-board');
    }
}
