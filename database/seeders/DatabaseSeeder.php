<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Models\Channel;
use App\Models\ChannelUser;
use App\Models\DmConversation;
use App\Models\DmParticipant;
use App\Models\Message;
use App\Models\DirectMessage;
use App\Models\File;
use App\Models\PinnedMessage;
use App\Models\Task;
use App\Enums\WorkspaceRole;
use App\Enums\TaskStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (with custom user_id matching original dump)
        User::create([
            'user_id' => 1,
            'username' => 'ali_raza',
            'email' => 'ali@example.com',
            'password_hash' => Hash::make('hash123'),
            'is_active' => true,
            'avatar_url' => 'http://img.com/ali.jpg',
        ]);

        User::create([
            'user_id' => 2,
            'username' => 'sara_k',
            'email' => 'sara@example.com',
            'password_hash' => Hash::make('hash456'),
            'is_active' => true,
            'avatar_url' => 'http://img.com/sara.jpg',
        ]);

        User::create([
            'user_id' => 3,
            'username' => 'john_d',
            'email' => 'john@example.com',
            'password_hash' => Hash::make('hash789'),
            'is_active' => true,
            'avatar_url' => null,
        ]);

        // 2. Seed Workspaces
        Workspace::create([
            'workspace_id' => 1,
            'name' => 'Tech Team',
            'description' => 'Main workspace for tech',
        ]);

        Workspace::create([
            'workspace_id' => 2,
            'name' => 'Design Hub',
            'description' => 'UI/UX workspace',
        ]);

        // 3. Seed Workspace Members
        WorkspaceMember::create([
            'member_id' => 1,
            'user_id' => 1,
            'workspace_id' => 1,
            'role' => WorkspaceRole::ADMIN,
            'joined_at' => '2026-06-07 11:09:43',
        ]);

        WorkspaceMember::create([
            'member_id' => 2,
            'user_id' => 2,
            'workspace_id' => 1,
            'role' => WorkspaceRole::MEMBER,
            'joined_at' => '2026-06-07 11:09:43',
        ]);

        WorkspaceMember::create([
            'member_id' => 3,
            'user_id' => 3,
            'workspace_id' => 2,
            'role' => WorkspaceRole::MEMBER,
            'joined_at' => '2026-06-07 11:09:43',
        ]);

        // 4. Seed Channels
        Channel::create([
            'channel_id' => 1,
            'workspace_id' => 1,
            'channel_name' => 'general',
            'is_private' => false,
        ]);

        Channel::create([
            'channel_id' => 2,
            'workspace_id' => 1,
            'channel_name' => 'dev-chat',
            'is_private' => false,
        ]);

        Channel::create([
            'channel_id' => 3,
            'workspace_id' => 2,
            'channel_name' => 'designs',
            'is_private' => true,
        ]);

        // 5. Seed Channel Users
        ChannelUser::create([
            'channel_user_id' => 1,
            'channel_id' => 1,
            'user_id' => 1,
            'joined_at' => '2026-06-07 11:12:22',
        ]);

        ChannelUser::create([
            'channel_user_id' => 2,
            'channel_id' => 1,
            'user_id' => 2,
            'joined_at' => '2026-06-07 11:12:22',
        ]);

        ChannelUser::create([
            'channel_user_id' => 3,
            'channel_id' => 2,
            'user_id' => 1,
            'joined_at' => '2026-06-07 11:12:22',
        ]);

        ChannelUser::create([
            'channel_user_id' => 4,
            'channel_id' => 3,
            'user_id' => 3,
            'joined_at' => '2026-06-07 11:12:22',
        ]);

        // 6. Seed DM Conversations
        DmConversation::create([
            'conversation_id' => 1,
            'created_at' => '2026-06-07 11:20:00',
            'updated_at' => '2026-06-07 11:20:00',
        ]);

        DmConversation::create([
            'conversation_id' => 2,
            'created_at' => '2026-06-07 11:20:00',
            'updated_at' => '2026-06-07 11:20:00',
        ]);

        // 7. Seed DM Participants
        DmParticipant::create([
            'dm_participant_id' => 1,
            'conversation_id' => 1,
            'user_id' => 1,
        ]);

        DmParticipant::create([
            'dm_participant_id' => 2,
            'conversation_id' => 1,
            'user_id' => 2,
        ]);

        DmParticipant::create([
            'dm_participant_id' => 3,
            'conversation_id' => 2,
            'user_id' => 2,
        ]);

        DmParticipant::create([
            'dm_participant_id' => 4,
            'conversation_id' => 2,
            'user_id' => 3,
        ]);

        // 8. Seed Channel Messages (Original dump data)
        Message::create([
            'message_id' => 1,
            'channel_id' => 1,
            'sender_id' => 1,
            'parent_id' => null,
            'msg_body' => 'Hello everyone!',
            'sent_at' => '2026-06-07 11:13:38',
            'msg_type' => 'text',
        ]);

        Message::create([
            'message_id' => 2,
            'channel_id' => 1,
            'sender_id' => 2,
            'parent_id' => null,
            'msg_body' => 'Hi Ali!',
            'sent_at' => '2026-06-07 11:13:38',
            'msg_type' => 'text',
        ]);

        Message::create([
            'message_id' => 3,
            'channel_id' => 2,
            'sender_id' => 1,
            'parent_id' => null,
            'msg_body' => 'Any updates on the build?',
            'sent_at' => '2026-06-07 11:13:38',
            'msg_type' => 'text',
        ]);

        // 9. Seed Direct Messages (Corrected and extended columns)
        DirectMessage::create([
            'dm_message_id' => 1,
            'conversation_id' => 1,
            'sender_id' => 1,
            'parent_id' => null,
            'msg_body' => 'Hey Sara, did you review the PR?',
            'sent_at' => '2026-06-07 11:22:00',
        ]);

        DirectMessage::create([
            'dm_message_id' => 2,
            'conversation_id' => 2,
            'sender_id' => 2,
            'parent_id' => null,
            'msg_body' => 'Hi John, the design specs are uploaded.',
            'sent_at' => '2026-06-07 11:23:00',
        ]);

        // 10. Seed Polymorphic Files
        File::create([
            'file_id' => 1,
            'attachable_id' => 3,
            'attachable_type' => Message::class,
            'file_name' => 'build_log.txt',
            'file_path' => 'attachments/workspaces/1/channels/2/build_log.txt',
            'file_size' => 2048,
            'mime_type' => 'text/plain',
        ]);

        // 11. Seed Polymorphic Pins
        PinnedMessage::create([
            'pin_id' => 1,
            'pinnable_id' => 1,
            'pinnable_type' => Message::class,
            'pinned_by' => 1,
        ]);

        // 12. Seed Tasks
        Task::create([
            'task_id' => 1,
            'channel_id' => 2,
            'created_by' => 1,
            'assigned_to' => 2,
            'title' => 'Fix login bug',
            'description' => 'Fix the bug causing users to be logged out after session times out.',
            'status' => TaskStatus::PENDING,
            'due_date' => '2026-07-01',
        ]);

        // 13. Generate ~20 extra fake messages per channel for dynamic paging & scroll testing
        $faker = Faker::create();
        $channels = Channel::all();
        $users = User::all();

        foreach ($channels as $channel) {
            // Get users in this channel's workspace to make seeding realistic
            $workspaceUserIds = WorkspaceMember::where('workspace_id', $channel->workspace_id)
                ->pluck('user_id')
                ->toArray();

            if (empty($workspaceUserIds)) {
                $workspaceUserIds = $users->pluck('user_id')->toArray();
            }

            for ($i = 0; $i < 20; $i++) {
                $senderId = $faker->randomElement($workspaceUserIds);
                Message::create([
                    'channel_id' => $channel->channel_id,
                    'sender_id' => $senderId,
                    'parent_id' => null,
                    'msg_body' => $faker->sentence(8),
                    'sent_at' => now()->subMinutes((20 - $i) * 10),
                    'msg_type' => 'text',
                ]);
            }
        }
    }
}
