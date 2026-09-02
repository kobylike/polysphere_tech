<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (
            $user
            && $user->must_change_password
            && !$request->routeIs('password.change.force')
            && !$request->routeIs('logout')
            && !$request->routeIs('livewire.*')
        ) {
            return redirect()->route('password.change.force');
        }

        return $next($request);
    }
}
