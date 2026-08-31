<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Update the database column
            $user->update(['last_seen_at' => now()]);

            // Also keep a cache for quick lookups (optional but handy)
            Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(5));
            Cache::put('user-last-seen-' . $user->id, now(), now()->addMinutes(5));
        }

        return $next($request);
    }
}
