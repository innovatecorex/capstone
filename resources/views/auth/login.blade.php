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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/encrypted.css') }}">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; }
    body { font-family: 'Inter', sans-serif; background: #0b1829; color: #0f172a; }

    /* ── Page entry ── */
    @keyframes page-in { from { opacity: 0; } to { opacity: 1; } }

    /* ══════════════════════════════════════════════
       LAYOUT
    ══════════════════════════════════════════════ */
    .lp-wrap {
      display: grid;
      grid-template-columns: 420px 1fr;
      min-height: 100vh;
      animation: page-in .4s ease both;
    }

    /* ══════════════════════════════════════════════
       LEFT PANEL
    ══════════════════════════════════════════════ */
    .lp-left {
      position: relative;
      display: flex;
      flex-direction: column;
      padding: 0;
      height: 100vh;
      overflow: hidden;

      /* deep navy gradient */
      background: linear-gradient(175deg, #0f2347 0%, #091830 45%, #060f1e 100%);

      /* subtle right border */
      border-right: 1px solid rgba(255,255,255,.06);
    }

    /* geometric accent strip */
    .lp-left::before {
      content: '';
      position: absolute;
      top: 0; left: 0; bottom: 0; width: 2px;
      background: linear-gradient(180deg,
        transparent 0%,
        rgba(62,207,160,.5) 20%,
        rgba(46,106,230,.6) 60%,
        transparent 100%);
    }

    /* subtle blueprint grid texture */
    .lp-left::after {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(255,255,255,.022) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.022) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
    }

    /* ── Inner scroll container ── */
    .lp-left-inner {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      height: 100%;
      padding: 40px 32px 28px;
      overflow: hidden;
    }

    /* ── Brand block ── */
    .lp-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 36px;
    }
    .lp-brand-logo {
      width: 38px; height: 38px;
      border-radius: 10px;
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.1);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .lp-brand-logo img {
      width: 26px; height: 26px;
      object-fit: contain;
      filter: brightness(0) invert(1);
      opacity: .85;
    }
    .lp-brand-text {}
    .lp-brand-name {
      font-size: .72rem;
      font-weight: 700;
      color: rgba(255,255,255,.9);
      letter-spacing: .01em;
      line-height: 1.2;
    }
    .lp-brand-tag {
      font-size: .56rem;
      font-weight: 600;
      color: rgba(62,207,160,.75);
      letter-spacing: .14em;
      text-transform: uppercase;
    }

    /* ── Divider ── */
    .lp-divider {
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.1) 30%, rgba(255,255,255,.1) 70%, transparent);
      margin-bottom: 36px;
    }

    /* ── Hero headline ── */
    .lp-headline {
      margin-bottom: 32px;
    }
    .lp-headline-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: .58rem;
      font-weight: 700;
      color: rgba(62,207,160,.8);
      letter-spacing: .16em;
      text-transform: uppercase;
      margin-bottom: 10px;
    }
    .lp-headline-eyebrow::before {
      content: '';
      display: inline-block;
      width: 16px; height: 1.5px;
      background: rgba(62,207,160,.6);
    }
    .lp-headline h1 {
      font-family: 'Merriweather', Georgia, serif;
      font-size: 1.55rem;
      font-weight: 900;
      color: #ffffff;
      line-height: 1.25;
      letter-spacing: -.02em;
      margin-bottom: 10px;
    }
    .lp-headline p {
      font-size: .78rem;
      color: rgba(255,255,255,.38);
      line-height: 1.65;
      font-weight: 400;
    }

    /* ── Role access list ── */
    .lp-roles-label {
      font-size: .57rem;
      font-weight: 700;
      color: rgba(255,255,255,.2);
      letter-spacing: .15em;
      text-transform: uppercase;
      margin-bottom: 10px;
    }
    .lp-roles {
      display: flex;
      flex-direction: column;
      gap: 5px;
      margin-bottom: 28px;
    }
    .lp-role {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 14px;
      border-radius: 10px;
      border: 1px solid rgba(255,255,255,.055);
      background: rgba(255,255,255,.03);
      transition: background .2s, border-color .2s, transform .2s;
    }
    .lp-role:hover {
      background: rgba(255,255,255,.065);
      border-color: rgba(255,255,255,.11);
      transform: translateX(2px);
    }
    .lp-role-dot {
      width: 7px; height: 7px;
      border-radius: 50%;
      flex-shrink: 0;
    }
    .lp-role-dot--amber   { background: #f5b432; box-shadow: 0 0 6px rgba(245,180,50,.5); }
    .lp-role-dot--emerald { background: #34d399; box-shadow: 0 0 6px rgba(52,211,153,.5); }
    .lp-role-dot--blue    { background: #60a5fa; box-shadow: 0 0 6px rgba(96,165,250,.5); }
    .lp-role-dot--violet  { background: #a78bfa; box-shadow: 0 0 6px rgba(167,139,250,.5); }
    .lp-role-body { flex: 1; min-width: 0; }
    .lp-role-name {
      font-size: .78rem;
      font-weight: 600;
      color: rgba(255,255,255,.78);
      margin-bottom: 1px;
    }
    .lp-role-desc {
      font-size: .63rem;
      color: rgba(255,255,255,.25);
      line-height: 1.4;
    }

    /* ── Admission CTA ── */
    .lp-admission {
      margin-top: auto;
      padding: 16px 18px;
      border-radius: 12px;
      border: 1px solid rgba(46,106,230,.3);
      background: linear-gradient(135deg, rgba(46,106,230,.12) 0%, rgba(62,207,160,.06) 100%);
      position: relative;
      overflow: hidden;
    }
    .lp-admission::before {
      content: '';
      position: absolute;
      top: -20px; right: -20px;
      width: 80px; height: 80px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(62,207,160,.15) 0%, transparent 70%);
      pointer-events: none;
    }
    .lp-adm-eyebrow {
      font-size: .56rem;
      font-weight: 700;
      color: rgba(62,207,160,.7);
      letter-spacing: .14em;
      text-transform: uppercase;
      margin-bottom: 6px;
    }
    .lp-adm-heading {
      font-size: .875rem;
      font-weight: 700;
      color: rgba(255,255,255,.9);
      margin-bottom: 4px;
    }
    .lp-adm-body {
      font-size: .65rem;
      color: rgba(255,255,255,.32);
      line-height: 1.55;
      margin-bottom: 12px;
    }
    .lp-adm-link {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: .52rem .9rem;
      background: rgba(46,106,230,.18);
      border: 1px solid rgba(46,106,230,.35);
      border-radius: 8px;
      color: rgba(160,200,255,.88);
      font-size: .75rem;
      font-weight: 600;
      text-decoration: none;
      letter-spacing: .01em;
      transition: background .2s, border-color .2s, color .2s, transform .15s;
    }
    .lp-adm-link:hover {
      background: rgba(46,106,230,.3);
      border-color: rgba(46,106,230,.55);
      color: #fff;
      transform: translateY(-1px);
    }
    .lp-adm-link svg { width: 13px; height: 13px; flex-shrink: 0; }

    /* ── Left footer ── */
    .lp-left-foot {
      margin-top: 14px;
      font-size: .58rem;
      color: rgba(255,255,255,.14);
      letter-spacing: .025em;
      text-align: center;
      background: none;
      border: none;
      cursor: pointer;
      font-family: inherit;
      font-weight: 500;
      transition: color .2s;
      width: 100%;
    }
    .lp-left-foot:hover { color: rgba(255,255,255,.3); }

    /* ══════════════════════════════════════════════
       RIGHT PANEL
    ══════════════════════════════════════════════ */
    .lp-right {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: #f4f7fb;
      padding: 40px 24px;
    }

    /* subtle top-right accent */
    .lp-right::before {
      content: '';
      position: absolute;
      top: 0; right: 0;
      width: 420px; height: 320px;
      background: radial-gradient(ellipse at top right, rgba(46,106,230,.08) 0%, transparent 65%);
      pointer-events: none;
    }
    .lp-right::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0;
      width: 320px; height: 260px;
      background: radial-gradient(ellipse at bottom left, rgba(62,207,160,.06) 0%, transparent 65%);
      pointer-events: none;
    }

    /* ── Form card ── */
    .lp-card {
      background: #ffffff;
      border-radius: 18px;
      border: 1px solid rgba(15,30,60,.08);
      box-shadow:
        0 1px 3px rgba(15,30,60,.05),
        0 8px 24px rgba(15,30,60,.07),
        0 32px 64px rgba(15,30,60,.06);
      width: 100%;
      max-width: 420px;
      position: relative;
      z-index: 1;
      overflow: hidden;
      animation: card-rise .5s cubic-bezier(.22,.68,0,1.1) .08s both;
    }
    @keyframes card-rise {
      from { opacity: 0; transform: translateY(16px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* colored top bar */
    .lp-card-bar {
      height: 3px;
      background: linear-gradient(90deg, #1a3a6b 0%, #2e6ae6 50%, #3ecfa0 100%);
    }

    /* card body padding */
    .lp-card-body {
      padding: 34px 36px 30px;
    }

    /* ── Card header ── */
    .lp-card-header {
      text-align: center;
      margin-bottom: 26px;
    }
    .lp-card-emblem {
      width: 52px; height: 52px;
      border-radius: 14px;
      background: linear-gradient(145deg, #1a3a6b 0%, #2060d8 100%);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 14px;
      box-shadow: 0 4px 14px rgba(26,58,107,.28);
    }
    .lp-card-emblem svg { width: 24px; height: 24px; color: #fff; }
    .lp-card-title {
      font-family: 'Merriweather', Georgia, serif;
      font-size: 1.08rem;
      font-weight: 700;
      color: #0a1628;
      letter-spacing: -.01em;
      margin-bottom: 4px;
    }
    .lp-card-sub {
      font-size: .72rem;
      color: #8496b0;
      font-weight: 400;
    }

    /* ── Alerts ── */
    .lp-alert {
      display: flex;
      align-items: flex-start;
      gap: 9px;
      padding: 10px 13px;
      border-radius: 9px;
      margin-bottom: 16px;
      font-size: .76rem;
      line-height: 1.55;
    }
    .lp-alert svg { width: 14px; height: 14px; flex-shrink: 0; margin-top: 1px; }
    .lp-alert--error  {
      background: #fff5f5;
      border: 1px solid #fecaca;
      color: #7f1d1d;
    }
    .lp-alert--warn {
      background: #fffbeb;
      border: 1px solid #fde68a;
      color: #78350f;
    }
    .lp-alert--warn a {
      color: #92400e;
      font-weight: 700;
      text-decoration: underline;
      text-underline-offset: 2px;
    }

    /* ── Form fields ── */
    .lp-group { margin-bottom: 14px; }
    .lp-label {
      display: block;
      font-size: .625rem;
      font-weight: 700;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: .1em;
      margin-bottom: 6px;
    }
    .lp-input {
      width: 100%;
      height: 44px;
      padding: 0 12px;
      border: 1.5px solid #e1e8f0;
      border-radius: 9px;
      font-size: .875rem;
      color: #0a1628;
      font-family: inherit;
      background: #f8fafd;
      outline: none;
      transition: border-color .18s, box-shadow .18s, background .18s;
    }
    .lp-input:focus {
      border-color: #2060d8;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(32,96,216,.12);
    }
    .lp-input.is-error {
      border-color: #ef4444;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(239,68,68,.09);
    }
    .lp-input::placeholder { color: #c4cfdc; }

    /* password wrapper */
    .lp-pw-wrap { position: relative; }
    .lp-pw-wrap .lp-input { padding-right: 42px; }
    .lp-pw-toggle {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      width: 16px; height: 16px; color: #b8c6d6; cursor: pointer;
      transition: color .18s;
      background: none; border: none; padding: 0;
      display: flex; align-items: center; justify-content: center;
    }
    .lp-pw-toggle:hover { color: #2060d8; }
    .lp-input::-ms-reveal, .lp-input::-ms-clear { display: none; }

    /* inline error */
    .lp-field-err {
      display: flex; align-items: center; gap: 4px;
      font-size: .68rem; color: #ef4444; margin-top: 5px; font-weight: 600;
    }
    .lp-field-err svg { width: 11px; height: 11px; flex-shrink: 0; }

    /* ── Options row ── */
    .lp-options {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 18px;
    }
    .lp-remember {
      display: flex; align-items: center; gap: 7px;
      font-size: .77rem; color: #64748b; cursor: pointer; font-weight: 500;
      user-select: none;
    }
    .lp-remember input[type="checkbox"] {
      width: 14px; height: 14px; accent-color: #2060d8; cursor: pointer;
    }
    .lp-forgot {
      font-size: .77rem; font-weight: 600;
      color: #2060d8; text-decoration: none;
      transition: color .15s;
    }
    .lp-forgot:hover { color: #1040a0; text-decoration: underline; }

    /* ── Submit button ── */
    .lp-submit {
      width: 100%;
      height: 46px;
      background: linear-gradient(135deg, #1a3a6b 0%, #2060d8 100%);
      color: #fff;
      border: none;
      border-radius: 9px;
      font-size: .875rem;
      font-weight: 600;
      font-family: inherit;
      cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      letter-spacing: .02em;
      box-shadow: 0 3px 12px rgba(32,96,216,.3);
      transition: box-shadow .2s, transform .15s, background .2s;
    }
    .lp-submit:hover {
      background: linear-gradient(135deg, #163060 0%, #1a55c8 100%);
      box-shadow: 0 6px 20px rgba(32,96,216,.4);
      transform: translateY(-1px);
    }
    .lp-submit:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(32,96,216,.25); }
    .lp-submit svg { width: 15px; height: 15px; }

    /* ── Security badges ── */
    .lp-security {
      display: flex; align-items: center; justify-content: center; gap: 12px;
      margin-top: 20px;
      padding-top: 18px;
      border-top: 1px solid #edf1f7;
    }
    .lp-sec-badge {
      display: flex; align-items: center; gap: 4px;
      font-size: .6rem; font-weight: 600; color: #94a3b8; letter-spacing: .04em;
    }
    .lp-sec-badge svg { width: 10px; height: 10px; color: #94a3b8; }
    .lp-sec-sep { width: 3px; height: 3px; border-radius: 50%; background: #dde4ee; }

    /* ── Disclaimer ── */
    .lp-disclaimer {
      margin-top: 14px;
      text-align: center;
      font-size: .62rem;
      color: #a8b4c4;
      line-height: 1.6;
    }

    /* ── Right footer ── */
    .lp-right-footer {
      margin-top: 28px;
      font-size: .62rem;
      color: #a8b4c4;
      font-weight: 500;
      letter-spacing: .02em;
      text-align: center;
      position: relative;
      z-index: 1;
    }

    /* ══════════════════════════════════════════════
       RESPONSIVE
    ══════════════════════════════════════════════ */
    @media (max-width: 840px) {
      .lp-wrap { grid-template-columns: 1fr; }
      .lp-left { display: none; }
      .lp-right { min-height: 100vh; }
      .lp-card-body { padding: 28px 24px 24px; }
    }
  </style>
</head>
<body>

<div class="lp-wrap">

  {{-- ═══════════════════════════════════════
       LEFT — Institutional Branding
  ════════════════════════════════════════ --}}
  <div class="lp-left">
    <div class="lp-left-inner">

      {{-- Brand lockup --}}
      <div class="lp-brand">
        <div class="lp-brand-logo">
          <img src="{{ asset('images/logo.png') }}" alt="PAS">
        </div>
        <div class="lp-brand-text">
          <div class="lp-brand-name">Philippine Academy of Sakya</div>
          <div class="lp-brand-tag">EncryptEd · AMS</div>
        </div>
      </div>

      <div class="lp-divider"></div>

      {{-- Hero headline --}}
      <div class="lp-headline">
        <div class="lp-headline-eyebrow">Official Portal</div>
        <h1>Academic Management System</h1>
        <p>A centralized, secure platform for students, faculty, and administrative staff of the Philippine Academy of Sakya.</p>
      </div>

      {{-- Authorized roles --}}
      <div class="lp-roles-label">Authorized Users</div>
      <div class="lp-roles">

        <div class="lp-role">
          <div class="lp-role-dot lp-role-dot--amber"></div>
          <div class="lp-role-body">
            <div class="lp-role-name">Students</div>
            <div class="lp-role-desc">Academic records, grades, schedule &amp; payments</div>
          </div>
        </div>

        <div class="lp-role">
          <div class="lp-role-dot lp-role-dot--emerald"></div>
          <div class="lp-role-body">
            <div class="lp-role-name">Faculty</div>
            <div class="lp-role-desc">Gradebook, attendance &amp; class management</div>
          </div>
        </div>

        <div class="lp-role">
          <div class="lp-role-dot lp-role-dot--blue"></div>
          <div class="lp-role-body">
            <div class="lp-role-name">Registrars</div>
            <div class="lp-role-desc">Admissions, enrollment &amp; official records</div>
          </div>
        </div>

        <div class="lp-role">
          <div class="lp-role-dot lp-role-dot--violet"></div>
          <div class="lp-role-body">
            <div class="lp-role-name">Administrators</div>
            <div class="lp-role-desc">System management &amp; configuration</div>
          </div>
        </div>

      </div>

      {{-- Admission CTA --}}
      <div class="lp-admission">
        <div class="lp-adm-eyebrow">New Students &amp; Parents</div>
        <div class="lp-adm-heading">Admission &amp; Enrollment</div>
        <div class="lp-adm-body">No account required. Submit your application online and receive a reference number to track your status.</div>
        <a href="{{ route('apply') }}" class="lp-adm-link">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
          </svg>
          Begin Application
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:11px;height:11px;margin-left:auto;opacity:.5;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
          </svg>
        </a>
      </div>

      <button type="button" class="lp-left-foot"
              onclick="document.getElementById('aboutModal').style.display='flex'">
        Security Policy &amp; Data Privacy (RA 10173)
      </button>

    </div>
  </div>

  {{-- ═══════════════════════════════════════
       RIGHT — Authentication Form
  ════════════════════════════════════════ --}}
  <div class="lp-right">

    <div class="lp-card">

      <div class="lp-card-bar"></div>

      <div class="lp-card-body">

        {{-- Header --}}
        <div class="lp-card-header">
          <div class="lp-card-emblem">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
            </svg>
          </div>
          <div class="lp-card-title">Sign in to your account</div>
          <div class="lp-card-sub">Philippine Academy of Sakya &nbsp;&middot;&nbsp; Official System</div>
        </div>

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
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#d97706;">
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
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
              </svg>
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
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
              </svg>
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
            Sign In to Portal
          </button>

        </form>

        {{-- Security badges --}}
        <div class="lp-security">
          <span class="lp-sec-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
            AES-256
          </span>
          <div class="lp-sec-sep"></div>
          <span class="lp-sec-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
            bcrypt · SHA-256
          </span>
          <div class="lp-sec-sep"></div>
          <span class="lp-sec-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
            </svg>
            RA 10173
          </span>
        </div>

        <div class="lp-disclaimer">
          Officially authorized system. Unauthorized access is prohibited<br>
          and subject to applicable laws of the Republic of the Philippines.
        </div>

      </div>
    </div>

    <div class="lp-right-footer">
      &copy; {{ date('Y') }} Philippine Academy of Sakya &nbsp;&middot;&nbsp; Powered by EncryptEd
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
    if (f.type === 'password') {
      f.type = 'text';
      i.innerHTML = eyeOff;
      i.style.color = '#2060d8';
    } else {
      f.type = 'password';
      i.innerHTML = eyeOpen;
      i.style.color = '#b8c6d6';
    }
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
      <h3 style="font-size:.72rem;font-weight:800;color:#0a1628;margin:16px 0 8px;text-transform:uppercase;letter-spacing:.1em;border-left:3px solid #2060d8;padding-left:8px;">Security Measures</h3>
      <ul style="margin:0 0 12px;padding-left:18px;display:flex;flex-direction:column;gap:6px;">
        <li><strong>bcrypt (cost 12)</strong> — Passwords are hashed and never stored in plain text.</li>
        <li><strong>AES-256 encryption</strong> — All personally identifiable information is encrypted at rest.</li>
        <li><strong>Brute-force protection</strong> — Accounts are locked for 10 minutes after 5 failed attempts.</li>
        <li><strong>SHA-256 audit logs</strong> — Sensitive actions are tamper-evidently recorded.</li>
        <li><strong>Session security</strong> — HttpOnly, SameSite cookies with 30-minute idle expiry.</li>
      </ul>
      <h3 style="font-size:.72rem;font-weight:800;color:#0a1628;margin:16px 0 8px;text-transform:uppercase;letter-spacing:.1em;border-left:3px solid #2060d8;padding-left:8px;">Data Privacy Act (RA 10173)</h3>
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
              style="background:#1a3a6b;color:#fff;border:none;padding:.5rem 1.4rem;border-radius:8px;font-size:.84rem;font-weight:700;cursor:pointer;font-family:inherit;transition:background .15s;"
              onmouseover="this.style.background='#122a52'" onmouseout="this.style.background='#1a3a6b'">
        Close
      </button>
    </div>
  </div>
</div>

</body>
</html>
