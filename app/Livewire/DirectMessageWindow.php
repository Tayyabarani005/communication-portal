<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Events\DirectMessageSent;
use App\Events\UserTyping;
use App\Models\DirectMessage;
use App\Models\DmConversation;
use App\Models\DmReadState;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\View\View;
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
            ->with(['sender', 'replies.sender', 'files'])
            ->latest('sent_at')
            ->limit(50)
            ->get()
            ->reverse()
            ->values()
            ->toArray();
    }

    private function markRead(): void
    {
        $last = DirectMessage::where('conversation_id', $this->conversation->conversation_id)
            ->latest('sent_at')
            ->first();

        if ($last) {
            DmReadState::updateOrCreate(
                ['conversation_id' => $this->conversation->conversation_id, 'user_id' => auth()->id()],
                ['last_read_message_id' => $last->dm_message_id, 'last_read_at' => now()]
            );
        }
    }

    public function send(): void
    {
        $this->authorize('view', $this->conversation);
        $this->validate([
            'body' => ['required', 'string', 'max:4000'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $message = DirectMessage::create([
            'conversation_id' => $this->conversation->conversation_id,
            'sender_id'       => auth()->user()->user_id,
            'parent_id'       => $this->parentId,
            'msg_body'        => $this->body,
            'msg_type'        => 'text',
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
        $this->parseMentions($this->body, $message);

        $message->load('sender');
        broadcast(new DirectMessageSent($message))->toOthers();

        $this->messages[] = $message->toArray();
        $this->body = '';
        $this->parentId = null;
        $this->replyPreview = '';
        $this->markRead();
        $this->dispatch('message-sent');
    }

    private function parseMentions(string $body, DirectMessage $message): void
    {
        if (preg_match_all('/@(\w+)/', $body, $matches)) {
            $usernames = array_unique($matches[1]);
            $users = User::whereIn('username', $usernames)
                ->where('user_id', '!=', auth()->user()->user_id)
                ->get();

            foreach ($users as $user) {
                Notification::create([
                    'user_id'    => $user->user_id,
                    'sender_id'  => auth()->user()->user_id,
                    'type'       => 'tag',
                    'text'       => auth()->user()->username . ' mentioned you in a direct message',
                ]);
            }
        }
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
        broadcast(new UserTyping(
            auth()->user()->user_id,
            auth()->user()->username,
            'dm',
            $this->conversation->conversation_id,
        ))->toOthers();
    }

    public function refreshMessages(): void
    {
        $this->loadMessages();
    }

    #[On('echo-private:dm.{conversation.conversation_id},DirectMessageSent')]
    public function onMessageSent(array $data): void
    {
        $this->messages[] = $data;
        $this->markRead();
        $this->dispatch('scroll-to-bottom');
    }

    #[On('echo-private:dm.{conversation.conversation_id},UserTyping')]
    public function onUserTyping(array $data): void
    {
        if ((int) $data['user_id'] !== auth()->user()->user_id) {
            $this->typingUser = $data['username'];
            $this->showTyping = true;
            $this->dispatch('hide-typing-after-delay');
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
