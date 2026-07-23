{{-- resources/views/landing.blade.php — public homepage
     Editorial "prospectus" design: ivory paper + deep navy, serif display type,
     numbered sections, photography as contained plates with offset gold mats. --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  {{-- Flag JS before paint so motion styles apply without a flash, and so
       no-JS visitors always see every element. --}}
  <script>document.documentElement.className += ' js';</script>
  <title>Philippine Academy of Sakya — Official Academic Portal</title>
  <meta name="description" content="Philippine Academy of Sakya — Junior &amp; Senior High School, PAASCU Accredited Level III, established 1960. Apply for admission or sign in to the secure academic portal.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
  <style>
    /* ══════════════════════════════════════════════════════════
       TOKENS
    ══════════════════════════════════════════════════════════ */
    :root {
      --navy:    #0a1a33;
      --navy-2:  #12305c;
      --navy-3:  #061223;
      --gold:    #d4a12a;
      --gold-2:  #f0c65a;
      --ink:     #101a2c;
      --body:    #55627a;
      --muted:   #8a95a8;
      --cream:   #f7f4ec;   /* warm paper — the editorial signature */
      --cream-2: #efe9dc;
      --line:    rgba(16,26,44,.12);
      --ease:    cubic-bezier(.22,.61,.36,1);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }
    body { font-family: 'Inter', sans-serif; color: var(--body); background: var(--cream); line-height: 1.65; overflow-x: hidden; }
    a { text-decoration: none; color: inherit; }
    img { max-width: 100%; display: block; }
    :focus-visible { outline: 2px solid var(--gold); outline-offset: 3px; border-radius: 3px; }
    ::selection { background: rgba(212,161,42,.3); color: var(--ink); }
    html { scrollbar-color: var(--gold) #e4ded1; scrollbar-width: thin; }
    ::-webkit-scrollbar { width: 10px; }
    ::-webkit-scrollbar-track { background: #e4ded1; }
    ::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 99px; border: 2px solid #e4ded1; }

    .shell { max-width: 1220px; margin: 0 auto; padding: 0 clamp(1.15rem, 4vw, 2.75rem); }

    /* display type */
    .display { font-family: 'Merriweather', Georgia, serif; font-weight: 900; letter-spacing: -.028em; line-height: 1.06; color: var(--ink); }
    .kicker { font-size: .66rem; font-weight: 700; letter-spacing: .24em; text-transform: uppercase; color: var(--gold); display: inline-flex; align-items: center; gap: .7rem; }
    .kicker::before { content: ''; width: 26px; height: 1px; background: var(--gold); opacity: .7; }

    /* ── buttons ─────────────────────────────── */
    .btn { position: relative; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; gap: 9px;
      font-family: inherit; font-weight: 700; font-size: .875rem; cursor: pointer; white-space: nowrap;
      border: 1px solid transparent; border-radius: 4px; min-height: 48px; padding: 0 1.5rem;
      transition: background-color .2s, color .2s, border-color .2s, transform .18s, box-shadow .2s; }
    .btn svg { width: 15px; height: 15px; }
    .btn--gold { background: var(--gold); color: #231904; }
    .btn--gold:hover { background: var(--gold-2); transform: translateY(-2px); box-shadow: 0 10px 26px rgba(212,161,42,.32); }
    .btn--gold::after { content: ''; position: absolute; top: 0; left: -130%; width: 52%; height: 100%; pointer-events: none;
      background: linear-gradient(100deg, transparent, rgba(255,255,255,.55), transparent); transform: skewX(-20deg);
      transition: left .7s var(--ease); }
    .btn--gold:hover::after { left: 150%; }
    .btn--line { background: transparent; color: #fff; border-color: rgba(255,255,255,.34); }
    .btn--line:hover { border-color: rgba(255,255,255,.75); background: rgba(255,255,255,.07); transform: translateY(-2px); }
    .btn--ink { background: transparent; color: var(--ink); border-color: rgba(16,26,44,.22); }
    .btn--ink:hover { border-color: var(--ink); transform: translateY(-2px); }
    .btn--sm { min-height: 40px; font-size: .8rem; padding: 0 1.1rem; }

    /* scroll meter */
    .meter { position: fixed; top: 0; left: 0; height: 2px; width: 0; z-index: 90;
      background: linear-gradient(90deg, var(--gold), var(--gold-2)); transition: width .1s linear; }

    /* ══════════════════════════════════════════════════════════
       NAV
    ══════════════════════════════════════════════════════════ */
    .nav { position: sticky; top: 0; z-index: 80; background: rgba(8,20,39,.86);
      -webkit-backdrop-filter: saturate(150%) blur(10px); backdrop-filter: saturate(150%) blur(10px);
      border-bottom: 1px solid rgba(255,255,255,.08); transition: background .3s, box-shadow .3s; }
    .nav--solid { background: rgba(6,16,32,.97); box-shadow: 0 14px 34px rgba(3,9,22,.4); }
    .nav__in { display: flex; align-items: center; justify-content: space-between; gap: 1.2rem;
      padding: .7rem clamp(1.15rem, 4vw, 2.75rem); }
    .nav__brand { display: flex; align-items: center; gap: .8rem; }
    .nav__brand img { width: 44px; height: 44px; border-radius: 50%; flex: none; box-shadow: 0 0 0 1px rgba(255,255,255,.16); }
    .nav__name { font-family: 'Merriweather', Georgia, serif; font-weight: 700; font-size: 1rem; color: #fff; letter-spacing: -.01em; line-height: 1.15; }
    .nav__sub { display: block; font-size: .58rem; font-weight: 600; letter-spacing: .2em; text-transform: uppercase; color: rgba(240,198,90,.75); margin-top: 4px; }
    .nav__links { display: flex; align-items: center; gap: 1.6rem; }
    .nav__link { position: relative; font-size: .78rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: rgba(255,255,255,.66); transition: color .2s; }
    .nav__link::after { content: ''; position: absolute; left: 0; right: 0; bottom: -6px; height: 1px; background: var(--gold); transform: scaleX(0); transform-origin: left; transition: transform .3s var(--ease); }
    .nav__link:hover { color: #fff; }
    .nav__link:hover::after { transform: scaleX(1); }
    .nav__cta { display: flex; gap: .55rem; }

    /* ══════════════════════════════════════════════════════════
       HERO
    ══════════════════════════════════════════════════════════ */
    /* The banner was designed as a full-width image, so it is used as one. It
       spans the entire hero under ONE continuous scrim — with no panel edge,
       no seam can ever appear, and nothing is boxed into an awkward crop. */
    .hero { position: relative; overflow: hidden; background: var(--navy-3); color: #fff;
      min-height: clamp(560px, 46vw, 840px);
      display: flex; align-items: center;
      padding: clamp(3rem, 6vw, 5rem) 0; }
    .hero__media { position: absolute; inset: 0; z-index: 0; overflow: hidden; }
    .hero__media-img { position: absolute; inset: -2%;
      background-image: url('{{ asset('images/landing-hero.jpg') }}');
      background-image: image-set(url('{{ asset('images/landing-hero.webp') }}') type('image/webp'), url('{{ asset('images/landing-hero.jpg') }}') type('image/jpeg'));
      background-size: cover; background-position: center 42%; background-repeat: no-repeat;
      animation: ken 36s ease-in-out infinite alternate; will-change: transform; }
    @keyframes ken { from { transform: scale(1.03); } to { transform: scale(1.10); } }
    .hero__media::after { content: ''; position: absolute; inset: 0;
      background:
        linear-gradient(97deg,
          rgba(6,15,30,.97) 0%, rgba(6,15,30,.93) 26%, rgba(7,18,38,.78) 42%,
          rgba(8,20,42,.42) 58%, rgba(10,26,51,.12) 76%, rgba(10,26,51,0) 90%),
        linear-gradient(180deg, rgba(5,13,28,.5) 0%, transparent 24%, transparent 68%, rgba(5,13,28,.6) 100%); }
    /* faint editorial texture over the darkened side */
    .hero::before { content: ''; position: absolute; inset: 0; z-index: 1; pointer-events: none;
      background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
      background-size: 26px 26px; }
    .hero__in { position: relative; z-index: 2; width: 100%; }
    .hero__content { max-width: 600px; }

    .hero__eyebrow { display: flex; align-items: center; gap: .85rem; margin-bottom: 1.5rem; }
    .hero__est { font-family: 'Merriweather', Georgia, serif; font-size: .92rem; font-weight: 700; color: var(--gold-2); letter-spacing: .02em; }
    .hero__eyebrow i { width: 30px; height: 1px; background: rgba(240,198,90,.45); display: block; }
    .hero__eyebrow span { font-size: .62rem; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: rgba(255,255,255,.62); }

    .hero h1 { font-family: 'Merriweather', Georgia, serif; font-weight: 900; color: #fff;
      font-size: clamp(2.35rem, 5.4vw, 4.15rem); line-height: 1.04; letter-spacing: -.03em; }
    .hero h1 .ln { display: block; overflow: hidden; }
    .hero h1 .ln > span { display: block; }
    .hero__rule { width: 84px; height: 2px; background: linear-gradient(90deg, var(--gold), rgba(212,161,42,.15)); margin: 1.5rem 0 1.25rem; }
    .hero__tag { font-size: clamp(.92rem, 1.4vw, 1.05rem); font-weight: 600; color: var(--gold-2); letter-spacing: .01em; }
    .hero__lead { font-size: clamp(.98rem, 1.35vw, 1.06rem); line-height: 1.8; color: rgba(255,255,255,.74); max-width: 44ch; margin: .9rem 0 2.1rem; }
    .hero__cta { display: flex; gap: .7rem; flex-wrap: wrap; }

    /* ══════════════════════════════════════════════════════════
       TICKER
    ══════════════════════════════════════════════════════════ */
    .ticker { overflow: hidden; background: var(--navy); border-top: 1px solid rgba(255,255,255,.07); }
    .ticker__track { display: flex; width: max-content; animation: tick 42s linear infinite; }
    .ticker:hover .ticker__track { animation-play-state: paused; }
    .ticker__item { display: flex; align-items: center; gap: 1.5rem; padding: .95rem 1.5rem;
      font-size: .68rem; font-weight: 600; letter-spacing: .24em; text-transform: uppercase;
      color: rgba(255,255,255,.55); white-space: nowrap; }
    .ticker__item i { width: 4px; height: 4px; border-radius: 50%; background: var(--gold); display: block; flex: none; }
    @keyframes tick { to { transform: translateX(-50%); } }

    /* ══════════════════════════════════════════════════════════
       FIGURES (at a glance)
    ══════════════════════════════════════════════════════════ */
    .figures { background: var(--cream); padding: clamp(3.5rem, 7vw, 5.5rem) 0; }
    .figures__grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: clamp(1rem, 3vw, 2.5rem); }
    .figure { position: relative; padding-left: 1.5rem; }
    .figure::before { content: ''; position: absolute; left: 0; top: .35rem; bottom: .35rem; width: 1px; background: var(--line); }
    .figure:hover::before { background: var(--gold); }
    .figure b { display: block; font-family: 'Merriweather', Georgia, serif; font-size: clamp(1.7rem, 3vw, 2.5rem); font-weight: 900; color: var(--ink); letter-spacing: -.03em; line-height: 1; font-variant-numeric: tabular-nums; }
    .figure span { display: block; font-size: .66rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; color: var(--muted); margin-top: .7rem; }

    /* ══════════════════════════════════════════════════════════
       SECTION HEADS
    ══════════════════════════════════════════════════════════ */
    .sec { padding: clamp(4rem, 9vw, 7rem) 0; }
    .sec--paper { background: var(--cream-2); }
    .sec__head { max-width: 620px; margin-bottom: clamp(2.25rem, 5vw, 3.5rem); }
    .sec__num { font-family: 'Merriweather', Georgia, serif; font-size: .8rem; font-weight: 700; color: var(--gold); letter-spacing: .1em; }
    .sec__title { font-family: 'Merriweather', Georgia, serif; font-weight: 900; color: var(--ink);
      font-size: clamp(1.75rem, 3.6vw, 2.7rem); letter-spacing: -.03em; line-height: 1.1; margin: .7rem 0 .85rem; }
    .sec__lead { font-size: 1rem; color: var(--body); max-width: 52ch; }

    /* ── admissions: numbered editorial list ── */
    .adm { display: grid; grid-template-columns: .85fr 1.15fr; gap: clamp(2rem, 5vw, 4.5rem); align-items: start; }
    .steps { position: relative; padding-left: 4.25rem; }
    .steps__line { position: absolute; left: 25px; top: 12px; bottom: 12px; width: 1px;
      background: linear-gradient(180deg, var(--gold), rgba(212,161,42,.12));
      transform: scaleY(0); transform-origin: top; transition: transform 1.3s var(--ease); }
    .steps.is-in .steps__line { transform: scaleY(1); }
    .step { position: relative; padding-bottom: 2.5rem; }
    .step:last-child { padding-bottom: 0; }
    .step__n { position: absolute; left: -4.25rem; top: -2px; width: 51px; height: 51px; border-radius: 50%;
      background: var(--cream); border: 1px solid var(--line); display: flex; align-items: center; justify-content: center;
      font-family: 'Merriweather', Georgia, serif; font-weight: 900; font-size: .95rem; color: var(--ink);
      transition: background .3s, color .3s, border-color .3s, transform .3s var(--ease); }
    .sec--paper .step__n { background: var(--cream-2); }
    .step:hover .step__n { background: var(--navy); color: var(--gold-2); border-color: var(--navy); transform: scale(1.06); }
    .step h3 { font-size: 1.08rem; font-weight: 800; color: var(--ink); margin-bottom: .4rem; letter-spacing: -.01em; }
    .step p { font-size: .92rem; color: var(--body); line-height: 1.75; max-width: 46ch; }

    /* ══════════════════════════════════════════════════════════
       CAMPUS LIFE — editorial plate grid
    ══════════════════════════════════════════════════════════ */
    .plates { display: grid; grid-template-columns: repeat(12, 1fr); grid-auto-rows: clamp(96px, 10.5vw, 140px); gap: clamp(.6rem, 1.1vw, 1rem); }
    .pl { position: relative; overflow: hidden; border-radius: 3px; cursor: zoom-in; background: var(--navy);
      box-shadow: 0 12px 30px rgba(16,26,44,.14); transition: transform .4s var(--ease), box-shadow .4s var(--ease); }
    .pl img { width: 100%; height: 100%; object-fit: cover; object-position: center 36%;
      transition: transform .8s var(--ease); }
    .pl:hover { transform: translateY(-4px); box-shadow: 0 26px 54px rgba(16,26,44,.26); }
    .pl:hover img { transform: scale(1.07); }
    .pl::after { content: ''; position: absolute; inset: 0; pointer-events: none; opacity: 0; transition: opacity .35s;
      background: linear-gradient(180deg, transparent 45%, rgba(6,16,32,.6)); }
    .pl:hover::after { opacity: 1; }
    .pl__zoom { position: absolute; right: 12px; bottom: 12px; z-index: 2; width: 34px; height: 34px; border-radius: 3px;
      background: var(--gold); color: #231904; display: flex; align-items: center; justify-content: center;
      opacity: 0; transform: translateY(8px); transition: opacity .35s, transform .35s var(--ease); }
    .pl:hover .pl__zoom { opacity: 1; transform: none; }
    .pl__zoom svg { width: 16px; height: 16px; }
    .pl--a { grid-column: span 7; grid-row: span 3; }
    .pl--b { grid-column: span 5; grid-row: span 2; }
    .pl--c { grid-column: span 5; grid-row: span 1; }
    .pl--d, .pl--e, .pl--f { grid-column: span 4; grid-row: span 2; }
    .pl--wide { grid-column: span 12; grid-row: span 2; }

    /* ══════════════════════════════════════════════════════════
       CLOSING CTA
    ══════════════════════════════════════════════════════════ */
    .close { position: relative; overflow: hidden; background: var(--navy-3); color: #fff;
      padding: clamp(1.9rem, 3.4vw, 2.6rem) 0 clamp(1rem, 1.8vw, 1.35rem); }
    .close::before { content: ''; position: absolute; inset: 0;
      background:
        radial-gradient(700px 420px at 78% 30%, rgba(212,161,42,.17) 0%, transparent 68%),
        radial-gradient(680px 420px at 10% 80%, rgba(24,60,120,.5) 0%, transparent 70%); }
    .close__in { position: relative; z-index: 2; display: flex; align-items: flex-end; justify-content: space-between; gap: 2.5rem; flex-wrap: wrap; }
    .close__pill { display: inline-flex; align-items: center; gap: 8px; font-size: .62rem; font-weight: 700;
      letter-spacing: .22em; text-transform: uppercase; color: var(--gold-2);
      border: 1px solid rgba(212,161,42,.38); border-radius: 99px; padding: .3rem .8rem; margin-bottom: .7rem; }
    .close__pill svg { width: 11px; height: 11px; }
    .close h2 { font-family: 'Merriweather', Georgia, serif; font-weight: 900; color: #fff;
      font-size: clamp(1.3rem, 2.3vw, 1.8rem); letter-spacing: -.025em; line-height: 1.12; }
    .close p { font-size: .88rem; color: rgba(255,255,255,.66); max-width: 48ch; margin-top: .45rem; }
    .close__mark { position: relative; z-index: 2; margin-top: clamp(1rem, 1.8vw, 1.4rem);
      padding-top: .8rem; border-top: 1px solid rgba(255,255,255,.09);
      font-size: .62rem; letter-spacing: .14em; text-transform: uppercase; color: rgba(255,255,255,.32); }

    /* ══════════════════════════════════════════════════════════
       SECTION RAIL
    ══════════════════════════════════════════════════════════ */
    .rail { position: fixed; right: 20px; top: 50%; transform: translateY(-50%); z-index: 70;
      display: flex; flex-direction: column; gap: 15px; }
    .rail a { display: flex; align-items: center; gap: 9px; justify-content: flex-end; }
    .rail i { width: 7px; height: 7px; border-radius: 50%; background: rgba(138,149,168,.6); display: block;
      transition: background .3s, transform .3s var(--ease), box-shadow .3s; }
    .rail a.is-active i { background: var(--gold); transform: scale(1.5); box-shadow: 0 0 0 4px rgba(212,161,42,.16); }
    .rail em { font-style: normal; font-size: .58rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase;
      color: var(--muted); opacity: 0; transform: translateX(6px); transition: opacity .3s, transform .3s var(--ease); }
    .rail a:hover em { opacity: 1; transform: none; }

    /* ══════════════════════════════════════════════════════════
       LIGHTBOX
    ══════════════════════════════════════════════════════════ */
    .lb { position: fixed; inset: 0; z-index: 999; display: flex; align-items: center; justify-content: center;
      padding: clamp(1rem, 4vw, 3rem); background: rgba(4,10,20,.94);
      -webkit-backdrop-filter: blur(7px); backdrop-filter: blur(7px);
      opacity: 0; visibility: hidden; transition: opacity .3s, visibility .3s; }
    .lb.is-open { opacity: 1; visibility: visible; }
    .lb img { max-width: 100%; max-height: 88vh; border-radius: 3px; box-shadow: 0 30px 80px rgba(0,0,0,.65); }
    .lb button { position: absolute; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.26);
      color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background .2s; }
    .lb button:hover { background: rgba(255,255,255,.24); }
    .lb__x { top: 20px; right: 22px; width: 44px; height: 44px; border-radius: 3px; }
    .lb__p, .lb__n { top: 50%; transform: translateY(-50%); width: 46px; height: 46px; border-radius: 50%; }
    .lb__p { left: 20px; } .lb__n { right: 20px; }
    .lb button svg { width: 20px; height: 20px; }
    .lb__i { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%);
      font-size: .72rem; letter-spacing: .16em; color: rgba(255,255,255,.72); }

    /* ══════════════════════════════════════════════════════════
       MOTION
    ══════════════════════════════════════════════════════════ */
    .js .reveal { opacity: 0; transform: translateY(28px); transition: opacity .85s var(--ease), transform .85s var(--ease); }
    .js .reveal.is-in { opacity: 1; transform: none; }
    .js [data-d="1"] { transition-delay: .09s; } .js [data-d="2"] { transition-delay: .18s; }
    .js [data-d="3"] { transition-delay: .27s; } .js [data-d="4"] { transition-delay: .36s; }
    .js [data-d="5"] { transition-delay: .45s; } .js [data-d="6"] { transition-delay: .54s; }

    /* hero entrance choreography */
    /* a soft fade — never a wipe, so no hard edge is ever dragged across */
    .js .hero__media { opacity: 0; animation: mediaIn 1.5s ease .05s forwards; }
    @keyframes mediaIn { to { opacity: 1; } }
    .js .hero h1 .ln > span { transform: translateY(112%); animation: lnUp 1.05s var(--ease) both; }
    .js .hero h1 .ln:nth-child(1) > span { animation-delay: .46s; }
    .js .hero h1 .ln:nth-child(2) > span { animation-delay: .58s; }
    .js .hero h1 .ln:nth-child(3) > span { animation-delay: .70s; }
    @keyframes lnUp { to { transform: translateY(0); } }
    .js .hero__rule { width: 0; animation: ruleDraw .85s var(--ease) .95s forwards; }
    @keyframes ruleDraw { to { width: 84px; } }
    .js .hero__eyebrow, .js .hero__tag, .js .hero__lead, .js .hero__cta { opacity: 0; animation: rise .85s var(--ease) both; }
    .js .hero__eyebrow { animation-delay: .34s; }
    .js .hero__tag  { animation-delay: 1.02s; }
    .js .hero__lead { animation-delay: 1.12s; }
    .js .hero__cta  { animation-delay: 1.22s; }
    @keyframes rise { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: none; } }

    @media (prefers-reduced-motion: reduce) {
      .js .reveal, .js .hero__eyebrow, .js .hero__tag, .js .hero__lead, .js .hero__cta {
        opacity: 1 !important; transform: none !important; animation: none !important; }
      .js .hero h1 .ln > span { transform: none !important; animation: none !important; }
      .js .hero__rule { width: 84px !important; animation: none !important; }
      .js .hero__media { opacity: 1 !important; animation: none !important; }
      .hero__media-img, .ticker__track { animation: none !important; transform: none !important; }
      *, *::before, *::after { animation-duration: .001ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
    }

    /* ══════════════════════════════════════════════════════════
       RESPONSIVE
    ══════════════════════════════════════════════════════════ */
    @media (max-width: 1080px) {
      .rail { display: none; }
      .nav__links { gap: 1.1rem; }
    }
    @media (max-width: 940px) {
      .hero { min-height: clamp(500px, 78vw, 620px); }
      .hero__content { max-width: 100%; }
      .hero__media::after { background:
        linear-gradient(180deg, rgba(5,13,28,.72) 0%, rgba(6,15,30,.86) 55%, rgba(6,15,30,.94) 100%); }
      .adm { grid-template-columns: 1fr; gap: 2.25rem; }
      .figures__grid { grid-template-columns: repeat(2, 1fr); gap: 1.75rem 1rem; }
      .plates { grid-auto-rows: clamp(90px, 15vw, 130px); }
      .pl--a { grid-column: span 12; grid-row: span 3; }
      .pl--b, .pl--c { grid-column: span 6; grid-row: span 2; }
      .pl--d, .pl--e, .pl--f { grid-column: span 6; grid-row: span 2; }
    }
    @media (max-width: 720px) {
      .nav__links .nav__link { display: none; }
      .nav__name { font-size: .92rem; }
      .nav__brand img { width: 38px; height: 38px; }
      .steps { padding-left: 3.4rem; }
      .step__n { left: -3.4rem; width: 42px; height: 42px; font-size: .85rem; }
      .steps__line { left: 21px; }
      .close__in { flex-direction: column; align-items: flex-start; }
      .pl--b, .pl--c, .pl--d, .pl--e, .pl--f { grid-column: span 12; grid-row: span 2; }
      .lb__p, .lb__n { width: 38px; height: 38px; }
    }
  </style>
</head>
<body>

<div class="meter" id="meter" aria-hidden="true"></div>

{{-- ══════════════ NAV ══════════════ --}}
<header class="nav" id="nav">
  <div class="nav__in">
    <a href="{{ route('landing') }}" class="nav__brand">
      <img src="{{ asset('images/logo.png') }}" alt="Philippine Academy of Sakya crest">
      <span>
        <span class="nav__name">Philippine Academy of Sakya</span>
        <span class="nav__sub">Junior &amp; Senior High &middot; PAASCU Level III</span>
      </span>
    </a>
    <nav class="nav__links">
      <a href="#admissions" class="nav__link">Admissions</a>
      <a href="#life" class="nav__link">Campus Life</a>
      <span class="nav__cta">
        <a href="{{ route('login') }}" class="btn btn--line btn--sm">Sign In</a>
        <a href="{{ route('apply') }}" class="btn btn--gold btn--sm">Apply</a>
      </span>
    </nav>
  </div>
</header>

{{-- ══════════════ HERO ══════════════ --}}
<section class="hero" id="top">
  <div class="hero__media" aria-hidden="true"><div class="hero__media-img"></div></div>
  <div class="shell hero__in">
    <div class="hero__content">
      <div class="hero__eyebrow">
        <span class="hero__est">Est. 1960</span>
        <i aria-hidden="true"></i>
        <span>Official Secure Academic Portal</span>
      </div>

      <h1>
        <span class="ln"><span>Philippine</span></span>
        <span class="ln"><span>Academy</span></span>
        <span class="ln"><span>of Sakya</span></span>
      </h1>

      <div class="hero__rule" aria-hidden="true"></div>
      <div class="hero__tag">Junior &amp; Senior High School &middot; PAASCU Accredited Level III</div>
      <p class="hero__lead">
        Six decades of forming students in scholarship and character. Apply for
        admission, enroll, and follow every grade in one secure portal.
      </p>

      <div class="hero__cta">
        <a href="{{ route('apply') }}" class="btn btn--gold">
          Begin Application
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
        <a href="{{ route('login') }}" class="btn btn--line">Portal Login</a>
      </div>
    </div>
  </div>
</section>

{{-- ══════════════ TICKER ══════════════ --}}
@php
  $marquee = ['Established 1960', 'PAASCU Accredited Level III', 'Junior &amp; Senior High School',
              'K to 12 &middot; DepEd Curriculum', '菲律賓佛教能仁中學', 'Scholarship &amp; Character'];
@endphp
<div class="ticker" aria-hidden="true">
  <div class="ticker__track">
    @for($pass = 0; $pass < 2; $pass++)
      @foreach($marquee as $m)
        <span class="ticker__item"><i></i>{!! $m !!}</span>
      @endforeach
    @endfor
  </div>
</div>

{{-- ══════════════ FIGURES ══════════════ --}}
<section class="figures" aria-label="At a glance">
  <div class="shell figures__grid">
    <div class="figure reveal"><b data-count="1960" data-from="1900">1960</b><span>Established</span></div>
    <div class="figure reveal" data-d="1"><b>Level III</b><span>PAASCU Accredited</span></div>
    <div class="figure reveal" data-d="2"><b>7&ndash;12</b><span>Junior &amp; Senior High</span></div>
    <div class="figure reveal" data-d="3"><b>K to 12</b><span>DepEd Curriculum</span></div>
  </div>
</section>

{{-- ══════════════ ADMISSIONS ══════════════ --}}
<section class="sec sec--paper" id="admissions">
  <div class="shell adm">
    <div class="sec__head reveal">
      <div class="sec__num">01 &mdash; Admissions</div>
      <h2 class="sec__title">Three steps, entirely online.</h2>
      <p class="sec__lead">Begin your application without setting foot on campus. The registrar reviews every submission and replies by email.</p>
      <div style="margin-top:1.6rem;">
        <a href="{{ route('apply') }}" class="btn btn--ink">Start your application</a>
      </div>
    </div>

    <div class="steps reveal" id="steps">
      <div class="steps__line" aria-hidden="true"></div>
      <article class="step">
        <div class="step__n">01</div>
        <h3>Apply online</h3>
        <p>Complete the admission form. A reference number is emailed to you immediately as proof of submission.</p>
      </article>
      <article class="step">
        <div class="step__n">02</div>
        <h3>Submit requirements</h3>
        <p>Upload the report card, birth certificate and remaining documents for the registrar to verify.</p>
      </article>
      <article class="step">
        <div class="step__n">03</div>
        <h3>Receive your decision</h3>
        <p>The registrar reviews your file and emails the result, together with the next steps for enrollment.</p>
      </article>
    </div>
  </div>
</section>

{{-- ══════════════ CAMPUS LIFE ══════════════ --}}
@php
  $plates = [
    ['g1', 'pl--a', 'Students of Philippine Academy of Sakya at a school competition'],
    ['g4', 'pl--b', "Pupils on Kids' Athletics Day"],
    ['g3', 'pl--c', 'A community and cultural gathering'],
    ['g2', 'pl--d', 'An awarding ceremony'],
    ['g5', 'pl--e', 'Student athletes with their medals'],
    ['g6', 'pl--f', 'Alumni of the academy giving back'],
    ['pano', 'pl--wide', 'A school assembly at Philippine Academy of Sakya'],
  ];
@endphp
<section class="sec" id="life">
  <div class="shell">
    <div class="sec__head reveal">
      <div class="sec__num">02 &mdash; Campus Life</div>
      <h2 class="sec__title">Beyond the classroom.</h2>
      <p class="sec__lead">Competitions, ceremonies, athletics and community — a glimpse of the years our students spend here.</p>
    </div>

    <div class="plates">
      @foreach($plates as $i => [$img, $cls, $alt])
      <figure class="pl {{ $cls }} reveal shot" data-d="{{ min($i, 6) }}" tabindex="0" role="button"
              data-full="{{ asset('images/gallery/'.$img.'.webp') }}" data-alt="{{ $alt }}">
        <picture>
          <source srcset="{{ asset('images/gallery/'.$img.'.webp') }}" type="image/webp">
          <img src="{{ asset('images/gallery/'.$img.'.jpg') }}" loading="lazy" alt="{{ $alt }}">
        </picture>
        <span class="pl__zoom" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.35-5.4a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0zM10.5 7.5v6m3-3h-6"/></svg>
        </span>
      </figure>
      @endforeach
    </div>
  </div>
</section>

{{-- ══════════════ CLOSING ══════════════ --}}
<section class="close" id="apply">
  <div class="shell">
    <div class="close__in reveal">
      <div>
        <span class="close__pill">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Admissions Open
        </span>
        <h2>Now enrolling for SY 2025&ndash;2026</h2>
        <p>Admission is open for Junior &amp; Senior High School. Begin your application online in minutes.</p>
      </div>
      <a href="{{ route('apply') }}" class="btn btn--gold">
        Begin Application
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
      </a>
    </div>
    <div class="close__mark">&copy; {{ date('Y') }} Philippine Academy of Sakya</div>
  </div>
</section>

{{-- ══════════════ SECTION RAIL ══════════════ --}}
<aside class="rail" aria-hidden="true">
  <a href="#top"><em>Top</em><i></i></a>
  <a href="#admissions"><em>Admissions</em><i></i></a>
  <a href="#life"><em>Campus Life</em><i></i></a>
  <a href="#apply"><em>Apply</em><i></i></a>
</aside>

{{-- ══════════════ LIGHTBOX ══════════════ --}}
<div class="lb" id="lb" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Photo viewer">
  <button class="lb__x" type="button" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
  <button class="lb__p" type="button" aria-label="Previous"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg></button>
  <img id="lbImg" src="" alt="">
  <button class="lb__n" type="button" aria-label="Next"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg></button>
  <div class="lb__i" id="lbI"></div>
</div>

<script>
(function () {
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* 1 · scroll reveals + steps connector */
  var reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (es) {
      es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
    }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('is-in'); });
  }

  /* 2 · nav state + scroll meter */
  var nav = document.getElementById('nav'), meter = document.getElementById('meter');
  function onScroll() {
    if (nav) nav.classList.toggle('nav--solid', window.scrollY > 40);
    if (meter) {
      var max = document.documentElement.scrollHeight - window.innerHeight;
      meter.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
    }
  }
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll, { passive: true });

  /* 3 · figure count-up */
  function countUp(el) {
    var to = parseInt(el.getAttribute('data-count'), 10),
        from = parseInt(el.getAttribute('data-from') || '0', 10);
    if (reduce || isNaN(to)) { el.textContent = to; return; }
    var dur = 1600, start = null;
    function tick(now) {
      if (start === null) start = now;
      var p = Math.min((now - start) / dur, 1);
      el.textContent = Math.round(from + (to - from) * (1 - Math.pow(1 - p, 3)));
      if (p < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }
  var counters = document.querySelectorAll('[data-count]');
  if ('IntersectionObserver' in window && !reduce) {
    counters.forEach(function (el) {
      var f = parseInt(el.getAttribute('data-from') || '0', 10);
      if (!isNaN(f)) el.textContent = f;
    });
    var cio = new IntersectionObserver(function (es) {
      es.forEach(function (e) { if (e.isIntersecting) { countUp(e.target); cio.unobserve(e.target); } });
    }, { threshold: 0.6 });
    counters.forEach(function (el) { cio.observe(el); });
  } else {
    counters.forEach(function (el) { el.textContent = el.getAttribute('data-count'); });
  }

  /* 4 · section rail */
  var rail = document.querySelectorAll('.rail a'),
      secs = ['top', 'admissions', 'life', 'apply'].map(function (id) { return document.getElementById(id); });
  if ('IntersectionObserver' in window) {
    var sio = new IntersectionObserver(function (es) {
      es.forEach(function (e) {
        if (!e.isIntersecting) return;
        var i = secs.indexOf(e.target);
        rail.forEach(function (a, k) { a.classList.toggle('is-active', k === i); });
      });
    }, { threshold: 0.4, rootMargin: '-20% 0px -40% 0px' });
    secs.forEach(function (s) { if (s) sio.observe(s); });
  }

  /* 5 · lightbox */
  var shots = Array.prototype.slice.call(document.querySelectorAll('.shot'));
  if (!shots.length) return;
  var lb = document.getElementById('lb'), lbImg = document.getElementById('lbImg'), lbI = document.getElementById('lbI'),
      slides = shots.map(function (el) { return { src: el.getAttribute('data-full'), alt: el.getAttribute('data-alt') || '' }; }),
      idx = 0;
  function show(i) {
    idx = (i + slides.length) % slides.length;
    lbImg.src = slides[idx].src; lbImg.alt = slides[idx].alt;
    lbI.textContent = (idx + 1) + ' / ' + slides.length;
  }
  function open(i) { show(i); lb.classList.add('is-open'); lb.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; }
  function close() { lb.classList.remove('is-open'); lb.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; }
  shots.forEach(function (el, i) {
    el.addEventListener('click', function () { open(i); });
    el.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(i); } });
  });
  lb.querySelector('.lb__x').addEventListener('click', close);
  lb.querySelector('.lb__p').addEventListener('click', function (e) { e.stopPropagation(); show(idx - 1); });
  lb.querySelector('.lb__n').addEventListener('click', function (e) { e.stopPropagation(); show(idx + 1); });
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
