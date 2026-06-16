@extends('layouts.app')

@section('title', 'Admissions')
@section('breadcrumb', 'Admissions')

@push('head')
<style>
/* ── Stat cards ────────────────────────────────────────────── */
.adm-stats {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: .75rem;
  margin-bottom: 1.25rem;
}
@media (max-width: 1100px) { .adm-stats { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 700px)  { .adm-stats { grid-template-columns: repeat(2, 1fr); } }

.adm-stat {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: .9rem 1rem;
  display: flex;
  flex-direction: column;
  gap: .3rem;
  cursor: pointer;
  transition: box-shadow .15s, border-color .15s, transform .12s;
  text-decoration: none;
}
.adm-stat:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); transform: translateY(-1px); border-color: currentColor; }

.adm-stat__num  { font-size: 1.55rem; font-weight: 900; line-height: 1; }
.adm-stat__lbl  { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #64748b; }

.adm-stat--pending    .adm-stat__num { color: #d97706; }
.adm-stat--review     .adm-stat__num { color: #2563eb; }
.adm-stat--accepted   .adm-stat__num { color: #16a34a; }
.adm-stat--rejected   .adm-stat__num { color: #dc2626; }
.adm-stat--eligible   .adm-stat__num { color: #7c3aed; }
.adm-stat--enrolled   .adm-stat__num { color: #0284c7; }

/* ── Pipeline funnel ─────────────────────────────────────── */
.adm-pipeline {
  display: flex;
  align-items: center;
  gap: 0;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  overflow: hidden;
  margin-bottom: 1.25rem;
}
.adm-pipe-step {
  flex: 1;
  display: flex;
  align-items: center;
  gap: .55rem;
  padding: .7rem 1rem;
  border-right: 1px solid #f1f5f9;
  position: relative;
}
.adm-pipe-step:last-child { border-right: none; }
.adm-pipe-num {
  width: 26px; height: 26px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: .72rem; font-weight: 800;
  flex-shrink: 0;
}
.adm-pipe-text { min-width: 0; }
.adm-pipe-label { font-size: .72rem; font-weight: 800; color: #0f172a; white-space: nowrap; }
.adm-pipe-count { font-size: 1.1rem; font-weight: 900; }

.adm-pipe-step--1 .adm-pipe-num { background: #fef3c7; color: #d97706; }
.adm-pipe-step--1 .adm-pipe-count { color: #d97706; }
.adm-pipe-step--2 .adm-pipe-num { background: #dbeafe; color: #2563eb; }
.adm-pipe-step--2 .adm-pipe-count { color: #2563eb; }
.adm-pipe-step--3 .adm-pipe-num { background: #dcfce7; color: #16a34a; }
.adm-pipe-step--3 .adm-pipe-count { color: #16a34a; }
.adm-pipe-step--4 .adm-pipe-num { background: #ede9fe; color: #7c3aed; }
.adm-pipe-step--4 .adm-pipe-count { color: #7c3aed; }
.adm-pipe-step--5 .adm-pipe-num { background: #e0f2fe; color: #0284c7; }
.adm-pipe-step--5 .adm-pipe-count { color: #0284c7; }

.adm-pipe-arrow {
  font-size: .65rem;
  color: #cbd5e1;
  padding: 0 .2rem;
  flex-shrink: 0;
}
@media (max-width: 900px) {
  .adm-pipeline { flex-wrap: wrap; }
  .adm-pipe-step { min-width: 45%; border-right: 1px solid #f1f5f9; }
  .adm-pipe-arrow { display: none; }
}

/* ── Table ───────────────────────────────────────────────── */
.adm-th {
  padding: 10px 14px;
  font-size: .73rem; font-weight: 700;
  color: var(--gray-500);
  text-transform: uppercase; letter-spacing: .05em;
  border-bottom: 1px solid rgba(15,23,42,.08);
  white-space: nowrap;
}
.adm-td {
  padding: 12px 14px;
  border-bottom: 1px solid rgba(15,23,42,.04);
  vertical-align: middle;
}
.adm-row:last-child td { border-bottom: none; }
.adm-row:hover td { background: rgba(15,23,42,.015); }

.adm-status {
  display: inline-block;
  padding: .22rem .65rem;
  border-radius: 999px;
  font-size: .73rem; font-weight: 700;
  white-space: nowrap;
}
.adm-status--pending                { background:#fef9c3; color:#854d0e; }
.adm-status--under_review           { background:#dbeafe; color:#1e40af; }
.adm-status--accepted               { background:#dcfce7; color:#166534; }
.adm-status--rejected               { background:#fee2e2; color:#991b1b; }
.adm-status--enrolled               { background:#e0f2fe; color:#0369a1; }
.adm-status--eligible_for_enrollment{ background:#ede9fe; color:#5b21b6; }
</style>
@endpush

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Admissions</h1>
      <p class="enc-page__subtitle">Review and process student admission applications.</p>
    </div>
    <a href="{{ route('apply') }}" target="_blank" class="enc-btn enc-btn--ghost" style="font-size:.82rem;">
      View Public Form ↗
    </a>
  </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#166534;margin-bottom:1rem;">
  {!! session('success') !!}
</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#991b1b;margin-bottom:1rem;">
  {{ session('error') }}
</div>
@endif

{{-- ── Stat cards ──────────────────────────────────────────────────────── --}}
<div class="adm-stats">
  <a href="{{ route('registrar.applicants.index', ['status'=>'pending']) }}" class="adm-stat adm-stat--pending">
    <div class="adm-stat__num">{{ $counts['pending'] }}</div>
    <div class="adm-stat__lbl">Pending</div>
  </a>
  <a href="{{ route('registrar.applicants.index', ['status'=>'under_review']) }}" class="adm-stat adm-stat--review">
    <div class="adm-stat__num">{{ $counts['under_review'] }}</div>
    <div class="adm-stat__lbl">Under Review</div>
  </a>
  <a href="{{ route('registrar.applicants.index', ['status'=>'accepted']) }}" class="adm-stat adm-stat--accepted">
    <div class="adm-stat__num">{{ $counts['accepted'] }}</div>
    <div class="adm-stat__lbl">Accepted</div>
  </a>
  <a href="{{ route('registrar.applicants.index', ['status'=>'eligible_for_enrollment']) }}" class="adm-stat adm-stat--eligible">
    <div class="adm-stat__num">{{ $counts['eligible_for_enrollment'] }}</div>
    <div class="adm-stat__lbl">Eligible</div>
  </a>
  <a href="{{ route('registrar.applicants.index', ['status'=>'enrolled']) }}" class="adm-stat adm-stat--enrolled">
    <div class="adm-stat__num">{{ $counts['enrolled'] }}</div>
    <div class="adm-stat__lbl">Enrolled</div>
  </a>
  <a href="{{ route('registrar.applicants.index', ['status'=>'rejected']) }}" class="adm-stat adm-stat--rejected">
    <div class="adm-stat__num">{{ $counts['rejected'] }}</div>
    <div class="adm-stat__lbl">Rejected</div>
  </a>
</div>

{{-- ── Onboarding pipeline funnel ──────────────────────────────────────── --}}
@php
  $total = array_sum($counts);
  $funnelPct = fn($n) => $total > 0 ? round(($n / $total) * 100) : 0;
@endphp
<div class="adm-pipeline">
  <div class="adm-pipe-step adm-pipe-step--1">
    <div class="adm-pipe-num">1</div>
    <div class="adm-pipe-text">
      <div class="adm-pipe-label">Submitted</div>
      <div class="adm-pipe-count">{{ $total }}</div>
    </div>
  </div>
  <div class="adm-pipe-arrow">▶</div>
  <div class="adm-pipe-step adm-pipe-step--2">
    <div class="adm-pipe-num">2</div>
    <div class="adm-pipe-text">
      <div class="adm-pipe-label">Under Review</div>
      <div class="adm-pipe-count">{{ $counts['under_review'] }}</div>
    </div>
  </div>
  <div class="adm-pipe-arrow">▶</div>
  <div class="adm-pipe-step adm-pipe-step--3">
    <div class="adm-pipe-num">3</div>
    <div class="adm-pipe-text">
      <div class="adm-pipe-label">Accepted</div>
      <div class="adm-pipe-count">{{ $counts['accepted'] }}</div>
    </div>
  </div>
  <div class="adm-pipe-arrow">▶</div>
  <div class="adm-pipe-step adm-pipe-step--4">
    <div class="adm-pipe-num">4</div>
    <div class="adm-pipe-text">
      <div class="adm-pipe-label">Eligible</div>
      <div class="adm-pipe-count">{{ $counts['eligible_for_enrollment'] }}</div>
    </div>
  </div>
  <div class="adm-pipe-arrow">▶</div>
  <div class="adm-pipe-step adm-pipe-step--5">
    <div class="adm-pipe-num">5</div>
    <div class="adm-pipe-text">
      <div class="adm-pipe-label">Enrolled</div>
      <div class="adm-pipe-count">{{ $counts['enrolled'] }}</div>
    </div>
  </div>
</div>

{{-- ── Filters ─────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('registrar.applicants.index') }}"
      style="display:flex;gap:.65rem;flex-wrap:wrap;margin-bottom:1.1rem;align-items:flex-end;">
  <div>
    <label style="display:block;font-size:.72rem;font-weight:700;color:var(--gray-500);margin-bottom:.25rem;">Search</label>
    <input type="text" name="search" value="{{ request('search') }}"
      placeholder="Name, LRN, Reference…"
      style="padding:.5rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.87rem;width:220px;">
  </div>
  <div>
    <label style="display:block;font-size:.72rem;font-weight:700;color:var(--gray-500);margin-bottom:.25rem;">Status</label>
    <select name="status"
      style="padding:.5rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.87rem;background:#fff;">
      <option value="">All statuses</option>
      @foreach(['pending','under_review','accepted','eligible_for_enrollment','enrolled','rejected'] as $s)
      <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
        {{ ucfirst(str_replace('_',' ',$s)) }}
      </option>
      @endforeach
    </select>
  </div>
  <div>
    <label style="display:block;font-size:.72rem;font-weight:700;color:var(--gray-500);margin-bottom:.25rem;">Grade</label>
    <select name="grade"
      style="padding:.5rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.87rem;background:#fff;">
      <option value="">All grades</option>
      @foreach($grades as $g)
      <option value="{{ $g }}" {{ request('grade') === $g ? 'selected' : '' }}>{{ $g }}</option>
      @endforeach
    </select>
  </div>
  @if($years->isNotEmpty())
  <div>
    <label style="display:block;font-size:.72rem;font-weight:700;color:var(--gray-500);margin-bottom:.25rem;">School Year</label>
    <select name="year"
      style="padding:.5rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.87rem;background:#fff;">
      <option value="">All years</option>
      @foreach($years as $y)
      <option value="{{ $y }}" {{ request('year') === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
      @endforeach
    </select>
  </div>
  @endif
  <button type="submit"
    style="padding:.5rem 1.1rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:.87rem;cursor:pointer;">
    Filter
  </button>
  @if(request()->hasAny(['search','status','grade','year']))
  <a href="{{ route('registrar.applicants.index') }}"
    style="padding:.5rem .9rem;background:rgba(15,23,42,.07);color:var(--navy);border-radius:8px;font-size:.87rem;text-decoration:none;font-weight:600;">
    Clear
  </a>
  @endif
</form>

{{-- ── Applicants table ────────────────────────────────────────────────── --}}
<div class="enc-card" style="padding:0;overflow:hidden;">
  @if($applicants->isEmpty())
    <div style="padding:3.5rem 2rem;text-align:center;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
           style="width:40px;height:40px;color:#cbd5e1;margin:0 auto .75rem;display:block;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
      </svg>
      <div style="font-weight:700;color:#374151;margin-bottom:.3rem;">No applicants found</div>
      <div style="font-size:.84rem;color:#94a3b8;">Try adjusting your filters or share the
        <a href="{{ route('apply') }}" target="_blank" style="color:var(--primary);text-decoration:none;font-weight:700;">public application form</a>.
      </div>
    </div>
  @else
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th class="adm-th" style="text-align:left;">Reference</th>
          <th class="adm-th" style="text-align:left;">Applicant</th>
          <th class="adm-th">Grade</th>
          <th class="adm-th">Year</th>
          <th class="adm-th">Submitted</th>
          <th class="adm-th">Status</th>
          <th class="adm-th"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($applicants as $a)
        <tr class="adm-row">
          <td class="adm-td" style="font-family:monospace;font-size:.8rem;color:var(--gray-500);">
            {{ $a->reference_number }}
          </td>
          <td class="adm-td">
            <div style="font-weight:700;color:var(--navy);">{{ $a->full_name }}</div>
            <div style="font-size:.77rem;color:var(--gray-400);">
              {{ $a->parent_contact }}
              @if($a->lrn) · LRN {{ $a->lrn }} @endif
            </div>
          </td>
          <td class="adm-td" style="text-align:center;font-size:.84rem;font-weight:600;">
            {{ $a->applying_for_grade }}
          </td>
          <td class="adm-td" style="text-align:center;font-size:.8rem;color:var(--gray-500);">
            {{ $a->applying_for_year ?? '—' }}
          </td>
          <td class="adm-td" style="text-align:center;font-size:.8rem;color:var(--gray-500);">
            {{ $a->created_at->format('M d, Y') }}
          </td>
          <td class="adm-td" style="text-align:center;">
            <span class="adm-status adm-status--{{ $a->status }}">
              {{ ucfirst(str_replace('_',' ',$a->status)) }}
            </span>
          </td>
          <td class="adm-td" style="text-align:right;">
            <a href="{{ route('registrar.applicants.show', $a->id) }}"
               style="font-size:.82rem;color:var(--primary);font-weight:700;text-decoration:none;white-space:nowrap;">
              Review →
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if($applicants->hasPages())
  <div style="padding:1rem 1.25rem;border-top:1px solid rgba(15,23,42,.06);">
    {{ $applicants->links() }}
  </div>
  @endif
  @endif
</div>
@endsection
