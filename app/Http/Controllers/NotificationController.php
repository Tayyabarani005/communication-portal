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
            ->with(['sender', 'workspace'])
            ->latest()
            ->limit(30)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark all notifications as seen (JSON).
     */
    public function markAllSeen(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()->user_id)
            ->where('is_seen', false)
            ->update(['is_seen' => true]);

        return response()->json(['ok' => true]);
    }
}
