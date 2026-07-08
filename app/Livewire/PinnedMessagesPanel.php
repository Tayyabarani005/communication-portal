<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Channel;
use App\Models\Message;
use App\Models\PinnedMessage;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PinnedMessagesPanel extends Component
{
    public Channel $channel;
    public bool $open = false;
    public int $pinCount = 0;

    /** @var array<int, array<string, mixed>> */
    public array $pins = [];

    public function mount(Channel $channel): void
    {
        $this->channel = $channel;
        $this->loadPinCount();
    }

    public function toggle(): void
    {
        $this->open = !$this->open;
        if ($this->open) {
            $this->loadPins();
        }
    }

    public function loadPinCount(): void
    {
        $this->pinCount = (int) \Illuminate\Support\Facades\Cache::remember(
            "channel-pin-count-{$this->channel->channel_id}",
            now()->addDay(),
            fn () => PinnedMessage::where('pinnable_type', Message::class)
                ->join('message', 'pinned_message.pinnable_id', '=', 'message.message_id')
                ->where('message.channel_id', $this->channel->channel_id)
                ->count()
        );
    }

    public function loadPins(): void
    {
        $this->pins = \Illuminate\Support\Facades\Cache::remember(
            "channel-pins-{$this->channel->channel_id}",
            now()->addDay(),
            fn () => PinnedMessage::where('pinnable_type', Message::class)
                ->join('message', 'pinned_message.pinnable_id', '=', 'message.message_id')
                ->where('message.channel_id', $this->channel->channel_id)
                ->select('pinned_message.*')
                ->with([
                    'pinnable' => fn ($query) => $query
                        ->select('message_id', 'channel_id', 'sender_id', 'msg_body', 'sent_at'),
                    'pinnable.sender:user_id,username',
                    'pinnedBy:user_id,username',
                ])
                ->latest('pinned_message.created_at')
                ->get()
                ->toArray()
        );

        $this->pinCount = count($this->pins);
    }

    public function unpin(int $pinId): void
    {
        $this->authorize('sendMessage', $this->channel);

        PinnedMessage::findOrFail($pinId)->delete();
        $this->loadPins();
    }

    public function render(): View
    {
        return view('livewire.pinned-messages-panel');
    }
}
