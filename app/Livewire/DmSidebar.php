<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\DirectMessage;
use App\Models\DmConversation;
use App\Models\DmReadState;
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

        $this->conversations = DmConversation::whereHas('dmParticipants', fn($q) => $q->where('user_id', $userId))
            ->with(['dmParticipants.user'])
            ->get()
            ->map(function (DmConversation $conv) use ($userId) {
                $otherUser = $conv->dmParticipants
                    ->firstWhere('user_id', '!=', $userId)?->user;

                $readState = DmReadState::where('conversation_id', $conv->conversation_id)
                    ->where('user_id', $userId)
                    ->first();

                $unread = DirectMessage::where('conversation_id', $conv->conversation_id)
                    ->when($readState?->last_read_message_id, fn($q, $id) => $q->where('dm_message_id', '>', $id))
                    ->count();

                return [
                    'conversation_id' => $conv->conversation_id,
                    'other_username'  => $otherUser?->username ?? 'Unknown',
                    'other_avatar'    => $otherUser?->avatar_url,
                    'unread'          => $unread,
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
