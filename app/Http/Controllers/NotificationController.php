<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get unseen notification count (JSON).
     */
    public function count(Request $request): JsonResponse
    {
        $count = Notification::where('user_id', $request->user()->user_id)
            ->where('is_seen', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get all notifications for the current user (JSON).
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', $request->user()->user_id)
            ->with([
                'sender:user_id,username,avatar_url',
                'workspace:workspace_id,name',
            ])
            ->select('id', 'user_id', 'sender_id', 'type', 'workspace_id', 'channel_id', 'message_id', 'text', 'is_seen', 'created_at')
            ->latest('created_at')
            ->limit(30)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark all notifications as seen (JSON).
     */
    public function markAllSeen(Request $request): JsonResponse
    {
        $userId = $request->user()->user_id;

        Notification::where('user_id', $userId)
            ->where('is_seen', false)
            ->update(['is_seen' => true]);

        \Illuminate\Support\Facades\Cache::forget('user-notif-count-' . $userId);
        \Illuminate\Support\Facades\Cache::forget('user-notifs-' . $userId);
        \Illuminate\Support\Facades\Cache::forget('layout-data-' . $userId);

        return response()->json(['ok' => true]);
    }
}
