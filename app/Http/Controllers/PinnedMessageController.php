<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Message;
use App\Models\PinnedMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PinnedMessageController extends Controller
{
    public function store(Request $request, Channel $channel, Message $message): RedirectResponse
    {
        $this->authorize('sendMessage', $channel);

        // Avoid duplicate pins
        PinnedMessage::firstOrCreate([
            'pinnable_id'   => $message->message_id,
            'pinnable_type' => Message::class,
        ], [
            'pinned_by' => $request->user()->user_id,
        ]);

        return back()->with('success', 'Message pinned.');
    }

    public function destroy(Request $request, Channel $channel, PinnedMessage $pin): RedirectResponse
    {
        $this->authorize('sendMessage', $channel);

        $pin->delete();

        return back()->with('success', 'Message unpinned.');
    }
}
