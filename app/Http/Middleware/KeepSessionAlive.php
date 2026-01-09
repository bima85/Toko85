<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KeepSessionAlive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Touch session to prevent expiry
        if ($request->hasSession()) {
            $request->session()->put('last_activity', now()->timestamp);
        }

        return $next($request);
    }
}
