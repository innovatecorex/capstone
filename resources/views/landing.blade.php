{{-- resources/views/landing.blade.php — public homepage --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Philippine Academy of Sakya — Official Academic Portal</title>
  <meta name="description" content="The official secure academic portal of Philippine Academy of Sakya — Junior &amp; Senior High School. Apply for admission or sign in to EncryptEd.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Merriweather:wght@700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/encrypted.css') }}">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f7fb;
      color: #1e293b;
      min-height: 100vh;
      overflow-x: hidden;
    }
    a { text-decoration: none; }
    :focus-visible { outline: 2px solid #2563eb; outline-offset: 2px; border-radius: 6px; }

    /* ══════════════ TOP NAV ══════════════ */
    .ld-nav {
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
    .ld-nav-brand { display: flex; align-items: center; gap: .75rem; min-width: 0; }
    .ld-nav-brand img {
      height: 32px; width: 32px;
      border-radius: 50%;
      border: 1.5px solid rgba(255,255,255,.15);
      flex-shrink: 0;
    }
    .ld-nav-name { font-size: .88rem; font-weight: 800; color: #fff; letter-spacing: -.01em; }
    .ld-nav-sub  { font-size: .62rem; color: rgba(255,255,255,.38); letter-spacing: .03em; }
    .ld-nav-sep  { flex: 1; }
    .ld-nav-actions { display: flex; align-items: center; gap: .6rem; flex-shrink: 0; }

    .ld-btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 7px;
      font-family: inherit; font-weight: 700; cursor: pointer;
      border-radius: 10px; transition: background .18s, color .18s, transform .15s, box-shadow .18s;
      white-space: nowrap;
    }
    .ld-btn svg { width: 14px; height: 14px; flex-shrink: 0; }

    /* nav-sized */
    .ld-btn--ghost {
      padding: .5rem 1rem; font-size: .78rem;
      color: rgba(255,255,255,.72);
      border: 1px solid rgba(255,255,255,.18);
      background: transparent;
    }
    .ld-btn--ghost:hover { color: #fff; background: rgba(255,255,255,.08); }
    .ld-btn--light {
      padding: .5rem 1rem; font-size: .78rem;
      background: #fff; color: #1d4ed8;
      box-shadow: 0 3px 12px rgba(0,0,0,.2);
    }
    .ld-btn--light:hover { background: #f0f7ff; transform: translateY(-1px); }

    /* hero-sized */
    .ld-btn--primary {
      padding: .8rem 1.6rem; font-size: .9rem;
      background: #fff; color: #1d4ed8;
      box-shadow: 0 6px 20px rgba(0,0,0,.25);
    }
    .ld-btn--primary:hover { background: #f0f7ff; transform: translateY(-1px); box-shadow: 0 10px 28px rgba(0,0,0,.3); }
    .ld-btn--outline {
      padding: .8rem 1.6rem; font-size: .9rem;
      background: rgba(255,255,255,.06); color: #fff;
      border: 1.5px solid rgba(255,255,255,.35);
    }
    .ld-btn--outline:hover { background: rgba(255,255,255,.14); transform: translateY(-1px); }

    /* solid blue (admission band) */
    .ld-btn--solid {
      padding: .8rem 1.7rem; font-size: .9rem;
      background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 60%, #2563eb 100%);
      color: #fff;
      box-shadow: 0 4px 16px rgba(29,78,216,.32);
    }
    .ld-btn--solid:hover { transform: translateY(-1px); box-shadow: 0 8px 26px rgba(29,78,216,.42); }

    /* ══════════════ HERO ══════════════ */
    .ld-hero {
      background: linear-gradient(135deg, #0b1e3d 0%, #1535a0 55%, #1d4ed8 100%);
      padding: 3.5rem 2rem 4.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .ld-hero::before {
      content: '';
      position: absolute; inset: 0;
      background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
      background-size: 24px 24px;
      pointer-events: none;
    }
    .ld-hero::after {
      content: '';
      position: absolute; bottom: -2px; left: 0; right: 0;
      height: 44px; background: #f4f7fb;
      clip-path: ellipse(55% 100% at 50% 100%);
      pointer-events: none;
    }
    .ld-hero-inner { position: relative; z-index: 1; max-width: 760px; margin: 0 auto; }
    .ld-hero-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(245,158,11,.15);
      border: 1px solid rgba(245,158,11,.3);
      color: #fde68a;
      font-size: .67rem; font-weight: 700;
      letter-spacing: .1em; text-transform: uppercase;
      padding: .3rem .85rem; border-radius: 99px;
      margin-bottom: 1rem;
    }
    .ld-hero-badge svg { width: 12px; height: 12px; }
    .ld-hero h1 {
      font-family: 'Merriweather', serif;
      font-size: 2.5rem; font-weight: 900; color: #fff;
      letter-spacing: -.02em; line-height: 1.15;
      margin-bottom: .6rem;
    }
    .ld-hero-tagline {
      font-size: .8rem; font-weight: 700;
      color: rgba(255,255,255,.5);
      letter-spacing: .12em; text-transform: uppercase;
      margin-bottom: 1rem;
    }
    .ld-hero-desc {
      font-size: 1rem; line-height: 1.7;
      color: rgba(255,255,255,.78);
      max-width: 560px; margin: 0 auto 2rem;
    }
    .ld-hero-cta { display: flex; gap: .8rem; justify-content: center; flex-wrap: wrap; }

    /* ══════════════ SECTIONS ══════════════ */
    .ld-section { max-width: 1040px; margin: 0 auto; padding: 3.5rem 1.5rem; }
    .ld-section-head { text-align: center; margin-bottom: 2rem; }
    .ld-eyebrow {
      font-size: .63rem; font-weight: 800; color: #94a3b8;
      text-transform: uppercase; letter-spacing: .12em; margin-bottom: .5rem;
    }
    .ld-section-title {
      font-family: 'Merriweather', serif;
      font-size: 1.6rem; font-weight: 900; color: #0f172a;
      letter-spacing: -.02em;
    }
    .ld-section-sub { font-size: .9rem; color: #64748b; margin-top: .5rem; }

    .ld-grid { display: grid; gap: 1.1rem; }
    .ld-grid--3 { grid-template-columns: repeat(3, 1fr); }
    .ld-grid--4 { grid-template-columns: repeat(4, 1fr); }

    /* card — mirrors .lp-card from the login page */
    .ld-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 2px 16px rgba(15,23,42,.07), 0 1px 3px rgba(15,23,42,.04);
      border: 1px solid rgba(226,232,240,.7);
      overflow: hidden;
      display: flex; flex-direction: column;
      transition: transform .18s, box-shadow .18s;
    }
    .ld-card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(15,23,42,.1); }
    .ld-card-bar { height: 3px; background: linear-gradient(90deg, #1a3a6b, #2563eb, #3ecfa0); }
    .ld-card-body { padding: 1.4rem; }
    .ld-card-icon {
      width: 40px; height: 40px; border-radius: 11px;
      background: #eff6ff;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: .9rem;
    }
    .ld-card-icon svg { width: 19px; height: 19px; }
    .ld-card-title { font-size: .95rem; font-weight: 800; color: #0f172a; margin-bottom: .35rem; letter-spacing: -.01em; }
    .ld-card-text  { font-size: .84rem; line-height: 1.65; color: #64748b; }

    /* ══════════════ ADMISSION BAND ══════════════ */
    .ld-band-wrap { padding: 0 1.5rem 3.5rem; }
    .ld-band {
      max-width: 1040px; margin: 0 auto;
      background: linear-gradient(135deg, #0b1e3d 0%, #1535a0 55%, #1d4ed8 100%);
      border-radius: 20px;
      padding: 2.5rem 2rem;
      text-align: center;
      position: relative; overflow: hidden;
    }
    .ld-band::before {
      content: '';
      position: absolute; inset: 0;
      background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
      background-size: 22px 22px;
      pointer-events: none;
    }
    .ld-band-inner { position: relative; z-index: 1; }
    .ld-band-eyebrow {
      display: inline-flex; align-items: center; gap: 6px;
      background: rgba(52,211,153,.15);
      border: 1px solid rgba(52,211,153,.35);
      color: #6ee7b7;
      font-size: .65rem; font-weight: 700;
      letter-spacing: .1em; text-transform: uppercase;
      padding: .3rem .85rem; border-radius: 99px;
      margin-bottom: .9rem;
    }
    .ld-band-eyebrow svg { width: 12px; height: 12px; }
    .ld-band h2 {
      font-family: 'Merriweather', serif;
      font-size: 1.7rem; font-weight: 900; color: #fff;
      letter-spacing: -.02em; margin-bottom: .5rem;
    }
    .ld-band p { font-size: .92rem; color: rgba(255,255,255,.75); margin-bottom: 1.5rem; }

    /* ══════════════ FOOTER ══════════════ */
    .ld-footer { background: #0b1e3d; padding: 2.5rem 1.5rem 1.5rem; }
    .ld-footer-inner {
      max-width: 1040px; margin: 0 auto;
      display: flex; justify-content: space-between; align-items: flex-start;
      gap: 2rem; flex-wrap: wrap;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid rgba(255,255,255,.08);
    }
    .ld-footer-brand { display: flex; align-items: center; gap: .75rem; }
    .ld-footer-brand img { height: 34px; width: 34px; border-radius: 50%; border: 1.5px solid rgba(255,255,255,.15); }
    .ld-footer-name { font-size: .9rem; font-weight: 800; color: #fff; }
    .ld-footer-sub  { font-size: .65rem; color: rgba(255,255,255,.4); margin-top: 2px; }
    .ld-footer-links { display: flex; flex-direction: column; gap: .45rem; }
    .ld-footer-links-title {
      font-size: .6rem; font-weight: 800; color: rgba(255,255,255,.45);
      text-transform: uppercase; letter-spacing: .12em; margin-bottom: .2rem;
    }
    .ld-footer-links a {
      font-size: .8rem; color: rgba(255,255,255,.65); font-weight: 500;
      transition: color .15s;
    }
    .ld-footer-links a:hover { color: #fff; }
    .ld-footer-note {
      max-width: 1040px; margin: 1.25rem auto 0;
      text-align: center;
      font-size: .68rem; color: rgba(255,255,255,.38);
      line-height: 1.7;
    }
    .ld-footer-note strong { color: rgba(255,255,255,.55); }

    /* ══════════════ RESPONSIVE ══════════════ */
    @media (max-width: 900px) {
      .ld-grid--4 { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 760px) {
      .ld-nav { padding: .7rem 1rem; flex-wrap: wrap; gap: .6rem; }
      .ld-nav-sub { display: none; }
      .ld-nav-actions { width: 100%; justify-content: stretch; }
      .ld-nav-actions .ld-btn { flex: 1; }
      .ld-hero { padding: 2.5rem 1.25rem 3.5rem; }
      .ld-hero h1 { font-size: 1.8rem; }
      .ld-hero-desc { font-size: .92rem; }
      .ld-hero-cta .ld-btn { width: 100%; }
      .ld-section { padding: 2.5rem 1.25rem; }
      .ld-section-title { font-size: 1.35rem; }
      .ld-grid--3, .ld-grid--4 { grid-template-columns: 1fr; }
      .ld-band { padding: 2rem 1.25rem; }
      .ld-band h2 { font-size: 1.35rem; }
      .ld-band .ld-btn { width: 100%; }
      .ld-footer-inner { flex-direction: column; gap: 1.5rem; }
    }
  </style>
</head>
<body>

{{-- ══════════ TOP NAV ══════════ --}}
<nav class="ld-nav">
  <a href="{{ route('landing') }}" class="ld-nav-brand">
    <img src="{{ asset('images/logo.png') }}" alt="Philippine Academy of Sakya logo">
    <div>
      <div class="ld-nav-name">Philippine Academy of Sakya</div>
      <div class="ld-nav-sub">EncryptEd · Academic Management System</div>
    </div>
  </a>
  <div class="ld-nav-sep"></div>
  <div class="ld-nav-actions">
    <a href="{{ route('apply') }}" class="ld-btn ld-btn--ghost">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
      </svg>
      Apply for Admission
    </a>
    <a href="{{ route('login') }}" class="ld-btn ld-btn--light">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
      </svg>
      Sign In
    </a>
  </div>
</nav>

{{-- ══════════ HERO ══════════ --}}
<header class="ld-hero">
  <div class="ld-hero-inner">
    <div class="ld-hero-badge">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
      Official Portal
    </div>

    <h1>Philippine Academy of Sakya</h1>
    <div class="ld-hero-tagline">EncryptEd · Academic Management System</div>
    <p class="ld-hero-desc">
      The official secure academic portal of Philippine Academy of Sakya —
      Junior &amp; Senior High School.
    </p>

    <div class="ld-hero-cta">
      <a href="{{ route('apply') }}" class="ld-btn ld-btn--primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
        Apply Now
      </a>
      <a href="{{ route('login') }}" class="ld-btn ld-btn--outline">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg>
        Portal Login
      </a>
    </div>
  </div>
</header>

{{-- ══════════ WHO IT'S FOR ══════════ --}}
<section class="ld-section">
  <div class="ld-section-head">
    <div class="ld-eyebrow">Who It's For</div>
    <h2 class="ld-section-title">One portal, every role</h2>
    <p class="ld-section-sub">Secure, role-based access for the whole school community.</p>
  </div>

  <div class="ld-grid ld-grid--3">
    <div class="ld-card">
      <div class="ld-card-bar"></div>
      <div class="ld-card-body">
        <div class="ld-card-icon" style="background:#fffbeb;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
          </svg>
        </div>
        <div class="ld-card-title">Students &amp; Parents</div>
        <p class="ld-card-text">View grades, schedules, attendance and enrollment status, and submit requirements online.</p>
      </div>
    </div>

    <div class="ld-card">
      <div class="ld-card-bar"></div>
      <div class="ld-card-body">
        <div class="ld-card-icon" style="background:#ecfdf5;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
          </svg>
        </div>
        <div class="ld-card-title">Faculty</div>
        <p class="ld-card-text">Encode and submit grades, manage class records, and track student attendance with confidence.</p>
      </div>
    </div>

    <div class="ld-card">
      <div class="ld-card-bar"></div>
      <div class="ld-card-body">
        <div class="ld-card-icon" style="background:#eff6ff;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#1d4ed8" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
          </svg>
        </div>
        <div class="ld-card-title">Registrar &amp; Admin</div>
        <p class="ld-card-text">Process admissions and enrollment, manage records and sections, and oversee system security.</p>
      </div>
    </div>
  </div>
</section>

{{-- ══════════ WHY ENCRYPTED ══════════ --}}
<section class="ld-section" style="padding-top:0;">
  <div class="ld-section-head">
    <div class="ld-eyebrow">Why EncryptEd</div>
    <h2 class="ld-section-title">Built for security and compliance</h2>
    <p class="ld-section-sub">Student data is protected at every layer, in line with Philippine law.</p>
  </div>

  <div class="ld-grid ld-grid--4">
    <div class="ld-card">
      <div class="ld-card-bar"></div>
      <div class="ld-card-body">
        <div class="ld-card-icon" style="background:#eff6ff;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#1d4ed8" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
          </svg>
        </div>
        <div class="ld-card-title">Data Encryption</div>
        <p class="ld-card-text">Personal data is AES-256 encrypted at rest, in compliance with RA 10173 (Data Privacy Act).</p>
      </div>
    </div>

    <div class="ld-card">
      <div class="ld-card-bar"></div>
      <div class="ld-card-body">
        <div class="ld-card-icon" style="background:#fef2f2;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
        </div>
        <div class="ld-card-title">Threat Monitoring</div>
        <p class="ld-card-text">Suspicious activity and injection attempts are detected, blocked and logged in real time.</p>
      </div>
    </div>

    <div class="ld-card">
      <div class="ld-card-bar"></div>
      <div class="ld-card-body">
        <div class="ld-card-icon" style="background:#f5f3ff;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
          </svg>
        </div>
        <div class="ld-card-title">Role-Based Access</div>
        <p class="ld-card-text">Every account sees only what its role permits — students, faculty, registrar and admin.</p>
      </div>
    </div>

    <div class="ld-card">
      <div class="ld-card-bar"></div>
      <div class="ld-card-body">
        <div class="ld-card-icon" style="background:#ecfdf5;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
          </svg>
        </div>
        <div class="ld-card-title">Digital Enrollment &amp; Grades</div>
        <p class="ld-card-text">Apply, enroll, pay and receive report cards online — no paper forms, no queues.</p>
      </div>
    </div>
  </div>
</section>

{{-- ══════════ ADMISSION BAND ══════════ --}}
<div class="ld-band-wrap">
  <div class="ld-band">
    <div class="ld-band-inner">
      <div class="ld-band-eyebrow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Now Enrolling
      </div>
      <h2>Now enrolling for SY 2025&ndash;2026</h2>
      <p>Admission is open for Junior &amp; Senior High School. Start your application online today.</p>
      <a href="{{ route('apply') }}" class="ld-btn ld-btn--primary">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
        Begin Application
      </a>
    </div>
  </div>
</div>

{{-- ══════════ FOOTER ══════════ --}}
<footer class="ld-footer">
  <div class="ld-footer-inner">
    <div class="ld-footer-brand">
      <img src="{{ asset('images/logo.png') }}" alt="Philippine Academy of Sakya logo">
      <div>
        <div class="ld-footer-name">Philippine Academy of Sakya</div>
        <div class="ld-footer-sub">Junior &amp; Senior High School</div>
      </div>
    </div>

    <div class="ld-footer-links">
      <div class="ld-footer-links-title">Quick Links</div>
      <a href="{{ route('login') }}">Portal Login</a>
      <a href="{{ route('apply') }}">Apply for Admission</a>
    </div>
  </div>

  <div class="ld-footer-note">
    &copy; {{ date('Y') }} Philippine Academy of Sakya &nbsp;&middot;&nbsp; Powered by <strong>EncryptEd</strong><br>
    All personal data is processed in compliance with <strong>RA 10173 (Data Privacy Act of 2012)</strong>.
  </div>
</footer>

</body>
</html>
