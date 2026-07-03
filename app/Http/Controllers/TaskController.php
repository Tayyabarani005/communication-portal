<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Task;
use App\Enums\TaskStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function create(Channel $channel): View
    {
        $this->authorize('sendMessage', $channel);
        $members = $channel->users()->get();

        return view('tasks.create', compact('channel', 'members'));
    }

    public function store(Request $request, Channel $channel): RedirectResponse
    {
        $this->authorize('sendMessage', $channel);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,user_id'],
            'due_date'    => ['nullable', 'date'],
            'status'      => ['required', 'string', 'in:pending,in_progress,done'],
        ]);

        Task::create([
            'channel_id'  => $channel->channel_id,
            'created_by'  => $request->user()->user_id,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status'      => TaskStatus::from($validated['status']),
            'due_date'    => $validated['due_date'] ?? null,
        ]);

        return redirect()->route('channels.show', $channel)
            ->with('success', 'Task created.');
    }

    public function edit(Channel $channel, Task $task): View
    {
        $this->authorize('update', $task);
        $members = $channel->users()->get();

        return view('tasks.edit', compact('channel', 'task', 'members'));
    }

    public function update(Request $request, Channel $channel, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,user_id'],
            'due_date'    => ['nullable', 'date'],
            'status'      => ['required', 'string', 'in:pending,in_progress,done'],
        ]);

        $task->update([
            'assigned_to' => $validated['assigned_to'] ?? null,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status'      => TaskStatus::from($validated['status']),
            'due_date'    => $validated['due_date'] ?? null,
        ]);

        return redirect()->route('channels.show', $channel)
            ->with('success', 'Task updated.');
    }

    public function destroy(Channel $channel, Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('channels.show', $channel)
            ->with('success', 'Task deleted.');
    }
}
