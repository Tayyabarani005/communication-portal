<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
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

        // Workspaces the user belongs to
        $myWorkspaces = $request->user()
            ->workspaces()
            ->with('workspaceMembers')
            ->get();

        // All other workspaces (public / discoverable)
        $otherWorkspaces = Workspace::whereDoesntHave('workspaceMembers', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('workspaceMembers')->get();

        return view('workspaces.index', compact('myWorkspaces', 'otherWorkspaces'));
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

        // Make creator an admin
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
        // Allow viewing if member; redirect to join prompt if not
        $isMember = WorkspaceMember::where('workspace_id', $workspace->workspace_id)
            ->where('user_id', auth()->user()->user_id)
            ->exists();

        $channels = $workspace->channels()
            ->with(['users' => fn($q) => $q->where('user_id', auth()->id())])
            ->get();

        $members = $workspace->workspaceMembers()->with('user')->get();

        return view('workspaces.show', compact('workspace', 'channels', 'members', 'isMember'));
    }

    /**
     * Request to join a workspace (non-member).
     */
    public function join(Request $request, Workspace $workspace): RedirectResponse
    {
        $userId = $request->user()->user_id;

        $alreadyMember = WorkspaceMember::where('workspace_id', $workspace->workspace_id)
            ->where('user_id', $userId)
            ->exists();

        if (!$alreadyMember) {
            WorkspaceMember::create([
                'user_id'      => $userId,
                'workspace_id' => $workspace->workspace_id,
                'role'         => WorkspaceRole::MEMBER,
                'joined_at'    => now(),
            ]);
        }

        return redirect()->route('workspaces.show', $workspace)
            ->with('success', 'You have joined this workspace!');
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
            ->with('success', "Invited {$invitee->username} to the workspace.");
    }

    public function edit(Workspace $workspace): View
    {
        $this->authorize('update', $workspace);

        return view('workspaces.edit', compact('workspace'));
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
