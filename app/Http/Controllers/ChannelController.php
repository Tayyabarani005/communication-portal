<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\ChannelUser;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChannelController extends Controller
{
    public function create(Workspace $workspace): View
    {
        $this->authorize('update', $workspace);

        // Load workspace members so we can select who to add to private channels
        $members = $workspace->workspaceMembers()->with('user')->get();

        // Also load channels for sidebar context
        $userId = auth()->user()->user_id;
        $channels = $workspace->channels()
            ->with(['users' => fn($q) => $q->where('channel_user.user_id', $userId)])
            ->get()
            ->filter(function ($channel) use ($userId) {
                if (!$channel->is_private) return true;
                return $channel->users->contains(fn($u) => $u->user_id === $userId);
            })->values();

        return view('channels.create', compact('workspace', 'members', 'channels'));
    }

    public function store(Request $request, Workspace $workspace): RedirectResponse
    {
        $this->authorize('update', $workspace);

        $validated = $request->validate([
            'channel_name' => ['required', 'string', 'max:100'],
            'is_private'   => ['boolean'],
            'members'      => ['nullable', 'array'],
            'members.*'    => ['integer', 'exists:users,user_id'],
        ]);

        $channel = Channel::create([
            'workspace_id' => $workspace->workspace_id,
            'channel_name' => $validated['channel_name'],
            'is_private'   => $validated['is_private'] ?? false,
        ]);

        // Auto-join the creator
        ChannelUser::create([
            'channel_id' => $channel->channel_id,
            'user_id'    => $request->user()->user_id,
            'joined_at'  => now(),
        ]);

        // For private channels, add selected members
        if (($validated['is_private'] ?? false) && !empty($validated['members'])) {
            foreach ($validated['members'] as $memberId) {
                if ((int) $memberId === $request->user()->user_id) continue; // skip creator
                ChannelUser::firstOrCreate([
                    'channel_id' => $channel->channel_id,
                    'user_id'    => $memberId,
                ], ['joined_at' => now()]);
            }
        }

        return redirect()->route('channels.show', $channel)
            ->with('success', 'Channel created.');
    }

    public function show(Channel $channel): View
    {
        $this->authorize('view', $channel);

        $channel->load('workspace');

        return view('channels.show', compact('channel'));
    }

    public function edit(Channel $channel): View
    {
        $this->authorize('update', $channel);

        return view('channels.edit', compact('channel'));
    }

    public function update(Request $request, Channel $channel): RedirectResponse
    {
        $this->authorize('update', $channel);

        $validated = $request->validate([
            'channel_name' => ['required', 'string', 'max:100'],
            'is_private'   => ['boolean'],
        ]);

        $channel->update($validated);

        return redirect()->route('channels.show', $channel)
            ->with('success', 'Channel updated.');
    }

    public function destroy(Channel $channel): RedirectResponse
    {
        $this->authorize('delete', $channel);
        $workspace = $channel->workspace;
        $channel->delete();

        return redirect()->route('workspaces.show', $workspace)
            ->with('success', 'Channel deleted.');
    }

    public function join(Request $request, Workspace $workspace, Channel $channel): RedirectResponse
    {
        $this->authorize('view', $channel);

        ChannelUser::firstOrCreate([
            'channel_id' => $channel->channel_id,
            'user_id'    => $request->user()->user_id,
        ], ['joined_at' => now()]);

        return redirect()->route('channels.show', $channel);
    }

    public function leave(Request $request, Workspace $workspace, Channel $channel): RedirectResponse
    {
        ChannelUser::where('channel_id', $channel->channel_id)
            ->where('user_id', $request->user()->user_id)
            ->delete();

        return redirect()->route('workspaces.show', $workspace)
            ->with('success', 'Left channel.');
    }
}
