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

    /** @var array<int, array<string, mixed>> */
    public array $pins = [];

    public function mount(Channel $channel): void
    {
        $this->channel = $channel;
        $this->loadPins(); // Load on mount so count badge is visible immediately
    }

    public function toggle(): void
    {
        $this->open = !$this->open;
        if ($this->open) {
            $this->loadPins();
        }
    }

    public function loadPins(): void
    {
        $this->pins = PinnedMessage::where('pinnable_type', Message::class)
            ->whereHas('pinnable', fn($q) => $q->where('channel_id', $this->channel->channel_id))
            ->with(['pinnable.sender', 'pinnedBy'])
            ->latest()
            ->get()
            ->toArray();
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
