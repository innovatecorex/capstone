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
      --navy:   #0a1a33;
      --navy-2: #12305c;
      --blue:   #1d4ed8;
      --gold:   #d4a12a;
      --gold-2: #f0c65a;
      --ink:    #0f172a;
      --body:   #55627a;
      --muted:  #8a95a8;
      --line:   #e6eaf1;
      --paper:  #ffffff;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
    body { font-family: 'Inter', sans-serif; color: var(--body); background: var(--paper); line-height: 1.6; }
    a { text-decoration: none; color: inherit; }
    img { max-width: 100%; display: block; }
    :focus-visible { outline: 2px solid var(--gold); outline-offset: 3px; border-radius: 4px; }
    .wrap { max-width: 1140px; margin: 0 auto; padding: 0 1.5rem; }

    /* ── buttons ─────────────────────────────── */
    .btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      font-family: inherit; font-weight: 700; cursor: pointer; white-space: nowrap;
      border: 1.5px solid transparent; border-radius: 10px;
      min-height: 46px; padding: 0 1.4rem; font-size: .9rem;
      transition: background-color .18s, color .18s, border-color .18s, box-shadow .18s, transform .15s;
    }
    .btn svg { width: 16px; height: 16px; }
    .btn--gold   { background: var(--gold); color: #241a04; }
    .btn--gold:hover { background: var(--gold-2); transform: translateY(-1px); box-shadow: 0 8px 22px rgba(212,161,42,.35); }
    .btn--glass  { background: rgba(255,255,255,.10); color: #fff; border-color: rgba(255,255,255,.45); backdrop-filter: blur(4px); }
    .btn--glass:hover { background: rgba(255,255,255,.20); transform: translateY(-1px); }
    .btn--navy   { background: var(--navy); color: #fff; }
    .btn--navy:hover { background: var(--navy-2); transform: translateY(-1px); box-shadow: 0 8px 22px rgba(10,26,51,.25); }
    .btn--ghost  { background: transparent; color: var(--ink); border-color: var(--line); }
    .btn--ghost:hover { border-color: #c7cfdc; background: #f5f7fb; }
    .btn--sm { min-height: 40px; padding: 0 1.05rem; font-size: .82rem; }

    /* ── top nav (over hero) ─────────────────── */
    .nav {
      position: absolute; top: 0; left: 0; right: 0; z-index: 10;
      display: flex; align-items: center; justify-content: flex-end; gap: .6rem;
      padding: 1.1rem 1.75rem;
    }

    /* ── HERO ────────────────────────────────── */
    .hero {
      position: relative;
      min-height: 82vh;
      display: flex; align-items: flex-end;
      color: #fff;
      /* scrim (top) for nav legibility + (bottom) for content, the banner
         image, then an on-brand gradient FALLBACK if the image isn't present. */
      background:
        linear-gradient(180deg, rgba(8,18,38,.45) 0%, rgba(8,18,38,0) 22%, rgba(8,18,38,0) 45%, rgba(8,18,38,.82) 100%),
        url('{{ asset('images/landing-hero.jpg') }}') center center / cover no-repeat,
        linear-gradient(135deg, #0a1a33 0%, #12305c 55%, #1d4ed8 100%);
    }
    .hero__inner { position: relative; z-index: 2; width: 100%; padding-bottom: 3.25rem; padding-top: 6rem; }
    .hero__eyebrow {
      display: inline-flex; align-items: center; gap: 7px;
      font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
      color: var(--gold-2);
      background: rgba(212,161,42,.12); border: 1px solid rgba(212,161,42,.4);
      padding: .32rem .8rem; border-radius: 999px; margin-bottom: 1.1rem;
    }
    .hero__eyebrow svg { width: 13px; height: 13px; }
    .hero h1 {
      font-family: 'Merriweather', Georgia, serif;
      font-size: clamp(1.9rem, 4vw, 3rem); font-weight: 900;
      letter-spacing: -.02em; line-height: 1.15; color: #fff;
      max-width: 16ch; text-shadow: 0 2px 20px rgba(0,0,0,.35);
    }
    .hero p {
      font-size: clamp(.98rem, 1.5vw, 1.12rem); line-height: 1.7;
      color: rgba(255,255,255,.9); max-width: 54ch; margin: .9rem 0 1.8rem;
      text-shadow: 0 1px 12px rgba(0,0,0,.4);
    }
    .hero__cta { display: flex; gap: .8rem; flex-wrap: wrap; }

    /* thin gold rule at the very bottom of the hero */
    .hero::after {
      content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 4px; z-index: 3;
      background: linear-gradient(90deg, var(--gold) 0%, var(--gold-2) 50%, var(--gold) 100%);
    }

    /* ── VALUE STRIP ─────────────────────────── */
    .values { background: #f8fafc; border-bottom: 1px solid var(--line); }
    .values__grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; padding: 2.75rem 0; }
    .value { display: flex; gap: .9rem; align-items: flex-start; }
    .value__ic {
      width: 42px; height: 42px; flex-shrink: 0; border-radius: 11px;
      background: #eef3fe; border: 1px solid #dce6fb;
      display: flex; align-items: center; justify-content: center;
    }
    .value__ic svg { width: 20px; height: 20px; color: var(--blue); }
    .value__t { font-size: .95rem; font-weight: 800; color: var(--ink); margin-bottom: .2rem; letter-spacing: -.01em; }
    .value__d { font-size: .85rem; color: var(--body); line-height: 1.6; }

    /* ── ROLES (kept short) ──────────────────── */
    .section { padding: 4rem 0; }
    .section__head { text-align: center; max-width: 620px; margin: 0 auto 2.25rem; }
    .eyebrow { font-size: .68rem; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
    .section__title { font-family: 'Merriweather', Georgia, serif; font-size: clamp(1.4rem, 2.4vw, 1.85rem); font-weight: 900; color: var(--ink); letter-spacing: -.02em; margin: .5rem 0 .5rem; }
    .section__sub { font-size: .95rem; color: var(--body); }
    .roles { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.1rem; }
    .role { border: 1px solid var(--line); border-radius: 14px; padding: 1.5rem 1.35rem; background: #fff; transition: border-color .2s, box-shadow .2s, transform .2s; }
    .role:hover { border-color: #cdd7e6; box-shadow: 0 12px 30px rgba(15,23,42,.07); transform: translateY(-2px); }
    .role__ic { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: .9rem; }
    .role__ic svg { width: 19px; height: 19px; }
    .role__t { font-size: 1rem; font-weight: 800; color: var(--ink); margin-bottom: .35rem; }
    .role__d { font-size: .86rem; color: var(--body); line-height: 1.65; }

    /* ── ADMISSION BAND ──────────────────────── */
    .band { position: relative; overflow: hidden; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 60%, var(--blue) 100%); color: #fff; border-top: 3px solid var(--gold); }
    .band::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px); background-size: 24px 24px; }
    .band__in { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap; padding: 3rem 0; }
    .band__pill { display: inline-flex; align-items: center; gap: 7px; background: rgba(212,161,42,.16); border: 1px solid rgba(212,161,42,.4); color: var(--gold-2); font-size: .66rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; padding: .32rem .8rem; border-radius: 999px; margin-bottom: .8rem; }
    .band__pill svg { width: 12px; height: 12px; }
    .band h2 { font-family: 'Merriweather', Georgia, serif; font-size: clamp(1.35rem, 2.2vw, 1.75rem); font-weight: 900; letter-spacing: -.02em; margin-bottom: .4rem; color: #fff; }
    .band p { font-size: .95rem; color: rgba(255,255,255,.75); max-width: 44ch; }

    /* ── FOOTER ──────────────────────────────── */
    .foot { background: var(--navy); color: rgba(255,255,255,.62); }
    .foot__in { display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap; padding: 2rem 0; }
    .foot__brand { display: flex; align-items: center; gap: .8rem; }
    .foot__brand img { width: 42px; height: 42px; border-radius: 50%; border: 1px solid rgba(255,255,255,.18); padding: 2px; }
    .foot__name { font-size: .92rem; font-weight: 800; color: #fff; letter-spacing: -.01em; }
    .foot__sub { font-size: .68rem; color: rgba(255,255,255,.45); margin-top: 2px; }
    .foot__links { display: flex; gap: 1.4rem; }
    .foot__links a { font-size: .85rem; font-weight: 600; color: rgba(255,255,255,.72); transition: color .15s; }
    .foot__links a:hover { color: #fff; }
    .foot__bar { border-top: 1px solid rgba(255,255,255,.09); padding: 1.1rem 0 1.75rem; text-align: center; font-size: .74rem; color: rgba(255,255,255,.42); line-height: 1.7; }
    .foot__bar strong { color: rgba(255,255,255,.6); font-weight: 700; }

    /* ── responsive ──────────────────────────── */
    @media (max-width: 860px) {
      .values__grid { grid-template-columns: 1fr; gap: 1.5rem; }
      .roles { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
      .nav { padding: .85rem 1rem; }
      .hero { min-height: 88vh; }
      .hero__inner { padding-bottom: 2.5rem; }
      .hero__cta .btn { flex: 1 1 100%; }
      .band__in .btn { width: 100%; }
      .foot__in { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>

{{-- ══════ HERO (school banner) ══════ --}}
<header class="hero">
  {{-- floating top-right auth — the banner already carries the school identity --}}
  <nav class="nav">
    <a href="{{ route('apply') }}" class="btn btn--glass btn--sm">Apply for Admission</a>
    <a href="{{ route('login') }}" class="btn btn--gold btn--sm">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
      </svg>
      Sign In
    </a>
  </nav>

  <div class="wrap hero__inner">
    <span class="hero__eyebrow">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
      Official Secure Academic Portal
    </span>

    <h1>Secure learning, from admission to report card.</h1>
    <p>
      The official academic management portal of Philippine Academy of Sakya —
      Junior &amp; Senior High School. Apply for admission, enroll, and access
      grades in one protected system.
    </p>

    <div class="hero__cta">
      <a href="{{ route('apply') }}" class="btn btn--gold">
        Apply Now
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
        </svg>
      </a>
      <a href="{{ route('login') }}" class="btn btn--glass">Portal Login</a>
    </div>
  </div>
</header>

{{-- ══════ VALUE STRIP ══════ --}}
<section class="values" aria-label="Why EncryptEd">
  <div class="wrap values__grid">
    <div class="value">
      <div class="value__ic">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
        </svg>
      </div>
      <div>
        <div class="value__t">Data Encryption</div>
        <div class="value__d">Personal records are AES-256 encrypted at rest, in line with RA 10173 (Data Privacy Act).</div>
      </div>
    </div>
    <div class="value">
      <div class="value__ic">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
        </svg>
      </div>
      <div>
        <div class="value__t">Role-Based Access</div>
        <div class="value__d">Students, faculty, registrar and admin each see only the records their role permits.</div>
      </div>
    </div>
    <div class="value">
      <div class="value__ic">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
        </svg>
      </div>
      <div>
        <div class="value__t">Digital Enrollment &amp; Grades</div>
        <div class="value__d">Apply, enroll, pay and receive report cards online — no paper forms, no queues.</div>
      </div>
    </div>
  </div>
</section>

{{-- ══════ ROLES ══════ --}}
<section class="section">
  <div class="wrap">
    <div class="section__head">
      <span class="eyebrow">Who It's For</span>
      <h2 class="section__title">One portal for the whole school</h2>
      <p class="section__sub">Secure, role-based access for every member of the community.</p>
    </div>
    <div class="roles">
      <article class="role">
        <div class="role__ic" style="background:#fffbeb;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
          </svg>
        </div>
        <div class="role__t">Students &amp; Parents</div>
        <div class="role__d">Track grades, schedules, attendance and enrollment, and receive report cards online.</div>
      </article>
      <article class="role">
        <div class="role__ic" style="background:#ecfdf5;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
          </svg>
        </div>
        <div class="role__t">Faculty</div>
        <div class="role__d">Encode and submit grades, manage class records, and track attendance with a verified workflow.</div>
      </article>
      <article class="role">
        <div class="role__ic" style="background:#eff6ff;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#1d4ed8" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z"/>
          </svg>
        </div>
        <div class="role__t">Registrar &amp; Admin</div>
        <div class="role__d">Process admissions and enrollment, manage sections and records, and oversee system security.</div>
      </article>
    </div>
  </div>
</section>

{{-- ══════ ADMISSION BAND ══════ --}}
<section class="band">
  <div class="wrap band__in">
    <div>
      <span class="band__pill">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Admissions Open
      </span>
      <h2>Now enrolling for SY 2025&ndash;2026</h2>
      <p>Admission is open for Junior &amp; Senior High School. Begin your application online in minutes.</p>
    </div>
    <a href="{{ route('apply') }}" class="btn btn--gold">
      Begin Application
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
      </svg>
    </a>
  </div>
</section>

{{-- ══════ FOOTER ══════ --}}
<footer class="foot">
  <div class="wrap">
    <div class="foot__in">
      <a href="{{ route('landing') }}" class="foot__brand">
        <img src="{{ asset('images/logo.png') }}" alt="Philippine Academy of Sakya crest">
        <span>
          <span class="foot__name">Philippine Academy of Sakya</span>
          <span class="foot__sub" style="display:block;">Junior &amp; Senior High School · PAASCU Level III</span>
        </span>
      </a>
      <div class="foot__links">
        <a href="{{ route('login') }}">Portal Login</a>
        <a href="{{ route('apply') }}">Apply for Admission</a>
      </div>
    </div>
    <div class="foot__bar">
      &copy; {{ date('Y') }} Philippine Academy of Sakya &nbsp;·&nbsp; Powered by <strong>EncryptEd</strong><br>
      Personal data is processed in compliance with <strong>RA 10173</strong> (Data Privacy Act of 2012).
    </div>
  </div>
</footer>

</body>
</html>
