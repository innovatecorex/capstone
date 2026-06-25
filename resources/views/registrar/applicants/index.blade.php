@extends('layouts.app')

@section('title', 'Admissions')
@section('breadcrumb', 'Admissions')

@push('head')
<style>
/* ── Stat grid ────────────────────────────────────────────────── */
.adm-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: .75rem;
  margin-bottom: 1.25rem;
}
@media (max-width: 1100px) { .adm-stats { grid-template-columns: repeat(3,1fr); } }
@media (max-width:  680px) { .adm-stats { grid-template-columns: repeat(2,1fr); } }

.adm-stat {
  background: #fff;
  border: 1px solid rgba(15,23,42,.08);
  border-radius: 12px;
  padding: .85rem 1rem;
  display: flex;
  align-items: center;
  gap: .75rem;
  text-decoration: none;
  transition: box-shadow .15s, border-color .15s;
}
.adm-stat:hover { box-shadow: 0 4px 14px rgba(0,0,0,.07); border-color: rgba(15,23,42,.15); }

.adm-stat-icon {
  width: 36px; height: 36px; border-radius: 9px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
}
.adm-stat-icon svg { width: 17px; height: 17px; }
.adm-stat-icon--amber  { background: #fef3c7; color: #b45309; }
.adm-stat-icon--blue   { background: #dbeafe; color: #1d4ed8; }
.adm-stat-icon--green  { background: #dcfce7; color: #16a34a; }
.adm-stat-icon--red    { background: #fee2e2; color: #dc2626; }
.adm-stat-icon--violet { background: #ede9fe; color: #7c3aed; }
.adm-stat-icon--sky    { background: #e0f2fe; color: #0369a1; }

.adm-stat-val   { font-size: 1.4rem; font-weight: 900; color: #0f172a; line-height: 1; }
.adm-stat-label { font-size: .68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; margin-top: 2px; }

/* ── Pipeline funnel ─────────────────────────────────────────── */
.adm-funnel {
  display: flex; align-items: stretch;
  background: #fff; border: 1px solid rgba(15,23,42,.08); border-radius: 12px;
  overflow: hidden; margin-bottom: 1.25rem;
}
.adm-funnel-step {
  flex: 1; padding: .6rem .85rem;
  display: flex; flex-direction: column; align-items: center;
  border-right: 1px solid rgba(15,23,42,.06);
  font-size: .7rem; color: #64748b; text-align: center; gap: 2px;
}
.adm-funnel-step:last-child { border-right: none; }
.adm-funnel-step strong { font-size: 1.1rem; font-weight: 900; color: #0f172a; }
.adm-funnel-step span   { font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
.adm-funnel-arrow {
  display: flex; align-items: center; padding: 0 2px;
  color: #cbd5e1; font-size: .9rem; flex-shrink: 0;
}

/* ── Filters ────────────────────────────────────────────────── */
.adm-filters { display: flex; gap: .6rem; flex-wrap: wrap; align-items: flex-end; margin-bottom: 1rem; }
.adm-filter-group { display: flex; flex-direction: column; gap: .2rem; }
.adm-filter-label { font-size: .68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
.adm-filter-ctrl {
  padding: .45rem .85rem; border: 1px solid rgba(15,23,42,.14);
  border-radius: 8px; font-size: .86rem; background: #fff; color: #0f172a;
  font-family: inherit; outline: none; height: 36px;
}
.adm-filter-ctrl:focus { border-color: var(--primary); }

/* ── Table ──────────────────────────────────────────────────── */
.adm-th {
  padding: 10px 14px; font-size: .68rem; font-weight: 700; color: #94a3b8;
  text-transform: uppercase; letter-spacing: .05em;
  border-bottom: 1px solid rgba(15,23,42,.08); white-space: nowrap;
}
.adm-td { padding: 11px 14px; border-bottom: 1px solid rgba(15,23,42,.04); vertical-align: middle; }
.adm-row:hover td { background: rgba(15,23,42,.012); }

.adm-status { display: inline-block; padding: .2rem .6rem; border-radius: 999px; font-size: .68rem; font-weight: 700; white-space: nowrap; }
.adm-status--pending                { background: #fef9c3; color: #854d0e; }
.adm-status--under_review           { background: #dbeafe; color: #1e40af; }
.adm-status--waitlisted             { background: #fef3c7; color: #92400e; }
.adm-status--accepted               { background: #dcfce7; color: #166534; }
.adm-status--rejected               { background: #fee2e2; color: #991b1b; }
.adm-status--eligible_for_enrollment{ background: #ede9fe; color: #5b21b6; }
.adm-status--enrolled               { background: #e0f2fe; color: #0369a1; }
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
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#166534;margin-bottom:1rem;">{!! session('success') !!}</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#991b1b;margin-bottom:1rem;">{{ session('error') }}</div>
@endif

{{-- 6 Stat cards --}}
<div class="adm-stats">

  <a href="{{ route('registrar.applicants.index', ['status'=>'pending'] + request()->except('status','page')) }}" class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div><div class="adm-stat-val">{{ $counts['pending'] }}</div><div class="adm-stat-label">Pending</div></div>
  </a>

  <a href="{{ route('registrar.applicants.index', ['status'=>'under_review'] + request()->except('status','page')) }}" class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <div><div class="adm-stat-val">{{ $counts['under_review'] }}</div><div class="adm-stat-label">Under Review</div></div>
  </a>

  <a href="{{ route('registrar.applicants.index', ['status'=>'waitlisted'] + request()->except('status','page')) }}" class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div><div class="adm-stat-val">{{ $counts['waitlisted'] }}</div><div class="adm-stat-label">Waitlisted</div></div>
  </a>

  <a href="{{ route('registrar.applicants.index', ['status'=>'accepted'] + request()->except('status','page')) }}" class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div><div class="adm-stat-val">{{ $counts['accepted'] }}</div><div class="adm-stat-label">Accepted</div></div>
  </a>

  <a href="{{ route('registrar.applicants.index', ['status'=>'rejected'] + request()->except('status','page')) }}" class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div><div class="adm-stat-val">{{ $counts['rejected'] }}</div><div class="adm-stat-label">Rejected</div></div>
  </a>

  <a href="{{ route('registrar.applicants.index', ['status'=>'eligible_for_enrollment'] + request()->except('status','page')) }}" class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--violet">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
    </div>
    <div><div class="adm-stat-val">{{ $counts['eligible_for_enrollment'] }}</div><div class="adm-stat-label">Eligible</div></div>
  </a>

  <a href="{{ route('registrar.applicants.index', ['status'=>'enrolled'] + request()->except('status','page')) }}" class="adm-stat">
    <div class="adm-stat-icon adm-stat-icon--sky">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
    </div>
    <div><div class="adm-stat-val">{{ $counts['enrolled'] }}</div><div class="adm-stat-label">Enrolled</div></div>
  </a>

</div>

{{-- Admissions funnel --}}
@php $total = array_sum($counts); $pct = fn($n) => $total > 0 ? round($n/$total*100) : 0; @endphp
<div class="adm-funnel">
  <div class="adm-funnel-step">
    <strong>{{ $counts['pending'] + $counts['under_review'] + $counts['waitlisted'] }}</strong>
    <span>In Review</span>
    <div style="font-size:.65rem;color:#94a3b8;">{{ $pct($counts['pending']+$counts['under_review']+$counts['waitlisted']) }}%</div>
  </div>
  <div class="adm-funnel-arrow">›</div>
  <div class="adm-funnel-step">
    <strong>{{ $counts['accepted'] }}</strong>
    <span>Accepted</span>
    <div style="font-size:.65rem;color:#94a3b8;">{{ $pct($counts['accepted']) }}%</div>
  </div>
  <div class="adm-funnel-arrow">›</div>
  <div class="adm-funnel-step">
    <strong>{{ $counts['eligible_for_enrollment'] }}</strong>
    <span>Eligible</span>
    <div style="font-size:.65rem;color:#94a3b8;">{{ $pct($counts['eligible_for_enrollment']) }}%</div>
  </div>
  <div class="adm-funnel-arrow">›</div>
  <div class="adm-funnel-step" style="color:#0369a1;">
    <strong style="color:#0369a1;">{{ $counts['enrolled'] }}</strong>
    <span>Enrolled</span>
    <div style="font-size:.65rem;color:#94a3b8;">{{ $pct($counts['enrolled']) }}%</div>
  </div>
  <div style="display:flex;align-items:center;padding:0 .75rem;color:#e2e8f0;">|</div>
  <div class="adm-funnel-step" style="color:#991b1b;">
    <strong style="color:#991b1b;">{{ $counts['rejected'] }}</strong>
    <span>Rejected</span>
    <div style="font-size:.65rem;color:#94a3b8;">{{ $pct($counts['rejected']) }}%</div>
  </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('registrar.applicants.index') }}" class="adm-filters">
  <div class="adm-filter-group">
    <span class="adm-filter-label">Search</span>
    <input type="text" name="search" value="{{ request('search') }}"
      placeholder="Name, LRN, Reference…" class="adm-filter-ctrl" style="width:210px;">
  </div>
  <div class="adm-filter-group">
    <span class="adm-filter-label">Status</span>
    <select name="status" class="adm-filter-ctrl" style="width:185px;">
      <option value="">All statuses</option>
      @foreach(['pending','under_review','waitlisted','accepted','rejected','eligible_for_enrollment','enrolled'] as $s)
      <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
      @endforeach
    </select>
  </div>
  <div class="adm-filter-group">
    <span class="adm-filter-label">Grade</span>
    <select name="grade" class="adm-filter-ctrl" style="width:125px;">
      <option value="">All grades</option>
      @foreach($grades as $g)
      <option value="{{ $g }}" {{ request('grade') === $g ? 'selected' : '' }}>{{ $g }}</option>
      @endforeach
    </select>
  </div>
  @if(isset($years) && $years->isNotEmpty())
  <div class="adm-filter-group">
    <span class="adm-filter-label">School Year</span>
    <select name="year" class="adm-filter-ctrl" style="width:130px;">
      <option value="">All years</option>
      @foreach($years as $y)
      <option value="{{ $y }}" {{ request('year') === $y ? 'selected' : '' }}>{{ $y }}</option>
      @endforeach
    </select>
  </div>
  @endif
  <div style="display:flex;gap:.5rem;align-items:flex-end;">
    <button type="submit" class="enc-btn enc-btn--primary" style="height:36px;font-size:.83rem;">Filter</button>
    @if(request()->hasAny(['search','status','grade','year']))
    <a href="{{ route('registrar.applicants.index') }}" class="enc-btn enc-btn--ghost" style="height:36px;font-size:.83rem;">Clear</a>
    @endif
  </div>
</form>

{{-- Table --}}
<div class="enc-card" style="padding:0;overflow:hidden;">
  @if($applicants->isEmpty())
    <div style="padding:3.5rem;text-align:center;">
      <div style="font-size:2rem;margin-bottom:.5rem;">📋</div>
      <div style="font-weight:700;color:#374151;margin-bottom:.3rem;">No applicants found</div>
      <div style="font-size:.84rem;color:#94a3b8;">Try adjusting your filters, or clear them to see all applicants.</div>
    </div>
  @else
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th class="adm-th" style="text-align:left;">Reference</th>
          <th class="adm-th" style="text-align:left;">Applicant</th>
          <th class="adm-th">Grade</th>
          <th class="adm-th">S.Y.</th>
          <th class="adm-th">Submitted</th>
          <th class="adm-th">Status</th>
          <th class="adm-th"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($applicants as $a)
        <tr class="adm-row">
          <td class="adm-td" style="font-family:monospace;font-size:.79rem;color:#64748b;">{{ $a->reference_number }}</td>
          <td class="adm-td">
            <div style="font-weight:700;color:#0f172a;">{{ $a->full_name }}</div>
            <div style="font-size:.74rem;color:#94a3b8;">
              {{ $a->parent_contact }}
              @if($a->lrn) · LRN {{ $a->lrn }} @endif
            </div>
          </td>
          <td class="adm-td" style="text-align:center;font-size:.84rem;font-weight:600;color:#374151;">{{ $a->applying_for_grade }}</td>
          <td class="adm-td" style="text-align:center;font-size:.76rem;color:#94a3b8;">{{ $a->applying_for_year ?? '—' }}</td>
          <td class="adm-td" style="text-align:center;font-size:.76rem;color:#94a3b8;">{{ $a->created_at->format('M d, Y') }}</td>
          <td class="adm-td" style="text-align:center;">
            <span class="adm-status adm-status--{{ $a->status }}">{{ ucfirst(str_replace('_',' ',$a->status)) }}</span>
          </td>
          <td class="adm-td" style="text-align:right;">
            <a href="{{ route('registrar.applicants.show', $a->id) }}"
               style="font-size:.82rem;color:var(--primary);font-weight:700;text-decoration:none;white-space:nowrap;">Review →</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if($applicants->hasPages())
  <div style="padding:.85rem 1.25rem;border-top:1px solid rgba(15,23,42,.06);">{{ $applicants->links() }}</div>
  @endif
  @endif
</div>

@endsection
