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
            $last    = session('last_activity_at');
            $timeout = $this->timeoutFor($request);

            if ($last !== null && (time() - $last) > $timeout) {
                // Flush session only — intentionally NOT calling Auth::logout() so
                // the remember cookie is preserved. Users who checked "Keep me signed
                // in" are automatically re-authenticated on their next visit via the
                // remember cookie; users without it must log in again.
                $request->session()->flush();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired.'], 401);
                }

                return redirect()->route('login')
                    ->with('status', 'Your session expired due to inactivity. Please sign in again.');
            }

            session(['last_activity_at' => time()]);
        }

        return $next($request);
    }

    /**
     * Idle timeout in seconds.
     * Remembered users get 8 hours; everyone else gets SESSION_LIFETIME (30 min default).
     */
    private function timeoutFor(Request $request): int
    {
        if (session('user_remembered', false)) {
            return 8 * 60 * 60; // 8 hours for "Keep me signed in" users
        }

        return (int) config('session.lifetime', 30) * 60;
    }
}
