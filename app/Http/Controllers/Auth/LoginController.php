<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ThreatEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:100'],
            // Cap the password length to stop oversized-input abuse, but never
            // restrict its characters — passwords must allow symbols.
            'password' => ['required', 'string', 'max:200'],
        ]);

        // reCAPTCHA v2 verification (fail-open on network error)
        $recaptchaSecret = config('services.recaptcha.secret_key');
        if (!empty($recaptchaSecret)) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (empty($recaptchaResponse)) {
                return back()->withInput($request->only('username'))->withErrors(['username' => 'Please complete the reCAPTCHA verification.']);
            }
            try {
                $verify = \Illuminate\Support\Facades\Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', ['secret' => $recaptchaSecret, 'response' => $recaptchaResponse, 'remoteip' => $request->ip()]);
                if ($verify->successful() && $verify->json('success') === false) {
                    return back()->withInput($request->only('username'))->withErrors(['username' => 'reCAPTCHA verification failed. Please try again.']);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('reCAPTCHA verify failed (allowing login): ' . $e->getMessage());
            }
        }

        $username = $request->input('username');
        $password = $request->input('password');

        // ── Entry-point character whitelist ────────────────────────────────
        // The identifier is a username ("stu.dan025"), a 9-12 digit LRN, or an
        // employee number — all built from letters, digits and . _ - @ only.
        // Anything else (quotes, <>, ;, =, spaces, backslashes, null bytes …)
        // is an injection/XSS probe: reject with a GENERIC message (never
        // reveal it was a format failure) and log it as a threat. The DB is
        // never touched for a rejected identifier.
        if (!preg_match('/^[A-Za-z0-9._@-]+$/', $username)) {
            $this->recordMaliciousLogin($request, $username);

            return back()->withErrors(['username' => 'Invalid credentials. Please try again.']);
        }

        // username is AES-256 encrypted; look up by its deterministic hash.
        $user = User::where('username_hash', User::hashFor('username', $username))->first();

        if (!$user) {
            // Perform a dummy hash check so the response time is the same whether
            // the username exists or not — prevents timing-based user enumeration.
            Hash::check($password, '$2y$12$UH2hwCyB9LSswaPW7o/q3.D.13NQy8auabkTCID4HUR9oZzuQJIyq');
            AuditLog::record(
                AuditLog::LOGIN_FAILED,
                ['username_attempted' => $username, 'reason' => 'User not found']
            );
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Invalid credentials. Please try again.']);
        }

        // Auto-unlock if lockout expired
        $user->checkAndUnlock();
        $user->refresh();

        if ($user->status === 'locked') {
            // Use the same generic message as unknown-user and deactivated branches.
            // The internal threat log captures the real lock state for admins.
            $minutesLeft = max(1, (int) now()->diffInMinutes($user->locked_until, false));
            AuditLog::record(
                AuditLog::LOGIN_FAILED,
                ['reason' => 'Account locked', 'minutes_remaining' => $minutesLeft],
                $user->id,
                $user->full_name
            );
            ThreatEvent::record(
                'brute_force',   // must match the threat_events enum + dashboard filter
                'medium',
                'Login Attempt on Locked Account',
                "Login attempted on locked account [{$user->username}]. {$minutesLeft} min remaining.",
                $user->id
            );
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Invalid credentials. Please try again.']);
        }

        if ($user->status === 'deactivated') {
            // Use generic message — specific wording would reveal that the username exists.
            AuditLog::record(AuditLog::LOGIN_FAILED, ['reason' => 'Account deactivated'], $user->id);
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Invalid credentials. Please try again.']);
        }

        if (!Hash::check($password, $user->password)) {
            $user->incrementFailedAttempts();
            $user->refresh();

            AuditLog::record(
                AuditLog::LOGIN_FAILED,
                ['reason' => 'Wrong password', 'failed_attempts' => $user->failed_attempts],
                $user->id, $user->full_name
            );

            if ($user->status === 'locked') {
                AuditLog::record(AuditLog::ACCOUNT_LOCKED, null, $user->id, $user->full_name);
                ThreatEvent::record('brute_force', 'critical', 'Account Locked — Brute Force',
                    "Account [{$user->username}] locked after 5 consecutive failed login attempts.", $user->id);
            }

            $remaining = max(0, 5 - $user->failed_attempts);
            $msg = $remaining > 0
                ? "Invalid credentials. {$remaining} attempt(s) remaining before lockout."
                : 'Account locked for 10 minutes due to too many failed attempts.';

            return back()->withInput($request->only('username'))->withErrors(['username' => $msg]);
        }

        // Success
        $remember = $request->boolean('remember');
        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Store remember flag so SessionTimeout can apply the extended idle window.
        session(['user_remembered' => $remember]);

        $user->clearFailedAttempts();
        $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);

        AuditLog::record(AuditLog::LOGIN_SUCCESS, ['location' => \App\Services\GeoLocator::describe($request->ip())], $user->id, $user->full_name);

        if ($user->password_reset_required) {
            return redirect()->route('password.force-reset')
                ->with('info', 'Please set a new password before continuing.');
        }

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            AuditLog::record(AuditLog::LOGOUT, null, $user->id, $user->full_name);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Record a login identifier that failed the character whitelist as an
     * injection threat — same channel InjectionDefenseMiddleware uses, so it
     * surfaces on the Threat Events dashboard with the source IP.
     *
     * The attacker's raw string is never stored verbatim: it is reduced to
     * printable ASCII, length-capped and HTML-escaped so a crafted payload
     * cannot poison the log or the dashboard that renders it.
     */
    private function recordMaliciousLogin(Request $request, string $identifier): void
    {
        $preview = preg_replace('/[^\x20-\x7E]/', '?', $identifier); // printable ASCII only
        $preview = e(mb_substr((string) $preview, 0, 60));           // cap + HTML-escape

        AuditLog::record(AuditLog::INJECTION_BLOCKED, [
            'context' => 'login_identifier',
            'route'   => $request->path(),
            'method'  => $request->method(),
        ]);

        ThreatEvent::record(
            'injection',   // must match the threat_events enum + dashboard filter
            'high',
            'Malicious Login Input Blocked',
            "A login identifier was rejected by the character whitelist. Sanitized preview: \"{$preview}\".",
            null
        );
    }

    private function redirectByRole(User $user)
    {
        return match($user->role_id) {
            '04' => redirect()->route('admin.dashboard'),
            '03' => redirect()->route('registrar.dashboard'),
            '02' => redirect()->route('faculty.dashboard'),
            '01' => redirect()->route('student.dashboard'),
            default => redirect()->route('admin.dashboard'),
        };
    }
}
