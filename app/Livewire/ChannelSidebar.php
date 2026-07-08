<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Channel;
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

        $channels = Channel::where('workspace_id', $this->workspace->workspace_id)
            ->whereHas('users', fn($q) => $q->where('channel_user.user_id', $userId))
            ->select('channel_id', 'workspace_id', 'channel_name', 'is_private')
            ->get();

        $channelIds = $channels->pluck('channel_id');

        $unreadCounts = $channelIds->isEmpty()
            ? collect()
            : Message::query()
                ->leftJoin('channel_read_state as crs', function ($join) use ($userId): void {
                    $join->on('message.channel_id', '=', 'crs.channel_id')
                        ->where('crs.user_id', '=', $userId);
                })
                ->whereIn('message.channel_id', $channelIds)
                ->where(function ($query): void {
                    $query->whereNull('crs.last_read_message_id')
                        ->orWhereColumn('message.message_id', '>', 'crs.last_read_message_id');
                })
                ->groupBy('message.channel_id')
                ->selectRaw('message.channel_id, COUNT(*) as unread_count')
                ->pluck('unread_count', 'message.channel_id');

        $this->channels = $channels
            ->map(function (Channel $channel) use ($unreadCounts) {
                return [
                    'channel_id'   => $channel->channel_id,
                    'channel_name' => $channel->channel_name,
                    'is_private'   => $channel->is_private,
                    'unread'       => (int) ($unreadCounts[$channel->channel_id] ?? 0),
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
