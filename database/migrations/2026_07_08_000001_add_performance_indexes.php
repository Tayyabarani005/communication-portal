<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('message', function (Blueprint $table) {
            $table->index(['channel_id', 'parent_id', 'sent_at', 'message_id'], 'message_channel_parent_sent_idx');
            $table->index(['channel_id', 'message_id'], 'message_channel_unread_idx');
        });

        Schema::table('direct_message', function (Blueprint $table) {
            $table->index(['conversation_id', 'parent_id', 'sent_at', 'dm_message_id'], 'dm_conv_parent_sent_idx');
            $table->index(['conversation_id', 'dm_message_id'], 'dm_conv_unread_idx');
        });

        Schema::table('channel_user', function (Blueprint $table) {
            $table->index(['user_id', 'channel_id'], 'channel_user_user_channel_idx');
            $table->index(['channel_id', 'user_id'], 'channel_user_channel_user_idx');
        });

        Schema::table('dm_participant', function (Blueprint $table) {
            $table->index(['user_id', 'conversation_id'], 'dm_participant_user_conv_idx');
            $table->index(['conversation_id', 'user_id'], 'dm_participant_conv_user_idx');
        });

        Schema::table('workspace_members', function (Blueprint $table) {
            $table->index(['user_id', 'workspace_id'], 'workspace_members_user_ws_idx');
            $table->index(['workspace_id', 'user_id'], 'workspace_members_ws_user_idx');
            $table->index(['workspace_id', 'role'], 'workspace_members_ws_role_idx');
        });

        Schema::table('channel', function (Blueprint $table) {
            $table->index(['workspace_id', 'is_private'], 'channel_workspace_private_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_seen'], 'notifications_user_seen_idx');
            $table->index(['user_id', 'created_at'], 'notifications_user_created_idx');
            $table->index(['workspace_id', 'user_id'], 'notifications_workspace_user_idx');
            $table->index(['channel_id', 'message_id'], 'notifications_channel_message_idx');
        });

        Schema::table('workspace_join_requests', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'workspace_join_requests_user_status_idx');
            $table->index(['workspace_id', 'status'], 'workspace_join_requests_ws_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('workspace_join_requests', function (Blueprint $table) {
            $table->dropIndex('workspace_join_requests_user_status_idx');
            $table->dropIndex('workspace_join_requests_ws_status_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_seen_idx');
            $table->dropIndex('notifications_user_created_idx');
            $table->dropIndex('notifications_workspace_user_idx');
            $table->dropIndex('notifications_channel_message_idx');
        });

        Schema::table('channel', function (Blueprint $table) {
            $table->dropIndex('channel_workspace_private_idx');
        });

        Schema::table('workspace_members', function (Blueprint $table) {
            $table->dropIndex('workspace_members_user_ws_idx');
            $table->dropIndex('workspace_members_ws_user_idx');
            $table->dropIndex('workspace_members_ws_role_idx');
        });

        Schema::table('dm_participant', function (Blueprint $table) {
            $table->dropIndex('dm_participant_user_conv_idx');
            $table->dropIndex('dm_participant_conv_user_idx');
        });

        Schema::table('channel_user', function (Blueprint $table) {
            $table->dropIndex('channel_user_user_channel_idx');
            $table->dropIndex('channel_user_channel_user_idx');
        });

        Schema::table('direct_message', function (Blueprint $table) {
            $table->dropIndex('dm_conv_parent_sent_idx');
            $table->dropIndex('dm_conv_unread_idx');
        });

        Schema::table('message', function (Blueprint $table) {
            $table->dropIndex('message_channel_parent_sent_idx');
            $table->dropIndex('message_channel_unread_idx');
        });
    }
};
