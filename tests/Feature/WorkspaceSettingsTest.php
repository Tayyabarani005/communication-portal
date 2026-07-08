<?php

use App\Enums\WorkspaceRole;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;

test('workspace admins can open and update workspace settings', function () {
    $user = User::factory()->create();
    $workspace = Workspace::create([
        'name' => 'Marketing Team',
        'description' => 'Original description',
        'avatar_url' => null,
    ]);

    WorkspaceMember::create([
        'user_id' => $user->user_id,
        'workspace_id' => $workspace->workspace_id,
        'role' => WorkspaceRole::ADMIN,
        'joined_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('workspaces.edit', $workspace))
        ->assertOk();

    $this->actingAs($user)
        ->patch(route('workspaces.update', $workspace), [
            'name' => 'Brand Team',
            'description' => 'Updated description',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('workspaces.show', $workspace));

    $this->assertDatabaseHas('workspace', [
        'workspace_id' => $workspace->workspace_id,
        'name' => 'Brand Team',
        'description' => 'Updated description',
    ]);
});