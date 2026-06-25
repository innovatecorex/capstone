@extends('layouts.app')

@section('title', 'Applicants')
@section('breadcrumb', 'Applicant Management')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Applicants</h1>
      <p class="enc-page__subtitle">Review and process admission applications.</p>
    </div>
    <a href="{{ route('apply') }}" target="_blank" class="app-btn app-btn--ghost" style="font-size:.8rem;">
      View Public Form ↗
    </a>
  </div>
</div>

{{-- Status stat cards --}}
<div class="enc-stats">

  <a href="{{ route('admin.applicants.index', ['status' => 'pending']) }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--amber">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <circle cx="50" cy="50" r="34" stroke="rgba(255,255,255,.8)" stroke-width="5" fill="none"/>
        <line x1="50" y1="28" x2="50" y2="52" stroke="rgba(255,255,255,.9)" stroke-width="6" stroke-linecap="round"/>
        <line x1="50" y1="52" x2="64" y2="63" stroke="rgba(255,255,255,.9)" stroke-width="6" stroke-linecap="round"/>
        <circle cx="50" cy="50" r="4" fill="rgba(255,255,255,.9)"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['pending'] }}</div>
      <div class="enc-stat-label">Pending</div>
    </div>
  </a>

  <a href="{{ route('admin.applicants.index', ['status' => 'under_review']) }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--blue">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <circle cx="44" cy="44" r="22" stroke="rgba(255,255,255,.85)" stroke-width="6" fill="none"/>
        <line x1="60" y1="60" x2="78" y2="78" stroke="rgba(255,255,255,.85)" stroke-width="7" stroke-linecap="round"/>
        <circle cx="44" cy="44" r="12" fill="rgba(255,255,255,.2)"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['under_review'] }}</div>
      <div class="enc-stat-label">Under Review</div>
    </div>
  </a>

  <a href="{{ route('admin.applicants.index', ['status' => 'waitlisted']) }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--purple">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <rect x="22" y="25" width="56" height="8" rx="4" fill="rgba(255,255,255,.85)"/>
        <rect x="22" y="43" width="56" height="8" rx="4" fill="rgba(255,255,255,.7)"/>
        <rect x="22" y="61" width="40" height="8" rx="4" fill="rgba(255,255,255,.55)"/>
        <circle cx="78" cy="72" r="12" fill="rgba(255,255,255,.85)" stroke="rgba(255,255,255,.4)" stroke-width="2"/>
        <line x1="78" y1="66" x2="78" y2="72" stroke="#7c3aed" stroke-width="3" stroke-linecap="round"/>
        <line x1="78" y1="72" x2="82" y2="75" stroke="#7c3aed" stroke-width="3" stroke-linecap="round"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['waitlisted'] }}</div>
      <div class="enc-stat-label">Waitlisted</div>
    </div>
  </a>

  <a href="{{ route('admin.applicants.index', ['status' => 'accepted']) }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--emerald">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <circle cx="50" cy="50" r="34" fill="rgba(255,255,255,.15)" stroke="rgba(255,255,255,.6)" stroke-width="4"/>
        <polyline points="32,50 45,63 68,37" stroke="rgba(255,255,255,.95)" stroke-width="8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['accepted'] }}</div>
      <div class="enc-stat-label">Accepted</div>
    </div>
  </a>

  <a href="{{ route('admin.applicants.index', ['status' => 'rejected']) }}" class="enc-stat-card">
    <div class="enc-stat-illus enc-stat-illus--red">
      <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" class="enc-stat-svg">
        <circle cx="50" cy="50" r="34" fill="rgba(255,255,255,.15)" stroke="rgba(255,255,255,.6)" stroke-width="4"/>
        <line x1="35" y1="35" x2="65" y2="65" stroke="rgba(255,255,255,.95)" stroke-width="8" stroke-linecap="round"/>
        <line x1="65" y1="35" x2="35" y2="65" stroke="rgba(255,255,255,.95)" stroke-width="8" stroke-linecap="round"/>
      </svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['rejected'] }}</div>
      <div class="enc-stat-label">Rejected</div>
    </div>
  </a>

</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.applicants.index') }}"
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
      @foreach(['pending','under_review','waitlisted','accepted','rejected','enrolled'] as $s)
      <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
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
  <button type="submit"
    style="padding:.5rem 1.1rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:.87rem;cursor:pointer;">
    Filter
  </button>
  @if(request()->hasAny(['search','status','grade']))
  <a href="{{ route('admin.applicants.index') }}"
    style="padding:.5rem .9rem;background:rgba(15,23,42,.07);color:var(--navy);border-radius:8px;font-size:.87rem;text-decoration:none;font-weight:600;">
    Clear
  </a>
  @endif
</form>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#166534;margin-bottom:1rem;">
  {{ session('success') }}
</div>
@endif

<div class="enc-card" style="padding:0;overflow:hidden;">
  @if($applicants->isEmpty())
    <div style="padding:3rem;text-align:center;color:var(--gray-400);font-size:.9rem;">
      No applicants found matching the current filters.
    </div>
  @else
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th class="atbl-th" style="text-align:left;">Reference</th>
          <th class="atbl-th" style="text-align:left;">Applicant</th>
          <th class="atbl-th">Grade</th>
          <th class="atbl-th">Submitted</th>
          <th class="atbl-th">Status</th>
          <th class="atbl-th"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($applicants as $a)
        <tr class="atbl-row">
          <td class="atbl-td" style="font-family:monospace;font-size:.82rem;color:var(--gray-500);">
            {{ $a->reference_number }}
          </td>
          <td class="atbl-td">
            <div style="font-weight:700;color:var(--navy);">{{ $a->full_name }}</div>
            <div style="font-size:.77rem;color:var(--gray-400);">
              {{ $a->parent_contact }}
              @if($a->lrn) · LRN {{ $a->lrn }} @endif
            </div>
          </td>
          <td class="atbl-td" style="text-align:center;font-size:.85rem;">{{ $a->applying_for_grade }}</td>
          <td class="atbl-td" style="text-align:center;font-size:.8rem;color:var(--gray-500);">
            {{ $a->created_at->format('M d, Y') }}
          </td>
          <td class="atbl-td" style="text-align:center;">
            <span class="status-chip status-{{ $a->status }}">
              {{ ucfirst(str_replace('_',' ',$a->status)) }}
            </span>
          </td>
          <td class="atbl-td" style="text-align:right;">
            <a href="{{ route('admin.applicants.show', $a->id) }}"
               style="font-size:.82rem;color:var(--primary);font-weight:700;text-decoration:none;">
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

@push('head')
<style>
.atbl-th { padding:10px 14px; font-size:.73rem; font-weight:700; color:var(--gray-500); text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid rgba(15,23,42,.08); text-align:center; }
.atbl-td { padding:12px 14px; border-bottom:1px solid rgba(15,23,42,.04); vertical-align:middle; }
.atbl-row:hover td { background:rgba(15,23,42,.015); }
.status-chip { display:inline-block; padding:.22rem .65rem; border-radius:999px; font-size:.73rem; font-weight:700; }
.status-pending       { background:#fef9c3; color:#854d0e; }
.status-under_review  { background:#dbeafe; color:#1e40af; }
.status-waitlisted    { background:#fef3c7; color:#92400e; }
.status-accepted      { background:#dcfce7; color:#166534; }
.status-rejected      { background:#fee2e2; color:#991b1b; }
.status-enrolled                { background:#e0f2fe; color:#0369a1; }
.status-eligible_for_enrollment { background:#fffbeb; color:#92400e; }
.app-btn { display:inline-flex; align-items:center; justify-content:center; padding:.55rem 1.1rem; border-radius:999px; font-weight:700; text-decoration:none; }
.app-btn--ghost { background:rgba(15,23,42,.07); color:var(--navy); }
</style>
@endpush
