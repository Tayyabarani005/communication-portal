<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Channel;
use App\Models\ChannelUser;
use App\Models\WorkspaceMember;
use App\Enums\WorkspaceRole;

class ChannelPolicy
{
    /**
     * Determine whether the user can view the channel.
     */
    public function view(User $user, Channel $channel): bool
    {
        // Must be a member of the workspace
        $isWorkspaceMember = WorkspaceMember::where('workspace_id', $channel->workspace_id)
            ->where('user_id', $user->user_id)
            ->exists();

        if (!$isWorkspaceMember) {
            return false;
        }

        // If private, must be in channel_user
        if ($channel->is_private) {
            return ChannelUser::where('channel_id', $channel->channel_id)
                ->where('user_id', $user->user_id)
                ->exists();
        }

        return true;
    }

    /**
     * Determine whether the user can send a message to the channel.
     */
    public function sendMessage(User $user, Channel $channel): bool
    {
        // User must be a member of the channel
        return ChannelUser::where('channel_id', $channel->channel_id)
            ->where('user_id', $user->user_id)
            ->exists();
    }

    /**
     * Determine whether the user can update or archive the channel.
     */
    public function update(User $user, Channel $channel): bool
    {
        // Must be a workspace admin
        $member = WorkspaceMember::where('workspace_id', $channel->workspace_id)
            ->where('user_id', $user->user_id)
            ->first();

        return $member && $member->role === WorkspaceRole::ADMIN;
    }

    public function delete(User $user, Channel $channel): bool
    {
        return $this->update($user, $channel);
    }
}
