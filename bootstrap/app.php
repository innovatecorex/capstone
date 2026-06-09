<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\InjectionDefenseMiddleware;
use App\Http\Middleware\SecurityHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Global middleware (runs on every request) ──────────────────────
        $middleware->web(append: [
            SecurityHeaders::class,         // outermost — headers apply to ALL responses
            InjectionDefenseMiddleware::class,
        ]);

        // ── Route-level aliases ────────────────────────────────────────────
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ── Custom 403 page ────────────────────────────────────────────────
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [
                    'message' => $e->getMessage() ?: 'You do not have permission to access this resource.',
                ], 403);
            }
        });

        // ── Expired CSRF token (419) ───────────────────────────────────────
        // When a page sits idle past the session lifetime, the CSRF token
        // expires and forms (including logout) throw a 419 "Page Expired".
        // For logout, just send them to login (they wanted out anyway).
        // For anything else, bounce back to login with a friendly notice.
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->is('logout')) {
                return redirect()->route('login');
            }
            return redirect()->route('login')
                ->with('status', 'Your session expired. Please sign in again.');
        });

        // ── Rate-limit threat event (FRS §Threat Monitoring) ───────────────
        // Every 429 response from Laravel's throttle middleware emits both an
        // audit log entry and a threat event so administrators can see when
        // limits are being hit (signal of credential-stuffing, scraping, etc.)
        $exceptions->reportable(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e) {
            try {
                $request = request();
                \App\Models\AuditLog::record(
                    \App\Models\AuditLog::RATE_LIMIT_EXCEEDED,
                    [
                        'route'  => $request->path(),
                        'method' => $request->method(),
                        'ip'     => $request->ip(),
                    ]
                );
                \App\Models\ThreatEvent::record(
                    'rate_limit_exceeded',
                    'medium',
                    'Rate Limit Exceeded',
                    "Throttle hit on {$request->method()} /{$request->path()} from {$request->ip()}",
                    auth()->id(),
                    $request->path()
                );
            } catch (\Throwable $t) {
                // never let logging break error response
                \Log::warning('rate-limit threat-event log failed: ' . $t->getMessage());
            }
            return false; // don't stop default reporting
        });

    })->create();
