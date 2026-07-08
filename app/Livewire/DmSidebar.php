<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\DirectMessage;
use App\Models\DmConversation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class DmSidebar extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $conversations = [];
    public ?int $activeConversationId = null;

    public function mount(): void
    {
        $this->activeConversationId = (int) request()->route('conversation')?->conversation_id;
        $this->loadConversations();
    }

    public function loadConversations(): void
    {
        $userId = auth()->user()->user_id;

        $conversations = DmConversation::whereHas('dmParticipants', fn($q) => $q->where('user_id', $userId))
            ->with(['dmParticipants.user'])
            ->get();

        $conversationIds = $conversations->pluck('conversation_id');

        $unreadCounts = $conversationIds->isEmpty()
            ? collect()
            : DirectMessage::query()
                ->leftJoin('dm_read_state as drs', function ($join) use ($userId): void {
                    $join->on('direct_message.conversation_id', '=', 'drs.conversation_id')
                        ->where('drs.user_id', '=', $userId);
                })
                ->whereIn('direct_message.conversation_id', $conversationIds)
                ->where(function ($query): void {
                    $query->whereNull('drs.last_read_message_id')
                        ->orWhereColumn('direct_message.dm_message_id', '>', 'drs.last_read_message_id');
                })
                ->groupBy('direct_message.conversation_id')
                ->selectRaw('direct_message.conversation_id, COUNT(*) as unread_count')
                ->pluck('unread_count', 'direct_message.conversation_id');

        $this->conversations = $conversations
            ->map(function (DmConversation $conv) use ($userId, $unreadCounts) {
                $otherUser = $conv->dmParticipants
                    ->firstWhere('user_id', '!=', $userId)?->user;

                return [
                    'conversation_id' => $conv->conversation_id,
                    'other_username'  => $otherUser?->username ?? 'Unknown',
                    'other_avatar'    => $otherUser?->avatar_url,
                    'is_online'       => $otherUser ? $otherUser->isOnline() : false,
                    'unread'          => (int) ($unreadCounts[$conv->conversation_id] ?? 0),
                    'url'             => route('dms.show', $conv),
                ];
            })
            ->toArray();
    }

    #[On('message-sent')]
    public function refresh(): void
    {
        $this->loadConversations();
    }

    public function render(): View
    {
        return view('livewire.dm-sidebar');
    }
}
