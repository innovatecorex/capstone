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
      border-bottom: 1px solid rgba(212,161,42,.20);
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

    /* ── HERO — split editorial composition ──────
       A deep navy panel carries the typography (perfect contrast), while the
       campus imagery bleeds off the right edge and feathers into the navy so
       there is never a hard seam. The photo becomes atmosphere, not backdrop. */
    .hero {
      position: relative; overflow: hidden;
      min-height: clamp(580px, 82vh, 860px);
      display: flex; align-items: center;
      color: #fff;
      background: #0a1a33;   /* base tone beneath the imagery */
    }
    /* a whisper of gold at the edge — a hairline that fades out, not a frame */
    .hero::after {
      content: ''; position: absolute; left: 0; right: 0; bottom: 0; height: 1px; z-index: 3;
      background: linear-gradient(90deg, transparent, rgba(212,161,42,.5), transparent);
    }
    /* fine dot texture for depth */
    .hero::before {
      content: ''; position: absolute; inset: 0; z-index: 1; pointer-events: none;
      background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
      background-size: 26px 26px;
    }
    /* Imagery spans the FULL hero — there is no panel edge, so no seam can ever
       appear, and the entrance can fade rather than drag a hard edge across. */
    .hero__media { position: absolute; inset: 0; z-index: 0; overflow: hidden; }
    .hero__media-img {
      position: absolute; inset: -3%;
      background-image:
        url('{{ asset('images/landing-hero.jpg') }}'),
        linear-gradient(135deg, #0a1a33, #12305c);
      background-image:
        image-set(
          url('{{ asset('images/landing-hero.webp') }}') type('image/webp'),
          url('{{ asset('images/landing-hero.jpg') }}') type('image/jpeg')
        ),
        linear-gradient(135deg, #0a1a33, #12305c);
      background-size: cover; background-position: 68% center; background-repeat: no-repeat;
      animation: heroKen 34s ease-in-out infinite alternate;
      will-change: transform;
    }
    @keyframes heroKen {
      from { transform: scale(1.04) translate3d(0, 0, 0); }
      to   { transform: scale(1.12) translate3d(-1.4%, -1%, 0); }
    }
    /* ONE continuous scrim: navy dissolves across the image on a slight
       diagonal, so the type sits on deep navy and the campus emerges at right. */
    .hero__media::after {
      content: ''; position: absolute; inset: 0;
      background:
        linear-gradient(96deg,
          rgba(7,17,36,.97) 0%,
          rgba(7,17,36,.94) 24%,
          rgba(8,20,42,.82) 40%,
          rgba(9,22,46,.48) 56%,
          rgba(10,26,51,.14) 74%,
          rgba(10,26,51,0) 88%),
        radial-gradient(760px 520px at 20% 45%, rgba(15,40,82,.55) 0%, transparent 70%),
        linear-gradient(180deg, rgba(6,15,32,.45) 0%, transparent 26%, transparent 68%, rgba(6,15,32,.58) 100%);
    }
    .hero__inner { position: relative; z-index: 2; width: 100%; }
    .hero__content { max-width: 600px; padding: 4rem 0; }
    .hero__eyebrow {
      display: inline-flex; align-items: center; gap: 7px;
      font-size: .7rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
      color: var(--gold-2);
      background: rgba(212,161,42,.14); border: 1px solid rgba(212,161,42,.45);
      padding: .34rem .85rem; border-radius: 999px; margin-bottom: 1.3rem;
    }
    .hero__eyebrow svg { width: 13px; height: 13px; }
    .hero__title {
      font-family: 'Merriweather', Georgia, serif;
      font-size: clamp(2.1rem, 4.6vw, 3.5rem); font-weight: 900;
      letter-spacing: -.025em; line-height: 1.08; color: #fff;
    }
    .hero__title .ln { display: block; overflow: hidden; }
    .hero__title .ln > span { display: block; }
    .hero__rule { width: 96px; height: 3px; border-radius: 2px;
      background: linear-gradient(90deg, var(--gold), var(--gold-2)); margin: 1.2rem 0 1rem; }
    .hero__tag { font-size: clamp(.92rem, 1.5vw, 1.08rem); font-weight: 700;
      letter-spacing: .01em; color: var(--gold-2); }
    .hero__desc { font-size: clamp(.98rem, 1.4vw, 1.08rem); line-height: 1.75;
      color: rgba(255,255,255,.82); max-width: 46ch; margin: .85rem 0 1.9rem; }
    .hero__cta { display: flex; gap: .8rem; flex-wrap: wrap; }
    /* animated scroll cue at the foot of the hero */
    /* aligned to the text column, not the viewport centre — reads deliberate */
    .hero__scroll {
      position: absolute; left: max(1.5rem, calc(50% - 570px + 1.5rem)); bottom: 24px; z-index: 4;
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

    /* ── SHOWCASE GALLERY — editorial mosaic ─── */
    .gallery { background: linear-gradient(180deg, #ffffff 0%, #f4f7fb 55%, #edf1f8 100%); }
    /* Feature banner — a controlled height so it never floats in dead space. */
    .gal-pano { position: relative; border-radius: 18px; overflow: hidden; margin: 0 0 1.1rem;
      box-shadow: 0 18px 46px rgba(10,26,51,.18); cursor: zoom-in; border: 1px solid rgba(10,26,51,.09);
      transition: box-shadow .3s, transform .3s, border-color .3s; }
    .gal-pano img { width: 100%; height: clamp(220px, 30vw, 400px); object-fit: cover; object-position: center 46%;
      display: block; transition: transform .7s cubic-bezier(.22,.61,.36,1); }
    .gal-pano:hover { transform: translateY(-3px); box-shadow: 0 26px 58px rgba(10,26,51,.26); border-color: rgba(212,161,42,.5); }
    .gal-pano:hover img { transform: scale(1.04); }

    /* Asymmetric mosaic: one large feature tile anchors the composition. */
    .gal-grid { display: grid; grid-template-columns: repeat(4, 1fr);
      grid-auto-rows: clamp(150px, 17vw, 215px); gap: 1.1rem; }
    .gal-item:nth-child(1) { grid-column: span 2; grid-row: span 2; }
    .gal-item:nth-child(2) { grid-column: span 2; }
    .gal-item:nth-child(5) { grid-column: span 2; }
    .gal-item:nth-child(6) { grid-column: span 2; }
    .gal-item { position: relative; border-radius: 14px; overflow: hidden; cursor: zoom-in;
      box-shadow: 0 10px 28px rgba(10,26,51,.13); border: 1px solid rgba(10,26,51,.08);
      background: var(--navy); transition: box-shadow .3s, transform .3s, border-color .3s; }
    /* crop biased upward so faces stay in frame, never cut off */
    .gal-item img { width: 100%; height: 100%; object-fit: cover; object-position: center 38%;
      display: block; transition: transform .6s cubic-bezier(.22,.61,.36,1); }
    .gal-item:hover { transform: translateY(-3px); box-shadow: 0 22px 46px rgba(10,26,51,.24); border-color: rgba(212,161,42,.55); }
    .gal-item:hover img { transform: scale(1.06); }
    .gal-item::after { content: ''; position: absolute; inset: 0; pointer-events: none;
      background: linear-gradient(180deg, transparent 52%, rgba(6,16,38,.55)); opacity: 0; transition: opacity .3s; }
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
    /* Closing section — a single restrained hairline, no gold frame. */
    .band { position: relative; overflow: hidden; background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 60%, var(--blue) 100%); color: #fff; border-top: 1px solid rgba(212,161,42,.26); }
    .band::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px); background-size: 24px 24px; }
    /* soft gold glow for depth */
    .band::after { content: ''; position: absolute; right: -6%; top: -55%; width: 460px; height: 460px; border-radius: 50%;
      background: radial-gradient(circle, rgba(212,161,42,.20), transparent 62%); pointer-events: none; z-index: 0; }
    .band__in { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap; padding: 3rem 0; }
    .band__pill { display: inline-flex; align-items: center; gap: 7px; background: rgba(212,161,42,.16); border: 1px solid rgba(212,161,42,.4); color: var(--gold-2); font-size: .66rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; padding: .32rem .8rem; border-radius: 999px; margin-bottom: .8rem; }
    .band__pill svg { width: 12px; height: 12px; }
    .band h2 { font-family: 'Merriweather', Georgia, serif; font-size: clamp(1.35rem, 2.2vw, 1.75rem); font-weight: 900; letter-spacing: -.02em; margin-bottom: .4rem; color: #fff; }
    .band p { font-size: .95rem; color: rgba(255,255,255,.75); max-width: 44ch; }

    /* ══════ MOTION — orchestrated, professional, GPU-cheap ══════ */

    /* 1 · Hero entrance — a directed sequence: the imagery wipes in, the
          headline rises line-by-line from behind a mask, then the gold rule
          draws itself and the supporting copy follows. */
    /* A soft fade, never a wipe — a moving hard edge would cut the image. */
    .js .hero__media { opacity: 0; animation: mediaIn 1.5s ease .05s forwards; }
    @keyframes mediaIn { to { opacity: 1; } }

    .js .hero__title .ln > span { transform: translateY(115%); animation: lineUp 1s cubic-bezier(.22,.61,.36,1) both; }
    .js .hero__title .ln:nth-child(1) > span { animation-delay: .52s; }
    .js .hero__title .ln:nth-child(2) > span { animation-delay: .65s; }
    @keyframes lineUp { to { transform: translateY(0); } }

    .js .hero__rule { width: 0; animation: ruleDraw .8s cubic-bezier(.22,.61,.36,1) .98s forwards; }
    @keyframes ruleDraw { to { width: 96px; } }

    .js .hero__eyebrow,
    .js .hero__tag,
    .js .hero__desc,
    .js .hero__cta { opacity: 0; animation: heroRise .8s cubic-bezier(.22,.61,.36,1) both; }
    .js .hero__eyebrow { animation-delay: .42s; }
    .js .hero__tag  { animation-delay: 1.04s; }
    .js .hero__desc { animation-delay: 1.14s; }
    .js .hero__cta  { animation-delay: 1.24s; }
    @keyframes heroRise {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: none; }
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
      border-bottom-color: rgba(212,161,42,.34);
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
      .js .reveal,
      .js .hero__eyebrow, .js .hero__tag, .js .hero__desc, .js .hero__cta {
        opacity: 1 !important; transform: none !important; filter: none !important; animation: none !important; }
      .js .hero__title .ln > span { transform: none !important; animation: none !important; }
      .js .hero__rule { width: 96px !important; animation: none !important; }
      .js .hero__media { opacity: 1 !important; animation: none !important; }
      .hero__media-img { animation: none !important; transform: none !important; }
      *, *::before, *::after { animation-duration: .001ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
    }

    /* ── responsive ──────────────────────────── */
    @media (max-width: 860px) {
      .facts__grid { grid-template-columns: repeat(2, 1fr); gap: 1.5rem 0; }
      .fact + .fact { border-left: none; }
      .steps { grid-template-columns: 1fr; }
      /* Stack on narrow screens: imagery band on top, editorial panel below. */
      .hero { display: block; min-height: 0; }
      .hero__media { position: relative; inset: auto; width: 100%; height: clamp(210px, 46vw, 320px); }
      .hero__media::after { background: linear-gradient(180deg, rgba(7,20,41,.25) 0%, rgba(10,26,51,.55) 60%, rgba(10,26,51,.95) 100%); }
      .hero__content { max-width: 100%; padding: 2rem 0 2.75rem; }
      .hero__desc { max-width: 100%; }
      .hero__scroll { display: none; }
      .gal-grid { grid-template-columns: repeat(2, 1fr); grid-auto-rows: clamp(140px, 29vw, 195px); }
      .gal-item:nth-child(1) { grid-column: span 2; grid-row: span 1; }
      .gal-item:nth-child(2), .gal-item:nth-child(5), .gal-item:nth-child(6) { grid-column: span 1; }
    }
    @media (max-width: 640px) {
      .gal-grid { grid-template-columns: 1fr; grid-auto-rows: clamp(190px, 54vw, 240px); }
      .gal-item:nth-child(n) { grid-column: span 1; grid-row: span 1; }
      .lb__nav { width: 40px; height: 40px; }
      .topbar__in { flex-direction: column; align-items: center; gap: .8rem; text-align: center; }
      .brand { flex-direction: column; text-align: center; gap: .45rem; }
      .topbar__nav { width: 100%; }
      .topbar__nav .btn { flex: 1 1 0; }
      .hero__cta .btn { flex: 1 1 100%; }
      .band__in .btn { width: 100%; }
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
  <div class="hero__media" aria-hidden="true"><div class="hero__media-img"></div></div>
  <div class="wrap hero__inner">
    <div class="hero__content">
      <span class="hero__eyebrow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
        </svg>
        Official Secure Academic Portal
      </span>

      <h1 class="hero__title">
        <span class="ln"><span>Philippine Academy</span></span>
        <span class="ln"><span>of Sakya</span></span>
      </h1>
      <div class="hero__rule" aria-hidden="true"></div>
      <div class="hero__tag">Junior &amp; Senior High School &middot; PAASCU Accredited Level III</div>
      <p class="hero__desc">
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

{{-- No footer: the school identity and both links already live in the sticky
     top bar, so the admissions band closes the page on its call to action. --}}

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
