<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Enums\WorkspaceRole;

class WorkspacePolicy
{
    /**
     * Determine whether the user can view the workspace.
     */
    public function view(User $user, Workspace $workspace): bool
    {
        return WorkspaceMember::where('workspace_id', $workspace->workspace_id)
            ->where('user_id', $user->user_id)
            ->exists();
    }

    /**
     * Determine whether the user can update the workspace.
     */
    public function update(User $user, Workspace $workspace): bool
    {
        $member = WorkspaceMember::where('workspace_id', $workspace->workspace_id)
            ->where('user_id', $user->user_id)
            ->first();

        return $member !== null && $member->isAdmin();
    }

    /**
     * Determine whether the user can delete the workspace.
     */
    public function delete(User $user, Workspace $workspace): bool
    {
        return $this->update($user, $workspace);
    }
}
