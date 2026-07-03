<?php

use App\Models\ChannelUser;
use App\Models\DmParticipant;
use App\Models\WorkspaceMember;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// User presence channel
Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return (int) $user->user_id === (int) $id;
});

// Private channel for workspace-wide events (task updates, member changes)
Broadcast::channel('workspace.{workspaceId}', function (User $user, int $workspaceId) {
    return WorkspaceMember::where('workspace_id', $workspaceId)
        ->where('user_id', $user->user_id)
        ->exists();
});

// Private channel for channel messages and typing indicators
Broadcast::channel('channel.{channelId}', function (User $user, int $channelId) {
    return ChannelUser::where('channel_id', $channelId)
        ->where('user_id', $user->user_id)
        ->exists();
});

// Private channel for direct messages
Broadcast::channel('dm.{conversationId}', function (User $user, int $conversationId) {
    return DmParticipant::where('conversation_id', $conversationId)
        ->where('user_id', $user->user_id)
        ->exists();
});
