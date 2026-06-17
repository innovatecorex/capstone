@extends('layouts.app')
@section('title', 'Gradebook')
@section('breadcrumb', 'Gradebook')

@push('head')
<style>
/* ── Page header ─────────────────────────────────────────── */
.gb-index-header { margin-bottom:1.25rem; }
.gb-index-header h1 { font-size:1.25rem; font-weight:800; color:#0f172a; margin:0 0 3px; }
.gb-index-header p  { font-size:.85rem; color:#94a3b8; margin:0; }

/* ── Active quarter banner ───────────────────────────────── */
.gb-quarter-banner {
  display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.6rem;
  background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px;
  padding:.75rem 1.1rem; margin-bottom:1.25rem; font-size:.84rem;
}
.gb-quarter-banner__label { color:#1e40af; font-weight:700; }
.gb-quarter-banner__sub   { color:#3b82f6; font-size:.75rem; }
.gb-quarter-banner--warn  { background:#fffbeb; border-color:#fcd34d; }
.gb-quarter-banner--warn .gb-quarter-banner__label { color:#92400e; }
.gb-quarter-banner--warn .gb-quarter-banner__sub   { color:#b45309; }

/* ── Class cards grid ────────────────────────────────────── */
.gb-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(290px,1fr)); gap:16px; }

.gb-class-card {
  display:flex; flex-direction:column;
  background:#fff; border:1px solid #e5e7eb; border-radius:16px;
  text-decoration:none; overflow:hidden;
  transition:box-shadow .15s, border-color .15s, transform .12s;
}
.gb-class-card:hover {
  box-shadow:0 6px 20px rgba(0,0,0,.09);
  border-color:#6366f1;
  transform:translateY(-2px);
}

/* Color bar at top keyed by subject color index */
.gb-card-bar { height:5px; }
.gb-card-bar--0 { background:linear-gradient(90deg,#6366f1,#818cf8); }
.gb-card-bar--1 { background:linear-gradient(90deg,#0ea5e9,#38bdf8); }
.gb-card-bar--2 { background:linear-gradient(90deg,#10b981,#34d399); }
.gb-card-bar--3 { background:linear-gradient(90deg,#f59e0b,#fbbf24); }
.gb-card-bar--4 { background:linear-gradient(90deg,#ef4444,#f87171); }
.gb-card-bar--5 { background:linear-gradient(90deg,#8b5cf6,#a78bfa); }

.gb-card-body { padding:18px 20px 14px; flex:1; display:flex; flex-direction:column; gap:10px; }

.gb-card-subject { font-size:.97rem; font-weight:800; color:#0f172a; line-height:1.25; }
.gb-card-section { font-size:.78rem; font-weight:700; color:#6366f1; }

/* Schedule chips */
.gb-card-chips { display:flex; flex-wrap:wrap; gap:5px; }
.gb-card-chip {
  display:inline-flex; align-items:center; gap:3px;
  background:#f1f5f9; color:#475569; border-radius:6px;
  padding:3px 8px; font-size:.7rem; font-weight:600;
}

/* Progress bar */
.gb-card-progress { display:flex; flex-direction:column; gap:4px; }
.gb-card-progress__bar {
  width:100%; height:5px; background:#f1f5f9; border-radius:999px; overflow:hidden;
}
.gb-card-progress__fill { height:100%; border-radius:999px; transition:width .3s; }
.gb-card-progress__fill--none       { background:#cbd5e1; }
.gb-card-progress__fill--draft      { background:#fbbf24; }
.gb-card-progress__fill--submitted  { background:#60a5fa; }
.gb-card-progress__fill--finalized  { background:#34d399; }
.gb-card-progress__fill--locked     { background:#ef4444; }
.gb-card-progress__meta { display:flex; align-items:center; justify-content:space-between; font-size:.72rem; color:#64748b; }

/* Status badge */
.gb-status-pill {
  display:inline-block; padding:.18rem .6rem; border-radius:999px;
  font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.04em;
  white-space:nowrap;
}
.gsp--none       { background:#f1f5f9; color:#64748b; }
.gsp--draft      { background:#fef9c3; color:#92400e; }
.gsp--submitted  { background:#dbeafe; color:#1e40af; }
.gsp--finalized  { background:#dcfce7; color:#166534; }
.gsp--locked     { background:#fee2e2; color:#991b1b; }

/* CTA row */
.gb-card-cta {
  display:flex; align-items:center; justify-content:space-between;
  padding:12px 20px; border-top:1px solid #f1f5f9;
  font-size:.8rem; font-weight:700; color:#6366f1;
}

/* Empty state */
.gb-empty {
  background:#fff; border:1px solid #e5e7eb; border-radius:16px;
  padding:4rem 2rem; text-align:center;
}
.gb-empty__icon  { font-size:2rem; margin-bottom:.6rem; }
.gb-empty__title { font-size:.95rem; font-weight:700; color:#374151; margin-bottom:.3rem; }
.gb-empty__sub   { font-size:.83rem; color:#94a3b8; }
</style>
@endpush

@section('content')

<div class="gb-index-header">
  <h1>Gradebook</h1>
  <p>Select a class to enter or review grades for the current grading period.</p>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:11px 16px;margin-bottom:16px;font-size:.85rem;color:#166534;font-weight:600;">
  ✓ {{ session('success') }}
</div>
@endif

{{-- Active quarter banner --}}
@if($activeQuarter)
<div class="gb-quarter-banner">
  <div>
    <div class="gb-quarter-banner__label">
      ● {{ $activeQuarter->quarter_name }} is active
      — {{ $activeYear->year_label }}
    </div>
    <div class="gb-quarter-banner__sub">
      @if($activeQuarter->start_date && $activeQuarter->end_date)
        {{ $activeQuarter->start_date->format('M d') }} – {{ $activeQuarter->end_date->format('M d, Y') }}
      @else
        Grade encoding is open for this period.
      @endif
    </div>
  </div>
  <span class="gb-status-pill gsp--finalized" style="font-size:.72rem;">Active Period</span>
</div>
@elseif($activeYear)
<div class="gb-quarter-banner gb-quarter-banner--warn">
  <div>
    <div class="gb-quarter-banner__label">⚠ No grading period is active for {{ $activeYear->year_label }}</div>
    <div class="gb-quarter-banner__sub">Grade encoding is disabled until the admin activates a grading quarter.</div>
  </div>
</div>
@else
<div class="gb-quarter-banner gb-quarter-banner--warn">
  <div>
    <div class="gb-quarter-banner__label">⚠ No active academic year</div>
    <div class="gb-quarter-banner__sub">Contact the admin to set the active academic year.</div>
  </div>
</div>
@endif

{{-- Class cards grid --}}
@if($allSchedules->isEmpty())
<div class="gb-empty">
  <div class="gb-empty__icon">📚</div>
  <div class="gb-empty__title">No classes assigned</div>
  <div class="gb-empty__sub">You have no classes assigned for the active academic year. Contact the admin if this is incorrect.</div>
</div>
@else
<div class="gb-grid">
  @foreach($allSchedules as $i => $sched)
  @php
    $pct = $sched->student_count > 0
      ? min(100, round(($sched->graded_count / $sched->student_count) * 100))
      : 0;
    $statusLabel = match($sched->grade_status) {
      'locked'    => 'Locked',
      'finalized' => 'Finalized',
      'submitted' => 'Submitted',
      'draft'     => 'Draft',
      default     => 'No Entry',
    };
  @endphp
  <a href="{{ route('faculty.gradebook.show', $sched->id) }}" class="gb-class-card">
    <div class="gb-card-bar gb-card-bar--{{ $i % 6 }}"></div>

    <div class="gb-card-body">
      {{-- Subject & section --}}
      <div>
        <div class="gb-card-subject">{{ $sched->subject_name ?? '—' }}</div>
        <div class="gb-card-section" style="margin-top:3px;">
          {{ $sched->section?->grade_level ? $sched->section->grade_level . ' — ' : '' }}{{ $sched->section_name ?? 'No Section' }}
        </div>
      </div>

      {{-- Schedule chips --}}
      <div class="gb-card-chips">
        @if($sched->schedule_days_label)
        <span class="gb-card-chip">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:10px;height:10px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
          {{ $sched->schedule_days_label }}
        </span>
        @endif
        @if($sched->start_time && $sched->end_time)
        <span class="gb-card-chip">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:10px;height:10px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg>
          {{ $sched->time_range }}
        </span>
        @endif
        @if($sched->room)
        <span class="gb-card-chip">{{ $sched->room }}</span>
        @endif
      </div>

      {{-- Grade progress --}}
      <div class="gb-card-progress">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
          <span class="gb-status-pill gsp--{{ $sched->grade_status }}">{{ $statusLabel }}</span>
          <span style="font-size:.72rem;color:#64748b;">
            @if($sched->student_count > 0)
              {{ $sched->graded_count }}/{{ $sched->student_count }} graded
            @else
              No students enrolled
            @endif
          </span>
        </div>
        <div class="gb-card-progress__bar">
          <div class="gb-card-progress__fill gb-card-progress__fill--{{ $sched->grade_status }}"
               style="width:{{ $pct }}%"></div>
        </div>
      </div>
    </div>

    <div class="gb-card-cta">
      <span>{{ $activeQuarter ? 'Enter / Review Grades' : 'View Grades' }}</span>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2.5" style="width:15px;height:15px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
      </svg>
    </div>
  </a>
  @endforeach
</div>

{{-- Summary strip --}}
@php
  $totalClasses    = $allSchedules->count();
  $notStarted      = $allSchedules->where('grade_status', 'none')->count();
  $inProgress      = $allSchedules->whereIn('grade_status', ['draft'])->count();
  $submitted       = $allSchedules->whereIn('grade_status', ['submitted', 'finalized'])->count();
  $locked          = $allSchedules->where('grade_status', 'locked')->count();
@endphp
<div style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:1.25rem;padding:.85rem 1.1rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;font-size:.78rem;color:#64748b;align-items:center;">
  <span style="font-weight:700;color:#0f172a;margin-right:.25rem;">{{ $totalClasses }} class{{ $totalClasses !== 1 ? 'es' : '' }}</span>
  @if($notStarted > 0)<span style="background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:6px;font-weight:600;">{{ $notStarted }} not started</span>@endif
  @if($inProgress > 0)<span style="background:#fef9c3;color:#92400e;padding:2px 8px;border-radius:6px;font-weight:600;">{{ $inProgress }} draft</span>@endif
  @if($submitted > 0)<span style="background:#dbeafe;color:#1e40af;padding:2px 8px;border-radius:6px;font-weight:600;">{{ $submitted }} submitted</span>@endif
  @if($locked > 0)<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:6px;font-weight:600;">{{ $locked }} locked</span>@endif
</div>
@endif

@endsection
