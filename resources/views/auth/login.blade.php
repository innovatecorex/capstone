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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/encrypted.css') }}">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; overflow: hidden; }
    body { font-family: 'Inter', sans-serif; background: #0c1a30; }

    /* ══════════════════════════════════════
       LAYOUT
    ══════════════════════════════════════ */
    .lp-wrap {
      display: grid;
      grid-template-columns: 430px 1fr;
      height: 100vh;
      overflow: hidden;
      animation: lp-in .5s cubic-bezier(.22,.68,0,1.2) both;
    }
    @keyframes lp-in {
      from { opacity: 0; transform: scale(.98); }
      to   { opacity: 1; transform: scale(1); }
    }

    /* ══════════════════════════════════════
       LEFT PANEL
    ══════════════════════════════════════ */
    .lp-left {
      background: linear-gradient(160deg, #0d2148 0%, #091630 55%, #050e1c 100%);
      border-right: 1px solid rgba(255,255,255,.07);
      display: flex;
      flex-direction: column;
      padding: 36px 30px 26px;
      height: 100vh;
      overflow: hidden;
      position: relative;
    }

    /* ── Animated orbs ── */
    .lp-orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(60px);
      pointer-events: none;
      opacity: 0;
      animation: orb-drift linear infinite;
    }
    .lp-orb-1 {
      width: 320px; height: 320px;
      top: -80px; left: -60px;
      background: radial-gradient(circle, rgba(56,120,220,.28) 0%, transparent 70%);
      animation-duration: 18s;
      animation-delay: 0s;
    }
    .lp-orb-2 {
      width: 260px; height: 260px;
      bottom: 60px; right: -40px;
      background: radial-gradient(circle, rgba(180,130,60,.2) 0%, transparent 70%);
      animation-duration: 22s;
      animation-delay: -6s;
    }
    .lp-orb-3 {
      width: 200px; height: 200px;
      top: 45%; left: 30%;
      background: radial-gradient(circle, rgba(80,190,160,.14) 0%, transparent 70%);
      animation-duration: 26s;
      animation-delay: -12s;
    }
    @keyframes orb-drift {
      0%   { opacity: 0; transform: translate(0,0) scale(1); }
      10%  { opacity: 1; }
      50%  { transform: translate(18px, -22px) scale(1.08); }
      90%  { opacity: 1; }
      100% { opacity: 0; transform: translate(0,0) scale(1); }
    }

    /* left accent line */
    .lp-left::before {
      content: '';
      position: absolute;
      top: 0; left: 0; bottom: 0; width: 3px;
      background: linear-gradient(180deg,
        transparent 0%,
        rgba(90,150,255,.6) 25%,
        rgba(180,130,60,.5) 65%,
        transparent 100%);
    }

    /* ── Seal block ── */
    .lp-seal-block {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding-bottom: 22px;
      border-bottom: 1px solid rgba(255,255,255,.07);
      margin-bottom: 20px;
      position: relative;
      z-index: 1;
    }
    .lp-seal-logos {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-bottom: 13px;
    }
    .lp-logo-enc  { height: 21px; filter: brightness(0) invert(1); opacity: .65; }
    .lp-logo-sep  { width: 1px; height: 18px; background: rgba(255,255,255,.13); }
    .lp-logo-school {
      height: 32px; width: 32px;
      border-radius: 50%;
      object-fit: cover;
      border: 1.5px solid rgba(180,145,70,.45);
      filter: brightness(0) invert(1);
      opacity: .82;
    }
    .lp-institution {
      font-family: 'Merriweather', Georgia, serif;
      font-size: 1.05rem;
      font-weight: 700;
      color: #fff;
      letter-spacing: .01em;
      line-height: 1.3;
      margin-bottom: 4px;
    }
    .lp-system-name {
      font-size: .58rem;
      font-weight: 700;
      color: rgba(170,135,60,.8);
      letter-spacing: .18em;
      text-transform: uppercase;
    }
    .lp-official-tag {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      background: rgba(90,150,255,.08);
      border: 1px solid rgba(90,150,255,.22);
      color: rgba(130,180,255,.8);
      font-size: .57rem;
      font-weight: 700;
      letter-spacing: .12em;
      text-transform: uppercase;
      padding: .22rem .75rem;
      border-radius: 3px;
      margin-top: 10px;
    }
    .lp-official-tag svg { width: 9px; height: 9px; }

    /* ── Role access list ── */
    .lp-access-title {
      font-size: .59rem;
      font-weight: 700;
      color: rgba(255,255,255,.22);
      letter-spacing: .14em;
      text-transform: uppercase;
      margin-bottom: 8px;
      position: relative;
      z-index: 1;
    }
    .lp-features {
      display: flex;
      flex-direction: column;
      gap: 6px;
      position: relative;
      z-index: 1;
    }
    .lp-feat {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 11px 13px;
      border-radius: 10px;
      border: 1px solid rgba(255,255,255,.06);
      background: rgba(255,255,255,.025);
      transition: background .2s, border-color .2s, transform .2s;
      cursor: default;
    }
    .lp-feat:hover {
      background: rgba(255,255,255,.055);
      border-color: rgba(255,255,255,.12);
      transform: translateX(3px);
    }
    .lp-feat-icon {
      width: 36px; height: 36px;
      border-radius: 9px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      transition: transform .2s;
    }
    .lp-feat:hover .lp-feat-icon { transform: scale(1.1); }
    .lp-feat-icon svg { width: 16px; height: 16px; }
    .lp-feat-icon--amber {
      background: linear-gradient(135deg, rgba(245,180,50,.22), rgba(245,150,20,.1));
      border: 1px solid rgba(245,180,50,.22);
    }
    .lp-feat-icon--emerald {
      background: linear-gradient(135deg, rgba(52,211,153,.18), rgba(16,185,129,.08));
      border: 1px solid rgba(52,211,153,.18);
    }
    .lp-feat-icon--blue {
      background: linear-gradient(135deg, rgba(99,179,255,.2), rgba(59,130,246,.1));
      border: 1px solid rgba(99,179,255,.18);
    }
    .lp-feat-title {
      font-size: .8rem;
      font-weight: 700;
      color: rgba(255,255,255,.82);
      margin-bottom: 1px;
    }
    .lp-feat-desc {
      font-size: .65rem;
      color: rgba(255,255,255,.28);
      line-height: 1.4;
    }

    /* ── Admission card ── */
    .lp-adm {
      margin-top: auto;
      border-radius: 12px;
      background: linear-gradient(135deg, rgba(26,58,107,.7), rgba(18,40,85,.5));
      border: 1px solid rgba(90,150,255,.2);
      padding: 16px 18px;
      position: relative;
      z-index: 1;
      overflow: hidden;
    }
    .lp-adm::after {
      content: '';
      position: absolute;
      top: -30px; right: -30px;
      width: 100px; height: 100px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(90,150,255,.1) 0%, transparent 70%);
      pointer-events: none;
    }
    .lp-adm-label {
      font-size: .57rem;
      font-weight: 700;
      color: rgba(180,145,70,.75);
      letter-spacing: .13em;
      text-transform: uppercase;
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .lp-adm-label::before {
      content: '';
      display: inline-block;
      width: 14px; height: 1px;
      background: rgba(180,145,70,.45);
    }
    .lp-adm-title {
      font-size: .9rem;
      font-weight: 800;
      color: #fff;
      margin-bottom: 4px;
      line-height: 1.3;
    }
    .lp-adm-desc {
      font-size: .67rem;
      color: rgba(255,255,255,.38);
      line-height: 1.55;
      margin-bottom: 12px;
    }
    .lp-adm-btn {
      display: flex;
      align-items: center;
      gap: 8px;
      width: 100%;
      padding: .65rem 1rem;
      background: rgba(90,150,255,.12);
      border: 1px solid rgba(90,150,255,.28);
      border-radius: 8px;
      color: rgba(180,210,255,.9);
      font-size: .8rem;
      font-weight: 700;
      text-decoration: none;
      letter-spacing: .01em;
      transition: background .2s, border-color .2s, transform .15s, color .2s;
    }
    .lp-adm-btn:hover {
      background: rgba(90,150,255,.22);
      border-color: rgba(90,150,255,.45);
      color: #fff;
      transform: translateY(-1px);
    }
    .lp-adm-btn svg { width: 14px; height: 14px; flex-shrink: 0; }
    .lp-adm-btn .arr { margin-left: auto; opacity: .45; }

    .lp-left-foot {
      margin-top: 12px;
      font-size: .58rem;
      color: rgba(255,255,255,.16);
      font-weight: 500;
      letter-spacing: .025em;
      text-align: center;
      position: relative;
      z-index: 1;
    }

    /* ══════════════════════════════════════
       RIGHT PANEL
    ══════════════════════════════════════ */
    .lp-right {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      overflow-y: auto;

      /* layered background: mesh gradient + dot grid */
      background:
        radial-gradient(ellipse 80% 60% at 80% 20%, rgba(99,150,255,.10) 0%, transparent 60%),
        radial-gradient(ellipse 60% 50% at 20% 80%, rgba(52,200,160,.07) 0%, transparent 60%),
        #eef2f8;
    }

    /* dot-grid texture */
    .lp-right::before {
      content: '';
      position: absolute; inset: 0;
      background-image: radial-gradient(rgba(26,58,107,.07) 1px, transparent 1px);
      background-size: 24px 24px;
      pointer-events: none;
    }

    /* corner decorations */
    .lp-right-deco {
      position: absolute;
      pointer-events: none;
    }
    .lp-right-deco-tl {
      top: -60px; left: -60px;
      width: 260px; height: 260px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(99,150,255,.09) 0%, transparent 70%);
      filter: blur(30px);
    }
    .lp-right-deco-br {
      bottom: -80px; right: -60px;
      width: 300px; height: 300px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(52,200,160,.08) 0%, transparent 70%);
      filter: blur(40px);
    }

    /* ── Form card ── */
    .lp-form-card {
      background: #ffffff;
      border-radius: 16px;
      border: 1px solid rgba(99,130,200,.14);
      box-shadow:
        0 1px 2px rgba(10,25,60,.04),
        0 4px 16px rgba(10,25,60,.07),
        0 20px 48px rgba(10,25,60,.08);
      width: 100%;
      max-width: 440px;
      padding: 38px 38px 32px;
      position: relative;
      z-index: 1;
      margin: 0 24px;
      animation: card-rise .55s cubic-bezier(.22,.68,0,1.1) .1s both;
    }
    @keyframes card-rise {
      from { opacity: 0; transform: translateY(18px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* top accent stripe — animated shimmer */
    .lp-form-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 4px;
      border-radius: 16px 16px 0 0;
      background: linear-gradient(90deg, #1a3a6b 0%, #2e6ae6 40%, #3ecfa0 70%, #1a3a6b 100%);
      background-size: 200% 100%;
      animation: stripe-move 4s linear infinite;
    }
    @keyframes stripe-move {
      0%   { background-position: 0% 0%; }
      100% { background-position: 200% 0%; }
    }

    /* ── Card header ── */
    .lf-card-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      margin-bottom: 22px;
      padding-bottom: 20px;
      border-bottom: 1px solid #e8ecf2;
    }
    .lf-emblem {
      width: 56px; height: 56px;
      border-radius: 50%;
      background: linear-gradient(145deg, #1a3a6b, #2060d8);
      border: 3px solid #dde4f0;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 13px;
      box-shadow: 0 4px 16px rgba(26,58,107,.3), 0 0 0 6px rgba(26,58,107,.06);
      animation: emblem-pulse 3s ease-in-out infinite;
    }
    @keyframes emblem-pulse {
      0%, 100% { box-shadow: 0 4px 16px rgba(26,58,107,.3), 0 0 0 6px rgba(26,58,107,.06); }
      50%       { box-shadow: 0 4px 20px rgba(26,58,107,.4), 0 0 0 9px rgba(26,58,107,.09); }
    }
    .lf-emblem svg { width: 26px; height: 26px; color: #fff; }
    .lf-title {
      font-family: 'Merriweather', Georgia, serif;
      font-size: 1.12rem;
      font-weight: 700;
      color: #0f1e38;
      letter-spacing: -.01em;
      margin-bottom: 3px;
    }
    .lf-sub { font-size: .74rem; color: #8496b0; font-weight: 500; }

    /* ── Alerts ── */
    .lf-alert {
      display: flex;
      align-items: flex-start;
      gap: 8px;
      padding: 10px 12px;
      border-radius: 8px;
      margin-bottom: 16px;
      font-size: .77rem;
      line-height: 1.55;
    }
    .lf-alert svg { width: 14px; height: 14px; flex-shrink: 0; margin-top: 1px; }
    .lf-alert--error  { background: #fef2f2; border: 1px solid #fca5a5; color: #7f1d1d; }
    .lf-alert--notice { background: #fffbeb; border: 1px solid #f0cc6a; color: #5a4008; }
    .lf-alert--notice a { color: #7a570a; font-weight: 700; text-decoration: underline; }

    /* ── Form fields ── */
    .lf-group { margin-bottom: 15px; }
    .lf-label {
      display: block;
      font-size: .63rem;
      font-weight: 700;
      color: #4a5872;
      text-transform: uppercase;
      letter-spacing: .1em;
      margin-bottom: 6px;
    }
    .lf-input {
      width: 100%;
      height: 46px;
      padding: 0 13px;
      border: 1.5px solid #d0d8e8;
      border-radius: 9px;
      font-size: .875rem;
      color: #0f1e38;
      font-family: inherit;
      background: #f7f9fc;
      outline: none;
      transition: border-color .2s, box-shadow .2s, background .2s;
    }
    .lf-input:focus {
      border-color: #2060d8;
      background: #fff;
      box-shadow: 0 0 0 4px rgba(32,96,216,.1);
    }
    .lf-input.is-error { border-color: #dc2626; box-shadow: 0 0 0 4px rgba(220,38,38,.08); }
    .lf-input::placeholder { color: #c0ccd8; }

    /* password toggle */
    .lf-pw-wrap { position: relative; }
    .lf-pw-wrap .lf-input { padding-right: 44px; }
    .lf-pw-toggle {
      position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
      width: 17px; height: 17px; color: #b8c4d6; cursor: pointer; transition: color .2s;
    }
    .lf-pw-toggle:hover { color: #2060d8; }
    .lf-input::-ms-reveal, .lf-input::-ms-clear { display: none; }

    /* inline error */
    .lf-err {
      display: flex; align-items: center; gap: 4px;
      font-size: .7rem; color: #dc2626; margin-top: 5px; font-weight: 600;
    }
    .lf-err svg { width: 11px; height: 11px; }

    /* remember / forgot row */
    .lf-row {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 18px;
    }
    .lf-remember {
      display: flex; align-items: center; gap: 6px;
      font-size: .78rem; color: #5a6b85; cursor: pointer; font-weight: 500;
    }
    .lf-remember input { width: 14px; height: 14px; accent-color: #2060d8; cursor: pointer; }
    .lf-forgot {
      color: #2060d8; font-weight: 700; text-decoration: none;
      font-size: .78rem; transition: color .15s;
    }
    .lf-forgot:hover { color: #1040a0; text-decoration: underline; }

    /* submit button — animated gradient shimmer */
    .lf-submit {
      width: 100%;
      height: 48px;
      background: linear-gradient(90deg, #1a3a6b 0%, #2060d8 50%, #1a3a6b 100%);
      background-size: 200% 100%;
      color: #fff;
      border: none;
      border-radius: 9px;
      font-size: .875rem;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      letter-spacing: .03em;
      box-shadow: 0 4px 16px rgba(32,96,216,.35);
      transition: background-position .4s ease, box-shadow .2s, transform .15s;
      animation: btn-idle 4s ease-in-out infinite;
    }
    @keyframes btn-idle {
      0%, 100% { background-position: 0% 0%; }
      50%       { background-position: 100% 0%; }
    }
    .lf-submit:hover {
      background-position: 100% 0%;
      box-shadow: 0 6px 24px rgba(32,96,216,.45);
      transform: translateY(-2px);
    }
    .lf-submit:active { transform: translateY(0); }
    .lf-submit svg { width: 16px; height: 16px; }

    /* ── Security strip ── */
    .lf-security {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 20px;
      padding-top: 16px;
      border-top: 1px solid #e8ecf2;
    }
    .lf-sec-item {
      display: flex; align-items: center; gap: 4px;
      font-size: .61rem; font-weight: 600; color: #9aabbd; letter-spacing: .03em;
    }
    .lf-sec-item svg { width: 10px; height: 10px; }
    .lf-sec-dot { width: 3px; height: 3px; border-radius: 50%; background: #cdd5e0; }

    /* ── Notice ── */
    .lf-notice {
      margin-top: 14px;
      text-align: center;
      font-size: .62rem;
      color: #9aabbd;
      letter-spacing: .02em;
      line-height: 1.55;
    }

    /* ── Page footer ── */
    .lp-right-footer {
      position: absolute;
      bottom: 14px;
      font-size: .62rem;
      color: #9aabbd;
      font-weight: 500;
      letter-spacing: .025em;
      z-index: 1;
    }

    /* responsive */
    @media (max-width: 820px) {
      .lp-wrap { grid-template-columns: 1fr; }
      .lp-left { display: none; }
      .lp-right { height: 100vh; }
      .lp-form-card { margin: 0 20px; padding: 32px 24px 24px; }
    }
  </style>
</head>
<body>

<div class="lp-wrap">

  {{-- ══════════ LEFT — Institutional Branding ══════════ --}}
  <div class="lp-left">

    {{-- Animated orbs --}}
    <div class="lp-orb lp-orb-1"></div>
    <div class="lp-orb lp-orb-2"></div>
    <div class="lp-orb lp-orb-3"></div>

    {{-- Seal block --}}
    <div class="lp-seal-block">
      <div class="lp-seal-logos">
        <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="lp-logo-enc">
        <div class="lp-logo-sep"></div>
        <img src="{{ asset('images/logo.png') }}" alt="Philippine Academy of Sakya" class="lp-logo-school">
      </div>
      <div class="lp-institution">Philippine Academy of Sakya</div>
      <div class="lp-system-name">Academic Management System</div>
      <div class="lp-official-tag">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
        </svg>
        Official Portal
      </div>
    </div>

    {{-- Authorized roles --}}
    <div class="lp-access-title">Authorized System Users</div>
    <div class="lp-features">

      <div class="lp-feat">
        <div class="lp-feat-icon lp-feat-icon--amber">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="rgba(245,180,50,.9)" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
          </svg>
        </div>
        <div class="lp-feat-body">
          <div class="lp-feat-title">Students</div>
          <div class="lp-feat-desc">Academic records, grades, schedule &amp; payments</div>
        </div>
      </div>

      <div class="lp-feat">
        <div class="lp-feat-icon lp-feat-icon--emerald">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="rgba(52,211,153,.9)" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
          </svg>
        </div>
        <div class="lp-feat-body">
          <div class="lp-feat-title">Faculty</div>
          <div class="lp-feat-desc">Gradebook entry, attendance &amp; class management</div>
        </div>
      </div>

      <div class="lp-feat">
        <div class="lp-feat-icon lp-feat-icon--blue">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="rgba(99,179,255,.9)" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </div>
        <div class="lp-feat-body">
          <div class="lp-feat-title">Registrars</div>
          <div class="lp-feat-desc">Admissions, enrollment &amp; official records</div>
        </div>
      </div>

    </div>

    {{-- Admission card --}}
    <div class="lp-adm">
      <div class="lp-adm-label">New Students &amp; Parents</div>
      <div class="lp-adm-title">Admission &amp; Enrollment Application</div>
      <div class="lp-adm-desc">No account required. Submit your application and receive a reference number to track your status at any time.</div>
      <a href="{{ route('apply') }}" class="lp-adm-btn">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
        </svg>
        Begin Application
        <svg class="arr" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
      </a>
    </div>

    <button type="button" class="lp-left-foot"
            onclick="document.getElementById('aboutModal').style.display='flex'"
            style="background:none;border:none;cursor:pointer;font-family:inherit;width:100%;text-align:center;">
      Security Policy &amp; Data Privacy (RA 10173)
    </button>

  </div>

  {{-- ══════════ RIGHT — Authentication Form ══════════ --}}
  <div class="lp-right">

    {{-- Background decorations --}}
    <div class="lp-right-deco lp-right-deco-tl"></div>
    <div class="lp-right-deco lp-right-deco-br"></div>

    <div class="lp-form-card">

      {{-- Card header --}}
      <div class="lf-card-header">
        <div class="lf-emblem">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
          </svg>
        </div>
        <div class="lf-title">Authorized Portal Access</div>
        <div class="lf-sub">Philippine Academy of Sakya · Official System</div>
      </div>

      {{-- Error alert --}}
      @if(session('error'))
      <div class="lf-alert lf-alert--error">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <span>{{ session('error') }}</span>
      </div>
      @endif

      {{-- Security notice --}}
      <div class="lf-alert lf-alert--notice">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <div>
          <strong>Security Notice:</strong> Five (5) consecutive failed attempts will lock your account for 10 minutes.
          If you are unable to sign in, <a href="{{ route('password.request') }}">reset your password here</a>.
        </div>
      </div>

      {{-- Form --}}
      <form method="POST" action="{{ route('login') }}" autocomplete="off">
        @csrf

        <div class="lf-group">
          <label class="lf-label" for="username">Username / LRN / Employee No.</label>
          <input type="text" id="username" name="username" value="{{ old('username') }}"
            class="lf-input {{ $errors->has('username') ? 'is-error' : '' }}"
            placeholder="Enter your identifier"
            autocomplete="username" autofocus>
          @error('username')
          <div class="lf-err">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/>
            </svg>
            {{ $message }}
          </div>
          @enderror
        </div>

        <div class="lf-group">
          <label class="lf-label" for="password">Password</label>
          <div class="lf-pw-wrap">
            <input type="password" id="password" name="password"
              class="lf-input {{ $errors->has('password') ? 'is-error' : '' }}"
              placeholder="Enter your password"
              autocomplete="current-password">
            <svg id="toggle-pw" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              stroke="currentColor" stroke-width="2" class="lf-pw-toggle" onclick="togglePw()">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          @error('password')
          <div class="lf-err">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/>
            </svg>
            {{ $message }}
          </div>
          @enderror
        </div>

        <div class="lf-row">
          <label class="lf-remember">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            Keep me signed in
          </label>
          <a href="{{ route('password.request') }}" class="lf-forgot">Forgot password?</a>
        </div>

        <button type="submit" class="lf-submit">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
          </svg>
          Sign In to Portal
        </button>
      </form>

      {{-- Security strip --}}
      <div class="lf-security">
        <span class="lf-sec-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
          </svg>
          AES-256
        </span>
        <div class="lf-sec-dot"></div>
        <span class="lf-sec-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
          </svg>
          bcrypt · SHA-256
        </span>
        <div class="lf-sec-dot"></div>
        <span class="lf-sec-item">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
          </svg>
          RA 10173
        </span>
      </div>

      <div class="lf-notice">
        This is an officially authorized system. Unauthorized access is prohibited<br>
        and subject to applicable laws of the Republic of the Philippines.
      </div>

    </div>

    <div class="lp-right-footer">
      &copy; {{ date('Y') }} Philippine Academy of Sakya &nbsp;&middot;&nbsp; Powered by EncryptEd
    </div>

  </div>
</div>

<script>
  function togglePw() {
    const f = document.getElementById('password');
    const i = document.getElementById('toggle-pw');
    const open = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>';
    const off  = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88"/>';
    if (f.type === 'password') { f.type = 'text';     i.innerHTML = off;  i.style.color = '#2060d8'; }
    else                       { f.type = 'password'; i.innerHTML = open; i.style.color = '#b8c4d6'; }
  }
</script>

{{-- About / Security Modal --}}
<div id="aboutModal"
     style="display:none;position:fixed;inset:0;background:rgba(6,13,28,.75);z-index:1000;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(6px);"
     onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#fff;border-radius:14px;max-width:580px;width:100%;max-height:86vh;overflow-y:auto;box-shadow:0 24px 64px rgba(0,0,0,.45);border:1px solid #d4dae6;">
    <div style="padding:16px 22px;border-bottom:1px solid #e8ecf2;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:#fff;border-radius:14px 14px 0 0;">
      <h2 style="margin:0;font-size:.95rem;font-weight:800;color:#0f1e38;">Security Policy &amp; Data Privacy</h2>
      <button type="button" onclick="document.getElementById('aboutModal').style.display='none'"
              style="background:#f1f5f9;border:1px solid #d4dae6;width:28px;height:28px;border-radius:7px;font-size:1rem;line-height:1;color:#64748b;cursor:pointer;display:flex;align-items:center;justify-content:center;">&times;</button>
    </div>
    <div style="padding:18px 22px;font-size:.84rem;line-height:1.7;color:#334155;">
      <p style="margin:0 0 12px;"><strong>EncryptEd</strong> is the official academic management platform of the Philippine Academy of Sakya. All personal data is handled in strict compliance with Republic Act No. 10173 (Data Privacy Act of 2012).</p>
      <h3 style="font-size:.75rem;font-weight:800;color:#0f1e38;margin:14px 0 7px;text-transform:uppercase;letter-spacing:.08em;">Security Measures</h3>
      <ul style="margin:0 0 10px;padding-left:15px;display:flex;flex-direction:column;gap:5px;">
        <li><strong>bcrypt (cost 12)</strong> — Passwords are hashed and never stored in plain text.</li>
        <li><strong>AES-256 encryption</strong> — All personally identifiable information is encrypted at rest.</li>
        <li><strong>Brute-force protection</strong> — Accounts are locked for 10 minutes after 5 failed attempts.</li>
        <li><strong>SHA-256 audit logs</strong> — All sensitive actions are tamper-evidently recorded.</li>
        <li><strong>Session security</strong> — HttpOnly, SameSite cookies; 30-minute idle session expiry.</li>
      </ul>
      <h3 style="font-size:.75rem;font-weight:800;color:#0f1e38;margin:14px 0 7px;text-transform:uppercase;letter-spacing:.08em;">RA 10173 — Data Privacy Act of 2012</h3>
      <ul style="margin:0;padding-left:15px;display:flex;flex-direction:column;gap:5px;">
        <li><strong>Data Minimization</strong> — Only operationally necessary data is collected.</li>
        <li><strong>Informed Consent</strong> — Explicit consent is obtained during the admission process.</li>
        <li><strong>Right of Access &amp; Rectification</strong> — Users may view and request correction of their records.</li>
        <li><strong>Right to Erasure</strong> — Data deletion requests may be submitted to the Data Protection Officer.</li>
      </ul>
      <p style="margin:12px 0 0;font-size:.72rem;color:#94a3b8;">For data privacy concerns, contact the Data Protection Officer through the School Registrar's Office.</p>
    </div>
    <div style="padding:12px 22px;border-top:1px solid #e8ecf2;text-align:right;position:sticky;bottom:0;background:#fff;border-radius:0 0 14px 14px;">
      <button type="button" onclick="document.getElementById('aboutModal').style.display='none'"
              style="background:#1a3a6b;color:#fff;border:none;padding:.45rem 1.2rem;border-radius:7px;font-size:.84rem;font-weight:700;cursor:pointer;">
        Close
      </button>
    </div>
  </div>
</div>

</body>
</html>
