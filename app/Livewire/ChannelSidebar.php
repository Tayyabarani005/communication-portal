<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Channel;
use App\Models\ChannelReadState;
use App\Models\Message;
use App\Models\Workspace;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ChannelSidebar extends Component
{
    public Workspace $workspace;
    public ?int $activeChannelId = null;

    /** @var array<int, array<string, mixed>> */
    public array $channels = [];

    public function mount(Workspace $workspace): void
    {
        $this->workspace = $workspace;
        $this->activeChannelId = (int) request()->route('channel')?->channel_id;
        $this->loadChannels();
    }

    public function loadChannels(): void
    {
        $userId = auth()->user()->user_id;

        $this->channels = Channel::where('workspace_id', $this->workspace->workspace_id)
            ->whereHas('users', fn($q) => $q->where('channel_user.user_id', $userId))
            ->with(['messages' => fn($q) => $q->latest('sent_at')->limit(1)])
            ->get()
            ->map(function (Channel $channel) use ($userId) {
                $readState = ChannelReadState::where('channel_id', $channel->channel_id)
                    ->where('user_id', $userId)
                    ->first();

                $unread = Message::where('channel_id', $channel->channel_id)
                    ->when($readState?->last_read_message_id, fn($q, $id) => $q->where('message_id', '>', $id))
                    ->count();

                return [
                    'channel_id'   => $channel->channel_id,
                    'channel_name' => $channel->channel_name,
                    'is_private'   => $channel->is_private,
                    'unread'       => $unread,
                    'url'          => route('channels.show', $channel),
                ];
            })
            ->toArray();
    }

    #[On('message-sent')]
    public function refresh(): void
    {
        $this->loadChannels();
    }

    public function render(): View
    {
        return view('livewire.channel-sidebar');
    }
}
