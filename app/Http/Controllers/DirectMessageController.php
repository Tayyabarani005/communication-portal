<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DmConversation;
use App\Models\DmParticipant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectMessageController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = DmConversation::whereHas('dmParticipants', function ($q) use ($request) {
            $q->where('user_id', $request->user()->user_id);
        })->with(['dmParticipants.user', 'directMessages' => fn($q) => $q->latest('sent_at')->limit(1)])
          ->get();

        return view('dms.index', compact('conversations'));
    }

    public function show(Request $request, DmConversation $conversation): View
    {
        $this->authorize('view', $conversation);

        $conversation->load(['dmParticipants.user']);

        return view('dms.show', compact('conversation'));
    }

    public function start(Request $request, User $user): RedirectResponse
    {
        $authUserId = $request->user()->user_id;
        $targetUserId = $user->user_id;

        // Find existing conversation between these two users
        $conversation = DmConversation::whereHas('dmParticipants', fn($q) => $q->where('user_id', $authUserId))
            ->whereHas('dmParticipants', fn($q) => $q->where('user_id', $targetUserId))
            ->withCount('dmParticipants')
            ->having('dm_participants_count', '=', 2)
            ->first();

        if (!$conversation) {
            $conversation = DmConversation::create([]);
            DmParticipant::create(['conversation_id' => $conversation->conversation_id, 'user_id' => $authUserId]);
            DmParticipant::create(['conversation_id' => $conversation->conversation_id, 'user_id' => $targetUserId]);
        }

        return redirect()->route('dms.show', $conversation);
    }
}
