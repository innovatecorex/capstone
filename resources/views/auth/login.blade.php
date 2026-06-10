{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login — EncryptEd</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="{{ asset('css/encrypted.css') }}">
  <style>
    *, *::before, *::after { box-sizing: border-box; }

    body {
      background:
        radial-gradient(1200px 700px at 15% 10%, rgba(251,191,36,.07), transparent 60%),
        radial-gradient(900px 500px at 90% 90%, rgba(28,58,110,.35), transparent 60%),
        var(--navy-dark);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      font-family: var(--font-body);
    }

    /* Grid pattern overlay */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(251,191,36,.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(251,191,36,.04) 1px, transparent 1px);
      background-size: 48px 48px;
      pointer-events: none;
    }

    /* ── Two-column wrapper ──────────────────────────────────────────── */
    .lp-wrap {
      display: grid;
      grid-template-columns: 1fr 1fr;
      width: 100%;
      max-width: 920px;
      min-height: 580px;
      border-radius: 22px;
      overflow: hidden;
      box-shadow: 0 32px 80px rgba(0,0,0,.55), 0 0 0 1px rgba(251,191,36,.12);
      animation: fadeUp .4s ease both;
      position: relative;
      z-index: 1;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(14px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── LEFT PANEL — Branding ───────────────────────────────────────── */
    .lp-left {
      background: linear-gradient(155deg, #0d1f3c 0%, #0a1628 55%, #050d1e 100%);
      padding: 48px 40px;
      display: flex;
      flex-direction: column;
      position: relative;
      overflow: hidden;
    }

    /* decorative blobs */
    .lp-left::before {
      content: '';
      position: absolute;
      top: -80px; left: -80px;
      width: 280px; height: 280px;
      background: radial-gradient(circle, rgba(251,191,36,.08) 0%, transparent 70%);
      pointer-events: none;
    }
    .lp-left::after {
      content: '';
      position: absolute;
      bottom: -60px; right: -60px;
      width: 220px; height: 220px;
      background: radial-gradient(circle, rgba(37,99,235,.14) 0%, transparent 70%);
      pointer-events: none;
    }

    .lp-logos {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 36px;
    }

    .lp-logo-enc {
      height: 40px;
      width: auto;
      filter: brightness(0) invert(1);
      opacity: .92;
    }

    .lp-logo-divider {
      width: 1px;
      height: 32px;
      background: rgba(255,255,255,.15);
    }

    .lp-logo-school {
      height: 44px;
      width: 44px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid rgba(255,255,255,.2);
      filter: brightness(0) invert(1);
      opacity: .85;
    }

    .lp-heading {
      font-size: 1.5rem;
      font-weight: 800;
      color: #fff;
      letter-spacing: -.02em;
      line-height: 1.25;
      margin-bottom: 6px;
    }

    .lp-sub {
      font-size: .8rem;
      color: rgba(251,191,36,.8);
      font-weight: 600;
      letter-spacing: .06em;
      text-transform: uppercase;
      margin-bottom: 28px;
    }

    .lp-divider {
      width: 40px;
      height: 3px;
      background: linear-gradient(90deg, var(--yellow-dark), var(--yellow));
      border-radius: 99px;
      margin-bottom: 28px;
    }

    .lp-features {
      display: flex;
      flex-direction: column;
      gap: 16px;
      flex: 1;
    }

    .lp-feature {
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .lp-feature-icon {
      width: 34px;
      height: 34px;
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      margin-top: 1px;
    }

    .lp-feature-icon svg { width: 17px; height: 17px; }

    .lp-feature-body {}
    .lp-feature-title {
      font-size: .82rem;
      font-weight: 700;
      color: #fff;
      margin-bottom: 2px;
    }
    .lp-feature-desc {
      font-size: .73rem;
      color: rgba(255,255,255,.45);
      line-height: 1.5;
    }

    .lp-bottom {
      margin-top: 32px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .lp-apply-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      padding: .6rem 1.1rem;
      background: linear-gradient(135deg, #1e40af, #2563eb);
      color: #fff;
      border-radius: 10px;
      font-size: .8rem;
      font-weight: 700;
      text-decoration: none;
      letter-spacing: .01em;
      transition: opacity .15s;
      box-shadow: 0 4px 14px rgba(37,99,235,.35);
    }
    .lp-apply-btn:hover { opacity: .88; }
    .lp-apply-btn svg { width: 15px; height: 15px; flex-shrink: 0; }

    .lp-about-btn {
      background: none;
      border: 1px solid rgba(251,191,36,.3);
      color: rgba(251,191,36,.75);
      padding: .45rem .9rem;
      border-radius: 10px;
      font-size: .75rem;
      font-weight: 700;
      letter-spacing: .04em;
      cursor: pointer;
      text-transform: uppercase;
      width: fit-content;
      transition: border-color .15s, color .15s;
    }
    .lp-about-btn:hover { border-color: rgba(251,191,36,.7); color: var(--yellow); }

    /* ── RIGHT PANEL — Form ──────────────────────────────────────────── */
    .lp-right {
      background: #fff;
      display: flex;
      flex-direction: column;
      position: relative;
    }

    /* Yellow top accent */
    .lp-right::before {
      content: '';
      display: block;
      height: 5px;
      background: linear-gradient(90deg, var(--yellow-dark) 0%, var(--yellow-bright) 50%, var(--yellow) 100%);
      flex-shrink: 0;
    }

    .lp-form-head {
      padding: 28px 36px 20px;
      border-bottom: 1px solid var(--gray-100);
      background: var(--gray-50);
    }

    .lp-form-title {
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--navy);
      margin-bottom: 3px;
      letter-spacing: -.01em;
    }

    .lp-form-subtitle {
      font-size: .75rem;
      color: var(--gray-400);
      font-family: var(--font-mono);
    }

    .lp-form-body {
      padding: 24px 36px 28px;
      flex: 1;
      overflow-y: auto;
    }

    /* ── Form elements ────────────────────────── */
    .login-form-group {
      display: flex;
      flex-direction: column;
      gap: 5px;
      margin-bottom: 16px;
    }

    .login-label {
      font-size: .72rem;
      font-weight: 700;
      color: var(--gray-500);
      text-transform: uppercase;
      letter-spacing: .07em;
    }

    .login-input {
      width: 100%;
      height: 44px;
      padding: 0 14px;
      border: 1.5px solid var(--gray-200);
      border-radius: var(--radius-md);
      font-size: .875rem;
      color: var(--gray-700);
      font-family: var(--font-body);
      background: white;
      outline: none;
      transition: border-color .15s, box-shadow .15s;
    }

    .login-input:focus {
      border-color: var(--yellow);
      box-shadow: 0 0 0 3px rgba(251,191,36,.18);
    }

    .login-input.is-error {
      border-color: var(--danger);
      box-shadow: 0 0 0 3px rgba(220,38,38,.08);
    }

    .login-input-wrap { position: relative; }

    .login-input-wrap svg {
      position: absolute;
      right: 13px;
      top: 50%;
      transform: translateY(-50%);
      width: 16px; height: 16px;
      color: var(--gray-300);
      cursor: pointer;
      transition: color .15s;
    }
    .login-input-wrap svg:hover { color: var(--gray-500); }

    .login-input--padded { padding-right: 40px; }
    .login-input::-ms-reveal,
    .login-input::-ms-clear { display: none; }

    .login-error {
      font-size: .75rem;
      color: var(--danger);
      margin-top: 4px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .login-error svg { width: 12px; height: 12px; flex-shrink: 0; }

    .login-options {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      font-size: .78rem;
    }

    .login-remember {
      display: flex;
      align-items: center;
      gap: 6px;
      color: var(--gray-500);
      cursor: pointer;
    }

    .login-remember input[type="checkbox"] {
      width: 14px; height: 14px;
      accent-color: var(--yellow-dark);
      cursor: pointer;
    }

    .login-forgot {
      color: var(--yellow-deep);
      font-weight: 700;
      transition: color .15s;
    }
    .login-forgot:hover { color: var(--navy); }

    .login-submit {
      width: 100%;
      height: 48px;
      background: linear-gradient(180deg, var(--navy-light) 0%, var(--navy) 100%);
      color: white;
      border: 1.5px solid var(--navy);
      border-radius: var(--radius-md);
      font-size: .92rem;
      font-weight: 700;
      font-family: var(--font-body);
      cursor: pointer;
      transition: all .2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      letter-spacing: .01em;
      box-shadow: 0 1px 0 rgba(255,255,255,.15) inset, 0 4px 10px rgba(10,31,68,.25);
      position: relative;
      overflow: hidden;
    }

    .login-submit::before {
      content: '';
      position: absolute;
      top: 0; left: -100%;
      width: 100%; height: 100%;
      background: linear-gradient(90deg, transparent, rgba(251,191,36,.25), transparent);
      transition: left .6s;
    }

    .login-submit:hover {
      background: linear-gradient(180deg, var(--navy-hover) 0%, var(--navy-light) 100%);
      transform: translateY(-1px);
      box-shadow: 0 1px 0 rgba(255,255,255,.15) inset, 0 8px 16px rgba(10,31,68,.35);
    }
    .login-submit:hover::before { left: 100%; }
    .login-submit:active { transform: translateY(0); }
    .login-submit svg { width: 16px; height: 16px; }

    .login-alert {
      background: var(--danger-bg);
      border: 1px solid var(--danger-border);
      border-left: 4px solid var(--danger);
      border-radius: var(--radius-md);
      padding: 10px 14px;
      margin-bottom: 16px;
      font-size: .8rem;
      color: #991b1b;
      display: flex;
      gap: 8px;
      align-items: flex-start;
    }
    .login-alert svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }

    /* ── Mobile: stack vertically ───────────────────────────────────── */
    @media (max-width: 680px) {
      .lp-wrap { grid-template-columns: 1fr; max-width: 440px; min-height: unset; }
      .lp-left { padding: 32px 28px; }
      .lp-features { display: none; }
    }
  </style>
</head>
<body>

<div class="lp-wrap">

  {{-- ════════════════════ LEFT — Branding ════════════════════ --}}
  <div class="lp-left">

    <div class="lp-logos">
      <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="lp-logo-enc">
      <div class="lp-logo-divider"></div>
      <img src="{{ asset('images/logo.png') }}"      alt="Phil. Academy of Sakya" class="lp-logo-school">
    </div>

    <div class="lp-heading">Academic<br>Management Portal</div>
    <div class="lp-sub">Philippine Academy of Sakya</div>

    <div class="lp-divider"></div>

    <div class="lp-features">

      <div class="lp-feature">
        <div class="lp-feature-icon" style="background:rgba(251,191,36,.12);">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="rgba(251,191,36,.9)" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
          </svg>
        </div>
        <div class="lp-feature-body">
          <div class="lp-feature-title">For Students</div>
          <div class="lp-feature-desc">View grades, report cards, schedule, attendance &amp; payments.</div>
        </div>
      </div>

      <div class="lp-feature">
        <div class="lp-feature-icon" style="background:rgba(52,211,153,.1);">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="rgba(52,211,153,.9)" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
          </svg>
        </div>
        <div class="lp-feature-body">
          <div class="lp-feature-title">For Faculty</div>
          <div class="lp-feature-desc">Manage gradebook, attendance, assignments &amp; class schedules.</div>
        </div>
      </div>

      <div class="lp-feature">
        <div class="lp-feature-icon" style="background:rgba(96,165,250,.1);">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="rgba(96,165,250,.9)" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </div>
        <div class="lp-feature-body">
          <div class="lp-feature-title">For Registrars</div>
          <div class="lp-feature-desc">Handle admissions, enrollment, grade finalization &amp; records.</div>
        </div>
      </div>

      <div class="lp-feature">
        <div class="lp-feature-icon" style="background:rgba(248,113,113,.1);">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="rgba(248,113,113,.9)" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
          </svg>
        </div>
        <div class="lp-feature-body">
          <div class="lp-feature-title">Secure &amp; Encrypted</div>
          <div class="lp-feature-desc">AES-256 PII encryption, bcrypt passwords, tamper-evident audit logs.</div>
        </div>
      </div>

    </div>

    <div class="lp-bottom">
      <a href="{{ route('apply') }}" class="lp-apply-btn">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
        </svg>
        Apply for Admission / Enrollment
      </a>
      <button type="button" class="lp-about-btn" onclick="document.getElementById('aboutModal').style.display='flex'">
        About &amp; Security
      </button>
    </div>

  </div>

  {{-- ════════════════════ RIGHT — Form ════════════════════ --}}
  <div class="lp-right">

    <div class="lp-form-head">
      <div class="lp-form-title">Sign In</div>
      <div class="lp-form-subtitle">Enter your credentials to access your portal</div>
    </div>

    <div class="lp-form-body">

      {{-- Session / Lockout error --}}
      @if(session('error'))
        <div class="login-alert">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      <div style="background:var(--yellow-tint);border:1px solid var(--yellow);border-left:4px solid var(--yellow-dark);border-radius:8px;padding:10px 13px;margin-bottom:18px;font-size:.76rem;color:var(--navy);line-height:1.5;">
        <strong>Heads-up:</strong> Multiple failed attempts will temporarily lock your account for 10 minutes.
        If you can't sign in, use <a href="{{ route('password.request') }}" style="text-decoration:underline;font-weight:700;color:var(--yellow-deep);">Forgot Password</a>.
      </div>

      <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf

        {{-- Username --}}
        <div class="login-form-group">
          <label class="login-label" for="username">Username / LRN / Employee No.</label>
          <input
            type="text"
            id="username"
            name="username"
            value="{{ old('username') }}"
            class="login-input {{ $errors->has('username') ? 'is-error' : '' }}"
            placeholder="Enter your ID or username"
            autocomplete="username"
            autofocus>
          @error('username')
            <div class="login-error">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/>
              </svg>
              {{ $message }}
            </div>
          @enderror
        </div>

        {{-- Password --}}
        <div class="login-form-group">
          <label class="login-label" for="password">Password</label>
          <div class="login-input-wrap">
            <input
              type="password"
              id="password"
              name="password"
              class="login-input login-input--padded {{ $errors->has('password') ? 'is-error' : '' }}"
              placeholder="Enter your password"
              autocomplete="current-password" />
            <svg id="toggle-pw" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" onclick="togglePassword()">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          @error('password')
            <div class="login-error">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/>
              </svg>
              {{ $message }}
            </div>
          @enderror
        </div>

        {{-- Remember + Forgot --}}
        <div class="login-options">
          <label class="login-remember">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            Remember me
          </label>
          <a href="{{ route('password.request') }}" class="login-forgot">Forgot password?</a>
        </div>

        {{-- Submit --}}
        <button type="submit" class="login-submit">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
          </svg>
          Sign In
        </button>

      </form>

    </div>
  </div>

</div>

<script>
  function togglePassword() {
    const field = document.getElementById('password');
    const icon  = document.getElementById('toggle-pw');

    const eyeOpen = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
    const eyeOff  = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88"/>';

    if (field.type === 'password') {
      field.type = 'text';
      icon.innerHTML = eyeOff;
      icon.style.color = 'var(--accent-blue)';
    } else {
      field.type = 'password';
      icon.innerHTML = eyeOpen;
      icon.style.color = '';
    }
  }
</script>

{{-- ── About & Security Modal ─────────────────────────────────────── --}}
<div id="aboutModal"
     style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.7);z-index:1000;align-items:center;justify-content:center;padding:20px;"
     onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#fff;border-radius:16px;max-width:640px;width:100%;max-height:88vh;overflow-y:auto;box-shadow:0 24px 60px rgba(0,0,0,.4);">
    <div style="padding:24px 28px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;border-radius:16px 16px 0 0;">
      <h2 style="margin:0;font-size:1.25rem;font-weight:800;color:#0f172a;">About EncryptEd</h2>
      <button type="button" onclick="document.getElementById('aboutModal').style.display='none'"
              style="background:none;border:none;font-size:1.6rem;line-height:1;color:#94a3b8;cursor:pointer;">&times;</button>
    </div>

    <div style="padding:24px 28px;font-size:.9rem;line-height:1.65;color:#334155;">
      <p style="margin:0 0 16px;">
        <strong>EncryptEd</strong> is a secure, web-based academic management platform for the
        Philippine Academy of Sakya. It manages the full student academic lifecycle — admission,
        enrollment, grading, and reporting — while protecting sensitive data through multiple
        layers of security.
      </p>

      <h3 style="font-size:.95rem;font-weight:800;color:#0f172a;margin:20px 0 10px;">Security Features</h3>
      <ul style="margin:0 0 16px;padding-left:20px;display:flex;flex-direction:column;gap:8px;">
        <li><strong>Password Protection.</strong> Passwords are hashed with bcrypt (cost 12) and unique salts. Plain-text passwords are never stored.</li>
        <li><strong>Data Encryption at Rest.</strong> Sensitive personal information (contact details, addresses, and other PII) is encrypted with AES-256 before being saved to the database.</li>
        <li><strong>Brute-Force Defense.</strong> Accounts lock for 10 minutes after 5 failed login attempts, with additional per-IP rate limiting.</li>
        <li><strong>Tamper-Evident Audit Logs.</strong> Every sensitive action is recorded in an append-only audit trail chained with SHA-256, so any tampering is detectable.</li>
        <li><strong>Threat Monitoring.</strong> The system actively detects and logs injection attempts, privilege-escalation attempts, and suspicious login activity.</li>
        <li><strong>Session Security.</strong> Sessions expire after 30 minutes of inactivity; cookies use HttpOnly and SameSite protections.</li>
      </ul>

      <h3 style="font-size:.95rem;font-weight:800;color:#0f172a;margin:20px 0 10px;">RA 10173 — Data Privacy Act of 2012</h3>
      <p style="margin:0 0 12px;">
        EncryptEd is built on a <strong>Privacy-by-Design</strong> framework in adherence to
        Republic Act No. 10173, the Data Privacy Act of 2012:
      </p>
      <ul style="margin:0;padding-left:20px;display:flex;flex-direction:column;gap:8px;">
        <li><strong>Data Minimization.</strong> Only the data required for academic operations is collected and stored.</li>
        <li><strong>Consent.</strong> Explicit consent is captured on the admission application before any applicant data is processed.</li>
        <li><strong>Right of Access.</strong> Users can view the personal data the system holds about them through their profile and security settings.</li>
        <li><strong>Right to Erasure.</strong> Archived records can be cryptographically shredded once the institutional retention period expires.</li>
      </ul>

      <p style="margin:20px 0 0;font-size:.8rem;color:#94a3b8;">
        For data privacy concerns, contact the institution's Data Protection Officer through the school registrar.
      </p>
    </div>

    <div style="padding:16px 28px;border-top:1px solid #e2e8f0;text-align:right;position:sticky;bottom:0;background:#fff;border-radius:0 0 16px 16px;">
      <button type="button" onclick="document.getElementById('aboutModal').style.display='none'"
              style="background:#1c3a6e;color:#fff;border:none;padding:.55rem 1.4rem;border-radius:8px;font-size:.875rem;font-weight:700;cursor:pointer;">
        Close
      </button>
    </div>
  </div>
</div>

</body>
</html>
