<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Admission Application — Philippine Academy of Sakya</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy:   #0b1e3d;
    --blue:   #1d4ed8;
    --blue-l: #3b82f6;
    --gold:   #f59e0b;
    --red:    #dc2626;
    --gray-50:  #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-400: #94a3b8;
    --gray-600: #475569;
    --gray-800: #1e293b;
  }

  html { scroll-behavior: smooth; }

  body {
    font-family: 'Inter', sans-serif;
    background: var(--gray-100);
    color: var(--gray-800);
    min-height: 100vh;
  }

  /* ═══════════════════════════════════
     TOP NAV BAR
  ═══════════════════════════════════ */
  .top-bar {
    background: var(--navy);
    padding: .7rem 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: sticky;
    top: 0;
    z-index: 200;
    box-shadow: 0 2px 12px rgba(0,0,0,.3);
  }
  .top-bar-logo {
    display: flex;
    align-items: center;
    gap: .75rem;
  }
  .top-bar-logo img {
    height: 32px;
    width: 32px;
    border-radius: 50%;
    border: 1.5px solid rgba(255,255,255,.15);
  }
  .top-bar-name {
    font-size: .9rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.01em;
  }
  .top-bar-sub {
    font-size: .68rem;
    color: rgba(255,255,255,.45);
    letter-spacing: .03em;
  }
  .top-bar-sep { flex: 1; }
  .top-bar-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: .75rem;
    font-weight: 600;
    color: rgba(255,255,255,.45);
    text-decoration: none;
    transition: color .15s;
  }
  .top-bar-back:hover { color: rgba(255,255,255,.8); }
  .top-bar-back svg { width: 14px; height: 14px; }

  /* ═══════════════════════════════════
     HERO BANNER
  ═══════════════════════════════════ */
  .hero {
    background: linear-gradient(135deg, #0b1e3d 0%, #1535a0 55%, #1d4ed8 100%);
    padding: 3rem 2rem 3.5rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  .hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
    background-size: 24px 24px;
  }
  .hero::after {
    content: '';
    position: absolute;
    bottom: -2px; left: 0; right: 0;
    height: 40px;
    background: var(--gray-100);
    clip-path: ellipse(55% 100% at 50% 100%);
  }
  .hero-inner { position: relative; }
  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(245,158,11,.15);
    border: 1px solid rgba(245,158,11,.3);
    color: #fde68a;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: .3rem .85rem;
    border-radius: 99px;
    margin-bottom: 1rem;
  }
  .hero-badge svg { width: 12px; height: 12px; }
  .hero h1 {
    font-size: 2rem;
    font-weight: 900;
    color: #fff;
    letter-spacing: -.035em;
    margin-bottom: .6rem;
  }
  .hero p {
    font-size: .875rem;
    color: rgba(255,255,255,.6);
    max-width: 480px;
    margin: 0 auto;
    line-height: 1.65;
  }
  .hero p strong { color: #fca5a5; }

  /* ═══════════════════════════════════
     PAGE BODY — sidebar + form
  ═══════════════════════════════════ */
  .page-body {
    max-width: 1020px;
    margin: 0 auto;
    padding: 2rem 1.5rem 5rem;
    display: grid;
    grid-template-columns: 210px 1fr;
    gap: 1.75rem;
    align-items: start;
  }

  /* ─── Sticky progress sidebar ─── */
  .progress-nav {
    position: sticky;
    top: 70px;
  }
  .progress-nav-title {
    font-size: .65rem;
    font-weight: 800;
    color: var(--gray-400);
    text-transform: uppercase;
    letter-spacing: .1em;
    margin-bottom: 12px;
    padding-left: 4px;
  }
  .progress-nav ul { list-style: none; display: flex; flex-direction: column; gap: 2px; }
  .progress-nav a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 10px;
    text-decoration: none;
    font-size: .78rem;
    font-weight: 600;
    color: var(--gray-400);
    transition: all .15s;
  }
  .progress-nav a:hover { background: #fff; color: var(--gray-800); box-shadow: 0 1px 6px rgba(0,0,0,.06); }
  .progress-nav a.active { background: #fff; color: var(--blue); box-shadow: 0 2px 10px rgba(0,0,0,.08); }
  .progress-nav .nav-num {
    width: 24px; height: 24px;
    border-radius: 50%;
    background: var(--gray-200);
    color: var(--gray-600);
    font-size: .7rem;
    font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: all .15s;
  }
  .progress-nav a.active .nav-num {
    background: var(--blue);
    color: #fff;
  }
  .progress-nav a:hover .nav-num {
    background: var(--gray-800);
    color: #fff;
  }

  /* required fields note */
  .req-note {
    margin-top: 16px;
    padding: 10px 12px;
    background: #fff;
    border-radius: 10px;
    border-left: 3px solid var(--gold);
    font-size: .72rem;
    color: var(--gray-600);
    line-height: 1.5;
    box-shadow: 0 1px 6px rgba(0,0,0,.05);
  }
  .req-note strong { color: var(--red); }

  /* ─── Error banner ─── */
  .error-banner {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    border-left: 4px solid var(--red);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 1.25rem;
    font-size: .84rem;
    color: #991b1b;
  }
  .error-banner strong { display: block; margin-bottom: .4rem; font-size: .88rem; }
  .error-banner ul { margin-left: 1.1rem; display: flex; flex-direction: column; gap: 3px; }

  /* ═══════════════════════════════════
     SECTION CARDS
  ═══════════════════════════════════ */
  .section-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(15,23,42,.06), 0 1px 3px rgba(15,23,42,.04);
    margin-bottom: 1.25rem;
    overflow: hidden;
    border: 1px solid rgba(226,232,240,.8);
    scroll-margin-top: 80px;
  }

  .section-head {
    padding: 1rem 1.4rem;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid var(--gray-100);
    background: var(--gray-50);
  }
  .section-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
  }
  .section-icon svg { width: 17px; height: 17px; }
  .section-num {
    font-size: .65rem;
    font-weight: 800;
    color: var(--gray-400);
    text-transform: uppercase;
    letter-spacing: .1em;
    margin-bottom: 1px;
  }
  .section-title {
    font-size: .92rem;
    font-weight: 800;
    color: var(--gray-800);
    letter-spacing: -.01em;
  }

  .section-body {
    padding: 1.4rem;
    display: grid;
    gap: 1.1rem;
  }
  .grid-2 { grid-template-columns: 1fr 1fr; }
  .grid-3 { grid-template-columns: 1fr 1fr 1fr; }
  .span-2 { grid-column: span 2; }
  .span-3 { grid-column: span 3; }

  /* ─── Field styling ─── */
  .field { display: flex; flex-direction: column; }
  .field-label {
    font-size: .69rem;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: 6px;
  }
  .field-label .req { color: var(--red); margin-left: 2px; }
  .field-label .opt {
    font-size: .62rem;
    font-weight: 500;
    color: var(--gray-400);
    text-transform: none;
    letter-spacing: 0;
    margin-left: 5px;
  }

  .field input,
  .field select,
  .field textarea {
    width: 100%;
    height: 44px;
    padding: 0 14px;
    border: 1.5px solid var(--gray-200);
    border-radius: 10px;
    font-size: .875rem;
    color: var(--gray-800);
    background: var(--gray-50);
    font-family: inherit;
    outline: none;
    transition: border-color .15s, box-shadow .15s, background .15s;
    -webkit-appearance: none;
    appearance: none;
  }
  .field select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
    padding-right: 38px;
    cursor: pointer;
  }
  .field textarea {
    height: auto;
    padding: 11px 14px;
    resize: vertical;
    min-height: 72px;
    line-height: 1.5;
  }
  .field input:focus,
  .field select:focus,
  .field textarea:focus {
    border-color: var(--blue-l);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(59,130,246,.1);
  }
  .field input.is-err,
  .field select.is-err,
  .field textarea.is-err {
    border-color: var(--red);
    box-shadow: 0 0 0 3px rgba(220,38,38,.08);
  }
  .field input::placeholder { color: var(--gray-400); }

  .field-hint {
    font-size: .7rem;
    color: var(--gray-400);
    margin-top: 5px;
    line-height: 1.4;
  }
  .field-err {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: .72rem;
    color: var(--red);
    font-weight: 600;
    margin-top: 5px;
  }
  .field-err svg { width: 12px; height: 12px; flex-shrink: 0; }

  /* ═══════════════════════════════════
     SUBMIT AREA
  ═══════════════════════════════════ */
  .submit-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid var(--gray-200);
    padding: 1.4rem;
    box-shadow: 0 2px 12px rgba(15,23,42,.06);
  }

  .disclaimer-box {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-left: 3px solid var(--blue-l);
    border-radius: 10px;
    padding: .85rem 1rem;
    font-size: .76rem;
    color: var(--gray-600);
    line-height: 1.65;
    margin-bottom: 1.1rem;
  }
  .disclaimer-box p + p { margin-top: .4rem; }

  .privacy-check {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 1.1rem;
    cursor: pointer;
  }
  .privacy-check input { width: 16px; height: 16px; accent-color: var(--blue); margin-top: 1px; flex-shrink: 0; cursor: pointer; }
  .privacy-check span { font-size: .78rem; color: var(--gray-600); line-height: 1.55; }
  .privacy-check strong { color: var(--gray-800); }

  .submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    width: 100%;
    padding: .9rem 1.5rem;
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: .95rem;
    font-weight: 800;
    font-family: inherit;
    cursor: pointer;
    letter-spacing: .01em;
    box-shadow: 0 4px 20px rgba(29,78,216,.4), 0 1px 0 rgba(255,255,255,.1) inset;
    transition: all .2s;
    position: relative;
    overflow: hidden;
  }
  .submit-btn::before {
    content: '';
    position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(251,191,36,.15), transparent);
    transition: left .5s;
  }
  .submit-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(29,78,216,.5); }
  .submit-btn:hover::before { left: 100%; }
  .submit-btn:active { transform: translateY(0); }
  .submit-btn svg { width: 18px; height: 18px; }

  /* ─── Responsive ─── */
  @media (max-width: 760px) {
    .page-body { grid-template-columns: 1fr; }
    .progress-nav { display: none; }
    .hero h1 { font-size: 1.5rem; }
    .grid-2, .grid-3 { grid-template-columns: 1fr; }
    .span-2, .span-3 { grid-column: span 1; }
  }
</style>
</head>
<body>

{{-- ── Top Bar ── --}}
<div class="top-bar">
  <div class="top-bar-logo">
    <img src="/images/logo.png" alt="PAS">
    <div>
      <div class="top-bar-name">Philippine Academy of Sakya</div>
      <div class="top-bar-sub">EncryptEd · Academic Management System</div>
    </div>
  </div>
  <div class="top-bar-sep"></div>
  <a href="{{ route('login') }}" class="top-bar-back">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
    </svg>
    Back to Login
  </a>
</div>

{{-- ── Hero ── --}}
<div class="hero">
  <div class="hero-inner">
    <div class="hero-badge">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
      </svg>
      Online Admission &amp; Enrollment
    </div>
    <h1>Application Form</h1>
    <p>Complete the form below to apply for admission or enrollment. All fields marked with <strong>*</strong> are required.</p>
  </div>
</div>

{{-- ── Page Body ── --}}
<div class="page-body">

  {{-- ── Progress Sidebar ── --}}
  <aside class="progress-nav">
    <div class="progress-nav-title">Sections</div>
    <ul>
      <li><a href="#sec-personal" class="active"><span class="nav-num">1</span> Personal Info</a></li>
      <li><a href="#sec-address"><span class="nav-num">2</span> Home Address</a></li>
      <li><a href="#sec-school"><span class="nav-num">3</span> Previous School</a></li>
      <li><a href="#sec-applying"><span class="nav-num">4</span> Applying For</a></li>
      <li><a href="#sec-parent"><span class="nav-num">5</span> Parent / Guardian</a></li>
    </ul>
    <div class="req-note">
      Fields marked <strong>*</strong> are required. Your information is encrypted and protected under RA 10173.
    </div>
  </aside>

  {{-- ── Form ── --}}
  <div>

    @if($errors->any())
    <div class="error-banner">
      <strong>⚠ Please correct the following errors:</strong>
      <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('apply.store') }}" novalidate>
      @csrf

      {{-- 1. Personal Information --}}
      <div class="section-card" id="sec-personal">
        <div class="section-head">
          <div class="section-icon" style="background:#eff6ff;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#1d4ed8" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
          </div>
          <div>
            <div class="section-num">Section 1</div>
            <div class="section-title">Personal Information</div>
          </div>
        </div>

        <div class="section-body grid-2">

          <div class="field">
            <label class="field-label">First Name <span class="req">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required maxlength="100"
              class="{{ $errors->has('first_name') ? 'is-err' : '' }}" placeholder="e.g. Juan">
            @error('first_name')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">Last Name <span class="req">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required maxlength="100"
              class="{{ $errors->has('last_name') ? 'is-err' : '' }}" placeholder="e.g. dela Cruz">
            @error('last_name')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">Middle Name <span class="opt">(optional)</span></label>
            <input type="text" name="middle_name" value="{{ old('middle_name') }}" maxlength="100" placeholder="e.g. Santos">
          </div>

          <div class="field">
            <label class="field-label">Suffix <span class="opt">Jr., Sr., III…</span></label>
            <input type="text" name="suffix" value="{{ old('suffix') }}" maxlength="20" placeholder="e.g. Jr.">
          </div>

          <div class="field">
            <label class="field-label">Date of Birth <span class="req">*</span></label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
              class="{{ $errors->has('date_of_birth') ? 'is-err' : '' }}">
            @error('date_of_birth')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">Sex <span class="req">*</span></label>
            <select name="sex" required class="{{ $errors->has('sex') ? 'is-err' : '' }}">
              <option value="">— Select —</option>
              <option value="Male"   {{ old('sex') === 'Male'   ? 'selected' : '' }}>Male</option>
              <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('sex')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">Learner Reference Number <span class="opt">(LRN)</span></label>
            <input type="text" name="lrn" value="{{ old('lrn') }}" maxlength="12" pattern="\d{12}"
              placeholder="12-digit LRN" class="{{ $errors->has('lrn') ? 'is-err' : '' }}">
            <div class="field-hint">Leave blank if not yet assigned.</div>
            @error('lrn')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">Nationality</label>
            <input type="text" name="nationality" value="{{ old('nationality', 'Filipino') }}" maxlength="80">
          </div>

        </div>
      </div>

      {{-- 2. Home Address --}}
      <div class="section-card" id="sec-address">
        <div class="section-head">
          <div class="section-icon" style="background:#f0fdf4;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
            </svg>
          </div>
          <div>
            <div class="section-num">Section 2</div>
            <div class="section-title">Home Address</div>
          </div>
        </div>

        <div class="section-body">

          <div class="field">
            <label class="field-label">Street / House No. / Purok <span class="req">*</span></label>
            <input type="text" name="address" value="{{ old('address') }}" required maxlength="300"
              placeholder="e.g. 123 Rizal St., Purok 2"
              class="{{ $errors->has('address') ? 'is-err' : '' }}">
            @error('address')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.1rem;">
            <div class="field">
              <label class="field-label">Barangay</label>
              <input type="text" name="barangay" value="{{ old('barangay') }}" maxlength="100" placeholder="Barangay">
            </div>
            <div class="field">
              <label class="field-label">Municipality / City</label>
              <input type="text" name="municipality" value="{{ old('municipality') }}" maxlength="100" placeholder="City / Municipality">
            </div>
            <div class="field">
              <label class="field-label">Province</label>
              <input type="text" name="province" value="{{ old('province') }}" maxlength="100" placeholder="Province">
            </div>
          </div>

        </div>
      </div>

      {{-- 3. Previous School --}}
      <div class="section-card" id="sec-school">
        <div class="section-head">
          <div class="section-icon" style="background:#fdf4ff;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#9333ea" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
            </svg>
          </div>
          <div>
            <div class="section-num">Section 3</div>
            <div class="section-title">Previous School <span style="font-size:.76rem;font-weight:500;color:var(--gray-400);">(if any)</span></div>
          </div>
        </div>

        <div class="section-body" style="grid-template-columns:1fr 1fr 1fr;">

          <div class="field span-2">
            <label class="field-label">School Name</label>
            <input type="text" name="previous_school" value="{{ old('previous_school') }}" maxlength="200"
              placeholder="Full name of last school attended">
          </div>

          <div class="field">
            <label class="field-label">Grade Level Completed</label>
            <select name="previous_grade_level">
              <option value="">— Select —</option>
              @foreach($gradeLevels as $lvl)
              <option value="{{ $lvl }}" {{ old('previous_grade_level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
              @endforeach
            </select>
          </div>

          <div class="field">
            <label class="field-label">School Year Completed</label>
            <input type="text" name="school_year_completed" value="{{ old('school_year_completed') }}"
              placeholder="e.g. 2024-2025" maxlength="20">
          </div>

        </div>
      </div>

      {{-- 4. Applying For --}}
      <div class="section-card" id="sec-applying">
        <div class="section-head">
          <div class="section-icon" style="background:#fff7ed;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#ea580c" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
          </div>
          <div>
            <div class="section-num">Section 4</div>
            <div class="section-title">Applying For</div>
          </div>
        </div>

        <div class="section-body grid-2">

          <div class="field">
            <label class="field-label">Grade Level <span class="req">*</span></label>
            <select name="applying_for_grade" required class="{{ $errors->has('applying_for_grade') ? 'is-err' : '' }}">
              <option value="">— Select Grade —</option>
              @foreach($gradeLevels as $lvl)
              <option value="{{ $lvl }}" {{ old('applying_for_grade') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
              @endforeach
            </select>
            @error('applying_for_grade')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">School Year <span class="opt">(optional)</span></label>
            <input type="text" name="applying_for_year" value="{{ old('applying_for_year') }}"
              placeholder="e.g. 2025-2026" maxlength="20">
          </div>

        </div>
      </div>

      {{-- 5. Parent / Guardian --}}
      <div class="section-card" id="sec-parent">
        <div class="section-head">
          <div class="section-icon" style="background:#fff1f2;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#e11d48" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
            </svg>
          </div>
          <div>
            <div class="section-num">Section 5</div>
            <div class="section-title">Parent / Guardian Information</div>
          </div>
        </div>

        <div class="section-body grid-2">

          <div class="field span-2">
            <label class="field-label">Full Name <span class="req">*</span></label>
            <input type="text" name="parent_guardian_name" value="{{ old('parent_guardian_name') }}" required maxlength="200"
              placeholder="Complete name of parent or guardian"
              class="{{ $errors->has('parent_guardian_name') ? 'is-err' : '' }}">
            @error('parent_guardian_name')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">Relationship <span class="req">*</span></label>
            <select name="relationship" required class="{{ $errors->has('relationship') ? 'is-err' : '' }}">
              <option value="">— Select —</option>
              @foreach(['Mother','Father','Guardian','Grandparent','Sibling','Other'] as $rel)
              <option value="{{ $rel }}" {{ old('relationship') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
              @endforeach
            </select>
            @error('relationship')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field">
            <label class="field-label">Contact Number <span class="req">*</span></label>
            <input type="text" name="parent_contact" value="{{ old('parent_contact') }}" required maxlength="20"
              placeholder="e.g. 09XX-XXX-XXXX"
              class="{{ $errors->has('parent_contact') ? 'is-err' : '' }}">
            @error('parent_contact')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

          <div class="field span-2">
            <label class="field-label">Email Address <span class="opt">(optional but recommended)</span></label>
            <input type="email" name="parent_email" value="{{ old('parent_email') }}" maxlength="180"
              placeholder="yourname@email.com"
              class="{{ $errors->has('parent_email') ? 'is-err' : '' }}">
            <div class="field-hint">Status updates and login credentials will be sent here once your application is reviewed.</div>
            @error('parent_email')
            <div class="field-err">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/></svg>
              {{ $message }}
            </div>
            @enderror
          </div>

        </div>
      </div>

      {{-- Submit Card --}}
      <div class="submit-card">
        <div class="disclaimer-box">
          <p>By submitting this form, I certify that all information provided is <strong>true, correct, and complete</strong> to the best of my knowledge.</p>
          <p>Submission of false or misleading information is grounds for disqualification from admission or enrollment, in accordance with DepEd regulations.</p>
        </div>
        <label class="privacy-check">
          <input type="checkbox" required>
          <span>I have read and agree to the school's data privacy policy. I consent to the collection and processing of the information above in accordance with <strong>RA 10173 (Data Privacy Act of 2012)</strong>.</span>
        </label>
        <button type="submit" class="submit-btn">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
          </svg>
          Submit Application
        </button>
      </div>

    </form>
  </div>
</div>

<script>
  // Highlight active nav item on scroll
  const sections = document.querySelectorAll('.section-card[id]');
  const navLinks  = document.querySelectorAll('.progress-nav a');

  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        navLinks.forEach(l => l.classList.remove('active'));
        const active = document.querySelector(`.progress-nav a[href="#${e.target.id}"]`);
        if (active) active.classList.add('active');
      }
    });
  }, { rootMargin: '-30% 0px -60% 0px' });

  sections.forEach(s => observer.observe(s));
</script>

</body>
</html>
