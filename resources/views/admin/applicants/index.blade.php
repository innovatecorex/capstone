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

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['pending'] }}</div>
      <div class="enc-stat-label">Pending</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['under_review'] }}</div>
      <div class="enc-stat-label">Under Review</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['waitlisted'] }}</div>
      <div class="enc-stat-label">Waitlisted</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['accepted'] }}</div>
      <div class="enc-stat-label">Accepted</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['rejected'] }}</div>
      <div class="enc-stat-label">Rejected</div>
    </div>
  </div>

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
