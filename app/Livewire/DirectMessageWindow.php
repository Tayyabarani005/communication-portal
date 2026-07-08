<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Events\DirectMessageSent;
use App\Events\NotificationCreated;
use App\Events\UserTyping;
use App\Models\DirectMessage;
use App\Models\DmConversation;
use App\Models\DmReadState;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class DirectMessageWindow extends Component
{
    use WithFileUploads;

    public DmConversation $conversation;
    public string $body = '';
    public ?int $parentId = null;
    public string $replyPreview = '';
    public bool $showTyping = false;
    public string $typingUser = '';
    public $attachment = null;

    /** @var array<int, array<string, mixed>> */
    public array $messages = [];
    public ?int $latestMessageId = null;
    public int $lastFullRefreshAt = 0;

    public function mount(DmConversation $conversation): void
    {
        $this->conversation = $conversation;
        $this->loadMessages();
        $this->markRead();
    }

    private function loadMessages(): void
    {
        $this->messages = DirectMessage::where('conversation_id', $this->conversation->conversation_id)
            ->whereNull('parent_id')
            ->with([
                'sender:user_id,username,avatar_url',
                'replies' => fn ($query) => $query
                    ->select('dm_message_id', 'conversation_id', 'sender_id', 'parent_id', 'msg_body', 'msg_type', 'sent_at')
                    ->oldest('sent_at')
                    ->oldest('dm_message_id'),
                'replies.sender:user_id,username,avatar_url',
                'files:file_id,attachable_id,attachable_type,file_name,file_path,file_size,mime_type',
            ])
            ->select('dm_message_id', 'conversation_id', 'sender_id', 'parent_id', 'msg_body', 'msg_type', 'sent_at')
            ->latest('sent_at')
            ->latest('dm_message_id')
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->toArray();

        $latestMessageId = DirectMessage::where('conversation_id', $this->conversation->conversation_id)
            ->latest('sent_at')
            ->latest('dm_message_id')
            ->value('dm_message_id');
        $this->latestMessageId = $latestMessageId ? (int) $latestMessageId : null;
        $this->lastFullRefreshAt = time();
    }

    private function markRead(): void
    {
        $lastMessageId = $this->latestMessageId ?? DirectMessage::where('conversation_id', $this->conversation->conversation_id)
            ->latest('sent_at')
            ->latest('dm_message_id')
            ->value('dm_message_id');

        if (!$lastMessageId) {
            return;
        }

        $readState = DmReadState::firstOrNew([
            'conversation_id' => $this->conversation->conversation_id,
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
        $this->authorize('view', $this->conversation);
        $user = auth()->user();

        $this->validate([
            'body' => [empty($this->attachment) ? 'required' : 'nullable', 'string', 'max:4000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $message = DirectMessage::create([
            'conversation_id' => $this->conversation->conversation_id,
            'sender_id'       => $user->user_id,
            'parent_id'       => $this->parentId,
            'msg_body'        => $this->body ?? '',
            'msg_type'        => $this->attachment ? 'file' : 'text',
            'sent_at'         => now(),
        ]);

        // Handle file attachment
        if ($this->attachment) {
            $path = $this->attachment->store("attachments/dm-{$message->dm_message_id}", 'public');
            \App\Models\File::create([
                'attachable_id'   => $message->dm_message_id,
                'attachable_type' => DirectMessage::class,
                'file_name'       => $this->attachment->getClientOriginalName(),
                'file_path'       => $path,
                'file_size'       => $this->attachment->getSize(),
                'mime_type'       => $this->attachment->getMimeType(),
            ]);
            $this->attachment = null;
        }

        // Parse @mentions
        $this->parseMentions($this->body ?? '', $message);

        $message->load([
            'sender:user_id,username,avatar_url',
            'files:file_id,attachable_id,attachable_type,file_name,file_path,file_size,mime_type',
        ]);

        app()->terminating(function () use ($message): void {
            try {
                broadcast(new DirectMessageSent($message))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('Direct message broadcast failed.', [
                    'dm_message_id' => $message->dm_message_id,
                    'conversation_id' => $this->conversation->conversation_id,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        $this->messages[] = $message->toArray();
        $this->latestMessageId = (int) $message->dm_message_id;
        $this->body = '';
        $this->parentId = null;
        $this->replyPreview = '';
        $this->markRead();
        $this->dispatch('message-sent');
    }

    private function parseMentions(string $body, DirectMessage $message): void
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
            ->map(function (User $user) use ($authUser): Notification {
                $notification = new Notification([
                    'user_id' => $user->user_id,
                    'sender_id' => $authUser->user_id,
                    'type' => 'tag',
                    'text' => $authUser->username . ' mentioned you in a direct message',
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
            'typing-dm-' . $this->conversation->conversation_id . '-' . $userId,
            auth()->user()->username,
            now()->addSeconds(6)
        );

        app()->terminating(function () use ($userId): void {
            try {
                broadcast(new UserTyping(
                    $userId,
                    auth()->user()->username,
                    'dm',
                    $this->conversation->conversation_id,
                ))->toOthers();
            } catch (\Throwable $e) {
                Log::debug('Direct message typing broadcast failed.', [
                    'conversation_id' => $this->conversation->conversation_id,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    public function checkTyping(): void
    {
        $userId = auth()->user()->user_id;
        $conversationId = $this->conversation->conversation_id;
        $participants = Cache::remember(
            "dm-participant-ids-{$conversationId}",
            now()->addMinute(),
            fn () => $this->conversation->dmParticipants()->pluck('user_id')->all()
        );
        $typingNames = [];
        foreach ($participants as $participantId) {
            if ((int) $participantId === $userId) continue;
            $name = Cache::get('typing-dm-' . $this->conversation->conversation_id . '-' . $participantId);
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

    public function refreshMessages(): void
    {
        $newestMessageId = DirectMessage::where('conversation_id', $this->conversation->conversation_id)
            ->latest('sent_at')
            ->latest('dm_message_id')
            ->value('dm_message_id');

        if ((int) $newestMessageId !== (int) $this->latestMessageId || time() - $this->lastFullRefreshAt >= 30) {
            $this->loadMessages();
        }

        $this->checkTyping();
    }

    #[On('echo-private:dm.{conversation.conversation_id},DirectMessageSent')]
    public function onMessageSent(array $data): void
    {
        $this->messages[] = $data;
        $this->latestMessageId = (int) ($data['dm_message_id'] ?? $this->latestMessageId);
        $this->markRead();
        $this->dispatch('scroll-to-bottom');
    }

    #[On('echo-private:dm.{conversation.conversation_id},UserTyping')]
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
        return view('livewire.direct-message-window');
    }
}
