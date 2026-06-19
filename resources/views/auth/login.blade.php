{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sign In — Philippine Academy of Sakya · EncryptEd</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Merriweather:wght@700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/encrypted.css') }}">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: 'Inter', sans-serif; background: #f4f7fb; color: #1e293b; min-height: 100vh; }

    /* ══════════════════════════════════════
       TOP BAR
    ══════════════════════════════════════ */
    .lp-topbar {
      background: #0b1e3d;
      padding: .7rem 2rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      position: sticky;
      top: 0;
      z-index: 200;
      box-shadow: 0 2px 12px rgba(0,0,0,.3);
    }
    .lp-topbar-brand {
      display: flex;
      align-items: center;
      gap: .75rem;
    }
    .lp-topbar-brand img {
      height: 30px; width: 30px;
      border-radius: 50%;
      border: 1.5px solid rgba(255,255,255,.15);
    }
    .lp-topbar-name  { font-size: .88rem; font-weight: 800; color: #fff; letter-spacing: -.01em; }
    .lp-topbar-sub   { font-size: .62rem; color: rgba(255,255,255,.38); letter-spacing: .03em; }
    .lp-topbar-sep   { flex: 1; }
    .lp-topbar-apply {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: .74rem;
      font-weight: 600;
      color: rgba(255,255,255,.45);
      text-decoration: none;
      transition: color .15s;
    }
    .lp-topbar-apply:hover { color: rgba(255,255,255,.8); }
    .lp-topbar-apply svg { width: 13px; height: 13px; }

    /* ══════════════════════════════════════
       HERO BANNER
    ══════════════════════════════════════ */
    .lp-hero {
      background: linear-gradient(135deg, #0b1e3d 0%, #1535a0 55%, #1d4ed8 100%);
      padding: 2rem 2rem 2.75rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .lp-hero::before {
      content: '';
      position: absolute; inset: 0;
      background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
      background-size: 24px 24px;
    }
    .lp-hero::after {
      content: '';
      position: absolute;
      bottom: -2px; left: 0; right: 0;
      height: 40px;
      background: #f4f7fb;
      clip-path: ellipse(55% 100% at 50% 100%);
    }
    .lp-hero-inner { position: relative; z-index: 1; }
    .lp-hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(245,158,11,.15);
      border: 1px solid rgba(245,158,11,.3);
      color: #fde68a;
      font-size: .67rem;
      font-weight: 700;
      letter-spacing: .1em;
      text-transform: uppercase;
      padding: .3rem .85rem;
      border-radius: 99px;
      margin-bottom: .75rem;
    }
    .lp-hero-badge svg { width: 12px; height: 12px; }
    .lp-hero h1 {
      font-size: 1.75rem;
      font-weight: 900;
      color: #fff;
      letter-spacing: -.035em;
      margin-bottom: 0;
    }

    /* ══════════════════════════════════════
       PAGE BODY — sidebar + form
    ══════════════════════════════════════ */
    .lp-body {
      max-width: 960px;
      margin: 0 auto;
      padding: 2rem 1.5rem 5rem;
      display: grid;
      grid-template-columns: 210px 1fr;
      gap: 1.75rem;
      align-items: start;
    }

    /* ── Sidebar ── */
    .lp-sidebar {
      position: sticky;
      top: 80px;
    }
    .lp-sidebar-title {
      font-size: .63rem;
      font-weight: 800;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: .1em;
      margin-bottom: 10px;
      padding-left: 4px;
    }

    .lp-roles { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
    .lp-role {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 12px;
      border-radius: 9px;
      background: #fff;
      border: 1px solid #e2e8f0;
    }
    .lp-role-dot {
      width: 7px; height: 7px;
      border-radius: 50%;
      flex-shrink: 0;
    }
    .lp-role-dot--amber   { background: #f5b432; box-shadow: 0 0 5px rgba(245,180,50,.5); }
    .lp-role-dot--emerald { background: #34d399; box-shadow: 0 0 5px rgba(52,211,153,.5); }
    .lp-role-dot--blue    { background: #60a5fa; box-shadow: 0 0 5px rgba(96,165,250,.5); }
    .lp-role-dot--violet  { background: #a78bfa; box-shadow: 0 0 5px rgba(167,139,250,.5); }
    .lp-role-name { font-size: .8rem; font-weight: 700; color: #0f172a; }

    /* admission CTA in sidebar */
    .lp-adm-card {
      background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
      border-radius: 14px;
      padding: 18px 16px;
      text-align: center;
    }
    .lp-adm-eyebrow {
      font-size: .6rem;
      font-weight: 700;
      color: rgba(255,255,255,.5);
      letter-spacing: .12em;
      text-transform: uppercase;
      margin-bottom: 6px;
    }
    .lp-adm-heading {
      font-size: .95rem;
      font-weight: 800;
      color: #fff;
      margin-bottom: 14px;
      line-height: 1.3;
    }
    .lp-adm-link {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      width: 100%;
      padding: .7rem 1rem;
      background: #fff;
      color: #1d4ed8;
      border-radius: 10px;
      font-size: .84rem;
      font-weight: 800;
      text-decoration: none;
      transition: background .15s, transform .15s, box-shadow .15s;
      box-shadow: 0 4px 14px rgba(0,0,0,.2);
    }
    .lp-adm-link:hover {
      background: #f0f7ff;
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(0,0,0,.25);
    }
    .lp-adm-link svg { width: 14px; height: 14px; }

    /* ══════════════════════════════════════
       FORM CARD (same style as ap-card)
    ══════════════════════════════════════ */
    .lp-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 2px 16px rgba(15,23,42,.07), 0 1px 3px rgba(15,23,42,.04);
      overflow: hidden;
      border: 1px solid rgba(226,232,240,.7);
    }
    .lp-card-bar {
      height: 3px;
      background: linear-gradient(90deg, #1a3a6b, #2563eb, #3ecfa0);
    }
    .lp-card-head {
      padding: 1rem 1.5rem;
      display: flex;
      align-items: center;
      gap: 12px;
      border-bottom: 1px solid #f1f5f9;
      background: #fafbfd;
    }
    .lp-card-icon {
      width: 36px; height: 36px;
      border-radius: 10px;
      background: #eff6ff;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .lp-card-icon svg { width: 18px; height: 18px; }
    .lp-card-section-num {
      font-size: .62rem;
      font-weight: 800;
      color: #94a3b8;
      text-transform: uppercase;
      letter-spacing: .1em;
      margin-bottom: 1px;
    }
    .lp-card-title {
      font-size: .92rem;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -.01em;
    }
    .lp-card-body { padding: 1.5rem; }

    /* alerts */
    .lp-alert {
      display: flex;
      align-items: flex-start;
      gap: 9px;
      padding: 10px 13px;
      border-radius: 10px;
      margin-bottom: 14px;
      font-size: .76rem;
      line-height: 1.55;
    }
    .lp-alert svg { width: 14px; height: 14px; flex-shrink: 0; margin-top: 1px; }
    .lp-alert--error { background: #fff5f5; border: 1px solid #fecaca; color: #7f1d1d; }
    .lp-alert--warn  { background: #fffbeb; border: 1px solid #fde68a; color: #78350f; }
    .lp-alert--warn a { color: #92400e; font-weight: 700; text-decoration: underline; text-underline-offset: 2px; }

    /* fields */
    .lp-group { margin-bottom: 14px; }
    .lp-label {
      display: block;
      font-size: .65rem;
      font-weight: 700;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: .09em;
      margin-bottom: 6px;
    }
    .lp-input {
      width: 100%;
      height: 44px;
      padding: 0 14px;
      border: 1.5px solid #e2e8f0;
      border-radius: 10px;
      font-size: .875rem;
      color: #0f172a;
      font-family: inherit;
      background: #f8fafc;
      outline: none;
      transition: border-color .18s, box-shadow .18s, background .18s;
    }
    .lp-input:focus {
      border-color: #2563eb;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .lp-input.is-error { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.09); }
    .lp-input::placeholder { color: #94a3b8; }

    .lp-pw-wrap { position: relative; }
    .lp-pw-wrap .lp-input { padding-right: 44px; }
    .lp-pw-toggle {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      width: 18px; height: 18px; color: #94a3b8; cursor: pointer;
      background: none; border: none; padding: 0;
      display: flex; align-items: center; justify-content: center;
      transition: color .15s;
    }
    .lp-pw-toggle:hover { color: #2563eb; }

    .lp-field-err {
      display: flex; align-items: center; gap: 4px;
      font-size: .68rem; color: #ef4444; margin-top: 5px; font-weight: 600;
    }
    .lp-field-err svg { width: 11px; height: 11px; flex-shrink: 0; }

    .lp-options {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 18px;
    }
    .lp-remember {
      display: flex; align-items: center; gap: 7px;
      font-size: .77rem; color: #64748b; cursor: pointer; font-weight: 500;
      user-select: none;
    }
    .lp-remember input[type="checkbox"] { width: 14px; height: 14px; accent-color: #2563eb; cursor: pointer; }
    .lp-forgot {
      font-size: .77rem; font-weight: 600;
      color: #2563eb; text-decoration: none;
      transition: color .15s;
    }
    .lp-forgot:hover { color: #1d4ed8; text-decoration: underline; }

    .lp-submit {
      width: 100%;
      height: 46px;
      background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 60%, #2563eb 100%);
      color: #fff;
      border: none;
      border-radius: 10px;
      font-size: .9rem;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      letter-spacing: .01em;
      box-shadow: 0 3px 14px rgba(29,78,216,.3);
      transition: box-shadow .2s, transform .15s;
    }
    .lp-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 22px rgba(29,78,216,.4); }
    .lp-submit:active { transform: translateY(0); }
    .lp-submit svg { width: 15px; height: 15px; }

    /* security badges */
    .lp-security {
      display: flex; align-items: center; justify-content: center; gap: 10px;
      margin-top: 18px; padding-top: 16px;
      border-top: 1px solid #f1f5f9;
    }
    .lp-sec-badge { display: flex; align-items: center; gap: 4px; font-size: .6rem; font-weight: 600; color: #94a3b8; }
    .lp-sec-badge svg { width: 10px; height: 10px; }
    .lp-sec-sep { width: 3px; height: 3px; border-radius: 50%; background: #dde4ee; }

    .lp-disclaimer {
      margin-top: 12px; text-align: center;
      font-size: .62rem; color: #94a3b8; line-height: 1.6;
    }

    /* footer */
    .lp-footer {
      text-align: center;
      font-size: .62rem;
      color: #94a3b8;
      margin-top: 1.25rem;
      font-weight: 500;
      letter-spacing: .02em;
    }

    /* ── Responsive ── */
    @media (max-width: 760px) {
      .lp-body { grid-template-columns: 1fr; }
      .lp-sidebar { display: none; }
      .lp-hero h1 { font-size: 1.5rem; }
    }
  </style>
</head>
<body>

{{-- ── Top Bar ── --}}
<div class="lp-topbar">
  <div class="lp-topbar-brand">
    <img src="{{ asset('images/logo.png') }}" alt="PAS">
    <div>
      <div class="lp-topbar-name">Philippine Academy of Sakya</div>
      <div class="lp-topbar-sub">EncryptEd · Academic Management System</div>
    </div>
  </div>
  <div class="lp-topbar-sep"></div>
  <a href="{{ route('apply') }}" class="lp-topbar-apply">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
    </svg>
    New Student? Apply Here
  </a>
</div>

{{-- ── Hero ── --}}
<div class="lp-hero">
  <div class="lp-hero-inner">
    <div class="lp-hero-badge">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
      Official Portal
    </div>
    <h1>Academic Management System</h1>
  </div>
</div>

{{-- ── Page Body ── --}}
<div class="lp-body">

  {{-- ── Sidebar ── --}}
  <aside class="lp-sidebar">
    <div class="lp-sidebar-title">Authorized Users</div>
    <div class="lp-roles">
      <div class="lp-role"><div class="lp-role-dot lp-role-dot--amber"></div><div class="lp-role-name">Students</div></div>
      <div class="lp-role"><div class="lp-role-dot lp-role-dot--emerald"></div><div class="lp-role-name">Faculty</div></div>
      <div class="lp-role"><div class="lp-role-dot lp-role-dot--blue"></div><div class="lp-role-name">Registrars</div></div>
      <div class="lp-role"><div class="lp-role-dot lp-role-dot--violet"></div><div class="lp-role-name">Administrators</div></div>
    </div>

    <div class="lp-adm-card">
      <div class="lp-adm-eyebrow">New Students &amp; Parents</div>
      <div class="lp-adm-heading">Apply for Admission &amp; Enrollment</div>
      <a href="{{ route('apply') }}" class="lp-adm-link">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
        Begin Application
      </a>
    </div>
  </aside>

  {{-- ── Login Form Card ── --}}
  <div>
    <div class="lp-card">
      <div class="lp-card-bar"></div>
      <div class="lp-card-head">
        <div class="lp-card-icon">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#1d4ed8" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
          </svg>
        </div>
        <div>
          <div class="lp-card-section-num">Philippine Academy of Sakya · Official System</div>
          <div class="lp-card-title">Sign in to your account</div>
        </div>
      </div>

      <div class="lp-card-body">

        {{-- Session-expired / info notice --}}
        @if(session('status'))
        <div class="lp-alert lp-alert--warn">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>{{ session('status') }}</span>
        </div>
        @endif

        {{-- Error --}}
        @if(session('error'))
        <div class="lp-alert lp-alert--error">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
          <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Security notice --}}
        <div class="lp-alert lp-alert--warn">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
          </svg>
          <div>
            <strong>Security Notice:</strong> Five (5) failed attempts will lock your account for 10 minutes.
            <a href="{{ route('password.request') }}">Reset your password</a> if you can't sign in.
          </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" autocomplete="off">
          @csrf

          <div class="lp-group">
            <label class="lp-label" for="username">Username / LRN / Employee No.</label>
            <input type="text" id="username" name="username"
              value="{{ old('username') }}"
              class="lp-input {{ $errors->has('username') ? 'is-error' : '' }}"
              placeholder="Enter your identifier"
              autocomplete="username" autofocus>
            @error('username')
            <div class="lp-field-err">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="lp-group">
            <label class="lp-label" for="password">Password</label>
            <div class="lp-pw-wrap">
              <input type="password" id="password" name="password"
                class="lp-input {{ $errors->has('password') ? 'is-error' : '' }}"
                placeholder="Enter your password"
                autocomplete="current-password">
              <button type="button" class="lp-pw-toggle" onclick="togglePw()" aria-label="Toggle password visibility">
                <svg id="pw-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </button>
            </div>
            @error('password')
            <div class="lp-field-err">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="lp-options">
            <label class="lp-remember">
              <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
              Keep me signed in
            </label>
            <a href="{{ route('password.request') }}" class="lp-forgot">Forgot password?</a>
          </div>

          <button type="submit" class="lp-submit">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
            </svg>
            Sign in to Portal
          </button>

        </form>

        {{-- Security badges --}}
        <div class="lp-security">
          <span class="lp-sec-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            AES-256
          </span>
          <div class="lp-sec-sep"></div>
          <span class="lp-sec-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            bcrypt · SHA-256
          </span>
          <div class="lp-sec-sep"></div>
          <span class="lp-sec-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/></svg>
            RA 10173
          </span>
        </div>

        <div class="lp-disclaimer">
          Officially authorized system. Unauthorized access is prohibited<br>
          and subject to applicable laws of the Republic of the Philippines.
        </div>

      </div>
    </div>

    <div class="lp-footer">
      &copy; {{ date('Y') }} Philippine Academy of Sakya &nbsp;&middot;&nbsp; Powered by EncryptEd
      &nbsp;&middot;&nbsp;
      <button type="button" style="background:none;border:none;color:#94a3b8;font-size:.62rem;font-weight:500;cursor:pointer;font-family:inherit;" onclick="document.getElementById('aboutModal').style.display='flex'">
        Security Policy &amp; Data Privacy (RA 10173)
      </button>
    </div>
  </div>

</div>

{{-- ── Password toggle ── --}}
<script>
  function togglePw() {
    const f = document.getElementById('password');
    const i = document.getElementById('pw-icon');
    const eyeOpen = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
    const eyeOff  = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88"/>';
    if (f.type === 'password') { f.type = 'text'; i.innerHTML = eyeOff; i.style.color = '#2563eb'; }
    else { f.type = 'password'; i.innerHTML = eyeOpen; i.style.color = '#94a3b8'; }
  }
</script>

{{-- ── Security & Privacy Modal ── --}}
<div id="aboutModal"
     style="display:none;position:fixed;inset:0;background:rgba(6,13,28,.72);z-index:1000;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(5px);"
     onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#fff;border-radius:16px;max-width:560px;width:100%;max-height:86vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.4);border:1px solid #d8e0ec;">
    <div style="padding:18px 24px;border-bottom:1px solid #e8ecf2;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;border-radius:16px 16px 0 0;z-index:1;">
      <div>
        <div style="font-size:.6rem;font-weight:700;color:#64748b;letter-spacing:.12em;text-transform:uppercase;margin-bottom:2px;">RA 10173 Compliance</div>
        <h2 style="margin:0;font-size:.95rem;font-weight:800;color:#0a1628;">Security Policy &amp; Data Privacy</h2>
      </div>
      <button type="button" onclick="document.getElementById('aboutModal').style.display='none'"
              style="background:#f1f5f9;border:1px solid #dde4ee;width:30px;height:30px;border-radius:8px;font-size:1rem;color:#64748b;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">&times;</button>
    </div>
    <div style="padding:20px 24px;font-size:.84rem;line-height:1.7;color:#334155;">
      <p style="margin:0 0 14px;"><strong>EncryptEd</strong> is the official academic management platform of the Philippine Academy of Sakya. All personal data is handled in strict compliance with Republic Act No. 10173 (Data Privacy Act of 2012).</p>
      <h3 style="font-size:.72rem;font-weight:800;color:#0a1628;margin:16px 0 8px;text-transform:uppercase;letter-spacing:.1em;border-left:3px solid #2563eb;padding-left:8px;">Security Measures</h3>
      <ul style="margin:0 0 12px;padding-left:18px;display:flex;flex-direction:column;gap:6px;">
        <li><strong>bcrypt (cost 12)</strong> — Passwords are hashed and never stored in plain text.</li>
        <li><strong>AES-256 encryption</strong> — All personally identifiable information is encrypted at rest.</li>
        <li><strong>Brute-force protection</strong> — Accounts are locked for 10 minutes after 5 failed attempts.</li>
        <li><strong>SHA-256 audit logs</strong> — Sensitive actions are tamper-evidently recorded.</li>
        <li><strong>Session security</strong> — HttpOnly, SameSite cookies with 30-minute idle expiry.</li>
      </ul>
      <h3 style="font-size:.72rem;font-weight:800;color:#0a1628;margin:16px 0 8px;text-transform:uppercase;letter-spacing:.1em;border-left:3px solid #2563eb;padding-left:8px;">Data Privacy Act (RA 10173)</h3>
      <ul style="margin:0;padding-left:18px;display:flex;flex-direction:column;gap:6px;">
        <li><strong>Data Minimization</strong> — Only operationally necessary data is collected.</li>
        <li><strong>Informed Consent</strong> — Explicit consent is obtained during the admission process.</li>
        <li><strong>Right of Access &amp; Rectification</strong> — Users may view and request correction of their records.</li>
        <li><strong>Right to Erasure</strong> — Data deletion requests may be submitted to the Data Protection Officer.</li>
      </ul>
      <p style="margin:14px 0 0;font-size:.72rem;color:#94a3b8;border-top:1px solid #e8ecf2;padding-top:12px;">For data privacy concerns, contact the Data Protection Officer through the School Registrar's Office.</p>
    </div>
    <div style="padding:14px 24px;border-top:1px solid #e8ecf2;display:flex;justify-content:flex-end;position:sticky;bottom:0;background:#fff;border-radius:0 0 16px 16px;">
      <button type="button" onclick="document.getElementById('aboutModal').style.display='none'"
              style="background:#1a3a6b;color:#fff;border:none;padding:.5rem 1.4rem;border-radius:8px;font-size:.84rem;font-weight:700;cursor:pointer;font-family:inherit;"
              onmouseover="this.style.background='#122a52'" onmouseout="this.style.background='#1a3a6b'">
        Close
      </button>
    </div>
  </div>
</div>

</body>
</html>
