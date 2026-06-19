<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeout = (int) config('session.lifetime', 30) * 60; // seconds
            $last    = session('last_activity_at');

            if ($last !== null && (time() - $last) > $timeout) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired.'], 401);
                }

                return redirect()->route('login')
                    ->with('status', 'Your session expired due to inactivity. Please sign in again.');
            }

            // Refresh the last-activity timestamp on every authenticated request.
            session(['last_activity_at' => time()]);
        }

        return $next($request);
    }
}
