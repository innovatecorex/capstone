<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * ForcePasswordReset
 *
 * If an authenticated user still has password_reset_required = true, every
 * request is redirected to the mandatory reset page until they actually change
 * their password. This closes the bypass where a user could navigate to any
 * other URL (or re-open the site in a new tab) and skip the forced reset.
 *
 * Allowed through without redirect:
 *   - the force-reset page itself (show + submit)
 *   - logout
 *   - the health check
 */
class ForcePasswordReset
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->password_reset_required) {
            // Routes the user is allowed to hit while the flag is still set.
            $allowed = [
                'password.force-reset',
                'password.force-reset.update',
                'logout',
            ];

            $routeName = $request->route()?->getName();

            if (!in_array($routeName, $allowed, true)) {
                // For normal page requests, redirect to the reset page.
                // For AJAX/JSON, return 409 so the client can react.
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Password reset required before continuing.',
                    ], 409);
                }
                return redirect()->route('password.force-reset');
            }
        }

        return $next($request);
    }
}
