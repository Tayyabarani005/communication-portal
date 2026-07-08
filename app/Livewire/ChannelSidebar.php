<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Channel;
use App\Models\Message;
use App\Models\Workspace;
use App\Models\WorkspaceMember;
use App\Enums\WorkspaceRole;
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
        $workspaceId = $this->workspace->workspace_id;

        // Fetch all public channels + private channels the user is a member of
        $channels = \Illuminate\Support\Facades\Cache::remember(
            "user-workspace-channels-{$userId}-{$workspaceId}",
            120,
            fn () => Channel::query()
                ->where('workspace_id', $workspaceId)
                ->where(function ($query) use ($userId): void {
                    $query->where('is_private', false)
                        ->orWhereExists(function ($q) use ($userId): void {
                            $q->select(\DB::raw(1))
                                ->from('channel_user')
                                ->whereColumn('channel_user.channel_id', 'channel.channel_id')
                                ->where('channel_user.user_id', $userId);
                        });
                })
                ->select('channel_id', 'workspace_id', 'channel_name', 'is_private')
                ->get()
        );

        $channelIds = $channels->pluck('channel_id');

        // Only query unread counts for channels the user is actually a member of (in channel_user).
        $joinedChannelIds = \DB::table('channel_user')
            ->where('user_id', $userId)
            ->whereIn('channel_id', $channelIds)
            ->pluck('channel_id');

        $unreadCounts = $joinedChannelIds->isEmpty()
            ? collect()
            : Message::query()
                ->leftJoin('channel_read_state as crs', function ($join) use ($userId): void {
                    $join->on('message.channel_id', '=', 'crs.channel_id')
                        ->where('crs.user_id', '=', $userId);
                })
                ->whereIn('message.channel_id', $joinedChannelIds)
                ->where(function ($query): void {
                    $query->whereNull('crs.last_read_message_id')
                        ->orWhereColumn('message.message_id', '>', 'crs.last_read_message_id');
                })
                ->groupBy('message.channel_id')
                ->selectRaw('message.channel_id, COUNT(*) as unread_count')
                ->pluck('unread_count', 'message.channel_id');

        $isAdmin = WorkspaceMember::where('workspace_id', $this->workspace->workspace_id)
            ->where('user_id', $userId)
            ->where('role', WorkspaceRole::ADMIN->value)
            ->exists();

        $this->channels = $channels
            ->map(function (Channel $channel) use ($unreadCounts, $joinedChannelIds, $isAdmin) {
                $isJoined = $joinedChannelIds->contains($channel->channel_id);
                return [
                    'channel_id'   => $channel->channel_id,
                    'channel_name' => $channel->channel_name,
                    'is_private'   => $channel->is_private,
                    'unread'       => $isJoined ? (int) ($unreadCounts[$channel->channel_id] ?? 0) : 0,
                    'url'          => route('channels.show', $channel),
                    'delete_url'   => route('channels.destroy', $channel),
                    'can_delete'   => $isAdmin,
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
