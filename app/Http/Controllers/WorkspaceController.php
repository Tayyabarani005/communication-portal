<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Enums\WorkspaceRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function index(Request $request): View
    {
        $workspaces = $request->user()
            ->workspaces()
            ->with('workspaceMembers')
            ->get();

        return view('workspaces.index', compact('workspaces'));
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
        $this->authorize('view', $workspace);

        $channels = $workspace->channels()
            ->with(['users' => fn($q) => $q->where('user_id', auth()->id())])
            ->get();

        $members = $workspace->workspaceMembers()->with('user')->get();

        return view('workspaces.show', compact('workspace', 'channels', 'members'));
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
