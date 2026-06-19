@extends('layouts.app')
@section('title', 'Grade Encoding')
@section('breadcrumb', 'Gradebook')

@push('head')
<style>
/* ── Config Bar ────────────────────────────────────────────── */
.gb-config-bar {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}
.gb-config-left { display: flex; flex-direction: column; gap: .25rem; }
.gb-subject-name { font-size: 1.2rem; font-weight: 800; color: #0f172a; line-height: 1.2; }
.gb-meta-chips { display: flex; gap: .5rem; flex-wrap: wrap; align-items: center; margin-top: .35rem; }
.gb-chip {
  display: inline-flex; align-items: center; gap: .3rem;
  padding: .2rem .55rem; border-radius: 6px; font-size: .74rem; font-weight: 600;
  background: #f1f5f9; color: #475569;
}
.gb-chip svg { width: 12px; height: 12px; flex-shrink: 0; }
.gb-chip--indigo { background: #eef2ff; color: #4338ca; }
.gb-chip--teal   { background: #f0fdfa; color: #0f766e; }
.gb-chip--slate  { background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; }

/* Quarter selector */
.gb-quarter-wrap { display: flex; align-items: center; gap: .6rem; }
.gb-quarter-label { font-size: .75rem; font-weight: 700; color: #64748b; white-space: nowrap; }
.gb-quarter-select {
  padding: .4rem .75rem; border: 1.5px solid #e2e8f0; border-radius: 8px;
  font-size: .84rem; font-weight: 600; color: #0f172a; background: #f8fafc;
  cursor: pointer; min-width: 150px;
}
.gb-quarter-select:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }

/* Status badge */
.gb-status-badge {
  padding: .3rem .85rem; border-radius: 999px; font-size: .76rem; font-weight: 700;
  white-space: nowrap;
}
.gb-status--draft      { background: #fef9c3; color: #713f12; }
.gb-status--submitted  { background: #dbeafe; color: #1d4ed8; }
.gb-status--finalized  { background: #dcfce7; color: #166534; }
.gb-status--locked     { background: #fee2e2; color: #991b1b; }
.gb-status--no-entry   { background: #f1f5f9; color: #64748b; }
.gb-status--readonly   { background: #f3e8ff; color: #6b21a8; }

/* Readonly banner */
.gb-readonly-banner {
  display: flex; align-items: center; gap: .6rem;
  background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 10px;
  padding: .65rem 1rem; margin-bottom: 1rem;
  font-size: .84rem; color: #6b21a8; font-weight: 600;
}

/* ── Grade Table ────────────────────────────────────────────── */
.gb-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
  margin-bottom: 1.25rem;
}
.gb-table { width: 100%; border-collapse: collapse; font-size: .84rem; }
.gb-table th {
  padding: 10px 12px; text-align: center; font-weight: 700; font-size: .69rem;
  text-transform: uppercase; letter-spacing: .04em; color: #64748b;
  background: #f8fafc; border-bottom: 2px solid #e2e8f0; white-space: nowrap;
}
.gb-table th.left { text-align: left; }
.gb-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.gb-table tr:last-child td { border-bottom: none; }
.gb-table tr:hover td { background: #fafbfc; }
.gb-table tr.dropped-row td { background: #fef2f2 !important; opacity: .85; }
.gb-table tr.drop-reason-row td { background: #fff7ed; border-bottom: 1px solid #fed7aa; }

/* Score inputs */
.gb-score-input {
  width: 78px; padding: 6px 8px; text-align: center;
  border: 1.5px solid #e2e8f0; border-radius: 8px;
  font-size: .84rem; color: #0f172a; background: #fff;
  transition: border-color .15s, box-shadow .15s;
}
.gb-score-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.gb-score-input.error { border-color: #ef4444; background: #fff5f5; }
.gb-score-input:disabled { background: #f8fafc; color: #94a3b8; cursor: default; }

/* Input error tooltip */
.gb-input-err {
  font-size: .67rem; color: #dc2626; font-weight: 600;
  display: none; margin-top: 2px; text-align: center;
}
.gb-score-input.error + .gb-input-err { display: block; }

/* Grade display cells */
.gb-grade-cell { font-size: .95rem; font-weight: 800; text-align: center; }
.gb-transmuted-cell { font-size: .95rem; font-weight: 800; text-align: center; }
.gb-desc-badge {
  display: inline-block; padding: .2rem .55rem; border-radius: 6px;
  font-size: .7rem; font-weight: 700; text-align: center;
}
.gb-desc--pass { background: #dcfce7; color: #166534; }
.gb-desc--fail { background: #fee2e2; color: #991b1b; }
.gb-desc--none { background: #f1f5f9; color: #94a3b8; }

/* Status chip */
.gb-row-status {
  display: inline-block; padding: .18rem .5rem; border-radius: 5px;
  font-size: .68rem; font-weight: 700; text-transform: uppercase; text-align: center;
}
.gb-rs--draft      { background: #fef9c3; color: #92400e; }
.gb-rs--submitted  { background: #dbeafe; color: #1e40af; }
.gb-rs--finalized  { background: #dcfce7; color: #166534; }
.gb-rs--locked     { background: #fee2e2; color: #991b1b; }
.gb-rs--dropped    { background: #fee2e2; color: #991b1b; }
.gb-rs--none       { background: #f1f5f9; color: #94a3b8; }

/* Stat strip */
.gb-stat-strip {
  display: flex; gap: 1rem; padding: .75rem 1.25rem;
  background: #f8fafc; border-top: 1px solid #e2e8f0;
  font-size: .78rem; color: #64748b; flex-wrap: wrap;
}
.gb-stat { display: flex; flex-direction: column; align-items: center; gap: 1px; }
.gb-stat__val { font-size: 1.05rem; font-weight: 800; color: #0f172a; }
.gb-stat__lbl { font-size: .68rem; text-transform: uppercase; letter-spacing: .04em; }

/* Action bar */
.gb-action-bar {
  padding: .9rem 1.25rem; background: #f8fafc; border-top: 1px solid #e2e8f0;
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: .75rem;
}
.gb-action-bar .info { font-size: .78rem; color: #64748b; }
.gb-btn {
  padding: .5rem 1.15rem; border: none; border-radius: 9px;
  font-size: .84rem; font-weight: 700; cursor: pointer; white-space: nowrap;
  transition: opacity .15s;
}
.gb-btn:hover { opacity: .88; }
.gb-btn--primary  { background: #6366f1; color: #fff; }
.gb-btn--dark     { background: #0f172a; color: #fff; }
.gb-btn--danger   { background: #dc2626; color: #fff; }
.gb-btn--ghost    { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.gb-btn--green    { background: #16a34a; color: #fff; }
.gb-btn--red-soft { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.gb-btn--green-soft { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }

/* Tab bar */
.gb-tabs { display: flex; gap: 4px; border-bottom: 2px solid #e2e8f0; margin-bottom: 1.25rem; }
.gb-tab {
  background: none; border: none; padding: 10px 20px;
  font-size: .9rem; font-weight: 700; color: #64748b;
  border-bottom: 2px solid transparent; margin-bottom: -2px; cursor: pointer;
}
.gb-tab.active { color: #1d4ed8; border-bottom-color: #1d4ed8; }

/* Unlock panel */
.gb-unlock-panel {
  padding: 1.1rem 1.25rem; background: #fef2f2; border-top: 1px solid #fecaca;
}
.gb-unlock-panel .title { font-size: .85rem; font-weight: 700; color: #991b1b; margin-bottom: .6rem; }

/* Flash */
.gb-flash { border-radius: 10px; padding: 11px 16px; margin-bottom: 1rem; font-size: .85rem; }
.gb-flash--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.gb-flash--error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

/* Empty state */
.gb-empty {
  background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
  padding: 3rem 1.5rem; text-align: center; color: #94a3b8;
}
.gb-empty__title { font-size: .95rem; font-weight: 700; color: #374151; margin-bottom: .4rem; }
.gb-empty__sub   { font-size: .84rem; }
</style>
@endpush

@section('content')
@php
  $wwPct = round(($subjectWeights['ww'] ?? 0.30) * 100);
  $ptPct = round(($subjectWeights['pt'] ?? 0.50) * 100);
  $qaPct = round(($subjectWeights['qa'] ?? 0.20) * 100);

  // Overall status for the badge
  $overallStatus = 'no-entry';
  if ($anyLocked)    $overallStatus = 'locked';
  elseif ($anyFinalized) $overallStatus = 'finalized';
  elseif ($allSubmitted) $overallStatus = 'submitted';
  elseif ($grades->isNotEmpty()) $overallStatus = 'draft';
  if (!$isActiveQuarter) $overallStatus = 'readonly';

  $overallLabel = match($overallStatus) {
    'locked'    => 'Locked',
    'finalized' => 'Finalized',
    'submitted' => 'Submitted — Awaiting Registrar',
    'draft'     => 'Draft',
    'readonly'  => ($quarter ? $quarter->quarter_name : 'Quarter') . ' — Read Only',
    default     => 'No Entry Yet',
  };

  // Schedule display
  $daysLabel = $ss->schedule_days_label ?? '';
  $timeRange = $ss->time_range ?? '';
@endphp

{{-- Back link --}}
<a href="{{ route('faculty.gradebook') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:.82rem;color:#6366f1;text-decoration:none;margin-bottom:16px;font-weight:600;">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
  </svg>
  All Classes
</a>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- CLASS CONFIGURATION BAR                                    --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="gb-config-bar">
  <div class="gb-config-left">
    <div class="gb-subject-name">{{ $ss->subject_name ?? '—' }}</div>
    <div class="gb-meta-chips">
      <span class="gb-chip gb-chip--indigo">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        {{ $ss->section_name ?? 'No Section' }}
      </span>
      @if($ss->section?->grade_level)
      <span class="gb-chip">{{ $ss->section->grade_level }}</span>
      @endif
      @if($daysLabel)
      <span class="gb-chip gb-chip--teal">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg>
        {{ $daysLabel }}{{ $timeRange ? ' · ' . $timeRange : '' }}
      </span>
      @endif
      @if($ss->room)
      <span class="gb-chip">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        {{ $ss->room }}
      </span>
      @endif
      <span class="gb-chip gb-chip--slate">
        WW {{ $wwPct }}% · PT {{ $ptPct }}% · QA {{ $qaPct }}%
      </span>
    </div>
  </div>

  <div style="display:flex;align-items:center;gap:.85rem;flex-wrap:wrap;">
    {{-- Quarter toggle --}}
    @if($allQuarters->isNotEmpty())
    <form method="GET" action="{{ route('faculty.gradebook.show', $ss) }}" class="gb-quarter-wrap">
      <label class="gb-quarter-label" for="gb-q-select">Quarter</label>
      <select id="gb-q-select" name="quarter_id" class="gb-quarter-select" onchange="this.form.submit()">
        @foreach($allQuarters as $q)
          <option value="{{ $q->id }}" @selected($quarter && $q->id === $quarter->id)>
            {{ $q->quarter_name }}{{ $q->id === ($activeQuarter?->id) ? ' (active)' : '' }}
          </option>
        @endforeach
      </select>
    </form>
    @endif

    {{-- Status badge --}}
    <span class="gb-status-badge gb-status--{{ $overallStatus }}">{{ $overallLabel }}</span>
  </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="gb-flash gb-flash--success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="gb-flash gb-flash--error">{{ session('error') }}</div>
@endif
@foreach($errors->all() as $err)
<div class="gb-flash gb-flash--error">{{ $err }}</div>
@endforeach

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- TAB BAR                                                    --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="gb-tabs">
  <button type="button" id="tab-btn-grades" class="gb-tab active" onclick="switchTab('grades')">
    Grades
    <span style="margin-left:5px;background:#e0e7ff;color:#3730a3;padding:1px 7px;border-radius:99px;font-size:.68rem;">
      {{ $enrollments->count() }}
    </span>
  </button>
  <button type="button" id="tab-btn-ann" class="gb-tab" onclick="switchTab('ann')">
    Announcements
    @if($sectionAnnouncements->isNotEmpty())
    <span style="margin-left:5px;background:#f1f5f9;color:#475569;padding:1px 7px;border-radius:99px;font-size:.68rem;">
      {{ $sectionAnnouncements->count() }}
    </span>
    @endif
  </button>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- GRADES TAB                                                 --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div id="tab-grades">

@if(!$quarter)
  <div class="gb-empty">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:44px;height:44px;margin:0 auto 12px;display:block;color:#cbd5e1;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
    </svg>
    <div class="gb-empty__title">No Active Grading Quarter</div>
    <div class="gb-empty__sub">Ask the admin to activate a grading quarter before entering grades.</div>
  </div>

@elseif($enrollments->isEmpty())
  <div class="gb-empty">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:44px;height:44px;margin:0 auto 12px;display:block;color:#cbd5e1;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
    </svg>
    <div class="gb-empty__title">No Active Students</div>
    <div class="gb-empty__sub">No active (enrolled) students found in this section. Inactive and transferred students are automatically excluded.</div>
  </div>

@else

  {{-- Read-only banner for historical quarters --}}
  @if(!$isActiveQuarter)
  <div class="gb-readonly-banner">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;flex-shrink:0;">
      <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
    </svg>
    Viewing <strong>&nbsp;{{ $quarter->quarter_name }}&nbsp;</strong> — Read-only. Switch to the active quarter to enter or edit grades.
  </div>
  @endif

  {{-- Standalone grade form (kept OUT of the table to avoid invalid nested
       forms — the drop/reinstate row forms live inside the table). Grade
       inputs link to it via form="grade-form". --}}
  <form method="POST" action="{{ route('faculty.gradebook.save-draft', $ss) }}" id="grade-form"></form>
  <input type="hidden" name="_token" value="{{ csrf_token() }}" form="grade-form">

  <div class="gb-card">
    <div style="overflow-x:auto;">
      <table class="gb-table">
        <thead>
          <tr>
            <th class="left" style="padding-left:16px;width:30px;">#</th>
            <th class="left" style="min-width:180px;">Student</th>
            <th style="min-width:100px;">
              Written Work
              <div style="font-size:.63rem;color:#94a3b8;font-weight:500;text-transform:none;letter-spacing:0;">{{ $wwPct }}% weight</div>
            </th>
            <th style="min-width:100px;">
              Performance Task
              <div style="font-size:.63rem;color:#94a3b8;font-weight:500;text-transform:none;letter-spacing:0;">{{ $ptPct }}% weight</div>
            </th>
            <th style="min-width:100px;">
              Quarterly Assessment
              <div style="font-size:.63rem;color:#94a3b8;font-weight:500;text-transform:none;letter-spacing:0;">{{ $qaPct }}% weight</div>
            </th>
            <th style="min-width:80px;">
              Initial Grade
              <div style="font-size:.63rem;color:#94a3b8;font-weight:500;text-transform:none;letter-spacing:0;">Weighted Avg</div>
            </th>
            <th style="min-width:80px;">
              Transmuted
              <div style="font-size:.63rem;color:#94a3b8;font-weight:500;text-transform:none;letter-spacing:0;">DepEd Table</div>
            </th>
            <th style="min-width:130px;">Descriptor</th>
            <th style="min-width:80px;">Status</th>
            <th style="min-width:90px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($enrollments as $i => $enrollment)
          @php
            $grade   = $grades->get($enrollment->id);
            $locked  = $grade && in_array($grade->status, ['finalized', 'locked']);
            $dropped = $grade?->isDropped();
            $student = $enrollment->student;
            $inputDisabled = $locked || $dropped || !$isActiveQuarter;
            $studentNameJs = addslashes($student?->full_name ?? '');
          @endphp

          <tr class="grade-row{{ $dropped ? ' dropped-row' : '' }}" data-enrollment="{{ $enrollment->id }}">

            {{-- # --}}
            <td style="padding-left:16px;color:#94a3b8;font-size:.8rem;">{{ $i + 1 }}</td>

            {{-- Student --}}
            <td>
              <div style="font-weight:700;color:{{ $dropped ? '#991b1b' : '#0f172a' }};{{ $dropped ? 'text-decoration:line-through;' : '' }}">
                {{ $student?->full_name ?? '—' }}
              </div>
              @if($student?->lrn)
              <div style="font-size:.71rem;color:#94a3b8;">LRN: {{ $student->lrn }}</div>
              @endif
              @if($dropped)
              <div style="font-size:.71rem;color:#dc2626;margin-top:2px;">
                Dropped {{ $grade->dropped_at?->format('M d, Y') }}
                @if($grade->drop_reason)· {{ Str::limit($grade->drop_reason, 45) }}@endif
              </div>
              @endif
            </td>

            {{-- Written Work --}}
            <td style="text-align:center;">
              <div>
                <input type="number"
                       name="grades[{{ $enrollment->id }}][written_work]" form="grade-form"
                       class="gb-score-input ww-input"
                       value="{{ old("grades.{$enrollment->id}.written_work", $grade?->written_work) }}"
                       min="0" max="100" step="0.01"
                       placeholder="0–100"
                       {{ $inputDisabled ? 'disabled' : '' }}
                       data-field="ww">
                <div class="gb-input-err">Out of range (0–100)</div>
              </div>
            </td>

            {{-- Performance Task --}}
            <td style="text-align:center;">
              <div>
                <input type="number"
                       name="grades[{{ $enrollment->id }}][performance_task]" form="grade-form"
                       class="gb-score-input pt-input"
                       value="{{ old("grades.{$enrollment->id}.performance_task", $grade?->performance_task) }}"
                       min="0" max="100" step="0.01"
                       placeholder="0–100"
                       {{ $inputDisabled ? 'disabled' : '' }}
                       data-field="pt">
                <div class="gb-input-err">Out of range (0–100)</div>
              </div>
            </td>

            {{-- Quarterly Assessment --}}
            <td style="text-align:center;">
              <div>
                <input type="number"
                       name="grades[{{ $enrollment->id }}][quarterly_assessment]" form="grade-form"
                       class="gb-score-input qa-input"
                       value="{{ old("grades.{$enrollment->id}.quarterly_assessment", $grade?->quarterly_assessment) }}"
                       min="0" max="100" step="0.01"
                       placeholder="0–100"
                       {{ $inputDisabled ? 'disabled' : '' }}
                       data-field="qa">
                <div class="gb-input-err">Out of range (0–100)</div>
              </div>
            </td>

            {{-- Initial Grade --}}
            <td style="text-align:center;">
              @if($dropped)
                <span style="font-size:.78rem;font-weight:600;color:#dc2626;">Dropped</span>
              @else
                <span class="gb-grade-cell">
                  {{ $grade?->final_grade !== null ? number_format($grade->final_grade, 2) : '—' }}
                </span>
              @endif
            </td>

            {{-- Transmuted Grade --}}
            <td style="text-align:center;">
              @if($dropped)
                <span style="color:#94a3b8;font-size:.8rem;">—</span>
              @else
                @php
                  $tg = null;
                  if ($grade?->final_grade !== null) {
                    $ig = (float) $grade->final_grade;
                    if ($ig >= 75) { $tg = round($ig); }
                    elseif ($ig >= 74) { $tg = 74; }
                    elseif ($ig >= 72) { $tg = 73; }
                    elseif ($ig >= 70) { $tg = 72; }
                    elseif ($ig >= 68) { $tg = 71; }
                    elseif ($ig >= 66) { $tg = 70; }
                    elseif ($ig >= 64) { $tg = 69; }
                    elseif ($ig >= 62) { $tg = 68; }
                    elseif ($ig >= 60) { $tg = 67; }
                    elseif ($ig >= 58) { $tg = 66; }
                    elseif ($ig >= 56) { $tg = 65; }
                    elseif ($ig >= 54) { $tg = 64; }
                    elseif ($ig >= 52) { $tg = 63; }
                    elseif ($ig >= 50) { $tg = 62; }
                    elseif ($ig >= 48) { $tg = 61; }
                    else { $tg = 60; }
                  }
                @endphp
                <span class="gb-transmuted-cell{{ $tg !== null ? ($tg >= 75 ? ' gb-transmuted--pass' : ' gb-transmuted--fail') : '' }}"
                      style="{{ $tg !== null ? ($tg >= 75 ? 'color:#166534;' : 'color:#dc2626;') : 'color:#94a3b8;' }}">
                  {{ $tg ?? '—' }}
                </span>
              @endif
            </td>

            {{-- Descriptor --}}
            <td style="text-align:center;">
              @if($dropped)
                <span class="gb-desc-badge gb-desc--none">—</span>
              @else
                <span class="descriptor-display gb-desc-badge {{ $grade?->final_grade !== null ? ($grade->final_grade >= 75 ? 'gb-desc--pass' : 'gb-desc--fail') : 'gb-desc--none' }}">
                  {{ $grade?->descriptor ?? '—' }}
                </span>
              @endif
            </td>

            {{-- Status --}}
            <td style="text-align:center;">
              @if($dropped)
                <span class="gb-row-status gb-rs--dropped">Dropped</span>
              @elseif(!$grade)
                <span class="gb-row-status gb-rs--none">No entry</span>
              @elseif($grade->status === 'locked')
                <span class="gb-row-status gb-rs--locked">Locked</span>
              @elseif($grade->status === 'finalized')
                <span class="gb-row-status gb-rs--finalized">Finalized</span>
              @elseif($grade->status === 'submitted')
                <span class="gb-row-status gb-rs--submitted">Submitted</span>
              @else
                <span class="gb-row-status gb-rs--draft">Draft</span>
              @endif
            </td>

            {{-- Actions --}}
            <td style="text-align:center;">
              @if($dropped && $isActiveQuarter)
                <form method="POST" action="{{ route('faculty.gradebook.reinstate', $ss) }}">
                  @csrf
                  <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                  <button type="submit" class="gb-btn gb-btn--green-soft" style="font-size:.72rem;padding:.28rem .65rem;">
                    Reinstate
                  </button>
                </form>
              @elseif(!$locked && !$dropped && $isActiveQuarter)
                <button type="button"
                        onclick="toggleDropForm({{ $enrollment->id }})"
                        class="gb-btn gb-btn--red-soft"
                        style="font-size:.72rem;padding:.28rem .65rem;">
                  Drop
                </button>
              @else
                <span style="color:#94a3b8;font-size:.75rem;">—</span>
              @endif
            </td>
          </tr>

          {{-- Inline drop reason form --}}
          @if(!$dropped && !$locked && $isActiveQuarter)
          <tr id="drop-form-{{ $enrollment->id }}" class="drop-reason-row" style="display:none;">
            <td colspan="10" style="padding:12px 20px;">
              <form method="POST" action="{{ route('faculty.gradebook.drop', $ss) }}">
                @csrf
                <input type="hidden" name="enrollment_id" value="{{ $enrollment->id }}">
                <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                  <div style="flex:1;min-width:260px;">
                    <label style="display:block;font-size:.74rem;font-weight:700;color:#92400e;margin-bottom:4px;">
                      Drop Reason <span style="color:#dc2626;">*</span> (min 10 chars)
                    </label>
                    <textarea name="drop_reason" rows="2" required minlength="10" maxlength="500"
                              placeholder="Reason the student is being dropped from this subject..."
                              style="width:100%;padding:7px 10px;border:1px solid #fed7aa;border-radius:8px;font-size:.83rem;resize:vertical;box-sizing:border-box;"></textarea>
                  </div>
                  <div style="display:flex;gap:8px;">
                    <button type="submit" class="gb-btn gb-btn--danger" style="font-size:.82rem;"
                            onclick="return confirm('Mark {{ $studentNameJs }} as dropped from this subject? This is logged and reversible.')">
                      Confirm Drop
                    </button>
                    <button type="button" onclick="toggleDropForm({{ $enrollment->id }})"
                            class="gb-btn gb-btn--ghost" style="font-size:.82rem;">
                      Cancel
                    </button>
                  </div>
                </div>
              </form>
            </td>
          </tr>
          @endif

          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Stats strip --}}
    @php
      $gradedCount   = $grades->filter(fn($g) => $g->final_grade !== null && !$g->isDropped())->count();
      $passingCount  = $grades->filter(fn($g) => $g->final_grade !== null && $g->final_grade >= 75 && !$g->isDropped())->count();
      $droppedCount  = $grades->filter(fn($g) => $g->isDropped())->count();
      $classAvg      = $gradedCount > 0 ? round($grades->filter(fn($g) => $g->final_grade !== null && !$g->isDropped())->avg('final_grade'), 2) : null;
    @endphp
    <div class="gb-stat-strip">
      <div class="gb-stat">
        <span class="gb-stat__val">{{ $enrollments->count() }}</span>
        <span class="gb-stat__lbl">Students</span>
      </div>
      <div class="gb-stat">
        <span class="gb-stat__val">{{ $gradedCount }}</span>
        <span class="gb-stat__lbl">Graded</span>
      </div>
      <div class="gb-stat">
        <span class="gb-stat__val" style="color:#16a34a;">{{ $passingCount }}</span>
        <span class="gb-stat__lbl">Passing</span>
      </div>
      @if($droppedCount > 0)
      <div class="gb-stat">
        <span class="gb-stat__val" style="color:#dc2626;">{{ $droppedCount }}</span>
        <span class="gb-stat__lbl">Dropped</span>
      </div>
      @endif
      @if($classAvg !== null)
      <div class="gb-stat">
        <span class="gb-stat__val" style="color:{{ $classAvg >= 75 ? '#16a34a' : '#dc2626' }};">{{ $classAvg }}</span>
        <span class="gb-stat__lbl">Class Avg</span>
      </div>
      @endif
      <div class="gb-stat" style="margin-left:auto;">
        <span class="gb-stat__val" style="font-size:.8rem;color:#6366f1;">WW {{ $wwPct }}% · PT {{ $ptPct }}% · QA {{ $qaPct }}%</span>
        <span class="gb-stat__lbl">Grade Weights</span>
      </div>
    </div>

    {{-- Action bar --}}
    @if($isActiveQuarter && !$anyFinalized)
    <div class="gb-action-bar">
      <div class="info">{{ $quarter?->quarter_name }} &nbsp;·&nbsp; {{ $enrollments->count() }} student(s)</div>
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        @if(!$allSubmitted)
        <button type="submit" form="grade-form" class="gb-btn gb-btn--primary">
          Save Draft
        </button>
        @endif
        @if(!$allSubmitted && $grades->isNotEmpty())
        <button type="button" class="gb-btn gb-btn--dark"
                onclick="submitGrades()">
          Submit for Review
        </button>
        @endif
      </div>
    </div>
    @endif

    {{-- Unlock request panel --}}
    @if($isActiveQuarter && $anyLocked && !$anyFinalized)
    <div class="gb-unlock-panel">
      <div class="title">Grades are locked — Request an Unlock</div>
      <form method="POST" action="{{ route('faculty.gradebook.request-unlock', $ss) }}">
        @csrf
        <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
          <div style="flex:1;min-width:220px;">
            <textarea name="reason" rows="2" required minlength="10" maxlength="1000"
                      placeholder="Explain why you need to edit these grades..."
                      style="width:100%;padding:8px 10px;border:1px solid #fca5a5;border-radius:8px;font-size:.83rem;resize:vertical;box-sizing:border-box;">{{ old('reason') }}</textarea>
            @error('reason')
            <div style="font-size:.74rem;color:#dc2626;margin-top:3px;">{{ $message }}</div>
            @enderror
          </div>
          <button type="submit" class="gb-btn gb-btn--danger" style="white-space:nowrap;">
            Submit Unlock Request
          </button>
        </div>
      </form>
    </div>
    @endif

  </div>{{-- /.gb-card --}}

  {{-- Hidden submit-grades form --}}
  <form id="submit-form" method="POST" action="{{ route('faculty.gradebook.submit', $ss) }}" style="display:none;">
    @csrf
  </form>

@endif
</div>{{-- /#tab-grades --}}

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ANNOUNCEMENTS TAB                                          --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div id="tab-ann" style="display:none;">
  <div class="gb-card" style="overflow:visible;">
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9;">
      <div style="font-size:1rem;font-weight:800;color:#0f172a;margin-bottom:3px;">Post an Announcement</div>
      <div style="font-size:.82rem;color:#64748b;">Sent only to enrolled students of <strong>{{ $ss->section_name ?? 'this section' }}</strong>.</div>
    </div>
    <div style="padding:1.25rem 1.5rem;">
      <form method="POST" action="{{ route('faculty.gradebook.announce', $ss) }}" style="display:flex;flex-direction:column;gap:14px;">
        @csrf
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;margin-bottom:5px;">Title</label>
          <input type="text" name="title" required maxlength="255" value="{{ old('title') }}"
                 style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;box-sizing:border-box;">
          @error('title')<div style="font-size:.74rem;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
        </div>
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;margin-bottom:5px;">Message</label>
          <textarea name="message" required maxlength="2000" rows="4"
                    style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;box-sizing:border-box;">{{ old('message') }}</textarea>
          @error('message')<div style="font-size:.74rem;color:#dc2626;margin-top:3px;">{{ $message }}</div>@enderror
        </div>
        <div style="max-width:200px;">
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;margin-bottom:5px;">Priority</label>
          <select name="priority" required style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
            <option value="low"    @selected(old('priority','low') === 'low')>Notice</option>
            <option value="medium" @selected(old('priority') === 'medium')>Medium</option>
            <option value="high"   @selected(old('priority') === 'high')>High</option>
          </select>
        </div>
        <div>
          <button type="submit" class="gb-btn gb-btn--primary">Post to Section</button>
        </div>
      </form>
    </div>
  </div>

  <div style="font-size:.9rem;font-weight:800;color:#0f172a;margin:0 0 12px;">Posted to this section</div>
  @forelse($sectionAnnouncements as $a)
    <div class="gb-card" style="overflow:visible;margin-bottom:10px;">
      <div style="padding:14px 18px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
          <div style="font-size:.92rem;font-weight:700;color:#0f172a;">{{ $a->title }}</div>
          <div style="font-size:.72rem;color:#94a3b8;white-space:nowrap;">{{ $a->created_at->format('M d, Y') }}</div>
        </div>
        <p style="font-size:.85rem;color:#475569;margin:6px 0 0;line-height:1.5;">{{ $a->message }}</p>
      </div>
    </div>
  @empty
    <div style="background:#f8fafc;border:1px dashed #cbd5e1;border-radius:12px;padding:28px;text-align:center;color:#94a3b8;font-size:.85rem;">
      No announcements posted to this section yet.
    </div>
  @endforelse
</div>{{-- /#tab-ann --}}

@endsection

@push('scripts')
<script>
(function () {
  'use strict';

  /* ── Weights from server ─────────────────────────────── */
  const WW_W = {{ $subjectWeights['ww'] }};
  const PT_W = {{ $subjectWeights['pt'] }};
  const QA_W = {{ $subjectWeights['qa'] }};

  /* ── DepEd Descriptor table ──────────────────────────── */
  const DESCRIPTORS = [
    { min: 90, max: 100, label: 'Outstanding',              pass: true  },
    { min: 85, max:  89, label: 'Very Satisfactory',        pass: true  },
    { min: 80, max:  84, label: 'Satisfactory',             pass: true  },
    { min: 75, max:  79, label: 'Fairly Satisfactory',      pass: true  },
    { min:  0, max:  74, label: 'Did Not Meet Expectations',pass: false },
  ];

  /* ── DepEd Transmutation table (DO 8 s. 2015) ───────── */
  function transmuteGrade(ig) {
    if (isNaN(ig)) return null;
    ig = Math.max(0, Math.min(100, ig));
    if (ig >= 75) return Math.round(ig);   // passing: TG = IG
    if (ig >= 74) return 74;
    if (ig >= 72) return 73;
    if (ig >= 70) return 72;
    if (ig >= 68) return 71;
    if (ig >= 66) return 70;
    if (ig >= 64) return 69;
    if (ig >= 62) return 68;
    if (ig >= 60) return 67;
    if (ig >= 58) return 66;
    if (ig >= 56) return 65;
    if (ig >= 54) return 64;
    if (ig >= 52) return 63;
    if (ig >= 50) return 62;
    if (ig >= 48) return 61;
    return 60;   // minimum grade (0–47.99)
  }

  function getDescriptor(tg) {
    for (const d of DESCRIPTORS) {
      if (tg >= d.min && tg <= d.max) return d;
    }
    return null;
  }

  /* ── Validate a single score input ──────────────────── */
  function validateInput(input) {
    const val = input.value.trim();
    if (val === '') {
      input.classList.remove('error');
      return true;
    }
    const num = parseFloat(val);
    const valid = !isNaN(num) && num >= 0 && num <= 100;
    input.classList.toggle('error', !valid);
    return valid;
  }

  /* ── Recalculate one grade row ───────────────────────── */
  function recalcRow(row) {
    const wwIn  = row.querySelector('.ww-input');
    const ptIn  = row.querySelector('.pt-input');
    const qaIn  = row.querySelector('.qa-input');
    const igEl  = row.querySelector('.gb-grade-cell');
    const tgEl  = row.querySelector('.gb-transmuted-cell');
    const descEl = row.querySelector('.descriptor-display');

    if (!igEl) return;   // dropped row — no display elements

    const ww = parseFloat(wwIn?.value);
    const pt = parseFloat(ptIn?.value);
    const qa = parseFloat(qaIn?.value);

    if (isNaN(ww) || isNaN(pt) || isNaN(qa)) {
      igEl.textContent  = '—';
      if (tgEl)  { tgEl.textContent = '—'; tgEl.style.color = '#94a3b8'; }
      if (descEl) {
        descEl.textContent = '—';
        descEl.className = 'descriptor-display gb-desc-badge gb-desc--none';
      }
      return;
    }

    const ig     = Math.min(100, Math.max(0, Math.round((ww * WW_W + pt * PT_W + qa * QA_W) * 100) / 100));
    const tg     = transmuteGrade(ig);
    const desc   = getDescriptor(tg);

    igEl.textContent = ig.toFixed(2);

    if (tgEl) {
      tgEl.textContent = tg !== null ? tg : '—';
      tgEl.style.color = tg !== null ? (tg >= 75 ? '#166534' : '#dc2626') : '#94a3b8';
    }

    if (descEl) {
      descEl.textContent = desc ? desc.label : '—';
      descEl.className   = 'descriptor-display gb-desc-badge ' +
        (desc ? (desc.pass ? 'gb-desc--pass' : 'gb-desc--fail') : 'gb-desc--none');
    }
  }

  /* ── Boot: wire up all rows ──────────────────────────── */
  document.querySelectorAll('.grade-row').forEach(function (row) {
    recalcRow(row);

    row.querySelectorAll('.gb-score-input').forEach(function (input) {
      /* Type + range validation on every keystroke */
      input.addEventListener('input', function () {
        validateInput(input);
        recalcRow(row);
      });

      /* Also validate on blur to catch paste events */
      input.addEventListener('blur', function () {
        validateInput(input);
      });

      /* Block non-numeric key characters (allow: digits, dot, minus, backspace, arrows) */
      input.addEventListener('keydown', function (e) {
        const allowed = ['Backspace','Delete','Tab','Escape','Enter','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','.'];
        if (allowed.includes(e.key)) return;
        if (e.ctrlKey || e.metaKey) return;   // allow Ctrl+C, Ctrl+V, etc.
        if (!/^\d$/.test(e.key)) {
          e.preventDefault();
        }
      });
    });
  });

  /* ── Tab switching ───────────────────────────────────── */
  window.switchTab = function (which) {
    const gradesDiv = document.getElementById('tab-grades');
    const annDiv    = document.getElementById('tab-ann');
    const btnG      = document.getElementById('tab-btn-grades');
    const btnA      = document.getElementById('tab-btn-ann');
    if (which === 'ann') {
      gradesDiv.style.display = 'none';
      annDiv.style.display    = '';
      btnA.classList.add('active');    btnG.classList.remove('active');
    } else {
      annDiv.style.display    = 'none';
      gradesDiv.style.display = '';
      btnG.classList.add('active');    btnA.classList.remove('active');
    }
  };

  /* Open announcements tab if announcement validation failed */
  @if($errors->any() && old('title') !== null)
    document.addEventListener('DOMContentLoaded', () => switchTab('ann'));
  @endif

  /* ── Submit grades confirmation ─────────────────────── */
  window.submitGrades = function () {
    // Check for any validation errors first
    const hasErrors = document.querySelectorAll('.gb-score-input.error').length > 0;
    if (hasErrors) {
      alert('Please fix the highlighted input errors before submitting grades.');
      return;
    }
    if (confirm('Submit all graded students for registrar review?\n\nThis cannot be undone. Make sure all scores are final.')) {
      document.getElementById('submit-form').submit();
    }
  };

  /* ── Drop form toggle ────────────────────────────────── */
  window.toggleDropForm = function (enrollmentId) {
    const row = document.getElementById('drop-form-' + enrollmentId);
    if (row) row.style.display = row.style.display === 'none' ? '' : 'none';
  };

})();
</script>
@endpush
