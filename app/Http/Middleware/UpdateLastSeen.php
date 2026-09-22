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

            // ── Quiet write ────────────────────────────────────────
            // forceFill + saveQuietly updates last_seen_at WITHOUT firing
            // model events, so Spatie's activity log stays clean.
            $user->forceFill(['last_seen_at' => now()])->saveQuietly();

            // Cache keeps the "who's online" lookups fast
            Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(5));
            Cache::put('user-last-seen-' . $user->id, now(), now()->addMinutes(5));
        }

        return $next($request);
    }
}
