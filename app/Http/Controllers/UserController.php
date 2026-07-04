<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Search users by username or email (JSON, for DM modal).
     */
    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $authId = auth()->user()->user_id;

        $users = User::where('user_id', '!=', $authId)
            ->where(function ($query) use ($q) {
                $query->where('username', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('name', 'like', "%{$q}%");
            })
            ->select('user_id', 'username', 'name', 'avatar_url')
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}
