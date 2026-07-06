<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkspaceMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Search users by username or name (JSON, for @mention autocomplete and DM modal).
     * If workspace_id is provided, restrict results to members of that workspace only.
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        $workspaceId = $request->input('workspace_id');

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $authId = auth()->user()->user_id;

        $query = User::where('user_id', '!=', $authId)
            ->where(function ($q2) use ($q) {
                $q2->where('username', 'like', "%{$q}%")
                   ->orWhere('name', 'like', "%{$q}%");
            });

        // If a workspace is specified, only return members of that workspace
        if ($workspaceId) {
            $memberUserIds = WorkspaceMember::where('workspace_id', $workspaceId)
                ->pluck('user_id');
            $query->whereIn('user_id', $memberUserIds);
        }

        $users = $query
            ->select('user_id', 'username', 'name', 'avatar_url')
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}
