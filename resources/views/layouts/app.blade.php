{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — EncryptEd · Phil. Academy of Sakya</title>

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  {{-- Platform CSS --}}
  <link rel="stylesheet" href="{{ asset('css/encrypted.css') }}">

  @if(auth()->check())
  <style>
  /* ═══════════════════════════════════════════════════════════
     STUDENT SIDEBAR — Clean professional student portal style
  ═══════════════════════════════════════════════════════════ */

  .enc-sidebar {
    background: #0f1c32;
    border-right: 1px solid rgba(255,255,255,.05);
  }

  /* ── Brand ────────────────────────────────────────────── */
  .stu-brand {
    padding: 20px 18px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,.07);
  }
  .stu-brand__glow { display: none; }
  .stu-brand__logo {
    width: 145px;
    height: auto;
    display: block;
    mix-blend-mode: lighten;
    filter: brightness(1.1);
  }

  /* ── School identity ──────────────────────────────────── */
  .stu-school {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px 13px;
    border-bottom: 1px solid rgba(255,255,255,.07);
  }
  .stu-school__seal {
    width: 34px; height: 34px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    mix-blend-mode: lighten;
    border: 1.5px solid rgba(255,255,255,.15);
  }
  .stu-school__name {
    font-size: .73rem;
    font-weight: 600;
    color: rgba(255,255,255,.8);
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .stu-school__sub {
    font-size: .62rem;
    color: rgba(255,255,255,.35);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-top: 2px;
  }

  /* ── Nav container ────────────────────────────────────── */
  .stu-nav {
    flex: 1;
    padding: 8px 10px;
    overflow-y: auto;
  }
  .stu-nav::-webkit-scrollbar { width: 3px; }
  .stu-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 99px; }

  /* ── Section labels ───────────────────────────────────── */
  .stu-section {
    padding: 16px 8px 4px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .stu-section__text {
    font-size: .62rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: rgba(255,255,255,.3);
    white-space: nowrap;
  }
  .stu-section__line {
    height: 1px;
    flex: 1;
    background: rgba(255,255,255,.07);
  }

  /* ── Nav items ────────────────────────────────────────── */
  .stu-nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: 10px;
    margin-bottom: 2px;
    text-decoration: none;
    color: rgba(255,255,255,.55);
    font-size: .855rem;
    font-weight: 500;
    transition: background .15s, color .15s;
    position: relative;
  }
  .stu-nav-item:hover {
    background: rgba(255,255,255,.07);
    color: rgba(255,255,255,.9);
  }
  .stu-nav-item.active {
    background: rgba(99,102,241,.2);
    color: #fff;
    font-weight: 700;
    border-left: 3px solid #818cf8;
    padding-left: 7px;
  }
  .stu-nav-item.active::after { display: none; }

  /* ── Icon bubbles ─────────────────────────────────────── */
  .stu-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .stu-icon svg { width: 14px; height: 14px; }
  .stu-nav-item:hover .stu-icon,
  .stu-nav-item.active .stu-icon { transform: none; box-shadow: none; }

  /* Soft, flat icon colors — no gradients, no glow */
  .si-rose    { background: rgba(244, 63, 94, .18);  color: #f87171; }
  .si-amber   { background: rgba(245,158, 11, .18);  color: #fbbf24; }
  .si-emerald { background: rgba( 16,185,129, .18);  color: #34d399; }
  .si-sky     { background: rgba( 56,189,248, .18);  color: #38bdf8; }
  .si-violet  { background: rgba(167,139,250, .18);  color: #a78bfa; }
  .si-yellow  { background: rgba(253,224, 71, .18);  color: #fde047; }
  .si-teal    { background: rgba( 45,212,191, .18);  color: #2dd4bf; }
  .si-orange  { background: rgba(251,146, 60, .18);  color: #fb923c; }

  /* ── User footer ──────────────────────────────────────── */
  .stu-footer {
    padding: 10px 10px 12px;
    border-top: 1px solid rgba(255,255,255,.07);
  }
  .stu-user-card {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: 10px;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.08);
    transition: background .15s;
  }
  .stu-user-card::before { display: none; }
  .stu-user-card:hover { background: rgba(255,255,255,.09); }

  .stu-avatar {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: #2d5fa8;
    display: flex; align-items: center; justify-content: center;
    font-size: .76rem;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    letter-spacing: -.5px;
  }
  .stu-user-info { flex: 1; min-width: 0; }
  .stu-user-name {
    font-size: .8rem;
    font-weight: 600;
    color: rgba(255,255,255,.85);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .stu-user-role {
    font-size: .65rem;
    color: rgba(255,255,255,.35);
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-top: 1px;
  }
  .stu-logout-btn {
    width: 26px; height: 26px;
    border-radius: 7px;
    border: 1px solid rgba(255,255,255,.1);
    background: transparent;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    cursor: pointer;
    transition: background .15s, border-color .15s;
  }
  .stu-logout-btn:hover { background: rgba(239,68,68,.15); border-color: rgba(239,68,68,.25); }
  .stu-logout-btn svg { width: 13px; height: 13px; color: rgba(255,255,255,.4); }
  .stu-logout-btn:hover svg { color: #fca5a5; }

  /* ── Glassmorphic top header ──────────────────────────── */
  .enc-header {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    background: rgba(255,255,255,.92) !important;
  }

  /* ══════════════════════════════════════════════════════
     SIDEBAR — Lively global design (all 4 roles)
  ══════════════════════════════════════════════════════ */

  /* Sidebar base */
  .enc-sidebar {
    background: linear-gradient(180deg, #0b1220 0%, #0d1628 55%, #0b1220 100%);
    border-right: 1px solid rgba(255,255,255,.04);
  }

  /* Animated 3px gradient accent at the very top of the sidebar */
  .enc-sidebar::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px; z-index: 10;
    background: linear-gradient(90deg,
      #6366f1 0%, #8b5cf6 15%, #ec4899 30%, #ef4444 45%,
      #f97316 60%, #fbbf24 75%, #6366f1 100%);
    background-size: 250% 100%;
    animation: accentFlow 6s linear infinite;
    pointer-events: none;
  }

  /* Brand area */
  .stu-brand {
    padding: 20px 20px 15px;
    border-bottom: 1px solid rgba(255,255,255,.05);
  }

  /* School identity */
  .stu-school {
    padding: 11px 16px 12px;
    background: rgba(0,0,0,.2);
    border-bottom: 1px solid rgba(255,255,255,.04);
  }
  .stu-school__name { color: rgba(255,255,255,.82); font-size: .73rem; }
  .stu-school__sub {
    color: #f59e0b; font-size: .59rem;
    text-transform: uppercase; letter-spacing: .08em;
    font-weight: 700; margin-top: 2px;
  }

  /* Section dividers */
  .stu-section { padding: 16px 8px 5px; }
  .stu-section__text {
    font-size: .58rem; font-weight: 700;
    letter-spacing: .12em; text-transform: uppercase;
    color: rgba(99,102,241,.6);
  }
  /* Gradient line — indigo → transparent */
  .stu-section__line {
    background: linear-gradient(90deg, rgba(99,102,241,.25), transparent);
  }

  /* Nav items */
  .stu-nav-item {
    color: rgba(255,255,255,.5);
    font-size: .845rem; font-weight: 500;
    padding: 9px 10px; border-radius: 10px; margin-bottom: 2px;
    border-left: 2px solid transparent;
    transition: background .14s, color .14s, transform .14s, border-color .14s;
  }
  .stu-nav-item:hover {
    background: rgba(255,255,255,.065);
    color: rgba(255,255,255,.9);
    transform: translateX(2px);
  }
  .stu-nav-item.active {
    background: rgba(99,102,241,.18);
    color: #e0e7ff; font-weight: 650;
    border-left: 2px solid #818cf8;
    padding-left: 10px;
    box-shadow: inset 3px 0 12px -4px rgba(99,102,241,.5);
  }
  .stu-nav-item.active::after { display: none; }

  /* Icon bubbles — per-color (vivid but tasteful) */
  .stu-icon {
    width: 28px; height: 28px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: transform .14s, filter .14s;
  }
  .stu-icon svg { width: 14px; height: 14px; }

  .si-rose    { background: rgba(244, 63, 94, .14);  color: #fb7185; }
  .si-amber   { background: rgba(245,158, 11, .14);  color: #fbbf24; }
  .si-emerald { background: rgba( 16,185,129, .14);  color: #34d399; }
  .si-sky     { background: rgba( 56,189,248, .14);  color: #38bdf8; }
  .si-violet  { background: rgba(167,139,250, .14);  color: #a78bfa; }
  .si-yellow  { background: rgba(253,224, 71, .14);  color: #fde047; }
  .si-teal    { background: rgba( 45,212,191, .14);  color: #2dd4bf; }
  .si-orange  { background: rgba(251,146, 60, .14);  color: #fb923c; }

  /* Hover: icon lifts and brightens */
  .stu-nav-item:hover .stu-icon { transform: scale(1.1); filter: brightness(1.25); }

  /* Active: icon gets a richer tinted background */
  .stu-nav-item.active .stu-icon { transform: none; filter: brightness(1.35); }
  .stu-nav-item.active .si-rose    { background: rgba(244, 63, 94, .24); }
  .stu-nav-item.active .si-amber   { background: rgba(245,158, 11, .24); }
  .stu-nav-item.active .si-emerald { background: rgba( 16,185,129, .24); }
  .stu-nav-item.active .si-sky     { background: rgba( 56,189,248, .24); }
  .stu-nav-item.active .si-violet  { background: rgba(167,139,250, .24); }
  .stu-nav-item.active .si-yellow  { background: rgba(253,224, 71, .24); }
  .stu-nav-item.active .si-teal    { background: rgba( 45,212,191, .24); }
  .stu-nav-item.active .si-orange  { background: rgba(251,146, 60, .24); }

  /* User footer */
  .stu-footer { border-top: 1px solid rgba(255,255,255,.05); padding: 8px 8px 10px; }
  .stu-user-card {
    background: rgba(255,255,255,.045);
    border: 1px solid rgba(255,255,255,.07);
    transition: background .15s, border-color .15s, box-shadow .15s;
  }
  .stu-user-card:hover {
    background: rgba(99,102,241,.1);
    border-color: rgba(99,102,241,.22);
    box-shadow: 0 0 0 3px rgba(99,102,241,.07);
  }
  .stu-avatar {
    background: linear-gradient(135deg, #312e81 0%, #4f46e5 100%);
    border-radius: 9px;
    box-shadow: 0 2px 8px rgba(99,102,241,.4);
  }
  .stu-user-name { color: rgba(255,255,255,.85); }
  /* Role shown as an indigo pill badge */
  .stu-user-role {
    display: inline-block;
    margin-top: 3px;
    font-size: .59rem; font-weight: 700;
    color: #a5b4fc;
    background: rgba(99,102,241,.16);
    border: 1px solid rgba(99,102,241,.28);
    border-radius: 99px;
    padding: 1px 7px;
    text-transform: uppercase;
    letter-spacing: .06em;
  }
  .stu-logout-btn { border-color: rgba(255,255,255,.08); }
  .stu-logout-btn:hover { background: rgba(239,68,68,.14); border-color: rgba(239,68,68,.28); }
  </style>
  @endif

  {{-- ── Shared Dashboard Design System (sd-*) ─────────────── --}}
  @if(auth()->check())
  <style>
  :root {
    --sd-bg:      #f0f4f8;
    --sd-card:    #ffffff;
    --sd-border:  rgba(15,23,42,.07);
    --sd-navy:    #0f1e3c;
    --sd-primary: #4f46e5;
    --sd-success: #10b981;
    --sd-warning: #f59e0b;
    --sd-danger:  #ef4444;
    --sd-muted:   #64748b;
    --sd-radius:  16px;
    --sd-shadow:  0 4px 24px rgba(15,23,42,.07);
  }

  /* ── Dark Mode ────────────────────────────────────────── */
  body.dark-mode {
    --sd-bg:      #0f172a;
    --sd-card:    #1e293b;
    --sd-border:  rgba(255,255,255,.08);
    --sd-navy:    #e2e8f0;
    --sd-muted:   #94a3b8;
    --sd-shadow:  0 4px 24px rgba(0,0,0,.4);
  }
  body.dark-mode .enc-main            { background: #0f172a; }
  body.dark-mode .enc-header          { background: #1e293b; border-bottom-color: rgba(255,255,255,.07); }
  body.dark-mode .enc-header__breadcrumb,
  body.dark-mode .enc-page__title,
  body.dark-mode .enc-page__subtitle  { color: #e2e8f0; }
  body.dark-mode .enc-card,
  body.dark-mode .sd-card,
  body.dark-mode .enc-card__header    { background: #1e293b; border-color: rgba(255,255,255,.08); color: #e2e8f0; }
  body.dark-mode .enc-card__title,
  body.dark-mode .enc-card__meta      { color: #e2e8f0; }
  body.dark-mode .enc-card__body      { color: #cbd5e1; }
  body.dark-mode table thead tr       { background: #0f172a; }
  body.dark-mode table tbody tr       { border-color: rgba(255,255,255,.06); }
  body.dark-mode table td,
  body.dark-mode table th             { color: #cbd5e1; }
  body.dark-mode input,
  body.dark-mode select,
  body.dark-mode textarea             { background: #0f172a; border-color: rgba(255,255,255,.12); color: #e2e8f0; }
  body.dark-mode input::placeholder,
  body.dark-mode textarea::placeholder { color: #475569; }

  /* ── Global academic-year picker (header) ─────────────── */
  .enc-year-picker {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--yellow-pale, #fefce8);
    border: 1.5px solid var(--yellow, #fbbf24);
    border-radius: 10px;
    padding: 5px 10px;
    transition: box-shadow .18s ease, border-color .18s ease;
  }
  .enc-year-picker:hover { box-shadow: 0 0 0 3px rgba(251,191,36,.18); }
  .enc-year-picker__icon { width: 16px; height: 16px; color: var(--yellow-dark, #d97706); flex-shrink: 0; }
  .enc-year-picker select {
    border: none;
    background: transparent;
    font-size: .82rem;
    font-weight: 700;
    color: var(--navy, #0a1f44);
    cursor: pointer;
    outline: none;
    padding-right: 2px;
    max-width: 220px;
  }
  .enc-year-picker select:focus { outline: none; }
  @media (max-width: 640px) {
    .enc-year-picker__icon { display: none; }
    .enc-year-picker select { max-width: 140px; }
  }

  /* ── Announcements ─────────────────────────────────────────── */
  .sd-announce-wrap { margin-bottom: 24px; }
  .sd-announce-header { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
  .sd-announce-header h2 { font-size: 1rem; font-weight: 700; color: var(--sd-navy); margin: 0; }
  .sd-announce-count { background: var(--sd-primary); color: #fff; font-size: .72rem; font-weight: 700; padding: .15rem .5rem; border-radius: 999px; }
  .sd-announce-list { display: flex; flex-direction: column; gap: 10px; }
  .sd-announce-item {
    display: flex; gap: 14px; align-items: flex-start; position: relative;
    background: var(--sd-card); border: 1px solid var(--sd-border); border-left: 4px solid transparent;
    border-radius: var(--sd-radius); padding: 10px 40px 10px 14px; box-shadow: var(--sd-shadow);
    transition: transform .18s ease, box-shadow .18s ease; animation: slideDown .35s ease both;
  }
  .sd-announce-item:hover { transform: translateY(-1px); box-shadow: 0 8px 32px rgba(15,23,42,.1); }
  .sd-announce-item--high   { border-left-color: var(--sd-danger);  background: linear-gradient(135deg,#fff5f5,#ffffff); }
  .sd-announce-item--medium { border-left-color: var(--sd-warning); background: linear-gradient(135deg,#fffbeb,#ffffff); }
  .sd-announce-item--low    { border-left-color: var(--sd-primary); background: linear-gradient(135deg,#eef2ff,#ffffff); }
  .sd-announce-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .sd-announce-item--high   .sd-announce-icon { background: rgba(239,68,68,.12);  color: var(--sd-danger); }
  .sd-announce-item--medium .sd-announce-icon { background: rgba(245,158,11,.12); color: var(--sd-warning); }
  .sd-announce-item--low    .sd-announce-icon { background: rgba(79,70,229,.12);  color: var(--sd-primary); }
  .sd-announce-body { flex: 1; min-width: 0; }
  .sd-announce-title { font-weight: 700; color: var(--sd-navy); font-size: .92rem; }
  .sd-announce-msg   { font-size: .83rem; color: var(--sd-muted); margin-top: 3px; line-height: 1.45; }
  .sd-announce-date  { font-size: .75rem; color: #94a3b8; margin-top: 6px; }
  .sd-priority-badge { flex-shrink: 0; font-size: .7rem; font-weight: 700; padding: .25rem .65rem; border-radius: 999px; align-self: flex-start; white-space: nowrap; }
  .badge--high   { background: rgba(239,68,68,.1);  color: #dc2626; }
  .badge--medium { background: rgba(245,158,11,.1); color: #d97706; }
  .badge--low    { background: rgba(79,70,229,.1);  color: #4338ca; }
  .sd-dismiss-btn { position: absolute; top: 10px; right: 12px; width: 22px; height: 22px; border: none; background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #94a3b8; transition: background .15s, color .15s; padding: 0; }
  .sd-dismiss-btn:hover { background: #f1f5f9; color: #374151; }

  /* ── Hero ──────────────────────────────────────────────────── */
  .sd-hero {
    background: #ffffff;
    border-radius: 16px; padding: 22px 28px;
    display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;
    margin-bottom: 20px; position: relative; overflow: hidden;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(10,31,68,.04), 0 6px 24px rgba(10,31,68,.07);
  }
  /* Subtle warm tint on the right side */
  .sd-hero::before {
    content: ''; position: absolute; top: 0; right: 0; bottom: 0; width: 40%;
    background: linear-gradient(270deg, #fefce8 0%, transparent 100%);
    pointer-events: none;
  }
  /* Soft navy radial on the left */
  .sd-hero::after {
    content: ''; position: absolute; top: -40px; left: -40px;
    width: 200px; height: 200px; border-radius: 50%;
    background: radial-gradient(circle, rgba(10,31,68,.04) 0%, transparent 70%);
    pointer-events: none;
  }
  /* 4px animated gradient top border */
  .sd-hero__accent {
    position: absolute; top: 0; left: 0; right: 0; height: 4px;
    background: linear-gradient(90deg,
      #0a1f44 0%, #1e40af 10%, #d97706 25%, #fbbf24 40%,
      #7c3aed 55%, #ec4899 68%, #fbbf24 80%, #d97706 90%, #0a1f44 100%);
    background-size: 250% 100%;
    border-radius: 16px 16px 0 0;
    animation: accentFlow 5s linear infinite;
  }
  @keyframes accentFlow {
    0%   { background-position: 0% center; }
    100% { background-position: -250% center; }
  }
  .sd-hero__left { position: relative; z-index: 1; display: flex; align-items: center; gap: 16px; }
  .sd-hero__avatar {
    width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0;
    background: linear-gradient(135deg, #0a1f44 0%, #1c3a6e 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; font-weight: 800; color: #fbbf24; letter-spacing: -.02em;
    box-shadow: 0 2px 12px rgba(10,31,68,.18);
  }
  .sd-hero__text h1 {
    font-size: 1.45rem; font-weight: 800; color: #0a1f44; margin: 0 0 3px;
    letter-spacing: -.025em; line-height: 1.15;
  }
  .sd-hero__text p {
    font-size: .79rem; color: #64748b; margin: 0;
    display: flex; align-items: center; gap: 5px; font-weight: 500;
  }
  .sd-hero__text p span.sep { color: #cbd5e1; }
  .sd-hero__pills { display: flex; gap: 7px; flex-wrap: wrap; position: relative; z-index: 1; align-items: center; }
  .sd-hero__pill {
    background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
    padding: .36rem .9rem; font-size: .75rem; font-weight: 600; color: #334155;
    display: flex; align-items: center; gap: 6px;
    transition: background .15s, border-color .15s, box-shadow .15s;
  }
  .sd-hero__pill:hover { background: #f1f5f9; border-color: #cbd5e1; box-shadow: 0 1px 4px rgba(10,31,68,.06); }
  .sd-hero__pill svg { width: 13px; height: 13px; color: #94a3b8; }
  .sd-hero__pill--active {
    background: #fef9c3; border-color: #fbbf24; color: #854d0e;
    box-shadow: 0 1px 6px rgba(251,191,36,.18);
  }
  .sd-hero__pill--active svg { color: #d97706; }
  .sd-hero__pill--warn { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
  .sd-hero__pill--warn svg { color: #dc2626; }

  /* ── Stat Strip ─────────────────────────────────────────────── */
  .sd-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 20px; }
  .sd-stat { background: var(--sd-card); border: 1px solid var(--sd-border); border-radius: var(--sd-radius); padding: 18px 16px; box-shadow: var(--sd-shadow); display: flex; align-items: center; gap: 12px; transition: transform .18s ease, box-shadow .18s ease; }
  .sd-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(15,23,42,.1); }
  .sd-stat__icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
  .sd-stat__icon svg { width: 20px; height: 20px; }
  .si--blue   { background: rgba(79,70,229,.1);  color: var(--sd-primary); }
  .si--green  { background: rgba(16,185,129,.1); color: var(--sd-success); }
  .si--orange { background: rgba(245,158,11,.1); color: var(--sd-warning); }
  .si--red    { background: rgba(239,68,68,.1);  color: var(--sd-danger);  }
  .sd-stat__val   { font-size: 1.3rem; font-weight: 800; color: var(--sd-navy); line-height: 1.1; }
  .sd-stat__label { font-size: .74rem; color: var(--sd-muted); margin-top: 2px; font-weight: 500; }

  /* ── Card ───────────────────────────────────────────────────── */
  .sd-card { background: var(--sd-card); border: 1px solid var(--sd-border); border-radius: var(--sd-radius); box-shadow: var(--sd-shadow); overflow: hidden; }
  .sd-card__head { padding: 16px 20px 0; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .sd-card__title { font-size: .92rem; font-weight: 700; color: var(--sd-navy); }
  .sd-card__meta  { font-size: .75rem; color: #94a3b8; }
  .sd-card__body  { padding: 16px 20px 20px; }

  /* ── Layout Grid ────────────────────────────────────────────── */
  .sd-main-grid { display: grid; grid-template-columns: 1fr 380px; gap: 20px; }
  @media(max-width:900px){ .sd-main-grid { grid-template-columns: 1fr; } }

  /* ── Schedule Timeline ──────────────────────────────────────── */
  .sd-schedule-item { display: flex; gap: 12px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
  .sd-schedule-item:last-child { border-bottom: none; padding-bottom: 0; }
  .sd-schedule-time { min-width: 88px; font-size: .75rem; font-weight: 600; color: var(--sd-muted); padding-top: 2px; font-family: monospace; }
  .sd-schedule-dot-col { display: flex; flex-direction: column; align-items: center; padding-top: 5px; }
  .sd-schedule-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--sd-primary); flex-shrink: 0; }
  .sd-schedule-line { width: 1px; flex: 1; background: #e2e8f0; margin-top: 4px; min-height: 24px; }
  .sd-schedule-item:last-child .sd-schedule-line { display: none; }
  .sd-schedule-info   { flex: 1; }
  .sd-schedule-subj   { font-size: .88rem; font-weight: 700; color: var(--sd-navy); }
  .sd-schedule-detail { font-size: .76rem; color: var(--sd-muted); margin-top: 2px; }
  .sd-schedule-room   { font-size: .72rem; background: #f1f5f9; color: var(--sd-muted); padding: .15rem .5rem; border-radius: 6px; font-weight: 600; align-self: flex-start; flex-shrink: 0; }

  /* ── Row / Badge ────────────────────────────────────────────── */
  .sd-login-row { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0; margin-bottom: 8px; }
  .sd-login-row:last-child { margin-bottom: 0; }
  .sd-login-type { font-size: .84rem; font-weight: 600; color: var(--sd-navy); }
  .sd-login-time { font-size: .75rem; color: var(--sd-muted); margin-top: 2px; }
  .sd-badge-pill { font-size: .72rem; font-weight: 700; padding: .2rem .65rem; border-radius: 999px; white-space: nowrap; }
  .pill--success { background: rgba(16,185,129,.1); color: #059669; }
  .pill--danger  { background: rgba(239,68,68,.1);  color: #dc2626; }
  .pill--neutral { background: #f1f5f9; color: #475569; }
  .pill--warning { background: rgba(245,158,11,.1); color: #d97706; }

  /* ── Quick Action Buttons ───────────────────────────────────── */
  .sd-quick-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .sd-quick-btn { display: flex; flex-direction: column; align-items: flex-start; gap: 6px; padding: 14px; border-radius: 14px; border: 1px solid var(--sd-border); text-decoration: none; transition: background .15s, border-color .15s, transform .15s; background: var(--sd-card); }
  .sd-quick-btn:hover { background: #f8fafc; border-color: #cbd5e1; transform: translateY(-1px); }
  .sd-quick-icon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
  .sd-quick-icon svg { width: 16px; height: 16px; }
  .sd-quick-label { font-size: .82rem; font-weight: 700; color: var(--sd-navy); }
  .sd-quick-desc  { font-size: .72rem; color: var(--sd-muted); }

  /* ── Enc-buttons ────────────────────────────────────────────── */
  .enc-button { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: .5rem 1rem; border-radius: 999px; font-size: .8rem; font-weight: 700; text-decoration: none; border: none; cursor: pointer; transition: transform .15s, box-shadow .15s; }
  .enc-button:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.12); }
  .enc-button--primary   { background: var(--sd-primary); color: #fff; }
  .enc-button--secondary { background: #f1f5f9; color: var(--sd-navy); }
  .enc-button--sm { padding: .4rem .85rem; font-size: .76rem; }

  @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

  /* ═══════════════════════════════════════════════════════════
     DARK MODE CSS — Comprehensive dark theme for all pages
  ═══════════════════════════════════════════════════════════ */
  body.dark-mode {
    --sd-bg: #0f1419;
    --sd-surface: #1a1f2e;
    --sd-border: #2d3748;
    --sd-text: #e2e8f0;
    --sd-muted: #a0aec0;
    --sd-navy: #e2e8f0;
    background-color: #0f1419;
    color: #e2e8f0;
  }

  body.dark-mode {
    --card-bg: #1a1f2e;
    --input-bg: #2d3748;
    --hover-bg: #374151;
  }

  /* Page & card backgrounds */
  body.dark-mode .enc-shell { background: #0f1419; }
  body.dark-mode .enc-page,
  body.dark-mode .sd-page { background: #0f1419; color: #e2e8f0; }
  body.dark-mode .enc-card,
  body.dark-mode .sd-card,
  body.dark-mode .enc-page__card { background: #1a1f2e; border-color: #2d3748; }
  body.dark-mode .enc-card__header { background: #0f1419; border-color: #2d3748; }
  body.dark-mode .enc-card__body { background: #1a1f2e; }
  body.dark-mode .enc-card__footer { background: #0f1419; border-color: #2d3748; }

  /* Sidebar dark theme */
  body.dark-mode .enc-sidebar { background: #0f1419; border-color: #2d3748; }
  body.dark-mode .enc-sidebar__brand,
  body.dark-mode .enc-sidebar__school { border-color: #2d3748; }
  body.dark-mode .enc-sidebar__section-label { color: #64748b; }
  body.dark-mode .enc-nav-item { color: #cbd5e1; }
  body.dark-mode .enc-nav-item:hover { background: #2d3748; color: #f1f5f9; }
  body.dark-mode .enc-nav-item.active { color: #60a5fa; background: rgba(96, 165, 250, 0.1); }
  body.dark-mode .enc-sidebar__footer { border-color: #2d3748; }
  body.dark-mode .enc-sidebar__user { color: #cbd5e1; }
  body.dark-mode .enc-sidebar__user-name { color: #f1f5f9; }
  body.dark-mode .enc-sidebar__user-role { color: #a0aec0; }

  /* Form elements */
  body.dark-mode .enc-input,
  body.dark-mode .enc-select,
  body.dark-mode .enc-textarea,
  body.dark-mode .st-input,
  body.dark-mode input[type="text"],
  body.dark-mode input[type="email"],
  body.dark-mode input[type="password"],
  body.dark-mode input[type="number"],
  body.dark-mode select,
  body.dark-mode textarea {
    background: #2d3748;
    color: #e2e8f0;
    border-color: #3f4757;
  }
  body.dark-mode .enc-input:focus,
  body.dark-mode .enc-select:focus,
  body.dark-mode .enc-textarea:focus,
  body.dark-mode .st-input:focus,
  body.dark-mode input:focus,
  body.dark-mode select:focus,
  body.dark-mode textarea:focus {
    background: #3f4757;
    border-color: #60a5fa;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
  }

  /* Buttons */
  body.dark-mode .enc-btn,
  body.dark-mode .st-btn,
  body.dark-mode button[type="submit"] {
    background: #1d4ed8;
    color: #fff;
    border-color: #1d4ed8;
  }
  body.dark-mode .enc-btn:hover,
  body.dark-mode .st-btn:hover,
  body.dark-mode button[type="submit"]:hover {
    background: #1e40af;
    border-color: #1e40af;
  }

  /* Tables */
  body.dark-mode .enc-table,
  body.dark-mode .gr-table,
  body.dark-mode table {
    background: #1a1f2e;
    border-color: #2d3748;
  }
  body.dark-mode .enc-table th,
  body.dark-mode .gr-table th,
  body.dark-mode table th {
    background: #0f1419;
    color: #cbd5e1;
    border-color: #2d3748;
  }
  body.dark-mode .enc-table td,
  body.dark-mode .gr-table td,
  body.dark-mode table td {
    border-color: #2d3748;
    color: #e2e8f0;
  }
  body.dark-mode .enc-table tr:hover,
  body.dark-mode .gr-table tr:hover,
  body.dark-mode table tr:hover {
    background: #2d3748;
  }

  /* Alerts and badges */
  body.dark-mode .enc-alert,
  body.dark-mode .st-alert { background: #1a1f2e; border-color: #2d3748; }
  body.dark-mode .enc-alert--success { background: rgba(34, 197, 94, 0.1); border-color: #22c55e; }
  body.dark-mode .enc-alert--error { background: rgba(239, 68, 68, 0.1); border-color: #ef4444; }
  body.dark-mode .enc-alert--warning { background: rgba(245, 158, 11, 0.1); border-color: #f59e0b; }
  body.dark-mode .enc-alert--info { background: rgba(59, 130, 246, 0.1); border-color: #3b82f6; }

  body.dark-mode .badge { background: #2d3748; color: #e2e8f0; }
  body.dark-mode .badge--primary { background: #1d4ed8; color: #fff; }
  body.dark-mode .badge--success { background: rgba(34, 197, 94, 0.2); color: #86efac; }
  body.dark-mode .badge--danger { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }

  /* Status badges */
  body.dark-mode .att-status,
  body.dark-mode .grade-badge { border: none; }
  body.dark-mode .att-status.present,
  body.dark-mode .grade-badge.locked { background: rgba(34, 197, 94, 0.2); color: #86efac; }
  body.dark-mode .att-status.absent { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
  body.dark-mode .att-status.late { background: rgba(245, 158, 11, 0.2); color: #fde047; }
  body.dark-mode .att-status.excused,
  body.dark-mode .grade-badge.finalized { background: rgba(59, 130, 246, 0.2); color: #93c5fd; }
  body.dark-mode .grade-badge.submitted { background: rgba(168, 85, 247, 0.2); color: #d8b4fe; }

  /* Stats cards */
  body.dark-mode .att-stat,
  body.dark-mode .gr-stat { background: #1a1f2e; border-color: #2d3748; }
  body.dark-mode .att-stat__label,
  body.dark-mode .gr-stat__label { color: #a0aec0; }
  body.dark-mode .att-stat__value,
  body.dark-mode .gr-stat__value { color: #f1f5f9; }

  /* Settings pages */
  body.dark-mode .st-page { background: #0f1419; color: #e2e8f0; }
  body.dark-mode .st-sidenav { background: transparent; border-color: #2d3748; }
  body.dark-mode .st-sidenav__item { color: #cbd5e1; }
  body.dark-mode .st-sidenav__item:hover { background: #2d3748; color: #f1f5f9; }
  body.dark-mode .st-sidenav__item.active { color: #60a5fa; background: rgba(96, 165, 250, 0.1); }
  body.dark-mode .st-tab { display: block; }
  body.dark-mode .st-card__head { background: #0f1419; border-color: #2d3748; }
  body.dark-mode .st-card__body { background: #1a1f2e; }
  body.dark-mode .st-card__foot { background: #0f1419; border-color: #2d3748; }
  body.dark-mode .st-label { color: #cbd5e1; }
  body.dark-mode .st-hint { color: #a0aec0; }
  body.dark-mode .st-toggle-row { border-color: #2d3748; }
  body.dark-mode .st-toggle-label { color: #f1f5f9; }
  body.dark-mode .st-toggle-desc { color: #a0aec0; }

  /* Switch/toggle components */
  body.dark-mode .sw { background: #2d3748; }
  body.dark-mode .sw__track { background: #3f4757; }
  body.dark-mode input:checked + .sw__track { background: #1d4ed8; }

  /* Enrollment cards & boxes */
  body.dark-mode .enc-card { background: #1a1f2e; }
  body.dark-mode .component-box { background: #2d3748; }
  body.dark-mode .component-box { border-color: #3f4757; }
  body.dark-mode .component-value { color: #60a5fa; }

  /* Text styling */
  body.dark-mode a { color: #60a5fa; }
  body.dark-mode a:hover { color: #93c5fd; }
  body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
    color: #f1f5f9;
  }

  /* Modals & dialogs */
  body.dark-mode [role="dialog"] { background: #1a1f2e; color: #e2e8f0; border-color: #2d3748; }
  body.dark-mode .modal-backdrop { background: rgba(0, 0, 0, 0.7); }

  /* Pagination */
  body.dark-mode .pagination a,
  body.dark-mode .pagination span { color: #cbd5e1; }
  body.dark-mode .pagination a:hover { background: #2d3748; }
  body.dark-mode .pagination .active { background: #1d4ed8; color: #fff; }

  /* Message threads */
  body.dark-mode .msg-bubble { background: #2d3748; color: #e2e8f0; }
  body.dark-mode .msg-bubble.mine { background: #1d4ed8; color: #fff; }

  /* Dropdown menus */
  body.dark-mode .dropdown-menu { background: #1a1f2e; border-color: #2d3748; }
  body.dark-mode .dropdown-menu a { color: #cbd5e1; }
  body.dark-mode .dropdown-menu a:hover { background: #2d3748; color: #f1f5f9; }

  /* Progress bars & indicators */
  body.dark-mode .att-bar { background: #2d3748; }
  body.dark-mode .progress { background: #2d3748; }

  /* Transition for smooth switching */
  body.dark-mode * {
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
  }
  </style>
  @endif

  @stack('head')

  <script>
  // Apply dark mode on page load based on localStorage
  (function() {
    const isDarkMode = localStorage.getItem('dark_mode') === '1' ||
                       (document.body.classList.contains('dark-mode'));
    if (isDarkMode) {
      document.body.classList.add('dark-mode');
      localStorage.setItem('dark_mode', '1');
    }
  })();
  </script>
</head>
<body class="{{ auth()->check() && auth()->user()->pref('dark_mode') ? 'dark-mode' : '' }}">

<div class="enc-shell">

  {{-- ════════════════════════════════
       SIDEBAR
  ═══════════════════════════════════ --}}
  <aside class="enc-sidebar" id="enc-sidebar">

  @if(auth()->user()->role_id === '01')
  {{-- ╔══════════════════════════════════════╗
       ║   STUDENT SIDEBAR — Aurora Dark      ║
       ╚══════════════════════════════════════╝ --}}

    {{-- Brand --}}
    <a href="{{ route('student.dashboard') }}" class="stu-brand" style="text-decoration:none;">
      <div class="stu-brand__glow"></div>
      <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="stu-brand__logo">
    </a>

    {{-- School identity --}}
    <div class="stu-school">
      <img src="{{ asset('images/logo.png') }}" alt="Phil. Academy of Sakya" class="stu-school__seal">
      <div>
        <div class="stu-school__name">Phil. Academy of Sakya</div>
        <div class="stu-school__sub">Student Portal · {{ date('Y') }}</div>
      </div>
    </div>

    {{-- Navigation --}}
    <nav class="stu-nav">

      {{-- My Account section --}}
      <div class="stu-section">
        <span class="stu-section__text">My Account</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.dashboard') }}"
         class="stu-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
        <span class="stu-icon si-rose">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
          </svg>
        </span>
        User Profile
      </a>

      <a href="{{ route('student.academic-holds') }}"
         class="stu-nav-item {{ request()->routeIs('student.academic-holds') ? 'active' : '' }}">
        <span class="stu-icon si-amber">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
        </span>
        Academic Holds
      </a>

      <a href="{{ route('student.payments.index') }}"
         class="stu-nav-item {{ request()->routeIs('student.payments.*') ? 'active' : '' }}">
        <span class="stu-icon si-emerald">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
          </svg>
        </span>
        Payments
      </a>

      {{-- Academics section --}}
      <div class="stu-section">
        <span class="stu-section__text">Academics</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.admission-documents') }}"
         class="stu-nav-item {{ request()->routeIs('student.admission-documents') ? 'active' : '' }}">
        <span class="stu-icon si-sky">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </span>
        Admission Documents
      </a>

      <a href="{{ route('student.course-offerings') }}"
         class="stu-nav-item {{ request()->routeIs('student.course-offerings') ? 'active' : '' }}">
        <span class="stu-icon si-violet">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
          </svg>
        </span>
        Course Offerings
      </a>

      <a href="{{ route('student.report-card') }}"
         class="stu-nav-item {{ request()->routeIs('student.report-card') ? 'active' : '' }}">
        <span class="stu-icon si-yellow">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
          </svg>
        </span>
        Grade Report
      </a>

      <a href="{{ route('student.grade-archive') }}"
         class="stu-nav-item {{ request()->routeIs('student.grade-archive') ? 'active' : '' }}">
        <span class="stu-icon si-yellow">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
          </svg>
        </span>
        Grade Archive
      </a>

      <a href="{{ route('student.program-curriculum') }}"
         class="stu-nav-item {{ request()->routeIs('student.program-curriculum') ? 'active' : '' }}">
        <span class="stu-icon si-teal">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
          </svg>
        </span>
        Program Curriculum
      </a>

      <a href="{{ route('student.schedule') }}"
         class="stu-nav-item {{ request()->routeIs('student.schedule') ? 'active' : '' }}">
        <span class="stu-icon si-orange">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zM9 12h.008v.008H9V12zm0 3h.008v.008H9v-.008zm0 3h.008v.008H9v-.008zm3-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
          </svg>
        </span>
        Schedule & Assessment
      </a>

      <a href="{{ route('student.attendance') }}"
         class="stu-nav-item {{ request()->routeIs('student.attendance') ? 'active' : '' }}">
        <span class="stu-icon si-indigo">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </span>
        My Attendance
      </a>

      <a href="{{ route('assignments.student.index') }}"
         class="stu-nav-item {{ request()->routeIs('assignments.student.*') ? 'active' : '' }}">
        <span class="stu-icon si-amber">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
          </svg>
        </span>
        My Assignments
      </a>

      <a href="{{ route('documents.student.index') }}"
         class="stu-nav-item {{ request()->routeIs('documents.student.*') ? 'active' : '' }}">
        <span class="stu-icon si-teal">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </span>
        Document Requests
      </a>

      <a href="{{ route('calendar.index') }}"
         class="stu-nav-item {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
        <span class="stu-icon si-sky">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zM9 12h.008v.008H9V12zm0 3h.008v.008H9v-.008zm0 3h.008v.008H9v-.008zm3-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
          </svg>
        </span>
        School Calendar
      </a>

      <a href="{{ route('student.inbox') }}"
         class="stu-nav-item {{ request()->routeIs('student.inbox*') ? 'active' : '' }}">
        <span class="stu-icon si-violet">
          @php $stuUnread = \App\Models\Message::where('recipient_id', auth()->id())->whereNull('read_at')->count(); @endphp
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
          </svg>
        </span>
        Inbox
        @if($stuUnread > 0)
          <span style="margin-left:auto;background:#ef4444;color:#fff;border-radius:99px;font-size:.65rem;font-weight:700;padding:.1rem .45rem;">{{ $stuUnread }}</span>
        @endif
      </a>

      {{-- Settings --}}
      <div class="stu-section">
        <span class="stu-section__text">Account</span>
        <div class="stu-section__line"></div>
      </div>

      <a href="{{ route('student.settings.index') }}"
         class="stu-nav-item {{ request()->routeIs('student.settings.*') ? 'active' : '' }}">
        <span class="stu-icon si-violet">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </span>
        Settings
      </a>

    </nav>

    {{-- User footer --}}
    <div class="stu-footer">
      <div class="stu-user-card">
        <div class="stu-avatar">
          {{ strtoupper(substr(auth()->user()->first_name ?? 'S', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
        </div>
        <div class="stu-user-info">
          <div class="stu-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
          <div class="stu-user-role">Student</div>
        </div>
        <button type="button" class="stu-logout-btn" title="Sign out" onclick="openLogoutModal()">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
          </svg>
        </button>
      </div>
    </div>

  @elseif(auth()->user()->role_id === '02')
  {{-- ╔══════════════════════════════════════╗
       ║   FACULTY SIDEBAR                    ║
       ╚══════════════════════════════════════╝ --}}

  {{-- Brand --}}
  <a href="{{ route('faculty.dashboard') }}" class="stu-brand" style="text-decoration:none;">
    <div class="stu-brand__glow"></div>
    <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="stu-brand__logo">
  </a>

  {{-- School identity --}}
  <div class="stu-school">
    <img src="{{ asset('images/logo.png') }}" alt="Phil. Academy of Sakya" class="stu-school__seal">
    <div>
      <div class="stu-school__name">Phil. Academy of Sakya</div>
      <div class="stu-school__sub">Faculty Portal · {{ date('Y') }}</div>
    </div>
  </div>

  {{-- Navigation --}}
  <nav class="stu-nav">

    <div class="stu-section">
      <span class="stu-section__text">Overview</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('faculty.dashboard') }}"
       class="stu-nav-item {{ request()->routeIs('faculty.dashboard') ? 'active' : '' }}">
      <span class="stu-icon si-rose">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
        </svg>
      </span>
      Dashboard
    </a>

    <div class="stu-section">
      <span class="stu-section__text">My Classes</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('faculty.classes') }}"
       class="stu-nav-item {{ request()->routeIs('faculty.classes') ? 'active' : '' }}">
      <span class="stu-icon si-sky">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
      </span>
      Teaching Load
    </a>

    <a href="{{ route('faculty.gradebook') }}"
       class="stu-nav-item {{ request()->routeIs('faculty.gradebook') ? 'active' : '' }}">
      <span class="stu-icon si-emerald">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
        </svg>
      </span>
      Gradebook
    </a>

    <a href="{{ route('faculty.attendance') }}"
       class="stu-nav-item {{ request()->routeIs('faculty.attendance') ? 'active' : '' }}">
      <span class="stu-icon si-teal">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
        </svg>
      </span>
      Attendance
    </a>

    <a href="{{ route('complaints.manage') }}"
       class="stu-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
      <span class="stu-icon si-amber">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
        </svg>
      </span>
      Grade Complaints
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Resources</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('faculty.my-schedule') }}"
       class="stu-nav-item {{ request()->routeIs('faculty.my-schedule') ? 'active' : '' }}">
      <span class="stu-icon si-orange">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zM9 12h.008v.008H9V12zm0 3h.008v.008H9v-.008zm0 3h.008v.008H9v-.008zm3-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
        </svg>
      </span>
      My Schedule
    </a>

    <a href="{{ route('faculty.announcements') }}"
       class="stu-nav-item {{ request()->routeIs('faculty.announcements') ? 'active' : '' }}">
      <span class="stu-icon si-amber">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
      </span>
      Announcements
    </a>

    <a href="{{ route('faculty.inbox') }}"
       class="stu-nav-item {{ request()->routeIs('faculty.inbox*') ? 'active' : '' }}">
      @php $facUnread = \App\Models\Message::where('recipient_id', auth()->id())->whereNull('read_at')->count(); @endphp
      <span class="stu-icon si-violet">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
        </svg>
      </span>
      Student Inbox
      @if($facUnread > 0)
        <span style="margin-left:auto;background:#ef4444;color:#fff;border-radius:99px;font-size:.65rem;font-weight:700;padding:.1rem .45rem;">{{ $facUnread }}</span>
      @endif
    </a>

    <a href="{{ route('assignments.faculty.index') }}"
       class="stu-nav-item {{ request()->routeIs('assignments.faculty.*') ? 'active' : '' }}">
      <span class="stu-icon si-yellow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"/>
        </svg>
      </span>
      Assignments
    </a>

    <a href="{{ route('leave.faculty.index') }}"
       class="stu-nav-item {{ request()->routeIs('leave.faculty.*') ? 'active' : '' }}">
      <span class="stu-icon si-teal">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
      </span>
      Leave Requests
    </a>

    <a href="{{ route('calendar.index') }}"
       class="stu-nav-item {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
      <span class="stu-icon si-sky">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zM9 12h.008v.008H9V12zm0 3h.008v.008H9v-.008zm0 3h.008v.008H9v-.008zm3-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
        </svg>
      </span>
      School Calendar
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Account</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('faculty.settings.index') }}"
       class="stu-nav-item {{ request()->routeIs('faculty.settings.*') ? 'active' : '' }}">
      <span class="stu-icon si-violet">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </span>
      Settings
    </a>

  </nav>

  {{-- User footer --}}
  <div class="stu-footer">
    <div class="stu-user-card">
      <div class="stu-avatar">
        {{ strtoupper(substr(auth()->user()->first_name ?? 'F', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
      </div>
      <div class="stu-user-info">
        <div class="stu-user-name">{{ auth()->user()->first_name ?? '' }} {{ auth()->user()->last_name ?? '' }}</div>
        <div class="stu-user-role">Faculty</div>
      </div>
      <button type="button" class="stu-logout-btn" title="Sign out" onclick="openLogoutModal()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
        </svg>
      </button>
    </div>
  </div>

  @elseif(auth()->user()->role_id === '03')
  {{-- ╔══════════════════════════════════════╗
       ║   REGISTRAR SIDEBAR                  ║
       ╚══════════════════════════════════════╝ --}}

  <a href="{{ route('registrar.dashboard') }}" class="stu-brand" style="text-decoration:none;">
    <div class="stu-brand__glow"></div>
    <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="stu-brand__logo">
  </a>

  <div class="stu-school">
    <img src="{{ asset('images/logo.png') }}" alt="Phil. Academy of Sakya" class="stu-school__seal">
    <div>
      <div class="stu-school__name">Phil. Academy of Sakya</div>
      <div class="stu-school__sub">Registrar Portal · {{ date('Y') }}</div>
    </div>
  </div>

  <nav class="stu-nav">

    <div class="stu-section">
      <span class="stu-section__text">Overview</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('registrar.dashboard') }}"
       class="stu-nav-item {{ request()->routeIs('registrar.dashboard') ? 'active' : '' }}">
      <span class="stu-icon si-rose">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
        </svg>
      </span>
      Dashboard
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Students</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('registrar.students') }}"
       class="stu-nav-item {{ request()->routeIs('registrar.students') ? 'active' : '' }}">
      <span class="stu-icon si-sky">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
      </span>
      Student Records
    </a>

    <a href="{{ route('registrar.enrollment') }}"
       class="stu-nav-item {{ request()->routeIs('registrar.enrollment') ? 'active' : '' }}">
      <span class="stu-icon si-emerald">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m2 7H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v11a2 2 0 01-2 2z"/>
        </svg>
      </span>
      Enrollment
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Documents</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('registrar.requests') }}"
       class="stu-nav-item {{ request()->routeIs('registrar.requests') ? 'active' : '' }}">
      <span class="stu-icon si-amber">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
      </span>
      Document Requests
    </a>

    <a href="{{ route('registrar.report-cards') }}"
       class="stu-nav-item {{ request()->routeIs('registrar.report-cards') ? 'active' : '' }}">
      <span class="stu-icon si-yellow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
        </svg>
      </span>
      Report Cards
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Academics</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('registrar.grades') }}"
       class="stu-nav-item {{ request()->routeIs('registrar.grades') ? 'active' : '' }}">
      <span class="stu-icon si-emerald">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
      </span>
      Grades & Records
    </a>

    <a href="{{ route('registrar.assessment') }}"
       class="stu-nav-item {{ request()->routeIs('registrar.assessment*') ? 'active' : '' }}">
      <span class="stu-icon si-teal">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
        </svg>
      </span>
      Assessment
    </a>

    <a href="{{ route('calendar.index') }}"
       class="stu-nav-item {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
      <span class="stu-icon si-sky">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
      </span>
      School Calendar
    </a>

    <a href="{{ route('complaints.manage') }}"
       class="stu-nav-item {{ request()->routeIs('complaints.*') ? 'active' : '' }}">
      <span class="stu-icon si-amber">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
        </svg>
      </span>
      Grade Complaints
    </a>

    <a href="{{ route('documents.registrar.index') }}"
       class="stu-nav-item {{ request()->routeIs('documents.registrar.*') ? 'active' : '' }}">
      <span class="stu-icon si-orange">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
        </svg>
      </span>
      Doc Requests (New)
    </a>

    <a href="{{ route('leave.admin.index') }}"
       class="stu-nav-item {{ request()->routeIs('leave.admin.*') ? 'active' : '' }}">
      <span class="stu-icon si-violet">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg>
      </span>
      Leave Management
    </a>

    <a href="{{ route('analytics.index') }}"
       class="stu-nav-item {{ request()->routeIs('analytics.index') ? 'active' : '' }}">
      <span class="stu-icon si-rose">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
      </span>
      Analytics
    </a>

    <div class="stu-section">
      <span class="stu-section__text">DepEd SF Forms</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('sf.sf1') }}"
       class="stu-nav-item {{ request()->routeIs('sf.sf1') ? 'active' : '' }}">
      <span class="stu-icon si-yellow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
        </svg>
      </span>
      SF1 Class List
    </a>

    <a href="{{ route('sf.sf2') }}"
       class="stu-nav-item {{ request()->routeIs('sf.sf2') ? 'active' : '' }}">
      <span class="stu-icon si-teal">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </span>
      SF2 Attendance
    </a>

    <a href="{{ route('sf.sf9') }}"
       class="stu-nav-item {{ request()->routeIs('sf.sf9') ? 'active' : '' }}">
      <span class="stu-icon si-sky">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
        </svg>
      </span>
      SF9 Report Card
    </a>

    <a href="{{ route('sf.sf10') }}"
       class="stu-nav-item {{ request()->routeIs('sf.sf10') ? 'active' : '' }}">
      <span class="stu-icon si-emerald">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
      </span>
      SF10 Permanent
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Resources</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('registrar.announcements') }}"
       class="stu-nav-item {{ request()->routeIs('registrar.announcements') ? 'active' : '' }}">
      <span class="stu-icon si-amber">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
      </span>
      Announcements
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Account</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('registrar.settings.index') }}"
       class="stu-nav-item {{ request()->routeIs('registrar.settings.*') ? 'active' : '' }}">
      <span class="stu-icon si-violet">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </span>
      Settings
    </a>

  </nav>

  <div class="stu-footer">
    <div class="stu-user-card">
      <div class="stu-avatar">
        {{ strtoupper(substr(auth()->user()->first_name ?? 'R', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
      </div>
      <div class="stu-user-info">
        <div class="stu-user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
        <div class="stu-user-role">Registrar</div>
      </div>
      <button type="button" class="stu-logout-btn" title="Sign out" onclick="openLogoutModal()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
        </svg>
      </button>
    </div>
  </div>

  @else
  {{-- ╔══════════════════════════════════════╗
       ║   ADMIN SIDEBAR                      ║
       ╚══════════════════════════════════════╝ --}}

  <a href="{{ route('admin.dashboard') }}" class="stu-brand" style="text-decoration:none;">
    <div class="stu-brand__glow"></div>
    <img src="{{ asset('images/EncryptEd.png') }}" alt="EncryptEd" class="stu-brand__logo">
  </a>

  <div class="stu-school">
    <img src="{{ asset('images/logo.png') }}" alt="Phil. Academy of Sakya" class="stu-school__seal">
    <div>
      <div class="stu-school__name">Phil. Academy of Sakya</div>
      <div class="stu-school__sub">Admin Portal · {{ date('Y') }}</div>
    </div>
  </div>

  <nav class="stu-nav">

    <div class="stu-section">
      <span class="stu-section__text">Overview</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('admin.dashboard') }}"
       class="stu-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <span class="stu-icon si-rose">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
        </svg>
      </span>
      Dashboard
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Academics</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('admin.users.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <span class="stu-icon si-sky">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
      </span>
      User Management
    </a>

    <a href="{{ route('admin.grades.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.grades.*') ? 'active' : '' }}">
      <span class="stu-icon si-emerald">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/>
        </svg>
      </span>
      Grades & Records
    </a>

    <a href="{{ route('admin.announcements.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
      <span class="stu-icon si-amber">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
      </span>
      Announcements
    </a>

    <a href="{{ route('admin.schedules.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
      <span class="stu-icon si-orange">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
      </span>
      Schedules
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Academic Setup</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('admin.academic-years.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.academic-years.*') ? 'active' : '' }}">
      <span class="stu-icon si-teal">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </span>
      Academic Years
    </a>

    <a href="{{ route('admin.sections.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.sections.*') ? 'active' : '' }}">
      <span class="stu-icon si-sky">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
      </span>
      Sections
    </a>

    <a href="{{ route('admin.subjects.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
      <span class="stu-icon si-emerald">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
      </span>
      Subjects
    </a>

    <a href="{{ route('admin.classrooms.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.classrooms.*') ? 'active' : '' }}">
      <span class="stu-icon si-amber">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4a2 2 0 012-2h14a2 2 0 012 2v4M3 21h18M5 21V7a2 2 0 012-2h10a2 2 0 012 2v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M9 17h.01M15 17h.01"/>
        </svg>
      </span>
      Classrooms
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Enrollment</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('admin.payments.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
      <span class="stu-icon si-yellow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
        </svg>
      </span>
      Payments
    </a>

    <a href="{{ route('analytics.index') }}"
       class="stu-nav-item {{ request()->routeIs('analytics.index') ? 'active' : '' }}">
      <span class="stu-icon si-rose">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
      </span>
      Analytics
    </a>

    <a href="{{ route('calendar.index') }}"
       class="stu-nav-item {{ request()->routeIs('calendar.index') ? 'active' : '' }}">
      <span class="stu-icon si-sky">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5m-9-6h.008v.008H12V12zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zM9 12h.008v.008H9V12zm0 3h.008v.008H9v-.008zm0 3h.008v.008H9v-.008zm3-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z"/>
        </svg>
      </span>
      School Calendar
    </a>

    <a href="{{ route('leave.admin.index') }}"
       class="stu-nav-item {{ request()->routeIs('leave.admin.*') ? 'active' : '' }}">
      <span class="stu-icon si-violet">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
        </svg>
      </span>
      Leave Management
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Security</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('admin.threat.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.threat.*') ? 'active' : '' }}">
      <span class="stu-icon si-rose">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
      </span>
      Threat Events
      @if(isset($activeThreats) && $activeThreats > 0)
        <span style="margin-left:auto;background:#ef4444;color:#fff;border-radius:99px;font-size:.65rem;font-weight:700;padding:.1rem .45rem;">{{ $activeThreats }}</span>
      @endif
    </a>

    <a href="{{ route('admin.audit.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
      <span class="stu-icon si-orange">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
      </span>
      Audit Log
    </a>

    <a href="{{ route('admin.compliance.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.compliance.*') ? 'active' : '' }}">
      <span class="stu-icon si-teal">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
        </svg>
      </span>
      Compliance & Reports
    </a>

    <div class="stu-section">
      <span class="stu-section__text">Account</span>
      <div class="stu-section__line"></div>
    </div>

    <a href="{{ route('admin.settings.index') }}"
       class="stu-nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
      <span class="stu-icon si-violet">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
      </span>
      Settings
    </a>

  </nav>

  <div class="stu-footer">
    <div class="stu-user-card">
      <div class="stu-avatar">
        {{ strtoupper(substr(auth()->user()->first_name ?? 'A', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? 'D', 0, 1)) }}
      </div>
      <div class="stu-user-info">
        <div class="stu-user-name">{{ auth()->user()->first_name ?? 'Administrator' }} {{ auth()->user()->last_name ?? '' }}</div>
        <div class="stu-user-role">{{ auth()->user()->role_label ?? 'Admin' }}</div>
      </div>
      <button type="button" class="stu-logout-btn" title="Sign out" onclick="openLogoutModal()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
        </svg>
      </button>
    </div>
  </div>

  @endif

  </aside>

  {{-- ════════════════════════════════
       MAIN CONTENT
  ═══════════════════════════════════ --}}
  <div class="enc-main">

    {{-- Mobile overlay --}}
    <div class="enc-sidebar-overlay" id="enc-sidebar-overlay"></div>

    {{-- Top Header --}}
    <header class="enc-header">
      <div class="enc-header__left">
        {{-- Hamburger (mobile only) --}}
        <button class="enc-hamburger" id="enc-hamburger" aria-label="Toggle menu" type="button">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
          </svg>
        </button>
        <div class="enc-header__breadcrumb">
          <span>{{ auth()->user()->role_label ?? 'Portal' }}</span>
          <span class="enc-header__breadcrumb-sep">›</span>
          <span>@yield('breadcrumb', 'Dashboard')</span>
        </div>
      </div>

      <div class="enc-header__right">
        {{-- Global Academic-Year selector (staff only) --}}
        @if(auth()->user()->role_id !== '01' && isset($globalAcademicYears) && $globalAcademicYears->isNotEmpty())
        <form method="POST" action="{{ route('academic-year.switch') }}" id="enc-year-form" class="enc-year-picker" title="Working academic year">
          @csrf
          <svg class="enc-year-picker__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
          </svg>
          <select name="academic_year_id" onchange="document.getElementById('enc-year-form').submit()" aria-label="Academic year">
            @foreach($globalAcademicYears as $ay)
              <option value="{{ $ay->id }}" {{ (int)$globalActiveYearId === (int)$ay->id ? 'selected' : '' }}>
                S.Y. {{ $ay->year_label }}{{ $ay->status === 'active' ? ' • active' : '' }}
              </option>
            @endforeach
          </select>
        </form>
        @endif

        <div class="enc-header__time" id="enc-clock">--:-- --</div>

        {{-- Notifications Dropdown --}}
        @php
          $unreadCount   = auth()->user()->unreadNotifications()->count();
          $recentNotifs  = \App\Models\Notification::where('user_id', auth()->id())
                             ->orderByDesc('created_at')->limit(8)->get();
        @endphp
        <div class="notif-wrap" id="notif-wrap">
          <button type="button" class="enc-icon-btn" id="notif-bell" title="Notifications" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" style="pointer-events:none;">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
            </svg>
            @if($unreadCount > 0)
              <span id="notif-badge" style="position:absolute;top:-4px;right:-4px;background:#e11d48;color:#fff;
                border-radius:999px;font-size:.6rem;font-weight:800;min-width:16px;height:16px;
                display:flex;align-items:center;justify-content:center;padding:0 3px;line-height:1;
                pointer-events:none;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
              </span>
            @else
              <span id="notif-badge" style="display:none;position:absolute;top:-4px;right:-4px;background:#e11d48;color:#fff;
                border-radius:999px;font-size:.6rem;font-weight:800;min-width:16px;height:16px;
                align-items:center;justify-content:center;padding:0 3px;line-height:1;pointer-events:none;"></span>
            @endif
          </button>

          <div class="notif-dropdown" id="notif-dropdown">
            <div class="notif-dropdown__head">
              <span class="notif-dropdown__title">
                Notifications
                @if($unreadCount > 0)
                  <span style="background:#e11d48;color:#fff;font-size:.65rem;padding:2px 6px;border-radius:99px;margin-left:6px;">{{ $unreadCount }}</span>
                @endif
              </span>
              @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}" style="margin:0;">
                  @csrf
                  <button type="submit" class="notif-mark-all-btn">Mark all read</button>
                </form>
              @endif
            </div>
            <div class="notif-list" id="notif-list">
              @forelse($recentNotifs as $notif)
                <div class="notif-item {{ $notif->isUnread() ? 'notif-item--unread' : '' }}">
                  <div class="notif-dot {{ $notif->isUnread() ? '' : 'notif-dot--read' }}"></div>
                  <div class="notif-body">
                    <div class="notif-title">{{ $notif->title }}</div>
                    <div class="notif-text">{{ $notif->body }}</div>
                    <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
                  </div>
                  @if($notif->isUnread())
                    <form method="POST" action="{{ route('notifications.mark-read', $notif) }}" style="margin:0;flex-shrink:0;">
                      @csrf
                      <button type="submit" title="Mark as read"
                        style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:.75rem;padding:2px 4px;border-radius:4px;transition:color .12s;"
                        onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='#94a3b8'">✓</button>
                    </form>
                  @endif
                </div>
              @empty
                <div class="notif-empty">
                  <div style="font-size:1.8rem;margin-bottom:6px;">🔔</div>
                  <div>No notifications yet</div>
                </div>
              @endforelse
            </div>
            <div class="notif-dropdown__foot">
              <a href="{{ route('notifications.index') }}">View all notifications →</a>
            </div>
          </div>
        </div>

        {{-- Logout --}}
        <button type="button" class="enc-icon-btn" title="Sign out" onclick="openLogoutModal()">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
          </svg>
        </button>
      </div>
    </header>

    {{-- Page Body --}}
    <main class="enc-page">
      @yield('content')
    </main>

  </div><!-- /.enc-main -->
</div><!-- /.enc-shell -->

{{-- ══════════════════════════════════════════════════════
     LOGOUT CONFIRMATION MODAL — shared across all roles
═══════════════════════════════════════════════════════ --}}

<style>
/* ════════════════════════════════════════════════════════════
   LOGOUT MODAL — ABSOLUTE CHAOS EDITION v4.0 — TOO MUCH
   ════════════════════════════════════════════════════════════ */
@keyframes lm-orb1 { 0%,100%{transform:translate(0,0) scale(1)} 33%{transform:translate(45px,-60px) scale(1.35)} 66%{transform:translate(-35px,40px) scale(.75)} }
@keyframes lm-orb2 { 0%,100%{transform:translate(0,0) scale(1)} 40%{transform:translate(-50px,38px) scale(1.25)} 75%{transform:translate(30px,-48px) scale(.8)} }
@keyframes lm-orb3 { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(38px,55px) scale(1.3)} }
@keyframes lm-orb4 { 0%,100%{transform:translate(0,0) scale(1)} 30%{transform:translate(-28px,-35px) scale(1.2)} 70%{transform:translate(42px,18px) scale(.8)} }
@keyframes lm-orb5 { 0%,100%{transform:translate(0,0) scale(1)} 60%{transform:translate(22px,-42px) scale(1.35)} }
@keyframes lm-spin-cw  { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
@keyframes lm-spin-ccw { from{transform:rotate(0deg)} to{transform:rotate(-360deg)} }
@keyframes lm-halo { 0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.9),0 0 0 0 rgba(239,68,68,.5)} 50%{box-shadow:0 0 0 18px rgba(239,68,68,.1),0 0 0 36px rgba(239,68,68,.05)} }
@keyframes lm-shimmer { 0%{background-position:-300% center} 100%{background-position:300% center} }
@keyframes lm-float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-11px)} }
@keyframes lm-bar-flow { 0%{background-position:0% center} 100%{background-position:-300% center} }
@keyframes lm-scan-v { 0%{top:-10%;opacity:0} 5%{opacity:1} 95%{opacity:1} 100%{top:110%;opacity:0} }
@keyframes lm-scan-v2 { 0%{top:110%;opacity:0} 5%{opacity:1} 95%{opacity:1} 100%{top:-10%;opacity:0} }
@keyframes lm-scan-h { 0%{left:-80%} 100%{left:180%} }
@keyframes lm-scan-h2 { 0%{right:-80%} 100%{right:180%} }
@keyframes lm-breathe { 0%,100%{opacity:.3;transform:scale(.88)} 50%{opacity:1;transform:scale(1.1)} }
@keyframes lm-orbit1 { from{transform:rotate(0deg) translateX(54px) rotate(0deg)} to{transform:rotate(360deg) translateX(54px) rotate(-360deg)} }
@keyframes lm-orbit2 { from{transform:rotate(120deg) translateX(54px) rotate(-120deg)} to{transform:rotate(480deg) translateX(54px) rotate(-480deg)} }
@keyframes lm-orbit3 { from{transform:rotate(240deg) translateX(54px) rotate(-240deg)} to{transform:rotate(600deg) translateX(54px) rotate(-600deg)} }
@keyframes lm-orbit-m1 { from{transform:rotate(0deg) translateX(76px) rotate(0deg)} to{transform:rotate(-360deg) translateX(76px) rotate(360deg)} }
@keyframes lm-orbit-m2 { from{transform:rotate(120deg) translateX(76px) rotate(-120deg)} to{transform:rotate(-240deg) translateX(76px) rotate(240deg)} }
@keyframes lm-orbit-m3 { from{transform:rotate(240deg) translateX(76px) rotate(-240deg)} to{transform:rotate(120deg) translateX(76px) rotate(-120deg)} }
@keyframes lm-orbit-o1 { from{transform:rotate(0deg) translateX(100px) rotate(0deg)} to{transform:rotate(360deg) translateX(100px) rotate(-360deg)} }
@keyframes lm-orbit-o2 { from{transform:rotate(90deg) translateX(100px) rotate(-90deg)} to{transform:rotate(450deg) translateX(100px) rotate(-450deg)} }
@keyframes lm-orbit-o3 { from{transform:rotate(180deg) translateX(100px) rotate(-180deg)} to{transform:rotate(540deg) translateX(100px) rotate(-540deg)} }
@keyframes lm-orbit-o4 { from{transform:rotate(270deg) translateX(100px) rotate(-270deg)} to{transform:rotate(630deg) translateX(100px) rotate(-630deg)} }
@keyframes lm-orbit-xx1 { from{transform:rotate(0deg) translateX(128px) rotate(0deg)} to{transform:rotate(-360deg) translateX(128px) rotate(360deg)} }
@keyframes lm-orbit-xx2 { from{transform:rotate(180deg) translateX(128px) rotate(-180deg)} to{transform:rotate(-180deg) translateX(128px) rotate(180deg)} }
@keyframes lm-ripple { 0%{transform:scale(.8);opacity:.7} 100%{transform:scale(3.2);opacity:0} }
@keyframes lm-ripple2 { 0%{transform:scale(.8);opacity:.5} 100%{transform:scale(4.8);opacity:0} }
@keyframes lm-ripple3 { 0%{transform:scale(.8);opacity:.3} 100%{transform:scale(6.5);opacity:0} }
@keyframes lm-twinkle { 0%,100%{opacity:.05;transform:scale(.4)} 50%{opacity:1;transform:scale(1.6)} }
@keyframes lm-data { 0%{transform:translateY(-100%);opacity:0} 4%{opacity:1} 92%{opacity:.5} 100%{transform:translateY(100vh);opacity:0} }
@keyframes lm-fadein { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
@keyframes lm-glitch { 0%,85%,100%{transform:translate(0)} 86%{transform:translate(-5px,2px)} 87%{transform:translate(5px,-2px)} 88%{transform:translate(-4px,4px)} 89%{transform:translate(4px,-4px)} 90%{transform:translate(0)} }
@keyframes lm-glitch2 { 0%,91%,100%{transform:translate(0) skewX(0)} 92%{transform:translate(6px,0) skewX(5deg)} 93%{transform:translate(-6px,0) skewX(-5deg)} 94%{transform:translate(0)} }
@keyframes lm-glitch-before { 0%,87%,100%{opacity:0;transform:translate(0)} 88%{opacity:1;transform:translate(-7px,2px)} 89%{transform:translate(7px,-2px)} 90%{opacity:0} }
@keyframes lm-glitch-after { 0%,82%,100%{opacity:0;transform:translate(0)} 83%{opacity:1;transform:translate(7px,-3px)} 84%{transform:translate(-6px,3px)} 85%{opacity:0} }
@keyframes lm-badge-pulse { 0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,.6)} 50%{box-shadow:0 0 0 8px rgba(34,197,94,.06)} }
@keyframes lm-icon-glow { 0%,100%{filter:drop-shadow(0 0 6px rgba(248,113,113,.9))} 50%{filter:drop-shadow(0 0 22px rgba(248,113,113,1)) drop-shadow(0 0 44px rgba(239,68,68,.8)) drop-shadow(0 0 70px rgba(239,68,68,.4))} }
@keyframes lm-neon-flash { 0%,18%,22%,53%,57%,100%{box-shadow:0 0 10px rgba(239,68,68,.7),0 0 25px rgba(239,68,68,.5),0 0 50px rgba(239,68,68,.3),0 0 100px rgba(239,68,68,.15)} 19%,21%{box-shadow:none} 54%,56%{box-shadow:0 0 4px rgba(239,68,68,.2)} }
@keyframes lm-hue-spin { from{filter:hue-rotate(0deg)} to{filter:hue-rotate(360deg)} }
@keyframes lm-blink { 0%,100%{opacity:1} 50%{opacity:0} }
@keyframes lm-hex-count { 0%{color:#ef4444} 16%{color:#f97316} 32%{color:#fbbf24} 48%{color:#22c55e} 64%{color:#22d3ee} 80%{color:#a78bfa} 100%{color:#ef4444} }
@keyframes lm-danger-flash { 0%,100%{background:rgba(239,68,68,.07);border-color:rgba(239,68,68,.2)} 50%{background:rgba(239,68,68,.22);border-color:rgba(239,68,68,.65)} }
@keyframes lm-wave-1 { 0%,100%{height:4px} 50%{height:22px} }
@keyframes lm-wave-2 { 0%,100%{height:11px} 50%{height:20px} }
@keyframes lm-wave-3 { 0%,100%{height:6px} 50%{height:24px} }
@keyframes lm-wave-4 { 0%,100%{height:16px} 50%{height:7px} }
@keyframes lm-wave-5 { 0%,100%{height:3px} 50%{height:21px} }
@keyframes lm-wave-6 { 0%,100%{height:18px} 50%{height:5px} }
@keyframes lm-wave-7 { 0%,100%{height:9px} 50%{height:23px} }
@keyframes lm-wave-8 { 0%,100%{height:14px} 50%{height:4px} }
@keyframes lm-scanline-move { 0%{background-position:0 0} 100%{background-position:0 80px} }
@keyframes lm-lightning { 0%,7%,100%{opacity:0} 3%{opacity:1} 5%{opacity:.2} 6%{opacity:.8} }
@keyframes lm-lightning2 { 0%,95%,100%{opacity:0} 96%{opacity:.9} 97%{opacity:0} 98%{opacity:.6} }
@keyframes lm-corner-pulse { 0%,100%{opacity:.6} 50%{opacity:1;box-shadow:0 0 8px currentColor} }

/* ── Backdrop ── */
#logout-backdrop {
  position:absolute; inset:0;
  background:rgba(1,2,10,.97);
  backdrop-filter:blur(26px) saturate(2.5) contrast(1.1);
  overflow:hidden;
}
#logout-backdrop::before {
  content:''; position:absolute; inset:0; pointer-events:none;
  background-image:radial-gradient(rgba(99,102,241,.1) 1px, transparent 1px);
  background-size:22px 22px;
  mask-image:radial-gradient(ellipse 90% 90% at 50% 50%, black 20%, transparent 100%);
  -webkit-mask-image:radial-gradient(ellipse 90% 90% at 50% 50%, black 20%, transparent 100%);
  animation:lm-hue-spin 6s linear infinite;
}
#logout-backdrop::after {
  content:''; position:absolute; inset:0; pointer-events:none;
  background:repeating-linear-gradient(0deg,transparent,transparent 3px,rgba(0,0,0,.18) 3px,rgba(0,0,0,.18) 4px);
  animation:lm-scanline-move 1.8s linear infinite;
}
.lm-bg-orb { position:absolute; border-radius:50%; pointer-events:none; filter:blur(90px); }
.lm-data-line { position:absolute; width:1px; pointer-events:none; background:linear-gradient(180deg,transparent,rgba(99,102,241,.7) 30%,rgba(168,85,247,.5) 70%,transparent); animation:lm-data linear infinite; opacity:0; }
.lm-bd-scan { position:absolute; left:0; right:0; height:2px; pointer-events:none; background:linear-gradient(90deg,transparent,rgba(99,102,241,.35) 30%,rgba(168,85,247,.55) 50%,rgba(99,102,241,.35) 70%,transparent); filter:blur(2px); }
.lm-bd-scan-x { position:absolute; top:0; bottom:0; width:3px; pointer-events:none; background:linear-gradient(180deg,transparent,rgba(239,68,68,.3) 30%,rgba(249,115,22,.45) 50%,rgba(239,68,68,.3) 70%,transparent); filter:blur(2px); animation:lm-scan-h 10s linear infinite; left:0; }
.lm-star { position:absolute; border-radius:50%; background:#fff; pointer-events:none; animation:lm-twinkle ease-in-out infinite; }
.lm-lightning { position:absolute; pointer-events:none; width:2px; background:linear-gradient(180deg,transparent,rgba(255,255,255,.95),rgba(99,102,241,.85),transparent); filter:blur(1px); opacity:0; }

/* ── Rainbow border wrapper ── */
#lm-border-wrap {
  position:relative; z-index:1;
  width:100%; max-width:464px; margin:0 16px;
  border-radius:28px; padding:2px;
  background:linear-gradient(135deg,#6366f1,#8b5cf6,#ec4899,#ef4444,#f97316,#fbbf24,#22d3ee,#a3e635,#6366f1);
  background-size:400% 400%;
  animation:lm-bar-flow 1.6s linear infinite;
  box-shadow:
    0 0 35px rgba(99,102,241,.6),
    0 0 70px rgba(239,68,68,.35),
    0 0 110px rgba(168,85,247,.25),
    0 0 160px rgba(34,211,238,.12),
    0 70px 160px rgba(0,0,0,.95);
}
#lm-border-wrap::before {
  content:''; position:absolute; inset:-8px; border-radius:36px;
  background:inherit; filter:blur(26px); opacity:.75; z-index:-1;
  animation:lm-hue-spin 3s linear infinite;
}
#lm-border-wrap::after {
  content:''; position:absolute; inset:-2px; border-radius:30px;
  border:1px solid rgba(255,255,255,.1); z-index:2; pointer-events:none;
}

/* ── Dialog card ── */
#logout-dialog {
  background:linear-gradient(150deg,#04061a 0%,#07091f 35%,#050210 65%,#080410 100%);
  border-radius:26px; overflow:hidden; position:relative;
  transform:scale(.82) translateY(36px); opacity:0;
  transition:transform .44s cubic-bezier(.34,1.56,.64,1), opacity .32s ease;
}
#logout-dialog::before {
  content:''; position:absolute; inset:0; pointer-events:none; z-index:2;
  background:repeating-linear-gradient(0deg,transparent,transparent 3px,rgba(0,0,0,.2) 3px,rgba(0,0,0,.2) 4px);
  animation:lm-scanline-move 1.4s linear infinite; opacity:.55;
}
.lm-card-orb { position:absolute; border-radius:50%; pointer-events:none; }
.lm-card-scan  { position:absolute; left:0; right:0; height:260px; pointer-events:none; background:linear-gradient(180deg,transparent,rgba(99,102,241,.045) 50%,transparent); animation:lm-scan-v 3.8s ease-in-out infinite; z-index:1; }
.lm-card-scan2 { position:absolute; left:0; right:0; height:210px; pointer-events:none; background:linear-gradient(180deg,transparent,rgba(239,68,68,.035) 50%,transparent); animation:lm-scan-v2 5s ease-in-out infinite 1.2s; z-index:1; }
.lm-card-scan-x { position:absolute; top:0; bottom:0; width:220px; pointer-events:none; background:linear-gradient(90deg,transparent,rgba(168,85,247,.04) 50%,transparent); animation:lm-scan-h 7s ease-in-out infinite .6s; z-index:1; }
.lm-corner { position:absolute; width:18px; height:18px; pointer-events:none; z-index:6; animation:lm-corner-pulse 2s ease-in-out infinite; }
.lm-corner.tl { top:14px; left:14px; border-top:2px solid rgba(99,102,241,.9); border-left:2px solid rgba(99,102,241,.9); border-radius:3px 0 0 0; }
.lm-corner.tr { top:14px; right:14px; border-top:2px solid rgba(239,68,68,.9); border-right:2px solid rgba(239,68,68,.9); border-radius:0 3px 0 0; animation-delay:.5s; }
.lm-corner.bl { bottom:14px; left:14px; border-bottom:2px solid rgba(168,85,247,.9); border-left:2px solid rgba(168,85,247,.9); border-radius:0 0 0 3px; animation-delay:1s; }
.lm-corner.br { bottom:14px; right:14px; border-bottom:2px solid rgba(251,191,36,.9); border-right:2px solid rgba(251,191,36,.9); border-radius:0 0 3px 0; animation-delay:1.5s; }

/* ── Icon zone ── */
.lm-icon-zone { position:relative; width:170px; height:170px; display:flex; align-items:center; justify-content:center; animation:lm-float 3.4s ease-in-out infinite; }
.lm-ring { position:absolute; border-radius:50%; }
.lm-particle   { position:absolute; width:10px; height:10px; border-radius:50%; background:radial-gradient(circle,#fca5a5,#ef4444); box-shadow:0 0 12px rgba(239,68,68,1),0 0 24px rgba(239,68,68,.5); }
.lm-particle.p1 { animation:lm-orbit1 2s linear infinite; }
.lm-particle.p2 { animation:lm-orbit2 2s linear infinite; }
.lm-particle.p3 { animation:lm-orbit3 2s linear infinite; }
.lm-pm { position:absolute; width:8px; height:8px; border-radius:50%; }
.lm-pm1 { background:radial-gradient(circle,#c4b5fd,#8b5cf6); box-shadow:0 0 10px rgba(139,92,246,1); animation:lm-orbit-m1 3.2s linear infinite; }
.lm-pm2 { background:radial-gradient(circle,#fde68a,#fbbf24); box-shadow:0 0 10px rgba(251,191,36,1); animation:lm-orbit-m2 3.2s linear infinite; }
.lm-pm3 { background:radial-gradient(circle,#6ee7b7,#10b981); box-shadow:0 0 10px rgba(16,185,129,1); animation:lm-orbit-m3 3.2s linear infinite; }
.lm-po { position:absolute; width:7px; height:7px; border-radius:50%; }
.lm-po1 { background:#22d3ee; box-shadow:0 0 9px rgba(34,211,238,1); animation:lm-orbit-o1 5s linear infinite; }
.lm-po2 { background:#f472b6; box-shadow:0 0 9px rgba(244,114,182,1); animation:lm-orbit-o2 5s linear infinite; }
.lm-po3 { background:#fb923c; box-shadow:0 0 9px rgba(251,146,60,1); animation:lm-orbit-o3 5s linear infinite; }
.lm-po4 { background:#a3e635; box-shadow:0 0 9px rgba(163,230,53,1); animation:lm-orbit-o4 5s linear infinite; }
.lm-pxx { position:absolute; width:5px; height:5px; border-radius:50%; }
.lm-pxx1 { background:#fff; box-shadow:0 0 7px rgba(255,255,255,.95); animation:lm-orbit-xx1 8s linear infinite; }
.lm-pxx2 { background:#ef4444; box-shadow:0 0 7px rgba(239,68,68,.95); animation:lm-orbit-xx2 8s linear infinite; }
.lm-ripple-ring { position:absolute; border-radius:50%; border:1.5px solid rgba(239,68,68,.6); pointer-events:none; width:54px; height:54px; }
.lm-ripple-ring.r1 { animation:lm-ripple 2.2s ease-out infinite; }
.lm-ripple-ring.r2 { animation:lm-ripple2 2.2s ease-out infinite .74s; }
.lm-ripple-ring.r3 { animation:lm-ripple3 2.2s ease-out infinite 1.48s; }

/* ── Gradient title + glitch ── */
.lm-grad-text {
  background:linear-gradient(90deg,#e2e8f0 0%,#c7d2fe 12%,#f9a8d4 26%,#fca5a5 40%,#fde68a 54%,#6ee7b7 68%,#a5f3fc 82%,#e2e8f0 100%);
  background-size:300% 100%; -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
  animation:lm-shimmer 1.8s linear infinite;
}
.lm-title-glitch { position:relative; }
.lm-title-glitch::before, .lm-title-glitch::after {
  content:attr(data-text); position:absolute; top:0; left:0; width:100%;
  background:linear-gradient(90deg,#e2e8f0 0%,#c7d2fe 12%,#f9a8d4 26%,#fca5a5 40%,#fde68a 54%,#6ee7b7 68%,#a5f3fc 82%,#e2e8f0 100%);
  background-size:300% 100%; -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
}
.lm-title-glitch::before { animation:lm-glitch-before 3.5s linear infinite, lm-shimmer 1.8s linear infinite; clip-path:polygon(0 15%,100% 15%,100% 40%,0 40%); }
.lm-title-glitch::after  { animation:lm-glitch-after 3.1s linear infinite .5s, lm-shimmer 1.8s linear infinite .7s; clip-path:polygon(0 62%,100% 62%,100% 85%,0 85%); filter:hue-rotate(130deg); }

/* ── User strip ── */
.lm-user-strip { position:relative; overflow:hidden; border-radius:16px; }
.lm-user-strip-border {
  position:absolute; inset:0; border-radius:16px;
  background:linear-gradient(135deg,rgba(99,102,241,.4),rgba(168,85,247,.3),rgba(236,72,153,.35),rgba(99,102,241,.4));
  background-size:300% 300%; animation:lm-bar-flow 2.5s linear infinite;
  -webkit-mask:linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude; padding:1px;
}
.lm-user-strip-inner {
  position:relative; z-index:1;
  background:linear-gradient(135deg,rgba(255,255,255,.05) 0%,rgba(99,102,241,.07) 50%,rgba(255,255,255,.03) 100%);
  border-radius:15px; backdrop-filter:blur(10px);
  display:flex; align-items:center; gap:14px; padding:14px 16px; margin-bottom:14px;
}
.lm-user-sweep  { position:absolute; top:0; bottom:0; width:50%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.09),transparent); animation:lm-scan-h 1.9s linear infinite; pointer-events:none; }
.lm-user-sweep2 { position:absolute; top:0; bottom:0; width:40%; background:linear-gradient(90deg,transparent,rgba(239,68,68,.06),transparent); animation:lm-scan-h2 2.6s linear infinite .6s; pointer-events:none; }

/* ── Badges ── */
.lm-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 9px; border-radius:20px; font-size:.62rem; font-weight:700; letter-spacing:.07em; text-transform:uppercase; }
.lm-badge-aes  { background:rgba(99,102,241,.14); border:1px solid rgba(99,102,241,.4);  color:#a5b4fc; }
.lm-badge-sess { background:rgba(34,197,94,.11);  border:1px solid rgba(34,197,94,.36); color:#86efac; animation:lm-badge-pulse 1.3s ease-in-out infinite; }
.lm-badge-ssl  { background:rgba(34,211,238,.09); border:1px solid rgba(34,211,238,.32); color:#67e8f9; }
.lm-badge-2fa  { background:rgba(251,191,36,.09); border:1px solid rgba(251,191,36,.32); color:#fde68a; }
.lm-badge-vpn  { background:rgba(163,230,53,.08); border:1px solid rgba(163,230,53,.28); color:#bef264; }

/* ── Waveform ── */
.lm-waveform { display:flex; align-items:center; gap:2.5px; height:28px; }
.lm-wave-bar { width:3px; border-radius:2px; }

/* ── Threat meter ── */
.lm-threat-wrap { display:flex; align-items:center; gap:8px; padding:7px 12px; border-radius:9px; animation:lm-danger-flash 1.2s ease-in-out infinite; }
.lm-threat-bar  { flex:1; height:6px; background:rgba(255,255,255,.07); border-radius:3px; overflow:hidden; }
.lm-threat-fill { height:100%; width:94%; border-radius:3px; background:linear-gradient(90deg,#fbbf24,#f97316,#ef4444,#dc2626); background-size:200% 100%; animation:lm-bar-flow .9s linear infinite; }

/* ── Warning strip ── */
.lm-warn { position:relative; overflow:hidden; display:flex; align-items:center; gap:10px; padding:12px 15px; border-radius:12px; margin-bottom:14px; animation:lm-danger-flash 1s ease-in-out infinite; }
.lm-warn::before { content:''; position:absolute; top:0; left:-80%; width:50%; height:100%; background:linear-gradient(90deg,transparent,rgba(239,68,68,.25),transparent); animation:lm-scan-h 1.3s linear infinite; }
.lm-warn::after  { content:''; position:absolute; top:0; right:-80%; width:50%; height:100%; background:linear-gradient(90deg,transparent,rgba(239,68,68,.18),transparent); animation:lm-scan-h2 1.8s linear infinite .3s; }

/* ── Sign Out button ── */
.lm-signout-btn {
  position:relative; overflow:hidden; flex:1.4;
  padding:.92rem 1rem; border:none; border-radius:14px;
  background:linear-gradient(135deg,#7f1d1d,#dc2626,#ef4444,#f97316,#fbbf24,#ef4444);
  background-size:400% 100%;
  color:#fff; font-size:.95rem; font-weight:800; letter-spacing:.05em;
  cursor:pointer; display:flex; align-items:center; justify-content:center; gap:9px;
  text-shadow:0 1px 8px rgba(0,0,0,.5);
  animation:lm-bar-flow 1.3s linear infinite, lm-neon-flash 1.8s ease-in-out infinite;
  box-shadow:0 0 0 1px rgba(239,68,68,.7),0 8px 40px rgba(239,68,68,.75),0 0 100px rgba(239,68,68,.3),inset 0 1px 0 rgba(255,255,255,.22);
  transition:transform .18s,box-shadow .18s;
}
.lm-signout-btn::before { content:''; position:absolute; top:0; left:-80%; width:60%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.4),transparent); animation:lm-scan-h 1s linear infinite; }
.lm-signout-btn::after  { content:''; position:absolute; inset:-5px; border-radius:19px; background:linear-gradient(135deg,#ef4444,#f97316,#fbbf24,#ef4444); background-size:400% 100%; animation:lm-bar-flow 1.3s linear infinite; z-index:-1; filter:blur(16px); opacity:.85; }
.lm-signout-btn:hover { transform:translateY(-3px) scale(1.03); box-shadow:0 0 0 2px rgba(239,68,68,1),0 16px 60px rgba(239,68,68,.95),0 0 130px rgba(239,68,68,.5),inset 0 1px 0 rgba(255,255,255,.28); }
.lm-signout-btn:active { transform:translateY(0) scale(.98); }

/* ── Stay button ── */
.lm-stay-btn {
  position:relative; overflow:hidden; flex:1;
  padding:.92rem 1rem; border:1px solid rgba(255,255,255,.12); border-radius:14px;
  background:rgba(255,255,255,.05); color:rgba(255,255,255,.65);
  font-size:.875rem; font-weight:600; cursor:pointer;
  transition:background .18s,border-color .18s,color .18s,box-shadow .18s;
  backdrop-filter:blur(8px);
}
.lm-stay-btn::before { content:''; position:absolute; top:0; left:-80%; width:50%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.08),transparent); animation:lm-scan-h 3.2s linear infinite; }
.lm-stay-btn:hover { background:rgba(99,102,241,.13); border-color:rgba(99,102,241,.42); color:rgba(255,255,255,.95); box-shadow:0 0 30px rgba(99,102,241,.28),inset 0 1px 0 rgba(255,255,255,.07); }

/* ── Stagger ── */
.lm-s1{animation:lm-fadein .32s ease .06s both}
.lm-s2{animation:lm-fadein .32s ease .14s both}
.lm-s3{animation:lm-fadein .32s ease .22s both}
.lm-s4{animation:lm-fadein .32s ease .30s both}
.lm-s5{animation:lm-fadein .32s ease .38s both}
.lm-s6{animation:lm-fadein .32s ease .46s both}
.lm-s7{animation:lm-fadein .32s ease .54s both}
.lm-s8{animation:lm-fadein .32s ease .62s both}
</style>

<div id="logout-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;">

  {{-- ═══ BACKDROP ═══ --}}
  <div id="logout-backdrop" onclick="closeLogoutModal()">

    {{-- Vertical scanning beams --}}
    <div class="lm-bd-scan" style="animation:lm-scan-v 4.5s linear infinite;"></div>
    <div class="lm-bd-scan" style="animation:lm-scan-v 6.5s linear infinite 1.8s;opacity:.45;"></div>
    <div class="lm-bd-scan" style="animation:lm-scan-v2 5.5s linear infinite .9s;background:linear-gradient(90deg,transparent,rgba(239,68,68,.25) 50%,transparent);"></div>
    {{-- Horizontal beams --}}
    <div class="lm-bd-scan-x"></div>
    <div class="lm-bd-scan-x" style="animation:lm-scan-h2 11s linear infinite 4s;background:linear-gradient(180deg,transparent,rgba(99,102,241,.28) 50%,transparent);"></div>

    {{-- 8 massive ambient orbs --}}
    <div class="lm-bg-orb" style="width:620px;height:620px;background:radial-gradient(circle,rgba(99,102,241,.2) 0%,transparent 70%);top:-160px;left:-160px;animation:lm-orb1 9s ease-in-out infinite;"></div>
    <div class="lm-bg-orb" style="width:570px;height:570px;background:radial-gradient(circle,rgba(239,68,68,.18) 0%,transparent 70%);bottom:-130px;right:-130px;animation:lm-orb2 11s ease-in-out infinite;"></div>
    <div class="lm-bg-orb" style="width:420px;height:420px;background:radial-gradient(circle,rgba(168,85,247,.15) 0%,transparent 70%);top:50%;left:58%;animation:lm-orb3 7s ease-in-out infinite;"></div>
    <div class="lm-bg-orb" style="width:370px;height:370px;background:radial-gradient(circle,rgba(34,211,238,.13) 0%,transparent 70%);top:12%;right:2%;animation:lm-orb4 8s ease-in-out infinite;"></div>
    <div class="lm-bg-orb" style="width:330px;height:330px;background:radial-gradient(circle,rgba(251,191,36,.11) 0%,transparent 70%);bottom:15%;left:2%;animation:lm-orb5 6.5s ease-in-out infinite;"></div>
    <div class="lm-bg-orb" style="width:290px;height:290px;background:radial-gradient(circle,rgba(244,114,182,.11) 0%,transparent 70%);top:32%;left:12%;animation:lm-orb1 10s ease-in-out infinite 2.5s;"></div>
    <div class="lm-bg-orb" style="width:270px;height:270px;background:radial-gradient(circle,rgba(16,185,129,.1) 0%,transparent 70%);bottom:32%;right:18%;animation:lm-orb2 8.5s ease-in-out infinite 1.2s;"></div>
    <div class="lm-bg-orb" style="width:210px;height:210px;background:radial-gradient(circle,rgba(163,230,53,.08) 0%,transparent 70%);top:8%;left:38%;animation:lm-orb3 12s ease-in-out infinite 3.5s;"></div>

    {{-- 13 data stream lines --}}
    <div class="lm-data-line" style="left:4%;height:38%;animation-duration:3.5s;animation-delay:0s;"></div>
    <div class="lm-data-line" style="left:11%;height:26%;animation-duration:5s;animation-delay:.5s;"></div>
    <div class="lm-data-line" style="left:18%;height:42%;animation-duration:4.3s;animation-delay:1s;"></div>
    <div class="lm-data-line" style="left:29%;height:22%;animation-duration:6s;animation-delay:.2s;"></div>
    <div class="lm-data-line" style="left:43%;height:30%;animation-duration:6.8s;animation-delay:2s;"></div>
    <div class="lm-data-line" style="left:56%;height:34%;animation-duration:3.9s;animation-delay:.7s;"></div>
    <div class="lm-data-line" style="left:67%;height:24%;animation-duration:5.5s;animation-delay:1.4s;"></div>
    <div class="lm-data-line" style="left:74%;height:40%;animation-duration:3.2s;animation-delay:.1s;"></div>
    <div class="lm-data-line" style="left:82%;height:28%;animation-duration:6.4s;animation-delay:1.7s;"></div>
    <div class="lm-data-line" style="left:90%;height:32%;animation-duration:4.1s;animation-delay:.4s;"></div>
    <div class="lm-data-line" style="left:96%;height:20%;animation-duration:5.2s;animation-delay:2.6s;"></div>
    <div class="lm-data-line" style="left:7%;height:44%;animation-duration:7.5s;animation-delay:3s;width:2px;"></div>
    <div class="lm-data-line" style="left:61%;height:16%;animation-duration:3s;animation-delay:1.2s;"></div>

    {{-- Lightning bolts --}}
    <div class="lm-lightning" style="left:17%;top:4%;height:42%;animation:lm-lightning 6.5s linear infinite .8s;transform:rotate(7deg);"></div>
    <div class="lm-lightning" style="left:74%;top:8%;height:32%;animation:lm-lightning2 10s linear infinite 3.5s;transform:rotate(-4deg);"></div>
    <div class="lm-lightning" style="left:41%;top:52%;height:38%;animation:lm-lightning 8.5s linear infinite 1.8s;transform:rotate(3deg);"></div>

    {{-- 15 twinkling stars --}}
    <div class="lm-star" style="width:2px;height:2px;top:7%;left:11%;animation-duration:1.7s;"></div>
    <div class="lm-star" style="width:3px;height:3px;top:14%;left:77%;animation-duration:2.8s;animation-delay:.5s;"></div>
    <div class="lm-star" style="width:2px;height:2px;top:24%;left:4%;animation-duration:2.2s;animation-delay:.9s;"></div>
    <div class="lm-star" style="width:2px;height:2px;top:37%;left:93%;animation-duration:1.5s;animation-delay:.2s;"></div>
    <div class="lm-star" style="width:3px;height:3px;top:51%;left:87%;animation-duration:3s;animation-delay:.8s;"></div>
    <div class="lm-star" style="width:2px;height:2px;top:64%;left:1%;animation-duration:2.6s;animation-delay:1.3s;"></div>
    <div class="lm-star" style="width:2px;height:2px;top:73%;left:65%;animation-duration:2s;animation-delay:.1s;"></div>
    <div class="lm-star" style="width:3px;height:3px;top:84%;left:32%;animation-duration:1.4s;animation-delay:.6s;"></div>
    <div class="lm-star" style="width:2px;height:2px;top:91%;left:79%;animation-duration:2.4s;animation-delay:1s;"></div>
    <div class="lm-star" style="width:2px;height:2px;top:3%;left:54%;animation-duration:3.2s;animation-delay:.3s;"></div>
    <div class="lm-star" style="width:3px;height:3px;top:47%;left:21%;animation-duration:1.8s;animation-delay:1.5s;"></div>
    <div class="lm-star" style="width:2px;height:2px;top:29%;left:47%;animation-duration:2.5s;animation-delay:.55s;"></div>
    <div class="lm-star" style="width:2px;height:2px;top:69%;left:49%;animation-duration:2.1s;animation-delay:1.2s;"></div>
    <div class="lm-star" style="width:3px;height:3px;top:17%;left:37%;animation-duration:3.4s;animation-delay:.75s;"></div>
    <div class="lm-star" style="width:2px;height:2px;top:57%;left:14%;animation-duration:1.6s;animation-delay:1.7s;"></div>
  </div>

  {{-- ═══ RAINBOW BORDER WRAPPER ═══ --}}
  <div id="lm-border-wrap">
    {{-- ═══ DIALOG ═══ --}}
    <div id="logout-dialog">

      {{-- Corner brackets --}}
      <div class="lm-corner tl"></div>
      <div class="lm-corner tr"></div>
      <div class="lm-corner bl"></div>
      <div class="lm-corner br"></div>

      {{-- Card interior orbs --}}
      <div class="lm-card-orb" style="width:310px;height:310px;background:radial-gradient(circle,rgba(99,102,241,.3) 0%,transparent 70%);top:-110px;left:-110px;filter:blur(65px);animation:lm-orb1 6.5s ease-in-out infinite;"></div>
      <div class="lm-card-orb" style="width:270px;height:270px;background:radial-gradient(circle,rgba(239,68,68,.26) 0%,transparent 70%);bottom:-90px;right:-90px;filter:blur(58px);animation:lm-orb2 8.5s ease-in-out infinite;"></div>
      <div class="lm-card-orb" style="width:210px;height:210px;background:radial-gradient(circle,rgba(168,85,247,.22) 0%,transparent 70%);top:38%;left:43%;filter:blur(52px);animation:lm-orb3 5.5s ease-in-out infinite;"></div>
      <div class="lm-card-orb" style="width:180px;height:180px;background:radial-gradient(circle,rgba(34,211,238,.16) 0%,transparent 70%);top:6%;right:6%;filter:blur(46px);animation:lm-orb4 7.5s ease-in-out infinite;"></div>
      <div class="lm-card-orb" style="width:150px;height:150px;background:radial-gradient(circle,rgba(251,191,36,.14) 0%,transparent 70%);bottom:18%;left:8%;filter:blur(42px);animation:lm-orb5 6.5s ease-in-out infinite .8s;"></div>

      {{-- Scanning beams --}}
      <div class="lm-card-scan"></div>
      <div class="lm-card-scan2"></div>
      <div class="lm-card-scan-x"></div>

      {{-- Top rainbow accent bar --}}
      <div style="position:relative;z-index:5;height:6px;background:linear-gradient(90deg,#6366f1,#8b5cf6,#ec4899,#ef4444,#f97316,#fbbf24,#22d3ee,#a3e635,#6366f1);background-size:400% 100%;animation:lm-bar-flow 1.2s linear infinite;box-shadow:0 2px 24px rgba(99,102,241,.7),0 0 50px rgba(239,68,68,.35);"></div>

      {{-- Sub-bar --}}
      <div style="position:relative;z-index:4;background:rgba(99,102,241,.09);border-bottom:1px solid rgba(255,255,255,.05);padding:5px 18px;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:.57rem;letter-spacing:.14em;text-transform:uppercase;font-family:monospace;animation:lm-hex-count 1.8s linear infinite;">SYS::AUTH v4.0</span>
        <div style="display:flex;align-items:center;gap:10px;">
          <span style="font-size:.57rem;letter-spacing:.1em;color:rgba(239,68,68,.8);font-family:monospace;animation:lm-blink .7s ease-in-out infinite;">⚠ TERMINATING</span>
          <span style="font-size:.57rem;letter-spacing:.08em;color:rgba(34,197,94,.55);font-family:monospace;display:flex;align-items:center;gap:4px;">
            <span style="width:5px;height:5px;border-radius:50%;background:#22c55e;display:inline-block;animation:lm-blink .9s ease-in-out infinite;box-shadow:0 0 8px rgba(34,197,94,.9);"></span>
            SESSION LIVE
          </span>
        </div>
      </div>

      {{-- Body --}}
      <div style="position:relative;z-index:3;padding:24px 24px 20px;">

        {{-- ── ICON ZONE ── --}}
        <div class="lm-s1" style="display:flex;flex-direction:column;align-items:center;margin-bottom:20px;">
          <div class="lm-icon-zone">
            {{-- 8 rings at varying speeds/directions --}}
            <div class="lm-ring" style="inset:-12px;border:1.5px dashed rgba(99,102,241,.22);animation:lm-spin-cw 20s linear infinite;"></div>
            <div class="lm-ring" style="inset:0;border:2px solid rgba(99,102,241,.38);animation:lm-spin-cw 11s linear infinite;"></div>
            <div class="lm-ring" style="inset:8px;border:1px solid rgba(239,68,68,.28);animation:lm-spin-ccw 6.5s linear infinite;"></div>
            <div class="lm-ring" style="inset:16px;border:2px dotted rgba(168,85,247,.38);animation:lm-spin-cw 4.5s linear infinite;"></div>
            <div class="lm-ring" style="inset:24px;border:1.5px solid rgba(34,211,238,.28);animation:lm-spin-ccw 3s linear infinite;"></div>
            <div class="lm-ring" style="inset:32px;border:1px solid rgba(251,191,36,.32);animation:lm-spin-cw 2.2s linear infinite;"></div>
            <div class="lm-ring" style="inset:40px;border:1.5px dashed rgba(244,114,182,.32);animation:lm-spin-ccw 1.8s linear infinite;"></div>
            <div class="lm-ring" style="inset:48px;border:1px solid rgba(163,230,53,.22);animation:lm-spin-cw 1.3s linear infinite;"></div>
            {{-- Inner particles (54px) --}}
            <div class="lm-particle p1"></div>
            <div class="lm-particle p2"></div>
            <div class="lm-particle p3"></div>
            {{-- Mid particles (76px) --}}
            <div class="lm-pm lm-pm1"></div>
            <div class="lm-pm lm-pm2"></div>
            <div class="lm-pm lm-pm3"></div>
            {{-- Outer particles (100px) --}}
            <div class="lm-po lm-po1"></div>
            <div class="lm-po lm-po2"></div>
            <div class="lm-po lm-po3"></div>
            <div class="lm-po lm-po4"></div>
            {{-- Extreme outer (128px) --}}
            <div class="lm-pxx lm-pxx1"></div>
            <div class="lm-pxx lm-pxx2"></div>
            {{-- 3 ripple rings --}}
            <div class="lm-ripple-ring r1"></div>
            <div class="lm-ripple-ring r2"></div>
            <div class="lm-ripple-ring r3"></div>
            {{-- Multi-layer aura --}}
            <div style="position:absolute;inset:30px;border-radius:50%;background:radial-gradient(circle,rgba(239,68,68,.6) 0%,rgba(168,85,247,.25) 50%,transparent 70%);animation:lm-breathe 1.8s ease-in-out infinite;filter:blur(16px);"></div>
            <div style="position:absolute;inset:38px;border-radius:50%;background:radial-gradient(circle,rgba(251,191,36,.3) 0%,transparent 70%);animation:lm-breathe 2.4s ease-in-out infinite .4s;filter:blur(8px);"></div>
            {{-- Icon container --}}
            <div style="position:relative;width:56px;height:56px;border-radius:17px;
                background:linear-gradient(135deg,rgba(239,68,68,.35) 0%,rgba(127,29,29,.55) 100%);
                border:1.5px solid rgba(239,68,68,.6);
                display:flex;align-items:center;justify-content:center;
                animation:lm-halo 2s ease-in-out infinite;
                box-shadow:inset 0 1px 0 rgba(255,255,255,.14),0 0 40px rgba(239,68,68,.5);">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="width:26px;height:26px;color:#f87171;animation:lm-icon-glow 1.8s ease-in-out infinite;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
              </svg>
            </div>
          </div>

          {{-- Heading --}}
          <div class="lm-s2" style="text-align:center;margin-top:14px;">
            <div style="font-size:.6rem;letter-spacing:.24em;text-transform:uppercase;font-family:monospace;margin-bottom:5px;animation:lm-glitch 3.5s ease-in-out infinite,lm-hex-count 2.5s linear infinite;">◈ TERMINATING SESSION ◈</div>
            <h2 style="margin:0 0 6px;font-size:1.44rem;font-weight:900;letter-spacing:-.02em;line-height:1.2;display:block;" class="lm-grad-text lm-title-glitch" data-text="Signing Out of EncryptEd?">
              Signing Out of EncryptEd?
            </h2>
            <p style="font-size:.8rem;color:rgba(148,163,184,.5);margin:0;line-height:1.65;max-width:300px;animation:lm-glitch2 5.5s ease-in-out infinite;">
              You'll be returned to the login page.<br>
              <span style="color:rgba(248,113,113,.6);animation:lm-blink 2.8s ease-in-out infinite;">Any unsaved work may be lost.</span>
            </p>
          </div>
        </div>

        {{-- ── 5 SECURITY BADGES ── --}}
        <div class="lm-s3" style="display:flex;align-items:center;justify-content:center;gap:5px;margin-bottom:10px;flex-wrap:wrap;">
          <span class="lm-badge lm-badge-aes">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:8px;height:8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            AES-256
          </span>
          <span class="lm-badge lm-badge-sess">
            <span style="width:5px;height:5px;border-radius:50%;background:#22c55e;display:inline-block;box-shadow:0 0 8px rgba(34,197,94,1);animation:lm-blink .65s ease-in-out infinite;"></span>
            SESSION LIVE
          </span>
          <span class="lm-badge lm-badge-ssl">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:8px;height:8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            SSL SECURED
          </span>
          <span class="lm-badge lm-badge-2fa">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:8px;height:8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
            2FA ACTIVE
          </span>
          <span class="lm-badge lm-badge-vpn">◉ VPN OK</span>
        </div>

        {{-- ── THREAT LEVEL BAR ── --}}
        <div class="lm-s3" style="margin-bottom:12px;">
          <div class="lm-threat-wrap">
            <span style="font-size:.58rem;font-weight:800;font-family:monospace;letter-spacing:.1em;color:rgba(239,68,68,.8);white-space:nowrap;animation:lm-blink .9s ease-in-out infinite;">THREAT LVL</span>
            <div class="lm-threat-bar"><div class="lm-threat-fill"></div></div>
            <span style="font-size:.58rem;font-weight:900;font-family:monospace;letter-spacing:.1em;color:#ef4444;white-space:nowrap;animation:lm-blink .45s ease-in-out infinite;">■ CRITICAL</span>
          </div>
        </div>

        {{-- ── WAVEFORM + HEX ── --}}
        <div class="lm-s4" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;padding:8px 12px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:10px;">
          <div class="lm-waveform">
            <div class="lm-wave-bar" style="animation:lm-wave-1 .65s ease-in-out infinite;background:linear-gradient(180deg,#ef4444,#f97316);"></div>
            <div class="lm-wave-bar" style="animation:lm-wave-2 .75s ease-in-out infinite .04s;background:linear-gradient(180deg,#f97316,#fbbf24);"></div>
            <div class="lm-wave-bar" style="animation:lm-wave-3 .6s ease-in-out infinite .08s;background:linear-gradient(180deg,#fbbf24,#a3e635);"></div>
            <div class="lm-wave-bar" style="animation:lm-wave-4 .85s ease-in-out infinite .12s;background:linear-gradient(180deg,#22d3ee,#6366f1);"></div>
            <div class="lm-wave-bar" style="animation:lm-wave-5 .68s ease-in-out infinite .16s;background:linear-gradient(180deg,#a78bfa,#ec4899);"></div>
            <div class="lm-wave-bar" style="animation:lm-wave-6 .8s ease-in-out infinite .2s;background:linear-gradient(180deg,#ec4899,#ef4444);"></div>
            <div class="lm-wave-bar" style="animation:lm-wave-7 .72s ease-in-out infinite .24s;background:linear-gradient(180deg,#ef4444,#a78bfa);"></div>
            <div class="lm-wave-bar" style="animation:lm-wave-8 .63s ease-in-out infinite .28s;background:linear-gradient(180deg,#22d3ee,#22c55e);"></div>
          </div>
          <div style="display:flex;flex-direction:column;align-items:flex-end;gap:2px;">
            <span style="font-size:.52rem;font-family:monospace;color:rgba(99,102,241,.5);letter-spacing:.08em;">TOKEN ID</span>
            <span style="font-size:.66rem;font-family:monospace;font-weight:700;animation:lm-hex-count 1s linear infinite;letter-spacing:.06em;">0xDEAD•F1RE</span>
          </div>
        </div>

        {{-- ── USER STRIP ── --}}
        <div class="lm-user-strip lm-s5" style="margin-bottom:12px;">
          <div class="lm-user-strip-border"></div>
          <div class="lm-user-strip-inner">
            <div class="lm-user-sweep"></div>
            <div class="lm-user-sweep2"></div>
            {{-- Avatar --}}
            <div style="position:relative;width:46px;height:46px;border-radius:12px;flex-shrink:0;background:linear-gradient(135deg,#312e81,#4f46e5,#6366f1);display:flex;align-items:center;justify-content:center;font-size:.88rem;font-weight:800;color:#fff;letter-spacing:-.5px;box-shadow:0 4px 18px rgba(99,102,241,.65);">
              {{ strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
              <div style="position:absolute;inset:-2px;border-radius:14px;border:1.5px solid rgba(99,102,241,.65);pointer-events:none;animation:lm-breathe 2.6s ease-in-out infinite;"></div>
            </div>
            <div style="min-width:0;flex:1;">
              <div style="font-size:.9rem;font-weight:700;color:rgba(241,245,249,.94);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:3px;">
                {{ auth()->user()->full_name ?? 'User' }}
              </div>
              <div style="font-size:.72rem;color:rgba(148,163,184,.5);display:flex;align-items:center;gap:5px;flex-wrap:wrap;">
                <span>{{ auth()->user()->email ?: '—' }}</span>
                <span style="width:3px;height:3px;border-radius:50%;background:rgba(148,163,184,.3);flex-shrink:0;"></span>
                <span style="padding:1px 6px;border-radius:8px;font-weight:700;font-size:.62rem;letter-spacing:.08em;text-transform:uppercase;background:rgba(167,139,250,.18);border:1px solid rgba(167,139,250,.38);color:rgba(196,181,253,.95);">
                  {{ auth()->user()->role_label ?? 'User' }}
                </span>
              </div>
            </div>
            <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:3px;">
              <div style="width:9px;height:9px;border-radius:50%;background:#22c55e;box-shadow:0 0 10px rgba(34,197,94,1),0 0 20px rgba(34,197,94,.4);animation:lm-badge-pulse 1.1s ease-in-out infinite;"></div>
              <span style="font-size:.54rem;color:rgba(134,239,172,.6);letter-spacing:.06em;text-transform:uppercase;font-family:monospace;">ONLINE</span>
            </div>
          </div>
        </div>

        {{-- ── WARNING ── --}}
        <div class="lm-warn lm-s6">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:#f87171;flex-shrink:0;animation:lm-icon-glow 1.4s ease-in-out infinite,lm-blink 1.8s ease-in-out infinite;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
          <span style="font-size:.74rem;color:rgba(252,165,165,.8);line-height:1.5;font-family:monospace;">
            <span style="color:rgba(252,165,165,.45);">[CRITICAL]</span> Active session will be <strong style="color:#fca5a5;animation:lm-blink 1.2s ease-in-out infinite;">terminated immediately</strong>. All tokens revoked. Data flushed.
          </span>
        </div>

        {{-- ── SYSTEM STATUS ROW ── --}}
        <div class="lm-s7" style="display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:18px;flex-wrap:wrap;">
          <span style="display:flex;align-items:center;gap:4px;font-size:.59rem;color:rgba(148,163,184,.42);font-family:monospace;letter-spacing:.05em;">
            <span style="width:5px;height:5px;border-radius:50%;background:#22c55e;box-shadow:0 0 6px rgba(34,197,94,.9);animation:lm-blink .85s ease-in-out infinite;"></span>
            SYS ONLINE
          </span>
          <span style="width:1px;height:10px;background:rgba(255,255,255,.08);"></span>
          <span style="display:flex;align-items:center;gap:4px;font-size:.59rem;color:rgba(148,163,184,.42);font-family:monospace;letter-spacing:.05em;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:8px;height:8px;color:rgba(34,211,238,.65);"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
            CONN SECURE
          </span>
          <span style="width:1px;height:10px;background:rgba(255,255,255,.08);"></span>
          <span style="display:flex;align-items:center;gap:4px;font-size:.59rem;font-family:monospace;letter-spacing:.05em;animation:lm-hex-count 1.8s linear infinite;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:8px;height:8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            TIMEOUT: MANUAL
          </span>
          <span style="width:1px;height:10px;background:rgba(255,255,255,.08);"></span>
          <span style="display:flex;align-items:center;gap:4px;font-size:.59rem;color:rgba(239,68,68,.6);font-family:monospace;letter-spacing:.05em;animation:lm-blink .55s ease-in-out infinite;">
            ▲ LOGOUT PENDING
          </span>
        </div>

        {{-- ── ACTIONS ── --}}
        <div class="lm-s8" style="display:flex;gap:10px;">
          <button onclick="closeLogoutModal()" type="button" class="lm-stay-btn">
            <span style="display:flex;align-items:center;gap:7px;justify-content:center;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
              Stay Signed In
            </span>
          </button>
          <button onclick="document.getElementById('logout-form').submit()" type="button" class="lm-signout-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" style="width:17px;height:17px;flex-shrink:0;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
            </svg>
            Sign Out
          </button>
        </div>

        {{-- Footer watermark --}}
        <div style="text-align:center;margin-top:14px;display:flex;align-items:center;justify-content:center;gap:5px;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:8px;height:8px;animation:lm-icon-glow 2.8s ease-in-out infinite;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
          <span style="font-size:.56rem;letter-spacing:.1em;text-transform:uppercase;font-family:monospace;animation:lm-hex-count 3.5s linear infinite;">EncryptEd · Secure Auth Gateway · AES-256 · v4.0</span>
        </div>

      </div>
    </div>
  </div>
</div>

{{-- Single logout form used by all triggers --}}
<form id="logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
  @csrf
</form>

{{-- ══════════════════════════════════════════════════════
     GLOBAL CONFIRM / ALERT MODAL — replaces confirm() & alert()
═══════════════════════════════════════════════════════ --}}
<div id="enc-confirm-modal" style="display:none;position:fixed;inset:0;z-index:10000;align-items:center;justify-content:center;">
  <div id="enc-confirm-backdrop" style="position:absolute;inset:0;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);"></div>
  <div id="enc-confirm-dialog" style="
      position:relative;z-index:1;
      background:#fff;border-radius:20px;
      width:100%;max-width:420px;margin:0 16px;
      box-shadow:0 24px 64px rgba(15,23,42,.18);
      overflow:hidden;
      transform:scale(.95) translateY(10px);
      opacity:0;
      transition:transform .22s cubic-bezier(.34,1.56,.64,1),opacity .18s ease;">
    <div id="enc-confirm-top" style="height:4px;background:linear-gradient(90deg,#7c3aed,#a78bfa);"></div>
    <div style="padding:28px 26px 24px;">
      <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:22px;">
        <div id="enc-confirm-icon-wrap" style="width:46px;height:46px;border-radius:13px;background:#fef3c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg id="enc-confirm-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:22px;height:22px;color:#d97706;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
        </div>
        <div style="flex:1;min-width:0;">
          <h3 id="enc-confirm-title" style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 5px;"></h3>
          <p id="enc-confirm-message" style="font-size:.875rem;color:#64748b;margin:0;line-height:1.55;"></p>
        </div>
      </div>
      <div id="enc-confirm-actions" style="display:flex;gap:10px;justify-content:flex-end;">
        <button id="enc-confirm-cancel" type="button"
          style="padding:.6rem 1.1rem;border:1px solid #e2e8f0;border-radius:9px;background:#fff;color:#374151;font-size:.875rem;font-weight:600;cursor:pointer;transition:background .15s,border-color .15s;"
          onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'"
          onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0'">
          Cancel
        </button>
        <button id="enc-confirm-ok" type="button"
          style="padding:.6rem 1.25rem;border:none;border-radius:9px;background:#7c3aed;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;transition:background .15s;"
          onmouseover="this.style.background='#6d28d9'"
          onmouseout="this.style.background=encConfirmOkColor||'#7c3aed'">
          Confirm
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  // ── Live clock ────────────────────────────────────────────────────────
  (function () {
    const el = document.getElementById('enc-clock');
    if (!el) return;
    function tick() {
      const now = new Date();
      el.textContent = now.toLocaleTimeString('en-PH', {
        hour: '2-digit', minute: '2-digit', second: '2-digit'
      });
    }
    tick();
    setInterval(tick, 1000);
  })();

  // ── Hamburger + sidebar toggle ────────────────────────────────────────
  (function () {
    const burger  = document.getElementById('enc-hamburger');
    const sidebar = document.getElementById('enc-sidebar');
    const overlay = document.getElementById('enc-sidebar-overlay');
    if (!burger || !sidebar) return;

    function openSidebar() {
      sidebar.classList.add('open');
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
      sidebar.classList.remove('open');
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }

    burger.addEventListener('click', function () {
      sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
    overlay.addEventListener('click', closeSidebar);

    // Close sidebar on nav link click (mobile UX)
    sidebar.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth <= 900) closeSidebar();
      });
    });
  })();

  // ── Notification dropdown ─────────────────────────────────────────────
  (function () {
    const bell     = document.getElementById('notif-bell');
    const dropdown = document.getElementById('notif-dropdown');
    const badge    = document.getElementById('notif-badge');
    if (!bell || !dropdown) return;

    bell.addEventListener('click', function (e) {
      e.stopPropagation();
      const open = dropdown.classList.toggle('open');
      bell.setAttribute('aria-expanded', open);
    });

    document.addEventListener('click', function (e) {
      if (!document.getElementById('notif-wrap').contains(e.target)) {
        dropdown.classList.remove('open');
        bell.setAttribute('aria-expanded', 'false');
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        dropdown.classList.remove('open');
        bell.setAttribute('aria-expanded', 'false');
      }
    });

    // Poll unread count every 30s and update badge
    function updateBadge() {
      fetch('{{ route("notifications.unread-count") }}')
        .then(r => r.json())
        .then(data => {
          if (!badge) return;
          if (data.count > 0) {
            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.style.display = 'flex';
          } else {
            badge.style.display = 'none';
          }
        })
        .catch(() => {});
    }
    setInterval(updateBadge, 30000);
    window.addEventListener('focus', updateBadge);
  })();

  // ── Logout modal ──────────────────────────────────────────────────────
  function openLogoutModal() {
    const modal  = document.getElementById('logout-modal');
    const dialog = document.getElementById('logout-dialog');
    modal.style.display = 'flex';
    // Trigger animation on next frame
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        dialog.style.transform = 'scale(1) translateY(0)';
        dialog.style.opacity   = '1';
      });
    });
    document.addEventListener('keydown', _escListener);
  }

  function closeLogoutModal() {
    const modal  = document.getElementById('logout-modal');
    const dialog = document.getElementById('logout-dialog');
    dialog.style.transform = 'scale(.95) translateY(10px)';
    dialog.style.opacity   = '0';
    setTimeout(function () { modal.style.display = 'none'; }, 200);
    document.removeEventListener('keydown', _escListener);
  }

  function _escListener(e) {
    if (e.key === 'Escape') closeLogoutModal();
  }

  // ── Global confirm / alert modal ─────────────────────────────────────
  let encConfirmOkColor = '#7c3aed';
  let _encConfirmResolve = null;

  function _encConfirmShow(message, opts) {
    opts = opts || {};
    const title       = opts.title       || 'Confirm Action';
    const confirmText = opts.confirmText || 'Confirm';
    const cancelText  = opts.cancelText  || null;
    const type        = opts.type        || 'warning'; // warning | danger | info | success

    const iconWrap = document.getElementById('enc-confirm-icon-wrap');
    const icon     = document.getElementById('enc-confirm-icon');
    const topBar   = document.getElementById('enc-confirm-top');
    const okBtn    = document.getElementById('enc-confirm-ok');
    const cancelBtn= document.getElementById('enc-confirm-cancel');

    const themes = {
      warning: { bg:'#fef3c7', color:'#d97706', bar:'linear-gradient(90deg,#f59e0b,#fbbf24)',
        btnBg:'#f59e0b', btnHover:'#d97706',
        icon:'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z' },
      danger:  { bg:'#fee2e2', color:'#dc2626', bar:'linear-gradient(90deg,#ef4444,#f87171)',
        btnBg:'#ef4444', btnHover:'#dc2626',
        icon:'M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374L10.051 3.378c.866-1.5 3.032-1.5 3.898 0L21.303 16.126zM12 15.75h.007v.008H12v-.008z' },
      info:    { bg:'#dbeafe', color:'#2563eb', bar:'linear-gradient(90deg,#3b82f6,#60a5fa)',
        btnBg:'#3b82f6', btnHover:'#2563eb',
        icon:'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z' },
      success: { bg:'#d1fae5', color:'#059669', bar:'linear-gradient(90deg,#10b981,#34d399)',
        btnBg:'#10b981', btnHover:'#059669',
        icon:'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    };
    const t = themes[type] || themes.warning;

    iconWrap.style.background = t.bg;
    icon.style.color = t.color;
    icon.querySelector('path').setAttribute('d', t.icon);
    topBar.style.background = t.bar;
    okBtn.textContent = confirmText;
    okBtn.style.background = t.btnBg;
    encConfirmOkColor = t.btnBg;
    okBtn.onmouseover = function(){ this.style.background = t.btnHover; };
    okBtn.onmouseout  = function(){ this.style.background = t.btnBg; };

    if (cancelText) {
      cancelBtn.textContent = cancelText;
      cancelBtn.style.display = '';
    } else {
      cancelBtn.style.display = 'none';
    }

    document.getElementById('enc-confirm-title').textContent   = title;
    document.getElementById('enc-confirm-message').textContent = message;

    const modal  = document.getElementById('enc-confirm-modal');
    const dialog = document.getElementById('enc-confirm-dialog');
    modal.style.display = 'flex';
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        dialog.style.transform = 'scale(1) translateY(0)';
        dialog.style.opacity   = '1';
      });
    });
  }

  function _encConfirmClose(result) {
    const modal  = document.getElementById('enc-confirm-modal');
    const dialog = document.getElementById('enc-confirm-dialog');
    dialog.style.transform = 'scale(.95) translateY(10px)';
    dialog.style.opacity   = '0';
    setTimeout(function () { modal.style.display = 'none'; }, 200);
    if (_encConfirmResolve) { _encConfirmResolve(result); _encConfirmResolve = null; }
  }

  document.getElementById('enc-confirm-ok').addEventListener('click', function () { _encConfirmClose(true); });
  document.getElementById('enc-confirm-cancel').addEventListener('click', function () { _encConfirmClose(false); });
  document.getElementById('enc-confirm-backdrop').addEventListener('click', function () { _encConfirmClose(false); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && document.getElementById('enc-confirm-modal').style.display === 'flex') {
      _encConfirmClose(false);
    }
  });

  window.encConfirm = function (message, opts) {
    return new Promise(function (resolve) {
      _encConfirmResolve = resolve;
      _encConfirmShow(message, opts);
    });
  };

  window.encAlert = function (message, opts) {
    opts = Object.assign({ type: 'info', title: 'Notice', confirmText: 'OK' }, opts || {});
    opts.cancelText = null;
    return window.encConfirm(message, opts);
  };

  // ── data-confirm interceptor — auto-wires any form/button ────────────
  document.addEventListener('submit', function (e) {
    const form = e.target;
    const msg  = form.getAttribute('data-confirm');
    if (!msg) return;
    e.preventDefault();
    const type  = form.getAttribute('data-confirm-type')  || 'warning';
    const title = form.getAttribute('data-confirm-title') || 'Confirm Action';
    const ok    = form.getAttribute('data-confirm-ok')    || 'Confirm';
    window.encConfirm(msg, { type: type, title: title, confirmText: ok }).then(function (confirmed) {
      if (confirmed) { form.removeAttribute('data-confirm'); form.submit(); }
    });
  }, true);

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('button[data-confirm], a[data-confirm]');
    if (!btn) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    const msg   = btn.getAttribute('data-confirm');
    const type  = btn.getAttribute('data-confirm-type')  || 'danger';
    const title = btn.getAttribute('data-confirm-title') || 'Confirm Action';
    const ok    = btn.getAttribute('data-confirm-ok')    || 'Confirm';
    window.encConfirm(msg, { type: type, title: title, confirmText: ok }).then(function (confirmed) {
      if (confirmed) {
        const formId = btn.getAttribute('form');
        const form   = formId ? document.getElementById(formId) : btn.closest('form');
        if (form) { form.removeAttribute('data-confirm'); form.submit(); }
      }
    });
  }, true);
</script>

@stack('scripts')

</body>
</html>
