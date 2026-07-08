<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastSeen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userId = Auth::user()->user_id;
            if (Cache::add('user-online-throttle-' . $userId, true, now()->addSeconds(30))) {
                Cache::put('user-online-' . $userId, true, now()->addMinutes(5));
            }
        }

        return $next($request);
    }
}
