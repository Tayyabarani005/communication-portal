<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Models\WorkspaceJoinRequest;
use App\Models\Notification;
use App\Enums\WorkspaceRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    /**
     * Show the user's own workspaces + discoverable public workspaces.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->user_id;

        $myWorkspaces = $request->user()
            ->workspaces()
            ->with('workspaceMembers')
            ->get();

        $otherWorkspaces = Workspace::whereDoesntHave('workspaceMembers', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('workspaceMembers')->get();

        // Map existing join request statuses for other workspaces
        $pendingRequestIds = WorkspaceJoinRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->pluck('workspace_id')
            ->toArray();

        return view('workspaces.index', compact('myWorkspaces', 'otherWorkspaces', 'pendingRequestIds'));
    }

    public function create(): View
    {
        return view('workspaces.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $workspace = Workspace::create($validated);

        WorkspaceMember::create([
            'user_id'      => $request->user()->user_id,
            'workspace_id' => $workspace->workspace_id,
            'role'         => WorkspaceRole::ADMIN,
            'joined_at'    => now(),
        ]);

        return redirect()->route('workspaces.show', $workspace)
            ->with('success', 'Workspace created successfully.');
    }

    public function show(Workspace $workspace): View
    {
        $userId = auth()->user()->user_id;

        $isMember = WorkspaceMember::where('workspace_id', $workspace->workspace_id)
            ->where('user_id', $userId)
            ->exists();

        $isAdmin = WorkspaceMember::where('workspace_id', $workspace->workspace_id)
            ->where('user_id', $userId)
            ->where('role', WorkspaceRole::ADMIN)
            ->exists();

        // Fix SQL ambiguity: qualify the user_id column explicitly
        // Also filter out private channels the user is NOT a member of
        $channels = $workspace->channels()
            ->with(['users' => fn($q) => $q->where('channel_user.user_id', $userId)])
            ->get()
            ->filter(function ($channel) use ($userId) {
                // Show public channels to everyone; show private channels only to members
                if (!$channel->is_private) {
                    return true;
                }
                return $channel->users->contains(fn($u) => $u->user_id === $userId);
            })
            ->values();

        $members = $workspace->workspaceMembers()->with('user')->get();

        // Pending join requests (for admin display)
        $joinRequests = $isAdmin
            ? WorkspaceJoinRequest::where('workspace_id', $workspace->workspace_id)
                ->where('status', 'pending')
                ->with('user')
                ->get()
            : collect();

        // Check if current user has a pending request
        $myRequest = WorkspaceJoinRequest::where('workspace_id', $workspace->workspace_id)
            ->where('user_id', $userId)
            ->first();

        return view('workspaces.show', compact('workspace', 'channels', 'members', 'isMember', 'isAdmin', 'joinRequests', 'myRequest'));
    }

    /**
     * Request to join a workspace (creates pending request, not immediate join).
     */
    public function join(Request $request, Workspace $workspace): RedirectResponse
    {
        $userId = $request->user()->user_id;

        // Check if already a member
        $alreadyMember = WorkspaceMember::where('workspace_id', $workspace->workspace_id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyMember) {
            return redirect()->route('workspaces.show', $workspace)
                ->with('info', 'You are already a member of this workspace.');
        }

        // Create or update join request
        WorkspaceJoinRequest::updateOrCreate(
            ['workspace_id' => $workspace->workspace_id, 'user_id' => $userId],
            ['status' => 'pending']
        );

        // Notify workspace admins
        $admins = WorkspaceMember::where('workspace_id', $workspace->workspace_id)
            ->where('role', WorkspaceRole::ADMIN)
            ->pluck('user_id');

        foreach ($admins as $adminId) {
            Notification::create([
                'user_id'      => $adminId,
                'sender_id'    => $userId,
                'type'         => 'join_request',
                'workspace_id' => $workspace->workspace_id,
                'text'         => $request->user()->username . ' requested to join ' . $workspace->name,
            ]);
        }

        return redirect()->route('workspaces.index')
            ->with('success', 'Join request sent! An admin will review your request.');
    }

    /**
     * Admin: approve a join request.
     */
    public function approveJoin(Request $request, Workspace $workspace, WorkspaceJoinRequest $joinRequest): RedirectResponse
    {
        $this->authorize('update', $workspace);

        $joinRequest->update(['status' => 'accepted']);

        WorkspaceMember::firstOrCreate(
            ['workspace_id' => $workspace->workspace_id, 'user_id' => $joinRequest->user_id],
            ['role' => WorkspaceRole::MEMBER, 'joined_at' => now()]
        );

        // Notify the user that they were accepted
        Notification::create([
            'user_id'      => $joinRequest->user_id,
            'sender_id'    => $request->user()->user_id,
            'type'         => 'join_accepted',
            'workspace_id' => $workspace->workspace_id,
            'text'         => 'You are now a member of ' . $workspace->name . '!',
        ]);

        return redirect()->route('workspaces.show', $workspace)
            ->with('success', "Request approved — {$joinRequest->user->username} has joined the workspace.");
    }

    /**
     * Admin: reject a join request.
     */
    public function rejectJoin(Request $request, Workspace $workspace, WorkspaceJoinRequest $joinRequest): RedirectResponse
    {
        $this->authorize('update', $workspace);

        $joinRequest->update(['status' => 'rejected']);

        // Notify the user that they were rejected
        Notification::create([
            'user_id'      => $joinRequest->user_id,
            'sender_id'    => $request->user()->user_id,
            'type'         => 'join_rejected',
            'workspace_id' => $workspace->workspace_id,
            'text'         => 'Your join request for ' . $workspace->name . ' was declined.',
        ]);

        return redirect()->route('workspaces.show', $workspace)
            ->with('success', "Request from {$joinRequest->user->username} was rejected.");
    }

    /**
     * Invite a user to a workspace by email (admin only).
     */
    public function invite(Request $request, Workspace $workspace): RedirectResponse
    {
        $this->authorize('update', $workspace);

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $invitee = User::where('email', $validated['email'])->first();

        $alreadyMember = WorkspaceMember::where('workspace_id', $workspace->workspace_id)
            ->where('user_id', $invitee->user_id)
            ->exists();

        if ($alreadyMember) {
            return redirect()->route('workspaces.show', $workspace)
                ->with('error', 'This user is already a member.');
        }

        WorkspaceMember::create([
            'user_id'      => $invitee->user_id,
            'workspace_id' => $workspace->workspace_id,
            'role'         => WorkspaceRole::MEMBER,
            'joined_at'    => now(),
        ]);

        return redirect()->route('workspaces.show', $workspace)
            ->with('success', "Added {$invitee->username} to the workspace.");
    }

    public function edit(Workspace $workspace): View
    {
        $this->authorize('update', $workspace);

        // Pass channels and members so sidebar remains intact
        $userId = auth()->user()->user_id;
        $channels = $workspace->channels()
            ->with(['users' => fn($q) => $q->where('channel_user.user_id', $userId)])
            ->get()
            ->filter(function ($channel) use ($userId) {
                if (!$channel->is_private) return true;
                return $channel->users->contains(fn($u) => $u->user_id === $userId);
            })->values();
        $members = $workspace->workspaceMembers()->with('user')->get();

        return view('workspaces.edit', compact('workspace', 'channels', 'members'));
    }

    public function update(Request $request, Workspace $workspace): RedirectResponse
    {
        $this->authorize('update', $workspace);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $workspace->update($validated);

        return redirect()->route('workspaces.show', $workspace)
            ->with('success', 'Workspace updated successfully.');
    }

    public function destroy(Workspace $workspace): RedirectResponse
    {
        $this->authorize('delete', $workspace);
        $workspace->delete();

        return redirect()->route('workspaces.index')
            ->with('success', 'Workspace deleted.');
    }
}
