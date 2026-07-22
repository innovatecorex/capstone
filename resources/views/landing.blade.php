{{-- resources/views/landing.blade.php — public homepage --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  {{-- Mark JS available before paint so motion styles apply without a flash of
       hidden content — and so no-JS visitors always see everything. --}}
  <script>document.documentElement.className += ' js';</script>
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

    /* ── premium global polish ─────────────────── */
    ::selection { background: rgba(212,161,42,.28); color: var(--ink); }
    html { scrollbar-color: var(--gold) #e9edf3; scrollbar-width: thin; }
    ::-webkit-scrollbar { width: 11px; height: 11px; }
    ::-webkit-scrollbar-track { background: #eef2f7; }
    ::-webkit-scrollbar-thumb { background: linear-gradient(var(--gold), #b8891f); border-radius: 999px; border: 2px solid #eef2f7; }
    ::-webkit-scrollbar-thumb:hover { background: linear-gradient(var(--gold-2), var(--gold)); }
    /* scroll-progress meter — a thin gold line that fills as the page is read */
    .progress { position: fixed; top: 0; left: 0; height: 3px; width: 0; z-index: 60;
      background: linear-gradient(90deg, var(--gold), var(--gold-2));
      box-shadow: 0 0 10px rgba(212,161,42,.6); transition: width .1s linear; }

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

    /* ── TOP BAR (school identity first) ─────── */
    /* A defined, sticky bar with a gold hairline — so it reads as a deliberate
       nav grounded to the top, not a slab of navy bleeding into the hero. */
    .topbar {
      position: sticky; top: 0; z-index: 50;
      background: rgba(10,26,51,.92);
      backdrop-filter: saturate(140%) blur(8px);
      -webkit-backdrop-filter: saturate(140%) blur(8px);
      border-bottom: 1px solid rgba(212,161,42,.32);
      box-shadow: 0 8px 24px rgba(3,9,22,.28);
    }
    /* Full-bleed: brand hugs the left edge, nav hugs the right edge — not
       penned inside the centered content column. */
    .topbar__in { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .7rem clamp(1rem, 3.5vw, 2.75rem); }
    .brand { display: flex; align-items: center; gap: .75rem; transition: opacity .15s; }
    .brand:hover { opacity: .9; }
    .brand img {
      width: 48px; height: 48px; border-radius: 50%; flex: none;
      box-shadow: 0 0 0 1px rgba(255,255,255,.14), 0 2px 8px rgba(0,0,0,.3);
    }
    .brand__name { font-family: 'Merriweather', Georgia, serif; font-weight: 800; font-size: 1.06rem; color: #fff; line-height: 1.1; letter-spacing: -.01em; }
    .brand__sub { display: block; font-size: .63rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: rgba(240,198,90,.82); margin-top: 4px; }
    .topbar__nav { display: flex; gap: .6rem; flex: none; }

    /* ── HERO — the FULL banner, never cropped ── */
    /* The hero is locked to the banner's own aspect ratio (1717×916), so the
       ENTIRE image shows edge-to-edge — no cropping, no letterbox. */
    .hero {
      position: relative;
      width: 100%;
      aspect-ratio: 1717 / 916;
      min-height: 460px;
      display: flex; align-items: center;
      color: #fff;
      overflow: hidden;
      border-bottom: 4px solid var(--gold);
      background-color: #0a1a33;
    }
    .hero__bg {
      position: absolute; inset: 0; z-index: 0;
      background-color: #0a1a33;
      background-position: center;
      background-size: contain;            /* contain = the whole image is always shown */
      background-repeat: no-repeat;
      background-image:
        url('{{ asset('images/landing-hero.jpg') }}'),
        linear-gradient(135deg, #0a1a33 0%, #12305c 55%, #1d4ed8 100%);
      background-image:
        image-set(
          url('{{ asset('images/landing-hero.webp') }}') type('image/webp'),
          url('{{ asset('images/landing-hero.jpg') }}') type('image/jpeg')
        ),
        linear-gradient(135deg, #0a1a33 0%, #12305c 55%, #1d4ed8 100%);
      animation: heroFade 1.2s ease both;  /* gentle reveal — no zoom, so nothing is ever cropped */
    }
    @keyframes heroFade { from { opacity: 0; } to { opacity: 1; } }
    /* navy scrim on the left so white hero text stays crisp, fading to reveal
       the artwork (building, medallions) on the right */
    .hero::before {
      content: ''; position: absolute; inset: 0; z-index: 1;
      background: linear-gradient(90deg,
        rgba(6,16,38,.90) 0%, rgba(6,16,38,.74) 30%,
        rgba(6,16,38,.34) 56%, rgba(6,16,38,0) 82%);
    }
    .hero__inner { position: relative; z-index: 2; width: 100%; max-width: 660px; padding: 4.5rem 0; }
    .hero__eyebrow {
      display: inline-flex; align-items: center; gap: 7px;
      font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
      color: var(--gold-2);
      background: rgba(212,161,42,.14); border: 1px solid rgba(212,161,42,.45);
      padding: .34rem .85rem; border-radius: 999px; margin-bottom: 1.15rem;
    }
    .hero__eyebrow svg { width: 13px; height: 13px; }
    .hero h1 {
      font-family: 'Merriweather', Georgia, serif;
      font-size: clamp(2.2rem, 5vw, 3.55rem); font-weight: 900;
      letter-spacing: -.02em; line-height: 1.1; color: #fff;
      max-width: 16ch; text-shadow: 0 2px 24px rgba(0,0,0,.5);
    }
    .hero__tag {
      font-size: clamp(.92rem, 1.6vw, 1.12rem); font-weight: 700;
      letter-spacing: .01em; color: var(--gold-2);
      margin-top: .75rem; text-shadow: 0 1px 12px rgba(0,0,0,.5);
    }
    .hero p {
      font-size: clamp(.98rem, 1.5vw, 1.12rem); line-height: 1.7;
      color: rgba(255,255,255,.9); max-width: 50ch; margin: .9rem 0 1.9rem;
      text-shadow: 0 1px 14px rgba(0,0,0,.55);
    }
    .hero__cta { display: flex; gap: .8rem; flex-wrap: wrap; }
    /* animated scroll cue at the foot of the hero */
    .hero__scroll {
      position: absolute; left: 50%; bottom: 20px; transform: translateX(-50%); z-index: 3;
      width: 28px; height: 46px; border: 2px solid rgba(255,255,255,.6); border-radius: 15px;
      display: flex; justify-content: center; padding-top: 7px;
      box-shadow: 0 2px 14px rgba(0,0,0,.35); transition: border-color .2s;
    }
    .hero__scroll:hover { border-color: var(--gold-2); }
    .hero__scroll span { width: 4px; height: 8px; border-radius: 2px; background: var(--gold-2);
      animation: scrollDot 1.7s ease-in-out infinite; }
    @keyframes scrollDot {
      0% { opacity: 0; transform: translateY(-3px); }
      30% { opacity: 1; }
      70% { opacity: 1; transform: translateY(11px); }
      100% { opacity: 0; transform: translateY(15px); }
    }

    /* ── FACTS / ACCREDITATION STRIP ─────────── */
    .facts { background: linear-gradient(180deg, #f8fafc, #f2f5fa); border-bottom: 1px solid var(--line); }
    .facts__grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; padding: 2.75rem 0; }
    .fact { text-align: center; padding: .35rem 1rem; }
    .fact + .fact { border-left: 1px solid var(--line); }
    .fact__n { position: relative; font-family: 'Merriweather', Georgia, serif; font-size: clamp(1.55rem, 2.4vw, 2rem); font-weight: 900; color: var(--navy); letter-spacing: -.02em; line-height: 1.05; padding-bottom: .6rem; }
    .fact__n::after { content: ''; position: absolute; left: 50%; bottom: 0; transform: translateX(-50%);
      width: 28px; height: 2px; border-radius: 2px; background: linear-gradient(90deg, transparent, var(--gold), transparent); }
    .fact__l { font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); margin-top: .5rem; }

    /* ── ROLES (kept short) ──────────────────── */
    .section { padding: 4.5rem 0; }
    .section__head { text-align: center; max-width: 640px; margin: 0 auto 2.5rem; }
    .eyebrow { font-size: .68rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--gold); }
    /* gold flanking ticks on centered eyebrows — an institutional signature */
    .section__head .eyebrow { display: inline-flex; align-items: center; gap: 11px; }
    .section__head .eyebrow::before, .section__head .eyebrow::after { content: ''; width: 22px; height: 1.5px; background: var(--gold); opacity: .55; }
    .section__title { font-family: 'Merriweather', Georgia, serif; font-size: clamp(1.5rem, 2.6vw, 2rem); font-weight: 900; color: var(--ink); letter-spacing: -.02em; margin: .55rem 0 .5rem; }
    .section__sub { font-size: .96rem; color: var(--body); }
    /* centered gold flourish closing the header */
    .section__head::after { content: ''; display: block; width: 64px; height: 3px; margin: 1.15rem auto 0;
      border-radius: 2px; background: linear-gradient(90deg, transparent, var(--gold), transparent); }
    .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.1rem; }
    .step { position: relative; overflow: hidden; border: 1px solid var(--line); border-radius: 14px; padding: 1.85rem 1.5rem; background: #fff; transition: border-color .2s, box-shadow .2s, transform .2s; }
    .step::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
      background: linear-gradient(90deg, var(--gold), var(--gold-2)); transform: scaleX(0); transform-origin: left;
      transition: transform .38s cubic-bezier(.22,.61,.36,1); }
    .step:hover::before { transform: scaleX(1); }
    .step:hover { border-color: #cdd7e6; box-shadow: 0 16px 36px rgba(15,23,42,.09); transform: translateY(-3px); }
    .step__n { width: 40px; height: 40px; border-radius: 10px; background: var(--navy); color: var(--gold-2); font-family: 'Merriweather', Georgia, serif; font-weight: 900; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
    .step__t { font-size: 1rem; font-weight: 800; color: var(--ink); margin-bottom: .35rem; }
    .step__d { font-size: .86rem; color: var(--body); line-height: 1.65; }

    /* ── SHOWCASE GALLERY ────────────────────── */
    .gallery { background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%); }
    .gal-pano { position: relative; border-radius: 18px; overflow: hidden; margin: 0 0 1.15rem;
      box-shadow: 0 16px 44px rgba(15,23,42,.14); cursor: zoom-in; border: 1px solid var(--line); }
    .gal-pano img { width: 100%; height: auto; display: block; transition: transform .6s cubic-bezier(.22,.61,.36,1); }
    .gal-pano:hover img { transform: scale(1.03); }
    .gal-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.1rem; }
    .gal-item { position: relative; border-radius: 14px; overflow: hidden; cursor: zoom-in;
      box-shadow: 0 10px 26px rgba(15,23,42,.10); background: var(--navy); }
    .gal-item img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; display: block;
      transition: transform .55s cubic-bezier(.22,.61,.36,1); }
    .gal-item:hover img { transform: scale(1.07); }
    .gal-item::after { content: ''; position: absolute; inset: 0; pointer-events: none;
      background: linear-gradient(180deg, transparent 55%, rgba(6,16,38,.5)); opacity: 0; transition: opacity .3s; }
    .gal-item:hover::after { opacity: 1; }
    .gal-zoom { position: absolute; right: 11px; bottom: 11px; z-index: 2; width: 34px; height: 34px;
      border-radius: 9px; background: rgba(212,161,42,.96); color: #241a04;
      display: flex; align-items: center; justify-content: center;
      opacity: 0; transform: translateY(6px); transition: opacity .3s, transform .3s; }
    .gal-item:hover .gal-zoom { opacity: 1; transform: none; }
    .gal-zoom svg { width: 17px; height: 17px; }
    /* row-based reveal stagger */
    .js .gal-grid .gal-item:nth-child(2) { transition-delay: .08s; }
    .js .gal-grid .gal-item:nth-child(3) { transition-delay: .16s; }
    .js .gal-grid .gal-item:nth-child(5) { transition-delay: .08s; }
    .js .gal-grid .gal-item:nth-child(6) { transition-delay: .16s; }

    /* Lightbox */
    .lb { position: fixed; inset: 0; z-index: 999; display: flex; align-items: center; justify-content: center;
      padding: clamp(1rem, 4vw, 3rem); background: rgba(4,9,20,.93);
      -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);
      opacity: 0; visibility: hidden; transition: opacity .3s ease, visibility .3s ease; }
    .lb.is-open { opacity: 1; visibility: visible; }
    .lb__img { max-width: 100%; max-height: 90vh; border-radius: 10px; box-shadow: 0 30px 80px rgba(0,0,0,.6); }
    .lb__btn { position: absolute; background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.28);
      color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background .15s; }
    .lb__btn:hover { background: rgba(255,255,255,.22); }
    .lb__close { top: 18px; right: 20px; width: 44px; height: 44px; border-radius: 10px; }
    .lb__nav { top: 50%; transform: translateY(-50%); width: 48px; height: 48px; border-radius: 50%; }
    .lb__prev { left: 18px; } .lb__next { right: 18px; }
    .lb__btn svg { width: 21px; height: 21px; }
    .lb__count { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%);
      color: rgba(255,255,255,.82); font-size: .8rem; font-weight: 600; letter-spacing: .06em; }

    /* ── ADMISSION BAND ──────────────────────── */
    .band { position: relative; overflow: hidden; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 60%, var(--blue) 100%); color: #fff; border-top: 3px solid var(--gold); }
    .band::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px); background-size: 24px 24px; }
    /* soft gold glow for depth */
    .band::after { content: ''; position: absolute; right: -6%; top: -55%; width: 460px; height: 460px; border-radius: 50%;
      background: radial-gradient(circle, rgba(212,161,42,.20), transparent 62%); pointer-events: none; z-index: 0; }
    .band__in { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap; padding: 3rem 0; }
    .band__pill { display: inline-flex; align-items: center; gap: 7px; background: rgba(212,161,42,.16); border: 1px solid rgba(212,161,42,.4); color: var(--gold-2); font-size: .66rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; padding: .32rem .8rem; border-radius: 999px; margin-bottom: .8rem; }
    .band__pill svg { width: 12px; height: 12px; }
    .band h2 { font-family: 'Merriweather', Georgia, serif; font-size: clamp(1.35rem, 2.2vw, 1.75rem); font-weight: 900; letter-spacing: -.02em; margin-bottom: .4rem; color: #fff; }
    .band p { font-size: .95rem; color: rgba(255,255,255,.75); max-width: 44ch; }

    /* ── FOOTER ──────────────────────────────── */
    .foot { position: relative; background: var(--navy); color: rgba(255,255,255,.62); }
    .foot::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
      background: linear-gradient(90deg, var(--gold), var(--gold-2), var(--gold)); }
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

    /* ══════ MOTION — orchestrated, professional, GPU-cheap ══════ */

    /* 1 · Hero entrance — a staggered, blur-to-focus rise on page load. Scoped
          to .js so no-JS visitors just see the content, no flash. */
    .js .hero__inner > * { opacity: 0; animation: heroRise .9s cubic-bezier(.22,.61,.36,1) both; }
    .js .hero__inner > *:nth-child(1) { animation-delay: .20s; }
    .js .hero__inner > *:nth-child(2) { animation-delay: .34s; }
    .js .hero__inner > *:nth-child(3) { animation-delay: .46s; }
    .js .hero__inner > *:nth-child(4) { animation-delay: .56s; }
    .js .hero__inner > *:nth-child(5) { animation-delay: .66s; }
    @keyframes heroRise {
      from { opacity: 0; transform: translateY(30px); filter: blur(7px); }
      to   { opacity: 1; transform: none;             filter: blur(0); }
    }

    /* 2 · Scroll reveals — sections rise into place as they enter the viewport. */
    .js .reveal { opacity: 0; transform: translateY(26px);
      transition: opacity .75s cubic-bezier(.22,.61,.36,1), transform .75s cubic-bezier(.22,.61,.36,1); }
    .js .reveal.is-in { opacity: 1; transform: none; }
    /* staggered children within a row */
    .js .facts__grid .fact:nth-child(2) { transition-delay: .09s; }
    .js .facts__grid .fact:nth-child(3) { transition-delay: .18s; }
    .js .facts__grid .fact:nth-child(4) { transition-delay: .27s; }
    .js .steps .step:nth-child(2) { transition-delay: .12s; }
    .js .steps .step:nth-child(3) { transition-delay: .24s; }

    /* 3 · Nav gains weight once you leave the hero. */
    .topbar { transition: background .3s ease, box-shadow .3s ease, border-color .3s ease; }
    .topbar--scrolled {
      background: rgba(7,14,29,.97);
      box-shadow: 0 12px 34px rgba(3,9,22,.45);
      border-bottom-color: rgba(212,161,42,.5);
    }

    /* 4 · Gold buttons get a single sweep of light on hover. */
    .btn--gold { position: relative; overflow: hidden; }
    .btn--gold::before {
      content: ''; position: absolute; top: 0; left: -140%;
      width: 55%; height: 100%; pointer-events: none;
      background: linear-gradient(100deg, transparent, rgba(255,255,255,.55), transparent);
      transform: skewX(-20deg);
      transition: left .65s cubic-bezier(.22,.61,.36,1);
    }
    .btn--gold:hover::before { left: 150%; }

    /* 5 · Step number badge flips to gold on card hover (springy). */
    .step__n { transition: transform .28s cubic-bezier(.34,1.56,.64,1), background .25s, color .25s; }
    .step:hover .step__n { transform: rotate(-8deg) scale(1.08); background: var(--gold); color: var(--navy); }

    /* 6 · The founding-year odometer sits in a fixed box so the roll doesn't reflow. */
    .fact__n[data-count] { font-variant-numeric: tabular-nums; }

    /* Respect the user's motion preference — everything resolves to a calm,
       static page; content is always visible. */
    @media (prefers-reduced-motion: reduce) {
      .js .reveal, .js .hero__inner > * { opacity: 1 !important; transform: none !important; filter: none !important; animation: none !important; }
      .hero__bg { animation: none !important; opacity: 1 !important; }
      *, *::before, *::after { animation-duration: .001ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
    }

    /* ── responsive ──────────────────────────── */
    @media (max-width: 860px) {
      .facts__grid { grid-template-columns: repeat(2, 1fr); gap: 1.5rem 0; }
      .fact + .fact { border-left: none; }
      .steps { grid-template-columns: 1fr; }
      /* On narrow screens, stack: the FULL banner on top, hero text below it on
         navy — still shown whole, never cropped, and text stays readable. */
      .hero { aspect-ratio: auto; min-height: 0; display: block; background: var(--navy); }
      .hero::before { display: none; }
      .hero__bg {
        position: relative; inset: auto; width: 100%;
        aspect-ratio: 1717 / 916; background-size: cover; background-position: center;
      }
      .hero__inner { position: relative; z-index: 2; max-width: 100%; padding: 1.75rem 0 2.25rem; }
      .hero__scroll { display: none; }
      .gal-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
      .gal-grid { grid-template-columns: 1fr; }
      .lb__nav { width: 40px; height: 40px; }
      .topbar__in { flex-direction: column; align-items: center; gap: .8rem; text-align: center; }
      .brand { flex-direction: column; text-align: center; gap: .45rem; }
      .topbar__nav { width: 100%; }
      .topbar__nav .btn { flex: 1 1 0; }
      .hero__cta .btn { flex: 1 1 100%; }
      .band__in .btn { width: 100%; }
      .foot__in { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>

<div class="progress" id="progress" aria-hidden="true"></div>

{{-- ══════ TOP BAR — school name seen first ══════ --}}
<header class="topbar">
  <div class="topbar__in">
    <a href="{{ route('landing') }}" class="brand">
      <img src="{{ asset('images/logo.png') }}" alt="Philippine Academy of Sakya crest">
      <span>
        <span class="brand__name">Philippine Academy of Sakya</span>
        <span class="brand__sub">Junior &amp; Senior High &middot; PAASCU Level III</span>
      </span>
    </a>
    <nav class="topbar__nav">
      <a href="{{ route('apply') }}" class="btn btn--glass btn--sm">Apply for Admission</a>
      <a href="{{ route('login') }}" class="btn btn--gold btn--sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg>
        Sign In
      </a>
    </nav>
  </div>
</header>

{{-- ══════ HERO — uploaded image as background ══════ --}}
<section class="hero" aria-label="Welcome">
  <div class="hero__bg" aria-hidden="true"></div>
  <div class="wrap hero__inner">
    <span class="hero__eyebrow">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
      </svg>
      Official Secure Academic Portal
    </span>

    <h1>Philippine Academy of Sakya</h1>
    <div class="hero__tag">Junior &amp; Senior High School &middot; PAASCU Accredited Level III</div>
    <p>
      Apply for admission, enroll, and access grades and report cards —
      all in one secure academic portal.
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
  <a href="#facts" class="hero__scroll" aria-label="Scroll down to explore"><span></span></a>
</section>

{{-- ══════ ACCREDITATION / KEY FACTS ══════ --}}
<section class="facts" id="facts" aria-label="School at a glance">
  <div class="wrap facts__grid">
    <div class="fact reveal">
      <div class="fact__n" data-count="1960" data-count-from="1900">1960</div>
      <div class="fact__l">Established</div>
    </div>
    <div class="fact reveal">
      <div class="fact__n">Level III</div>
      <div class="fact__l">PAASCU Accredited</div>
    </div>
    <div class="fact reveal">
      <div class="fact__n">Grades 7&ndash;12</div>
      <div class="fact__l">Junior &amp; Senior High</div>
    </div>
    <div class="fact reveal">
      <div class="fact__n">K to 12</div>
      <div class="fact__l">DepEd Curriculum</div>
    </div>
  </div>
</section>

{{-- ══════ HOW TO APPLY ══════ --}}
<section class="section">
  <div class="wrap">
    <div class="section__head reveal">
      <span class="eyebrow">Admissions</span>
      <h2 class="section__title">How to apply</h2>
      <p class="section__sub">Three simple steps, all online — start your application without visiting the campus.</p>
    </div>
    <div class="steps">
      <article class="step reveal">
        <div class="step__n">1</div>
        <div class="step__t">Apply Online</div>
        <div class="step__d">Complete the online admission form. You&rsquo;ll receive a reference number by email as your proof of submission.</div>
      </article>
      <article class="step reveal">
        <div class="step__n">2</div>
        <div class="step__t">Submit Requirements</div>
        <div class="step__d">Upload the required documents — report card, birth certificate, and the rest — for the registrar to review.</div>
      </article>
      <article class="step reveal">
        <div class="step__n">3</div>
        <div class="step__t">Get Notified</div>
        <div class="step__d">The registrar reviews your application and emails you the decision, along with the next steps for enrollment.</div>
      </article>
    </div>
  </div>
</section>

{{-- ══════ SHOWCASE — LIFE AT SAKYA ══════ --}}
@php
  $gallery = [
    ['g1', 'Sakya students at a school competition'],
    ['g2', 'Sakya awarding ceremony'],
    ['g3', 'Sakya community and cultural event'],
    ['g4', "Sakya pupils on Kids' Athletics Day"],
    ['g5', 'Sakya student athletes with their medals'],
    ['g6', 'Philippine Academy of Sakya alumni giving back'],
  ];
@endphp
<section class="section gallery" aria-label="Life at Sakya">
  <div class="wrap">
    <div class="section__head reveal">
      <span class="eyebrow">Campus Life</span>
      <h2 class="section__title">Life at Sakya</h2>
      <p class="section__sub">A glimpse of student life beyond the classroom — competitions, ceremonies, athletics, and community.</p>
    </div>

    <figure class="gal-pano reveal gal-open" tabindex="0" role="button"
            data-full="{{ asset('images/gallery/pano.webp') }}" data-alt="A school assembly at Philippine Academy of Sakya">
      <picture>
        <source srcset="{{ asset('images/gallery/pano.webp') }}" type="image/webp">
        <img src="{{ asset('images/gallery/pano.jpg') }}" loading="lazy" width="1920" height="680"
             alt="A school assembly at Philippine Academy of Sakya">
      </picture>
    </figure>

    <div class="gal-grid">
      @foreach($gallery as [$img, $alt])
      <figure class="gal-item reveal gal-open" tabindex="0" role="button"
              data-full="{{ asset('images/gallery/'.$img.'.webp') }}" data-alt="{{ $alt }}">
        <picture>
          <source srcset="{{ asset('images/gallery/'.$img.'.webp') }}" type="image/webp">
          <img src="{{ asset('images/gallery/'.$img.'.jpg') }}" loading="lazy" width="1200" height="900" alt="{{ $alt }}">
        </picture>
        <span class="gal-zoom" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.35-5.4a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0zM10.5 7.5v6m3-3h-6"/>
          </svg>
        </span>
      </figure>
      @endforeach
    </div>
  </div>
</section>

{{-- ══════ ADMISSION BAND ══════ --}}
<section class="band">
  <div class="wrap band__in reveal">
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
  </div>
</footer>

{{-- ══════ MOTION ENGINE — dependency-free, ~40 lines ══════ --}}
<script>
(function () {
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // 1 · Scroll reveals
  var reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
      });
    }, { threshold: 0.16, rootMargin: '0px 0px -8% 0px' });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('is-in'); });
  }

  // 2 · Nav gains weight after the hero + scroll-progress meter
  var topbar = document.querySelector('.topbar');
  var progress = document.getElementById('progress');
  function onScroll() {
    if (topbar) topbar.classList.toggle('topbar--scrolled', window.scrollY > 40);
    if (progress) {
      var max = document.documentElement.scrollHeight - window.innerHeight;
      progress.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
    }
  }
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll, { passive: true });

  // 3 · Count-up (founding year odometer)
  function countUp(el) {
    var to = parseInt(el.getAttribute('data-count'), 10);
    var from = parseInt(el.getAttribute('data-count-from') || '0', 10);
    if (reduce || isNaN(to)) { el.textContent = to; return; }
    var dur = 1500, start = null;
    function tick(now) {
      if (start === null) start = now;
      var p = Math.min((now - start) / dur, 1);
      var eased = 1 - Math.pow(1 - p, 3); // easeOutCubic
      el.textContent = Math.round(from + (to - from) * eased);
      if (p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }
  var counters = document.querySelectorAll('[data-count]');
  if ('IntersectionObserver' in window && !reduce) {
    // Pre-set to the start value so the roll never flashes the final number.
    counters.forEach(function (el) {
      var from = parseInt(el.getAttribute('data-count-from') || '0', 10);
      if (!isNaN(from)) el.textContent = from;
    });
    var cio = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { if (e.isIntersecting) { countUp(e.target); cio.unobserve(e.target); } });
    }, { threshold: 0.55 });
    counters.forEach(function (el) { cio.observe(el); });
  } else {
    counters.forEach(function (el) { el.textContent = el.getAttribute('data-count'); });
  }
})();
</script>

{{-- ══════ LIGHTBOX (click any photo to enlarge) ══════ --}}
<div class="lb" id="lightbox" aria-hidden="true" aria-modal="true" role="dialog" aria-label="Photo viewer">
  <button class="lb__btn lb__close" type="button" aria-label="Close">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
  </button>
  <button class="lb__btn lb__nav lb__prev" type="button" aria-label="Previous photo">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
  </button>
  <img class="lb__img" id="lbImg" src="" alt="">
  <button class="lb__btn lb__nav lb__next" type="button" aria-label="Next photo">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
  </button>
  <div class="lb__count" id="lbCount"></div>
</div>
<script>
(function () {
  var items = Array.prototype.slice.call(document.querySelectorAll('.gal-open'));
  if (!items.length) return;
  var lb = document.getElementById('lightbox'),
      lbImg = document.getElementById('lbImg'),
      lbCount = document.getElementById('lbCount'),
      slides = items.map(function (el) { return { src: el.getAttribute('data-full'), alt: el.getAttribute('data-alt') || '' }; }),
      idx = 0;

  function show(i) {
    idx = (i + slides.length) % slides.length;
    lbImg.src = slides[idx].src;
    lbImg.alt = slides[idx].alt;
    lbCount.textContent = (idx + 1) + ' / ' + slides.length;
  }
  function open(i) { show(i); lb.classList.add('is-open'); lb.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; }
  function close() { lb.classList.remove('is-open'); lb.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; }

  items.forEach(function (el, i) {
    el.addEventListener('click', function () { open(i); });
    el.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(i); } });
  });
  lb.querySelector('.lb__close').addEventListener('click', close);
  lb.querySelector('.lb__prev').addEventListener('click', function (e) { e.stopPropagation(); show(idx - 1); });
  lb.querySelector('.lb__next').addEventListener('click', function (e) { e.stopPropagation(); show(idx + 1); });
  lb.addEventListener('click', function (e) { if (e.target === lb) close(); });
  document.addEventListener('keydown', function (e) {
    if (!lb.classList.contains('is-open')) return;
    if (e.key === 'Escape') close();
    else if (e.key === 'ArrowLeft') show(idx - 1);
    else if (e.key === 'ArrowRight') show(idx + 1);
  });
})();
</script>

</body>
</html>
