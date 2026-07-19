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
    :root {
      --navy:        #0a1a33;
      --navy-700:    #12305c;
      --indigo:      #1d4ed8;
      --indigo-600:  #2563eb;
      --gold:        #c9962f;
      --gold-soft:   #f3d89a;
      --ink:         #0f172a;
      --body:        #55627a;
      --muted:       #8a95a8;
      --line:        #e5e9f0;
      --paper:       #ffffff;
      --canvas:      #f7f9fc;
      --radius:      14px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
    body {
      font-family: 'Inter', sans-serif;
      background: var(--paper);
      color: var(--body);
      line-height: 1.65;
      overflow-x: hidden;
    }
    a { text-decoration: none; color: inherit; }
    img { max-width: 100%; }
    :focus-visible { outline: 2px solid var(--indigo); outline-offset: 3px; border-radius: 4px; }

    .wrap { max-width: 1120px; margin: 0 auto; padding: 0 1.5rem; }

    /* ── shared type ─────────────────────────────────── */
    .eyebrow {
      display: inline-block;
      font-size: .68rem; font-weight: 700;
      letter-spacing: .16em; text-transform: uppercase;
      color: var(--muted);
    }
    .serif { font-family: 'Merriweather', Georgia, serif; }

    /* ── buttons ─────────────────────────────────────── */
    .btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      font-family: inherit; font-weight: 600; cursor: pointer;
      border: 1px solid transparent; border-radius: 10px;
      min-height: 44px; padding: 0 1.25rem;
      font-size: .875rem; white-space: nowrap;
      transition: background-color .18s ease, color .18s ease, border-color .18s ease, box-shadow .18s ease;
    }
    .btn svg { width: 15px; height: 15px; flex-shrink: 0; }
    .btn--navy   { background: var(--navy); color: #fff; }
    .btn--navy:hover { background: var(--navy-700); box-shadow: 0 6px 18px rgba(10,26,51,.22); }
    .btn--quiet  { background: transparent; color: var(--ink); border-color: var(--line); }
    .btn--quiet:hover { border-color: #c7cfdc; background: #f4f6fa; }
    .btn--gold   { background: var(--gold); color: #241a04; }
    .btn--gold:hover { background: #dda93c; box-shadow: 0 6px 18px rgba(201,150,47,.32); }
    .btn--onDark { background: rgba(255,255,255,.07); color: #fff; border-color: rgba(255,255,255,.24); }
    .btn--onDark:hover { background: rgba(255,255,255,.14); border-color: rgba(255,255,255,.45); }
    .btn--lg { min-height: 50px; padding: 0 1.6rem; font-size: .92rem; }

    /* ══════════════ NAV ══════════════ */
    .nav {
      position: sticky; top: 0; z-index: 100;
      background: rgba(255,255,255,.88);
      backdrop-filter: saturate(180%) blur(14px);
      -webkit-backdrop-filter: saturate(180%) blur(14px);
      border-bottom: 1px solid var(--line);
    }
    .nav__in { display: flex; align-items: center; gap: 1rem; height: 68px; }
    .nav__brand { display: flex; align-items: center; gap: .7rem; min-width: 0; }
    .nav__crest {
      width: 38px; height: 38px; border-radius: 50%;
      border: 1px solid var(--line); padding: 2px; background: #fff;
      flex-shrink: 0;
    }
    .nav__name { font-size: .9rem; font-weight: 800; color: var(--ink); letter-spacing: -.015em; line-height: 1.2; }
    .nav__sub  { font-size: .66rem; color: var(--muted); letter-spacing: .06em; text-transform: uppercase; margin-top: 1px; }
    .nav__sp { flex: 1; }
    .nav__acts { display: flex; align-items: center; gap: .6rem; flex-shrink: 0; }

    /* ══════════════ HERO ══════════════ */
    .hero {
      position: relative;
      background:
        radial-gradient(1000px 460px at 78% 8%, rgba(37,99,235,.30), transparent 60%),
        linear-gradient(180deg, #0a1a33 0%, #0d2246 100%);
      color: #fff;
      overflow: hidden;
      border-bottom: 3px solid var(--gold);
    }
    /* faint academic grid texture */
    .hero::before {
      content: '';
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
      background-size: 62px 62px;
      mask-image: radial-gradient(70% 60% at 50% 40%, #000 40%, transparent 100%);
      -webkit-mask-image: radial-gradient(70% 60% at 50% 40%, #000 40%, transparent 100%);
      pointer-events: none;
    }
    .hero__in {
      position: relative; z-index: 1;
      display: grid;
      grid-template-columns: 1.15fr .85fr;
      gap: 3.5rem;
      align-items: center;
      padding: 5rem 0 5.5rem;
    }
    .hero__eyebrow { color: var(--gold-soft); }
    .hero__eyebrow::after {
      content: ''; display: block;
      width: 34px; height: 2px; background: var(--gold);
      margin-top: .7rem;
    }
    .hero h1 {
      font-size: clamp(2rem, 3.6vw, 2.85rem);
      font-weight: 900; color: #fff;
      letter-spacing: -.025em; line-height: 1.18;
      margin: 1.1rem 0 .5rem;
    }
    .hero__tagline {
      font-size: .72rem; font-weight: 700;
      letter-spacing: .18em; text-transform: uppercase;
      color: rgba(255,255,255,.44);
      margin-bottom: 1.1rem;
    }
    .hero__desc {
      font-size: 1.02rem; line-height: 1.75;
      color: rgba(255,255,255,.74);
      max-width: 52ch;
      margin-bottom: 2rem;
    }
    .hero__cta { display: flex; gap: .75rem; flex-wrap: wrap; }

    /* crest panel */
    .crest {
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.12);
      border-radius: 20px;
      padding: 2.25rem 1.75rem;
      text-align: center;
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
    }
    .crest img {
      width: 108px; height: 108px; border-radius: 50%;
      border: 2px solid rgba(201,150,47,.55);
      padding: 4px; background: rgba(255,255,255,.06);
      margin-bottom: 1.1rem;
    }
    .crest__name { font-size: 1rem; font-weight: 800; color: #fff; letter-spacing: -.01em; }
    .crest__rule { width: 40px; height: 2px; background: var(--gold); margin: .8rem auto; }
    .crest__meta { font-size: .78rem; color: rgba(255,255,255,.6); line-height: 1.7; }

    /* ══════════════ TRUST STRIP ══════════════ */
    .trust { background: var(--canvas); border-bottom: 1px solid var(--line); }
    .trust__in {
      display: grid; grid-template-columns: repeat(4, 1fr);
      gap: 1rem; padding: 1.35rem 0;
    }
    .trust__item { display: flex; align-items: center; gap: .65rem; justify-content: center; }
    .trust__item svg { width: 17px; height: 17px; color: var(--indigo); flex-shrink: 0; }
    .trust__label { font-size: .8rem; font-weight: 600; color: var(--ink); }

    /* ══════════════ SECTIONS ══════════════ */
    .sec { padding: 5rem 0; }
    .sec--tint { background: var(--canvas); border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
    .sec__head { max-width: 640px; margin-bottom: 2.75rem; }
    .sec__head--center { margin-left: auto; margin-right: auto; text-align: center; }
    .sec__title {
      font-size: clamp(1.5rem, 2.4vw, 1.95rem);
      font-weight: 900; color: var(--ink);
      letter-spacing: -.022em; line-height: 1.25;
      margin: .7rem 0 .6rem;
    }
    .sec__sub { font-size: .96rem; color: var(--body); line-height: 1.75; }

    .grid { display: grid; gap: 1.25rem; }
    .grid--3 { grid-template-columns: repeat(3, 1fr); }
    .grid--4 { grid-template-columns: repeat(4, 1fr); }

    /* role cards — hairline, numbered, restrained */
    .role {
      background: var(--paper);
      border: 1px solid var(--line);
      border-radius: var(--radius);
      padding: 1.75rem 1.5rem;
      transition: border-color .2s ease, box-shadow .2s ease;
    }
    .role:hover { border-color: #cbd5e6; box-shadow: 0 10px 30px rgba(15,23,42,.07); }
    .role__top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.1rem; }
    .role__ico {
      width: 42px; height: 42px; border-radius: 11px;
      display: flex; align-items: center; justify-content: center;
      background: #eef3fe; border: 1px solid #dce6fb;
    }
    .role__ico svg { width: 19px; height: 19px; color: var(--indigo); }
    .role__num { font-size: .72rem; font-weight: 800; color: var(--muted); letter-spacing: .1em; }
    .role__title { font-size: 1rem; font-weight: 800; color: var(--ink); margin-bottom: .4rem; letter-spacing: -.01em; }
    .role__text { font-size: .875rem; color: var(--body); line-height: 1.7; }

    /* feature cards */
    .feat {
      background: var(--paper);
      border: 1px solid var(--line);
      border-radius: var(--radius);
      padding: 1.6rem 1.35rem;
      transition: border-color .2s ease, transform .2s ease, box-shadow .2s ease;
    }
    .feat:hover { border-color: #cbd5e6; transform: translateY(-2px); box-shadow: 0 12px 30px rgba(15,23,42,.08); }
    .feat__ico {
      width: 40px; height: 40px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      margin-bottom: 1rem;
    }
    .feat__ico svg { width: 19px; height: 19px; }
    .feat__title { font-size: .93rem; font-weight: 800; color: var(--ink); margin-bottom: .35rem; letter-spacing: -.01em; }
    .feat__text  { font-size: .84rem; color: var(--body); line-height: 1.7; }

    /* ══════════════ ADMISSION BAND ══════════════ */
    .band {
      position: relative;
      background: linear-gradient(180deg, #0a1a33 0%, #12305c 100%);
      color: #fff; overflow: hidden;
      border-top: 3px solid var(--gold);
    }
    .band::before {
      content: '';
      position: absolute; inset: 0;
      background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
      background-size: 26px 26px;
      pointer-events: none;
    }
    .band__in {
      position: relative; z-index: 1;
      display: flex; align-items: center; justify-content: space-between;
      gap: 2rem; flex-wrap: wrap;
      padding: 3.25rem 0;
    }
    .band__pill {
      display: inline-flex; align-items: center; gap: 7px;
      background: rgba(201,150,47,.16);
      border: 1px solid rgba(201,150,47,.4);
      color: var(--gold-soft);
      font-size: .67rem; font-weight: 700;
      letter-spacing: .12em; text-transform: uppercase;
      padding: .32rem .8rem; border-radius: 999px;
      margin-bottom: .9rem;
    }
    .band__pill svg { width: 12px; height: 12px; }
    .band h2 {
      font-size: clamp(1.4rem, 2.2vw, 1.8rem);
      font-weight: 900; color: #fff;
      letter-spacing: -.022em; margin-bottom: .45rem;
    }
    .band p { font-size: .95rem; color: rgba(255,255,255,.7); max-width: 46ch; }

    /* ══════════════ FOOTER ══════════════ */
    .foot { background: var(--navy); color: rgba(255,255,255,.6); }
    .foot__in {
      display: grid; grid-template-columns: 2fr 1fr 1fr;
      gap: 2.5rem; padding: 3.25rem 0 2.25rem;
    }
    .foot__brand { display: flex; align-items: center; gap: .8rem; margin-bottom: .9rem; }
    .foot__brand img {
      width: 40px; height: 40px; border-radius: 50%;
      border: 1px solid rgba(255,255,255,.18); padding: 2px;
    }
    .foot__name { font-size: .95rem; font-weight: 800; color: #fff; letter-spacing: -.01em; }
    .foot__sub  { font-size: .7rem; color: rgba(255,255,255,.42); margin-top: 2px; }
    .foot__blurb { font-size: .84rem; line-height: 1.75; max-width: 42ch; color: rgba(255,255,255,.55); }
    .foot__h {
      font-size: .66rem; font-weight: 800; color: rgba(255,255,255,.85);
      text-transform: uppercase; letter-spacing: .14em; margin-bottom: 1rem;
    }
    .foot__links { display: flex; flex-direction: column; gap: .6rem; }
    .foot__links a { font-size: .86rem; color: rgba(255,255,255,.6); transition: color .16s ease; }
    .foot__links a:hover { color: #fff; }
    .foot__bar {
      border-top: 1px solid rgba(255,255,255,.09);
      padding: 1.35rem 0 2rem;
      display: flex; justify-content: space-between; align-items: center;
      gap: 1rem; flex-wrap: wrap;
      font-size: .76rem; color: rgba(255,255,255,.42);
    }
    .foot__bar strong { color: rgba(255,255,255,.62); font-weight: 700; }

    /* ══════════════ RESPONSIVE ══════════════ */
    @media (max-width: 980px) {
      .hero__in { grid-template-columns: 1fr; gap: 2.5rem; padding: 3.5rem 0 4rem; }
      .crest { max-width: 380px; }
      .trust__in { grid-template-columns: repeat(2, 1fr); gap: 1rem 1.25rem; }
      .trust__item { justify-content: flex-start; }
      .grid--4 { grid-template-columns: repeat(2, 1fr); }
      .foot__in { grid-template-columns: 1fr 1fr; gap: 2rem; }
      .foot__brand-col { grid-column: 1 / -1; }
    }
    @media (max-width: 700px) {
      .nav__in { height: 60px; }
      .nav__sub { display: none; }
      .nav__acts .btn--quiet { display: none; }   /* keep one clear CTA on mobile */
      .sec { padding: 3.25rem 0; }
      .hero__desc { font-size: .95rem; }
      .hero__cta .btn { flex: 1 1 100%; }
      .grid--3, .grid--4 { grid-template-columns: 1fr; }
      .band__in { padding: 2.5rem 0; }
      .band__in .btn { width: 100%; }
      .foot__in { grid-template-columns: 1fr; }
      .foot__bar { justify-content: flex-start; }
    }
  </style>
</head>
<body>

{{-- ══════════ NAV ══════════ --}}
<nav class="nav">
  <div class="wrap nav__in">
    <a href="{{ route('landing') }}" class="nav__brand" aria-label="Philippine Academy of Sakya — home">
      <img class="nav__crest" src="{{ asset('images/logo.png') }}" alt="Philippine Academy of Sakya crest">
      <span>
        <span class="nav__name">Philippine Academy of Sakya</span>
        <span class="nav__sub">Junior &amp; Senior High School</span>
      </span>
    </a>
    <div class="nav__sp"></div>
    <div class="nav__acts">
      <a href="{{ route('apply') }}" class="btn btn--quiet">Apply for Admission</a>
      <a href="{{ route('login') }}" class="btn btn--navy">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg>
        Sign In
      </a>
    </div>
  </div>
</nav>

{{-- ══════════ HERO ══════════ --}}
<header class="hero">
  <div class="wrap hero__in">
    <div>
      <span class="eyebrow hero__eyebrow">Official School Portal</span>

      <h1 class="serif">Philippine Academy of Sakya</h1>
      <div class="hero__tagline">EncryptEd · Academic Management System</div>

      <p class="hero__desc">
        The official secure academic portal of Philippine Academy of Sakya —
        Junior &amp; Senior High School. Admissions, enrollment, grades and
        records, in one protected system.
      </p>

      <div class="hero__cta">
        <a href="{{ route('apply') }}" class="btn btn--lg btn--gold">
          Apply Now
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
          </svg>
        </a>
        <a href="{{ route('login') }}" class="btn btn--lg btn--onDark">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
          </svg>
          Portal Login
        </a>
      </div>
    </div>

    <aside class="crest">
      <img src="{{ asset('images/logo.png') }}" alt="Philippine Academy of Sakya crest">
      <div class="crest__name">Philippine Academy of Sakya</div>
      <div class="crest__rule"></div>
      <div class="crest__meta">
        Grades 7 &ndash; 12<br>
        Junior &amp; Senior High School<br>
        Republic of the Philippines
      </div>
    </aside>
  </div>
</header>

{{-- ══════════ TRUST STRIP ══════════ --}}
<section class="trust" aria-label="Institutional standards">
  <div class="wrap trust__in">
    <div class="trust__item">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
      </svg>
      <span class="trust__label">Grades 7 &ndash; 12</span>
    </div>
    <div class="trust__item">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <span class="trust__label">DepEd-Aligned Curriculum</span>
    </div>
    <div class="trust__item">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
      </svg>
      <span class="trust__label">RA 10173 Compliant</span>
    </div>
  </div>
</section>

{{-- ══════════ WHO IT'S FOR ══════════ --}}
<section class="sec">
  <div class="wrap">
    <div class="sec__head">
      <span class="eyebrow">Who It's For</span>
      <h2 class="sec__title serif">One portal for the whole school community</h2>
      <p class="sec__sub">Every account sees exactly what its role permits — nothing more.</p>
    </div>

    <div class="grid grid--3">
      <article class="role">
        <div class="role__top">
          <div class="role__ico">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
            </svg>
          </div>
          <span class="role__num">01</span>
        </div>
        <h3 class="role__title">Students &amp; Parents</h3>
        <p class="role__text">Track grades, schedules, attendance and enrollment status, submit requirements, and receive report cards online.</p>
      </article>

      <article class="role">
        <div class="role__top">
          <div class="role__ico">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
            </svg>
          </div>
          <span class="role__num">02</span>
        </div>
        <h3 class="role__title">Faculty</h3>
        <p class="role__text">Encode and submit grades, manage class records, and monitor attendance with a verified, auditable workflow.</p>
      </article>

      <article class="role">
        <div class="role__top">
          <div class="role__ico">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
            </svg>
          </div>
          <span class="role__num">03</span>
        </div>
        <h3 class="role__title">Registrar &amp; Admin</h3>
        <p class="role__text">Process admissions and enrollment, manage sections and records, and oversee system security and audit trails.</p>
      </article>
    </div>
  </div>
</section>

      </svg>
    </a>
  </div>
</section>

{{-- ══════════ FOOTER ══════════ --}}
<footer class="foot">
  <div class="wrap">
    <div class="foot__in">
      <div class="foot__brand-col">
        <div class="foot__brand">
          <img src="{{ asset('images/logo.png') }}" alt="Philippine Academy of Sakya crest">
          <div>
            <div class="foot__name">Philippine Academy of Sakya</div>
            <div class="foot__sub">Junior &amp; Senior High School</div>
          </div>
        </div>
        <p class="foot__blurb">
          The official secure academic portal of Philippine Academy of Sakya,
          powered by EncryptEd.
        </p>
      </div>

      <div>
        <div class="foot__h">Portal</div>
        <div class="foot__links">
          <a href="{{ route('login') }}">Portal Login</a>
          <a href="{{ route('apply') }}">Apply for Admission</a>
        </div>
      </div>

      <div>
        <div class="foot__h">Admissions</div>
        <div class="foot__links">
          <a href="{{ route('apply') }}">Begin Application</a>
          <a href="{{ route('login') }}">Check Your Status</a>
        </div>
      </div>
    </div>

    <div class="foot__bar">
      <span>&copy; {{ date('Y') }} Philippine Academy of Sakya &middot; Powered by <strong>EncryptEd</strong></span>
      <span>Data processed in compliance with <strong>RA 10173</strong> (Data Privacy Act of 2012)</span>
    </div>
  </div>
</footer>

</body>
</html>
