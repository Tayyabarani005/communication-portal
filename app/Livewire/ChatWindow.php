<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Models\Channel;
use App\Models\ChannelReadState;
use App\Models\Message;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatWindow extends Component
{
    public Channel $channel;
    public string $body = '';
    public ?int $parentId = null;
    public string $replyPreview = '';
    public bool $showTyping = false;
    public string $typingUser = '';

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

        $this->validate(['body' => ['required', 'string', 'max:4000']]);

        $message = Message::create([
            'channel_id' => $this->channel->channel_id,
            'sender_id'  => auth()->user()->user_id,
            'parent_id'  => $this->parentId,
            'msg_body'   => $this->body,
            'msg_type'   => 'text',
            'sent_at'    => now(),
        ]);

        $message->load('sender');

        broadcast(new MessageSent($message))->toOthers();

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
            'channel',
            $this->channel->channel_id,
        ))->toOthers();
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
            // Auto-hide after 3 seconds
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
        return view('livewire.chat-window');
    }
}
