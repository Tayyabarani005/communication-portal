<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\DirectMessage;
use App\Models\DmConversation;
use App\Models\DmParticipant;

class DirectMessagePolicy
{
    /**
     * Determine whether the user can view the direct message conversation.
     */
    public function view(User $user, DmConversation $conversation): bool
    {
        return DmParticipant::where('conversation_id', $conversation->conversation_id)
            ->where('user_id', $user->user_id)
            ->exists();
    }

    /**
     * Determine whether the user can update or delete the direct message.
     */
    public function update(User $user, DirectMessage $message): bool
    {
        return $message->sender_id === $user->user_id;
    }

    public function delete(User $user, DirectMessage $message): bool
    {
        return $message->sender_id === $user->user_id;
    }
}
