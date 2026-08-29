<?php



namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotSuspended
{
    public function handle($request, Closure $next)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (Auth::check() && $user->isSuspended()) {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Your account has been suspended.');
        }

        return $next($request);
    }
}
