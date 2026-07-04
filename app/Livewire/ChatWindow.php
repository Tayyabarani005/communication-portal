<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\Channel;
use App\Models\ChannelReadState;
use App\Models\Message;
use App\Models\Notification;
use App\Models\PinnedMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
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
            ->with(['sender', 'replies.sender', 'files', 'pins'])
            ->latest('sent_at')
            ->limit($this->perPage)
            ->get()
            ->reverse()
            ->values()
            ->toArray();
    }

    private function markRead(): void
    {
        $lastMessage = Message::where('channel_id', $this->channel->channel_id)
            ->latest('sent_at')
            ->first();

        if ($lastMessage) {
            ChannelReadState::updateOrCreate(
                ['channel_id' => $this->channel->channel_id, 'user_id' => auth()->id()],
                ['last_read_message_id' => $lastMessage->message_id, 'last_read_at' => now()]
            );
        }
    }

    public function send(): void
    {
        $this->authorize('sendMessage', $this->channel);

        $this->validate([
            'body' => [empty($this->attachment) ? 'required' : 'nullable', 'string', 'max:4000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $message = Message::create([
            'channel_id' => $this->channel->channel_id,
            'sender_id'  => auth()->user()->user_id,
            'parent_id'  => $this->parentId,
            'msg_body'   => $this->body ?? '',
            'msg_type'   => 'text',
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

        $message->load('sender');

        broadcast(new MessageSent($message))->toOthers();

        $this->messages[] = $message->toArray();
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
        if (preg_match_all('/@(\w+)/', $body, $matches)) {
            $usernames = array_unique($matches[1]);
            $users = User::whereIn('username', $usernames)
                ->where('user_id', '!=', auth()->user()->user_id)
                ->get();

            foreach ($users as $user) {
                Notification::create([
                    'user_id'      => $user->user_id,
                    'sender_id'    => auth()->user()->user_id,
                    'type'         => 'tag',
                    'channel_id'   => $this->channel->channel_id,
                    'message_id'   => $message->message_id,
                    'text'         => auth()->user()->username . ' mentioned you in #' . $this->channel->channel_name,
                ]);
            }
        }
    }

    public function pinMessage(int $messageId): void
    {
        $this->authorize('sendMessage', $this->channel);

        $exists = PinnedMessage::where('pinnable_id', $messageId)
            ->where('pinnable_type', Message::class)
            ->exists();

        if (!$exists) {
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
        \Illuminate\Support\Facades\Cache::put(
            'typing-channel-' . $this->channel->channel_id . '-' . $userId,
            auth()->user()->username,
            now()->addSeconds(6)
        );

        try {
            broadcast(new UserTyping(
                $userId,
                auth()->user()->username,
                'channel',
                $this->channel->channel_id,
            ))->toOthers();
        } catch (\Exception $e) {}
    }

    public function checkTyping(): void
    {
        $userId = auth()->user()->user_id;
        $members = $this->channel->workspace->workspaceMembers()->pluck('user_id');
        $typingNames = [];
        foreach ($members as $memberId) {
            if ((int) $memberId === $userId) continue;
            $name = \Illuminate\Support\Facades\Cache::get('typing-channel-' . $this->channel->channel_id . '-' . $memberId);
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
        $this->loadMessages();
        $this->checkTyping();
    }

    #[On('echo-private:channel.{channel.channel_id},MessageSent')]
    public function onMessageSent(array $data): void
    {
        $this->messages[] = $data;
        $this->markRead();
        $this->dispatch('scroll-to-bottom');
    }

    #[On('echo-private:channel.{channel.channel_id},UserTyping')]
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
