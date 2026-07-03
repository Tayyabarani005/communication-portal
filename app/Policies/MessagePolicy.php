<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Message;
use App\Models\WorkspaceMember;
use App\Enums\WorkspaceRole;

class MessagePolicy
{
    /**
     * Determine whether the user can update or delete the message.
     */
    public function update(User $user, Message $message): bool
    {
        if ($message->sender_id === $user->user_id) {
            return true;
        }

        $channel = $message->channel;
        if ($channel) {
            $member = WorkspaceMember::where('workspace_id', $channel->workspace_id)
                ->where('user_id', $user->user_id)
                ->first();

            return $member && $member->role === WorkspaceRole::ADMIN;
        }

        return false;
    }

    public function delete(User $user, Message $message): bool
    {
        return $this->update($user, $message);
    }
}
