{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@push('head')
<style>
/* ── Dashboard layout tokens ─────────────────────────── */
.adm-section-title {
  font-size:.82rem; font-weight:700; text-transform:uppercase;
  letter-spacing:.07em; color:#64748b; margin:0 0 14px;
  display:flex; align-items:center; gap:8px;
}
.adm-section-title svg { width:15px; height:15px; }

/* ── Two-column widget row ───────────────────────────── */
.adm-widget-row {
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:20px;
  margin-top:20px;
}
@media(max-width:860px){ .adm-widget-row { grid-template-columns:1fr; } }

.adm-widget {
  background:#fff;
  border:1px solid #e2e8f0;
  border-radius:16px;
  overflow:hidden;
  box-shadow:0 2px 12px rgba(15,23,42,.05);
}
.adm-widget__head {
  display:flex; align-items:center; justify-content:space-between;
  padding:14px 18px;
  border-bottom:1px solid #f1f5f9;
}
.adm-widget__title {
  font-size:.92rem; font-weight:700; color:#0f172a;
  display:flex; align-items:center; gap:8px;
}
.adm-widget__title svg { width:16px; height:16px; color:#4f46e5; }
.adm-widget__meta { font-size:.76rem; color:#94a3b8; }
.adm-widget__action {
  font-size:.78rem; font-weight:700; color:#4f46e5;
  text-decoration:none; padding:.3rem .75rem;
  border:1px solid rgba(79,70,229,.2); border-radius:7px;
  transition:background .15s;
}
.adm-widget__action:hover { background:#eef2ff; }
.adm-widget__body { padding:14px 18px; }

/* ── Announcement mini items ─────────────────────────── */
.adm-ann-item {
  display:flex; align-items:flex-start; gap:10px;
  padding:10px 0; border-bottom:1px solid #f8fafc;
}
.adm-ann-item:last-child { border-bottom:none; padding-bottom:0; }
.adm-ann-bar {
  width:4px; border-radius:2px; align-self:stretch;
  min-height:36px; flex-shrink:0;
}
.bar-high   { background:#ef4444; }
.bar-medium { background:#f59e0b; }
.bar-low    { background:#4f46e5; }
.adm-ann-info { flex:1; min-width:0; }
.adm-ann-title { font-size:.86rem; font-weight:700; color:#0f172a;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.adm-ann-sub { font-size:.74rem; color:#94a3b8; margin-top:2px; }
.adm-ann-badge {
  font-size:.68rem; font-weight:700; padding:.15rem .5rem;
  border-radius:999px; white-space:nowrap; align-self:flex-start; flex-shrink:0;
}
.ab-all      { background:rgba(16,185,129,.1); color:#059669; }
.ab-student  { background:rgba(99,102,241,.1); color:#4338ca; }
.ab-faculty  { background:rgba(245,158,11,.1); color:#d97706; }
.ab-registrar{ background:rgba(14,165,233,.1); color:#0284c7; }

/* ── Schedule mini items ─────────────────────────────── */
.adm-sch-item {
  display:flex; align-items:center; gap:10px;
  padding:9px 0; border-bottom:1px solid #f8fafc;
}
.adm-sch-item:last-child { border-bottom:none; padding-bottom:0; }
.adm-sch-avatar {
  width:32px; height:32px; border-radius:9px;
  background:linear-gradient(135deg,#0f766e,#0d9488);
  display:flex; align-items:center; justify-content:center;
  font-size:.7rem; font-weight:800; color:#fff; flex-shrink:0;
}
.adm-sch-info { flex:1; min-width:0; }
.adm-sch-subject { font-size:.86rem; font-weight:700; color:#0f172a; }
.adm-sch-detail  { font-size:.74rem; color:#94a3b8; margin-top:1px; }
.adm-sch-time    { font-size:.74rem; color:#475569; white-space:nowrap; font-family:monospace; }

.adm-empty {
  text-align:center; padding:24px 12px; color:#94a3b8;
  font-size:.84rem;
}

/* ── Summary chips on widget head ────────────────────── */
.adm-chip {
  display:inline-flex; align-items:center; gap:4px;
  font-size:.74rem; font-weight:600; padding:.2rem .6rem;
  border-radius:6px; background:#f1f5f9; color:#475569;
}
.adm-chip--green { background:rgba(16,185,129,.1); color:#059669; }

/* ── Illustrated stat cards ──────────────────────────── */
.enc-stat-card {
  display: flex !important;
  flex-direction: column !important;
  padding: 0 !important;
  overflow: hidden;
  border-radius: 18px !important;
}
.enc-stat-card::before { display: none !important; }
.enc-stat-illus {
  width: 100%;
  height: 110px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  flex-shrink: 0;
}
.enc-stat-svg {
  width: 82px;
  height: 82px;
  filter: drop-shadow(0 4px 12px rgba(0,0,0,.22));
  transition: transform .25s cubic-bezier(.34,1.56,.64,1);
  position: relative;
  z-index: 1;
}
.enc-stat-card:hover .enc-stat-svg {
  transform: scale(1.14) translateY(-4px);
}
/* Subtle sheen overlay */
.enc-stat-illus::after {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(180deg, rgba(255,255,255,.12) 0%, transparent 70%);
  pointer-events: none;
}
/* Per-card gradient backgrounds */
.enc-stat-illus--blue    { background: linear-gradient(145deg, #1e40af 0%, #3b82f6 60%, #60a5fa 100%); }
.enc-stat-illus--green   { background: linear-gradient(145deg, #14532d 0%, #16a34a 60%, #4ade80 100%); }
.enc-stat-illus--teal    { background: linear-gradient(145deg, #0e7490 0%, #0891b2 60%, #22d3ee 100%); }
.enc-stat-illus--indigo  { background: linear-gradient(145deg, #3730a3 0%, #6366f1 60%, #a5b4fc 100%); }
.enc-stat-illus--emerald { background: linear-gradient(145deg, #065f46 0%, #059669 60%, #34d399 100%); }
.enc-stat-illus--red     { background: linear-gradient(145deg, #7f1d1d 0%, #dc2626 60%, #f87171 100%); }
.enc-stat-illus--amber   { background: linear-gradient(145deg, #78350f 0%, #d97706 60%, #fbbf24 100%); }

.enc-stat-card .enc-stat-body {
  padding: 14px 16px 16px !important;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  background: #fff;
  flex: 1;
}
.enc-stat-card .enc-stat-value {
  font-size: 1.85rem;
  font-weight: 800;
  color: #0a1f44;
  line-height: 1;
  letter-spacing: -.03em;
}
.enc-stat-card .enc-stat-label {
  font-size: .67rem;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: .08em;
  margin-top: 5px;
  line-height: 1.3;
}
</style>
@endpush

@section('content')

{{-- ── Page header ──────────────────────────────────────────────────── --}}
<div class="sd-hero">
  <div class="sd-hero__accent"></div>
  <div class="sd-hero__left">
    <div class="sd-hero__avatar">
      {{ strtoupper(substr(auth()->user()->first_name ?? 'A', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
    </div>
    <div class="sd-hero__text">
      <h1>Welcome back, {{ auth()->user()->first_name }}</h1>
      <p>
        <span>{{ now()->format('l, F d, Y') }}</span>
        <span class="sep">·</span>
        <span>Admin Portal</span>
      </p>
    </div>
  </div>
  <div class="sd-hero__pills">
    <div class="sd-hero__pill">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
      System Admin
    </div>
    <div class="sd-hero__pill sd-hero__pill--active">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      {{ ucfirst(auth()->user()->status ?? 'Active') }}
    </div>
  </div>
</div>
<div class="enc-page__actions" style="display:flex;justify-content:flex-end;gap:8px;margin-bottom:1.25rem;">
  <a href="{{ route('admin.announcements.index') }}" class="enc-btn enc-btn--secondary">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
    </svg>
    Post Announcement
  </a>
  <a href="{{ route('admin.users.create') }}" class="enc-btn enc-btn--primary">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
    </svg>
    New User
  </a>
</div>

{{-- ── Stat strip ───────────────────────────────────────────────────── --}}
<div class="enc-stats">

  {{-- Total Students --}}
  <a href="{{ route('admin.students.index') }}" class="enc-stat-card" data-label="Total Students">
    <div class="enc-stat-illus enc-stat-illus--blue">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        {{-- Mortarboard cap --}}
        <polygon points="50,14 84,30 50,46 16,30" fill="white" opacity="0.95"/>
        <rect x="46" y="46" width="8" height="14" rx="2" fill="white" opacity="0.75"/>
        {{-- Tassel --}}
        <line x1="84" y1="30" x2="84" y2="46" stroke="rgba(255,255,255,0.7)" stroke-width="2.5"/>
        <circle cx="84" cy="46" r="4" fill="#fbbf24"/>
        <line x1="81" y1="50" x2="79" y2="60" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"/>
        <line x1="84" y1="50" x2="84" y2="62" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"/>
        <line x1="87" y1="50" x2="89" y2="60" stroke="#fbbf24" stroke-width="2" stroke-linecap="round"/>
        {{-- Open book --}}
        <path d="M22 64 Q22 60 28 60 L50 62 L50 86 L28 84 Q22 84 22 80 Z" fill="white" opacity="0.82"/>
        <path d="M78 64 Q78 60 72 60 L50 62 L50 86 L72 84 Q78 84 78 80 Z" fill="white" opacity="0.68"/>
        <line x1="34" y1="68" x2="46" y2="68" stroke="rgba(29,78,216,0.25)" stroke-width="1.5"/>
        <line x1="34" y1="74" x2="46" y2="74" stroke="rgba(29,78,216,0.2)" stroke-width="1.5"/>
        <line x1="34" y1="80" x2="46" y2="80" stroke="rgba(29,78,216,0.15)" stroke-width="1.5"/>
        <line x1="54" y1="68" x2="66" y2="68" stroke="rgba(29,78,216,0.2)" stroke-width="1.5"/>
        <line x1="54" y1="74" x2="66" y2="74" stroke="rgba(29,78,216,0.15)" stroke-width="1.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $studentCount }}</div>
      <div class="enc-stat-label">Total Students</div>
    </div>
  </a>

  {{-- Faculty --}}
  <a href="{{ route('admin.faculty.index') }}" class="enc-stat-card" data-label="Faculty Members">
    <div class="enc-stat-illus enc-stat-illus--green">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        {{-- Whiteboard --}}
        <rect x="6" y="24" width="60" height="44" rx="5" fill="white" opacity="0.88"/>
        <rect x="6" y="24" width="60" height="8" rx="5" fill="rgba(255,255,255,0.5)"/>
        <line x1="12" y1="40" x2="60" y2="40" stroke="rgba(22,163,74,0.18)" stroke-width="1.5"/>
        <line x1="12" y1="50" x2="52" y2="50" stroke="rgba(22,163,74,0.14)" stroke-width="1.5"/>
        <line x1="12" y1="60" x2="44" y2="60" stroke="rgba(22,163,74,0.14)" stroke-width="1.5"/>
        {{-- Board stand --}}
        <line x1="20" y1="68" x2="16" y2="82" stroke="rgba(255,255,255,0.65)" stroke-width="3" stroke-linecap="round"/>
        <line x1="46" y1="68" x2="50" y2="82" stroke="rgba(255,255,255,0.65)" stroke-width="3" stroke-linecap="round"/>
        {{-- Teacher head --}}
        <circle cx="82" cy="30" r="12" fill="white" opacity="0.92"/>
        {{-- Teacher body --}}
        <path d="M68 48 Q68 42 82 42 Q96 42 96 48 L96 74 L68 74 Z" fill="white" opacity="0.78"/>
        {{-- Pointer arm --}}
        <line x1="68" y1="56" x2="44" y2="44" stroke="white" stroke-width="3" stroke-linecap="round" opacity="0.9"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $facultyCount }}</div>
      <div class="enc-stat-label">Faculty</div>
    </div>
  </a>

  {{-- Registrars --}}
  <a href="{{ route('admin.registrars.index') }}" class="enc-stat-card" data-label="Registrar Staff">
    <div class="enc-stat-illus enc-stat-illus--teal">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        {{-- Bottom folder --}}
        <rect x="12" y="52" width="76" height="34" rx="5" fill="white" opacity="0.6"/>
        <path d="M12 58 Q12 52 18 52 L38 52 L44 46 L82 46 Q88 46 88 52 L88 86 Q88 86 82 86 L18 86 Q12 86 12 80 Z" fill="white" opacity="0.72"/>
        {{-- Middle folder --}}
        <path d="M18 42 Q18 36 24 36 L44 36 L50 30 L76 30 Q82 30 82 36 L82 70 Q82 70 76 70 L24 70 Q18 70 18 64 Z" fill="white" opacity="0.84"/>
        {{-- Top folder --}}
        <path d="M24 30 Q24 24 30 24 L50 24 L56 18 L70 18 Q76 18 76 24 L76 58 Q76 58 70 58 L30 58 Q24 58 24 52 Z" fill="white" opacity="0.95"/>
        {{-- Paper lines --}}
        <line x1="32" y1="34" x2="68" y2="34" stroke="rgba(8,145,178,0.3)" stroke-width="1.5"/>
        <line x1="32" y1="40" x2="60" y2="40" stroke="rgba(8,145,178,0.22)" stroke-width="1.5"/>
        <line x1="32" y1="46" x2="54" y2="46" stroke="rgba(8,145,178,0.18)" stroke-width="1.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $registrarCount }}</div>
      <div class="enc-stat-label">Registrars</div>
    </div>
  </a>

  {{-- Announcements --}}
  <a href="{{ route('admin.announcements.index') }}" class="enc-stat-card" data-label="Active Announcements">
    <div class="enc-stat-illus enc-stat-illus--indigo">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        {{-- Megaphone body --}}
        <path d="M18 36 L18 64 L38 64 L72 80 L72 20 Z" fill="white" opacity="0.92"/>
        {{-- Megaphone cone highlight --}}
        <path d="M38 38 L72 22 L72 30 L38 48 Z" fill="rgba(255,255,255,0.3)"/>
        {{-- Handle --}}
        <rect x="10" y="36" width="10" height="28" rx="5" fill="white" opacity="0.75"/>
        {{-- Sound waves --}}
        <path d="M78 34 Q86 42 86 50 Q86 58 78 66" stroke="white" stroke-width="3" stroke-linecap="round" opacity="0.8"/>
        <path d="M82 28 Q94 38 94 50 Q94 62 82 72" stroke="white" stroke-width="2.5" stroke-linecap="round" opacity="0.5"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $activeAnnouncements }}</div>
      <div class="enc-stat-label">Active Announcements</div>
    </div>
  </a>

  {{-- Faculty Schedules --}}
  <a href="{{ route('admin.schedules.index') }}" class="enc-stat-card" data-label="Faculty Schedules">
    <div class="enc-stat-illus enc-stat-illus--emerald">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        {{-- Calendar body --}}
        <rect x="10" y="22" width="80" height="68" rx="8" fill="white" opacity="0.9"/>
        {{-- Header bar --}}
        <rect x="10" y="22" width="80" height="22" rx="8" fill="rgba(4,120,87,0.35)"/>
        <rect x="10" y="36" width="80" height="8" fill="rgba(4,120,87,0.35)"/>
        {{-- Ring hangers --}}
        <rect x="28" y="14" width="8" height="16" rx="4" fill="white" opacity="0.9"/>
        <rect x="64" y="14" width="8" height="16" rx="4" fill="white" opacity="0.9"/>
        {{-- Grid cells --}}
        <rect x="16" y="50" width="16" height="12" rx="3" fill="rgba(4,120,87,0.18)"/>
        <rect x="36" y="50" width="16" height="12" rx="3" fill="rgba(4,120,87,0.18)"/>
        <rect x="56" y="50" width="16" height="12" rx="3" fill="rgba(4,120,87,0.25)"/>
        <rect x="76" y="50" width="10" height="12" rx="3" fill="rgba(4,120,87,0.15)"/>
        <rect x="16" y="66" width="16" height="12" rx="3" fill="rgba(4,120,87,0.18)"/>
        <rect x="36" y="66" width="16" height="12" rx="3" fill="rgba(4,120,87,0.25)"/>
        <rect x="56" y="66" width="16" height="12" rx="3" fill="rgba(4,120,87,0.15)"/>
        {{-- Checkmark on highlighted cell --}}
        <path d="M58 55 L62 59 L70 53" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M38 71 L42 75 L50 68" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $totalSchedules }}</div>
      <div class="enc-stat-label">Faculty Schedules</div>
    </div>
  </a>

  {{-- Active Threats --}}
  <a href="{{ route('admin.threat.index') }}" class="enc-stat-card" data-label="Active Threats">
    <div class="enc-stat-illus enc-stat-illus--red">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        {{-- Speedometer arc background --}}
        <path d="M18 72 A34 34 0 1 1 82 72" stroke="rgba(255,255,255,0.25)" stroke-width="10" stroke-linecap="round"/>
        {{-- Danger zone arc --}}
        <path d="M60 26 A34 34 0 0 1 82 72" stroke="rgba(255,255,255,0.7)" stroke-width="10" stroke-linecap="round"/>
        {{-- Safe zone arc --}}
        <path d="M18 72 A34 34 0 0 1 40 26" stroke="rgba(255,255,255,0.35)" stroke-width="10" stroke-linecap="round"/>
        {{-- Center hub --}}
        <circle cx="50" cy="72" r="8" fill="white" opacity="0.95"/>
        {{-- Needle pointing to danger --}}
        <line x1="50" y1="72" x2="72" y2="38" stroke="white" stroke-width="4" stroke-linecap="round"/>
        <circle cx="50" cy="72" r="5" fill="rgba(220,38,38,0.8)"/>
        {{-- Tick marks --}}
        <line x1="18" y1="72" x2="24" y2="72" stroke="white" stroke-width="2" opacity="0.5"/>
        <line x1="50" y1="38" x2="50" y2="44" stroke="white" stroke-width="2" opacity="0.5"/>
        <line x1="82" y1="72" x2="76" y2="72" stroke="white" stroke-width="2" opacity="0.5"/>
        {{-- Warning label --}}
        <text x="50" y="90" text-anchor="middle" fill="white" font-size="9" font-weight="700" opacity="0.8" font-family="sans-serif">HIGH</text>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $activeThreats }}</div>
      <div class="enc-stat-label">Active Threats</div>
    </div>
  </a>

  {{-- Locked Accounts --}}
  <a href="{{ route('admin.locked-accounts.index') }}" class="enc-stat-card" data-label="Locked Accounts">
    <div class="enc-stat-illus enc-stat-illus--amber">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        {{-- Lock body --}}
        <rect x="18" y="46" width="64" height="46" rx="10" fill="white" opacity="0.92"/>
        {{-- Lock body shine --}}
        <rect x="18" y="46" width="64" height="14" rx="10" fill="rgba(255,255,255,0.4)"/>
        <rect x="18" y="52" width="64" height="8" fill="rgba(255,255,255,0.4)"/>
        {{-- Shackle --}}
        <path d="M32 46 L32 30 Q32 14 50 14 Q68 14 68 30 L68 46" stroke="white" stroke-width="9" stroke-linecap="round" fill="none" opacity="0.9"/>
        {{-- Shackle inner cutout for realism --}}
        <path d="M38 46 L38 30 Q38 20 50 20 Q62 20 62 30 L62 46" stroke="rgba(217,119,6,0.55)" stroke-width="4" stroke-linecap="round" fill="none"/>
        {{-- Keyhole --}}
        <circle cx="50" cy="65" r="8" fill="rgba(217,119,6,0.4)"/>
        <rect x="47" y="65" width="6" height="10" rx="2" fill="rgba(217,119,6,0.4)"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $lockedAccounts }}</div>
      <div class="enc-stat-label">Locked Accounts</div>
    </div>
  </a>
</div>

{{-- ── Grade Level Enrollment Breakdown ────────────────────────────── --}}
<div class="adm-widget" style="margin-top:20px;">
  <div class="adm-widget__head">
    <div class="adm-widget__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
      </svg>
      Grade Level Enrollment
      <span class="adm-chip">JHS</span>
    </div>
    <a href="{{ route('admin.students.index') }}" class="adm-widget__action">All Students →</a>
  </div>
  <div class="adm-widget__body" style="padding:0;">

    {{-- Grade-level rows --}}
    @foreach($gradeBreakdown as $row)
    <div style="border-bottom:1px solid #f1f5f9;">
      {{-- Grade header row --}}
      <a href="{{ route('admin.students.index', ['grade_level' => $row['grade']]) }}"
         style="display:flex;align-items:center;justify-content:space-between;padding:11px 18px;text-decoration:none;transition:background .12s;"
         onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:8px;height:8px;border-radius:50%;background:#4f46e5;flex-shrink:0;"></div>
          <span style="font-size:.88rem;font-weight:700;color:#0f172a;">{{ $row['grade'] }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
          <span style="font-size:.82rem;font-weight:700;color:#4f46e5;">{{ $row['count'] }} student{{ $row['count'] === 1 ? '' : 's' }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:#94a3b8;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
          </svg>
        </div>
      </a>

      {{-- Section sub-rows --}}
      @foreach($row['sections'] as $sec)
      <a href="{{ route('admin.students.index', ['grade_level' => $row['grade']]) }}"
         style="display:flex;align-items:center;justify-content:space-between;padding:7px 18px 7px 36px;text-decoration:none;transition:background .12s;background:#fafafa;"
         onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#fafafa'">
        <span style="font-size:.78rem;color:#475569;">{{ $sec->section_name }}</span>
        <span style="font-size:.78rem;color:#64748b;font-weight:600;">{{ $sec->enrolled_count }}</span>
      </a>
      @endforeach

      @if($row['sections']->isEmpty() && $row['count'] > 0)
      <div style="padding:6px 18px 6px 36px;font-size:.75rem;color:#94a3b8;">
        No sections assigned yet
      </div>
      @endif
    </div>
    @endforeach

    {{-- Unassigned row --}}
    @if($unassignedStudents > 0)
    <a href="{{ route('admin.students.index', ['grade_level' => 'unassigned']) }}"
       style="display:flex;align-items:center;justify-content:space-between;padding:11px 18px;text-decoration:none;transition:background .12s;"
       onmouseover="this.style.background='#fff7ed'" onmouseout="this.style.background=''">
      <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:8px;height:8px;border-radius:50%;background:#f59e0b;flex-shrink:0;"></div>
        <span style="font-size:.88rem;font-weight:700;color:#92400e;">Unassigned</span>
      </div>
      <div style="display:flex;align-items:center;gap:10px;">
        <span style="font-size:.82rem;font-weight:700;color:#d97706;">{{ $unassignedStudents }} student{{ $unassignedStudents === 1 ? '' : 's' }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:#94a3b8;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
      </div>
    </a>
    @endif

  </div>
</div>

{{-- ── Announcements + Schedules widgets ───────────────────────────── --}}
<div class="adm-widget-row">

  {{-- Announcements widget --}}
  <div class="adm-widget">
    <div class="adm-widget__head">
      <div class="adm-widget__title">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
        </svg>
        Announcements
        <span class="adm-chip adm-chip--green">{{ $activeAnnouncements }} active</span>
      </div>
      <a href="{{ route('admin.announcements.index') }}" class="adm-widget__action">Manage →</a>
    </div>
    <div class="adm-widget__body">
      @if($recentAnnouncements->isEmpty())
        <div class="adm-empty">No announcements yet. <a href="{{ route('admin.announcements.index') }}" style="color:#4f46e5;font-weight:600;">Post one now</a></div>
      @else
        @foreach($recentAnnouncements as $ann)
        <div class="adm-ann-item">
          <div class="adm-ann-bar bar-{{ $ann->priority }}"></div>
          <div class="adm-ann-info">
            <div class="adm-ann-title">{{ $ann->title }}</div>
            <div class="adm-ann-sub">{{ $ann->created_at->diffForHumans() }} · {{ $ann->author->first_name ?? 'Admin' }}</div>
          </div>
          <span class="adm-ann-badge ab-{{ $ann->target_audience }}">{{ $ann->audience_label }}</span>
        </div>
        @endforeach
        @if($totalAnnouncements > 4)
          <div style="text-align:center;padding-top:10px;font-size:.78rem;color:#94a3b8;">
            <a href="{{ route('admin.announcements.index') }}" style="color:#4f46e5;font-weight:600;">View all {{ $totalAnnouncements }} announcements →</a>
          </div>
        @endif
      @endif
    </div>
  </div>

  {{-- Faculty Schedules widget --}}
  <div class="adm-widget">
    <div class="adm-widget__head">
      <div class="adm-widget__title" style="color:#0f172a;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#0f766e;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
        Faculty Schedules
        <span class="adm-chip">{{ $totalSchedules }} assigned</span>
      </div>
      <a href="{{ route('admin.schedules.index') }}" class="adm-widget__action" style="color:#0f766e;border-color:rgba(15,118,110,.2);">Manage →</a>
    </div>
    <div class="adm-widget__body">
      @if($recentSchedules->isEmpty())
        <div class="adm-empty">No schedules assigned yet. <a href="{{ route('admin.schedules.index') }}" style="color:#0f766e;font-weight:600;">Assign one now</a></div>
      @else
        @foreach($recentSchedules as $sch)
        <div class="adm-sch-item">
          <div class="adm-sch-avatar">
            {{ strtoupper(substr($sch->faculty->first_name ?? 'F', 0, 1)) }}{{ strtoupper(substr($sch->faculty->last_name ?? '', 0, 1)) }}
          </div>
          <div class="adm-sch-info">
            <div class="adm-sch-subject">{{ $sch->subject_name }} @if($sch->section) · <span style="font-weight:500;color:#475569;">{{ $sch->section }}</span>@endif</div>
            <div class="adm-sch-detail">{{ $sch->faculty->last_name ?? '—' }}, {{ $sch->faculty->first_name ?? '' }} @if($sch->room) · {{ $sch->room }}@endif</div>
          </div>
          <div class="adm-sch-time">{{ $sch->days_label }}</div>
        </div>
        @endforeach
        @if($totalSchedules > 4)
          <div style="text-align:center;padding-top:10px;font-size:.78rem;color:#94a3b8;">
            <a href="{{ route('admin.schedules.index') }}" style="color:#0f766e;font-weight:600;">View all {{ $totalSchedules }} schedules →</a>
          </div>
        @endif
      @endif
    </div>
  </div>

</div>

{{-- ── Security & Activity Overview ────────────────────────────────── --}}
<div class="enc-card" style="margin-top:20px;">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Security & Activity Overview
    </div>
    <div class="enc-card__meta">Fast access to your most important admin tools</div>
  </div>
  <div class="enc-card__body">
    <div class="enc-report-grid">
      <a href="{{ route('admin.threat.index') }}" class="enc-report-card">
        <div class="enc-report-card__icon enc-report-card__icon--audit">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
        </div>
        <div class="enc-report-card__title">Threat Events</div>
        <div class="enc-report-card__desc">Monitor active threats and take action on ongoing security events.</div>
        <div class="enc-report-card__footer"><span>Active items</span><strong>{{ $activeThreats }}</strong></div>
      </a>

      <a href="{{ route('admin.audit.index') }}" class="enc-report-card">
        <div class="enc-report-card__icon enc-report-card__icon--audit">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </div>
        <div class="enc-report-card__title">Audit Log</div>
        <div class="enc-report-card__desc">Review trail information for compliance and incident investigation.</div>
        <div class="enc-report-card__footer"><span>Audit history</span><strong>Open</strong></div>
      </a>

      <a href="{{ route('admin.compliance.index') }}" class="enc-report-card">
        <div class="enc-report-card__icon enc-report-card__icon--full">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
          </svg>
        </div>
        <div class="enc-report-card__title">Compliance Reports</div>
        <div class="enc-report-card__desc">Export incident reports and RA 10173 compliance records.</div>
        <div class="enc-report-card__footer"><span>Ready</span><strong>View</strong></div>
      </a>
    </div>
  </div>
</div>

{{-- ── Quick Actions ────────────────────────────────────────────────── --}}
<div class="enc-report-grid" style="margin-top:20px;">
  <a href="{{ route('admin.users.create') }}" class="enc-report-card">
    <div class="enc-report-card__icon enc-report-card__icon--audit">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Create User</div>
    <div class="enc-report-card__desc">Add a new student, faculty, registrar, or admin account quickly.</div>
  </a>

  <a href="{{ route('admin.announcements.index') }}" class="enc-report-card">
    <div class="enc-report-card__icon" style="background:rgba(99,102,241,.08);color:#4f46e5;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Post Announcement</div>
    <div class="enc-report-card__desc">Send a targeted announcement to students, faculty, registrars, or everyone.</div>
  </a>

  <a href="{{ route('admin.schedules.index') }}" class="enc-report-card">
    <div class="enc-report-card__icon" style="background:rgba(15,118,110,.08);color:#0f766e;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Assign Schedule</div>
    <div class="enc-report-card__desc">Assign subjects, sections, and classrooms to faculty members.</div>
  </a>

  <a href="{{ route('admin.sections.index') }}" class="enc-report-card">
    <div class="enc-report-card__icon" style="background:rgba(217,119,6,.1);color:#b45309;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
      </svg>
    </div>
    <div class="enc-report-card__title">Assign Students to Sections</div>
    <div class="enc-report-card__desc">Enroll students into class sections so faculty can grade and take attendance.</div>
  </a>
</div>

@endsection
