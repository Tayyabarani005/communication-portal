<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Events\DirectMessageSent;
use App\Events\UserTyping;
use App\Models\DirectMessage;
use App\Models\DmConversation;
use App\Models\DmReadState;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class DirectMessageWindow extends Component
{
    public DmConversation $conversation;
    public string $body = '';
    public ?int $parentId = null;
    public string $replyPreview = '';
    public bool $showTyping = false;
    public string $typingUser = '';

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
        $this->validate(['body' => ['required', 'string', 'max:4000']]);

        $message = DirectMessage::create([
            'conversation_id' => $this->conversation->conversation_id,
            'sender_id'       => auth()->user()->user_id,
            'parent_id'       => $this->parentId,
            'msg_body'        => $this->body,
            'msg_type'        => 'text',
            'sent_at'         => now(),
        ]);

        $message->load('sender');
        broadcast(new DirectMessageSent($message))->toOthers();

        $this->messages[] = $message->toArray();
        $this->body = '';
        $this->parentId = null;
        $this->replyPreview = '';
        $this->markRead();
        $this->dispatch('message-sent');
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
