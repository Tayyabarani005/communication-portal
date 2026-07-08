<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Events\MessageSent;
use App\Events\NotificationCreated;
use App\Events\UserTyping;
use App\Models\Channel;
use App\Models\ChannelReadState;
use App\Models\Message;
use App\Models\Notification;
use App\Models\PinnedMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChatWindow extends Component
{
    use WithFileUploads;

    public Channel $channel;
    public string $body = '';
    public ?int $parentId = null;
    public string $replyPreview = '';
    public bool $showTyping = false;
    public string $typingUser = '';
    public $attachment = null; // file upload

    /** @var array<int, array<string, mixed>> */
    public array $messages = [];
    public ?int $latestMessageId = null;
    public int $lastFullRefreshAt = 0;

    private int $perPage = 50;

    public function mount(Channel $channel): void
    {
        $this->channel = $channel;
        $this->loadMessages();
        $this->markRead();
    }

    private function loadMessages(): void
    {
        $this->messages = Message::where('channel_id', $this->channel->channel_id)
            ->whereNull('parent_id')
            ->with([
                'sender:user_id,username,avatar_url',
                'replies' => fn ($query) => $query
                    ->select('message_id', 'channel_id', 'sender_id', 'parent_id', 'msg_body', 'msg_type', 'sent_at')
                    ->oldest('sent_at')
                    ->oldest('message_id'),
                'replies.sender:user_id,username,avatar_url',
                'files:file_id,attachable_id,attachable_type,file_name,file_path,file_size,mime_type',
                'pins:pin_id,pinnable_id,pinnable_type,pinned_by',
            ])
            ->select('message_id', 'channel_id', 'sender_id', 'parent_id', 'msg_body', 'msg_type', 'sent_at')
            ->latest('sent_at')
            ->latest('message_id')
            ->limit($this->perPage)
            ->get()
            ->reverse()
            ->values()
            ->toArray();

        $latestMessageId = Message::where('channel_id', $this->channel->channel_id)
            ->latest('sent_at')
            ->latest('message_id')
            ->value('message_id');
        $this->latestMessageId = $latestMessageId ? (int) $latestMessageId : null;
        $this->lastFullRefreshAt = time();
    }

    private function markRead(): void
    {
        $lastMessageId = $this->latestMessageId ?? Message::where('channel_id', $this->channel->channel_id)
            ->latest('sent_at')
            ->latest('message_id')
            ->value('message_id');

        if (!$lastMessageId) {
            return;
        }

        $readState = ChannelReadState::firstOrNew([
            'channel_id' => $this->channel->channel_id,
            'user_id' => auth()->user()->user_id,
        ]);

        if ((int) $readState->last_read_message_id >= (int) $lastMessageId) {
            return;
        }

        $readState->last_read_message_id = (int) $lastMessageId;
        $readState->last_read_at = now();
        $readState->save();
    }

    public function send(): void
    {
        $this->authorize('sendMessage', $this->channel);
        $user = auth()->user();

        $this->validate([
            'body' => [empty($this->attachment) ? 'required' : 'nullable', 'string', 'max:4000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $message = Message::create([
            'channel_id' => $this->channel->channel_id,
            'sender_id'  => $user->user_id,
            'parent_id'  => $this->parentId,
            'msg_body'   => $this->body ?? '',
            'msg_type'   => $this->attachment ? 'file' : 'text',
            'sent_at'    => now(),
        ]);

        // Handle file attachment
        if ($this->attachment) {
            $path = $this->attachment->store("attachments/{$message->message_id}", 'public');
            \App\Models\File::create([
                'attachable_id'   => $message->message_id,
                'attachable_type' => Message::class,
                'file_name'       => $this->attachment->getClientOriginalName(),
                'file_path'       => $path,
                'file_size'       => $this->attachment->getSize(),
                'mime_type'       => $this->attachment->getMimeType(),
            ]);
            $this->attachment = null;
        }

        // Parse @mentions and create notifications
        $this->parseMentions($this->body ?? '', $message);

        $message->load([
            'sender:user_id,username,avatar_url',
            'files:file_id,attachable_id,attachable_type,file_name,file_path,file_size,mime_type',
            'pins:pin_id,pinnable_id,pinnable_type,pinned_by',
        ]);

        app()->terminating(function () use ($message): void {
            try {
                broadcast(new MessageSent($message))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('Channel message broadcast failed.', [
                    'message_id' => $message->message_id,
                    'channel_id' => $this->channel->channel_id,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        $this->messages[] = $message->toArray();
        $this->latestMessageId = (int) $message->message_id;
        $this->body = '';
        $this->parentId = null;
        $this->replyPreview = '';

        $this->markRead();
        $this->dispatch('message-sent');
    }

    /**
     * Parse @username mentions from message body and create notifications.
     */
    private function parseMentions(string $body, Message $message): void
    {
        if (!preg_match_all('/@(\w+)/', $body, $matches)) {
            return;
        }

        $authUser = auth()->user();
        $usernames = array_unique($matches[1]);
        $users = User::whereIn('username', $usernames)
            ->where('user_id', '!=', $authUser->user_id)
            ->get(['user_id']);

        if ($users->isEmpty()) {
            return;
        }

        $notifications = $users
            ->map(function (User $user) use ($authUser, $message): Notification {
                $notification = new Notification([
                    'user_id' => $user->user_id,
                    'sender_id' => $authUser->user_id,
                    'type' => 'tag',
                    'channel_id' => $this->channel->channel_id,
                    'message_id' => $message->message_id,
                    'text' => $authUser->username . ' mentioned you in #' . $this->channel->channel_name,
                ]);

                $notification->saveQuietly();
                $notification->setRelation('sender', $authUser);

                return $notification;
            })
            ->all();

        app()->terminating(function () use ($notifications): void {
            foreach ($notifications as $notification) {
                try {
                    broadcast(new NotificationCreated($notification));
                } catch (\Throwable $e) {
                    Log::warning('Mention notification broadcast failed.', [
                        'notification_id' => $notification->getKey(),
                        'user_id' => $notification->user_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });
    }

    public function pinMessage(int $messageId): void
    {
        $this->authorize('sendMessage', $this->channel);

        $pin = PinnedMessage::where('pinnable_id', $messageId)
            ->where('pinnable_type', Message::class)
            ->first();

        if ($pin) {
            $pin->delete();
        } else {
            PinnedMessage::create([
                'pinnable_id'   => $messageId,
                'pinnable_type' => Message::class,
                'pinned_by'     => auth()->user()->user_id,
            ]);
        }

        $this->loadMessages();
    }

    public function setReply(int $messageId, string $preview): void
    {
        $this->parentId = $messageId;
        $this->replyPreview = $preview;
    }

    public function clearReply(): void
    {
        $this->parentId = null;
        $this->replyPreview = '';
    }

    public function broadcastTyping(): void
    {
        $userId = auth()->user()->user_id;
        Cache::put(
            'typing-channel-' . $this->channel->channel_id . '-' . $userId,
            auth()->user()->username,
            now()->addSeconds(6)
        );

        app()->terminating(function () use ($userId): void {
            try {
                broadcast(new UserTyping(
                    $userId,
                    auth()->user()->username,
                    'channel',
                    $this->channel->channel_id,
                ))->toOthers();
            } catch (\Throwable $e) {
                Log::debug('Channel typing broadcast failed.', [
                    'channel_id' => $this->channel->channel_id,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    public function checkTyping(): void
    {
        $userId = auth()->user()->user_id;
        $workspaceId = $this->channel->workspace_id;
        $members = Cache::remember(
            "workspace-member-ids-{$workspaceId}",
            now()->addMinute(),
            fn () => $this->channel->workspace->workspaceMembers()->pluck('user_id')->all()
        );
        $typingNames = [];
        foreach ($members as $memberId) {
            if ((int) $memberId === $userId) continue;
            $name = Cache::get('typing-channel-' . $this->channel->channel_id . '-' . $memberId);
            if ($name) {
                $typingNames[] = $name;
            }
        }

        if (!empty($typingNames)) {
            $this->typingUser = implode(', ', $typingNames);
            $this->showTyping = true;
        } else {
            $this->showTyping = false;
            $this->typingUser = '';
        }
    }

    /**
     * Refresh messages (called by wire:poll).
     */
    public function refreshMessages(): void
    {
        $newestMessageId = Message::where('channel_id', $this->channel->channel_id)
            ->latest('sent_at')
            ->latest('message_id')
            ->value('message_id');

        if ((int) $newestMessageId !== (int) $this->latestMessageId || time() - $this->lastFullRefreshAt >= 30) {
            $this->loadMessages();
        }

        $this->checkTyping();
    }

    public function onMessageSent(array $data): void
    {
        $this->messages[] = $data;
        $this->latestMessageId = (int) ($data['message_id'] ?? $this->latestMessageId);
        $this->markRead();
        $this->dispatch('scroll-to-bottom');
    }

    public function onUserTyping(array $data): void
    {
        if ((int) $data['user_id'] !== auth()->user()->user_id) {
            $this->typingUser = $data['username'];
            $this->showTyping = true;
        }
    }

    public function hideTyping(): void
    {
        $this->showTyping = false;
        $this->typingUser = '';
    }

    public function render(): View
    {
        return view('livewire.chat-window');
    }
}
