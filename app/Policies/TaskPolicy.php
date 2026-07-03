<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use App\Models\ChannelUser;
use App\Models\WorkspaceMember;
use App\Enums\WorkspaceRole;

class TaskPolicy
{
    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        return ChannelUser::where('channel_id', $task->channel_id)
            ->where('user_id', $user->user_id)
            ->exists();
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        if ($task->created_by === $user->user_id || $task->assigned_to === $user->user_id) {
            return true;
        }

        $channel = $task->channel;
        if ($channel) {
            $member = WorkspaceMember::where('workspace_id', $channel->workspace_id)
                ->where('user_id', $user->user_id)
                ->first();

            return $member && $member->role === WorkspaceRole::ADMIN;
        }

        return false;
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }
}
