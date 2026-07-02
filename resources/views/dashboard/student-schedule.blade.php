@extends('layouts.app')

@section('title', 'Schedule & Assessment')
@section('breadcrumb', 'Schedule & Assessment')

@push('head')
<style>
/* ── Student schedule: toggle + print ─────────────────────────── */
.sched-print-header { display: none; }
.sched-weekly-list  { display: none; } /* JS shows in list mode; print CSS always forces it visible */

.sched-btn-active {
  background: rgba(99,102,241,.35) !important;
  border-color: rgba(99,102,241,.6) !important;
  color: #a5b4fc !important;
}

/* Grid table */
.sched-grid-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
.sched-grid-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 540px;
  table-layout: fixed;
}
.sched-grid-table thead tr { border-bottom: 1px solid rgba(255,255,255,.1); }
.sched-grid-table tbody tr { border-bottom: 1px solid rgba(255,255,255,.05); }
.sched-grid-table th, .sched-grid-table td { padding: 8px 6px; vertical-align: top; }
.sched-grid-table td { border-right: 1px solid rgba(255,255,255,.05); }
.sched-grid-table td:last-child, .sched-grid-table th:last-child { border-right: none; }

/* Today column highlight */
.sched-col-today th { color: #a5b4fc !important; background: rgba(99,102,241,.14); }
.sched-col-today td { background: rgba(99,102,241,.08); }

@media print {
  /* Hide all app chrome and glass elements */
  .enc-sidebar, #enc-sidebar, .enc-header,
  .enc-page__header, .student-glass-card, .no-print { display: none !important; }

  /* Show print-only elements regardless of JS toggle state */
  .sched-print-header { display: block !important; }
  .sched-weekly-list  { display: block !important;
                         background: transparent !important;
                         border: none !important;
                         border-radius: 0 !important;
                         padding: 0 !important; }

  html, body { background: #fff !important; }
  .enc-main, [class*="enc-main"] {
    margin-left: 0 !important;
    padding-left: 0 !important;
    padding-top: 0 !important;
  }

  /* Clean up list day cards for print */
  .sched-list-day {
    border: 1px solid #d1d5db !important;
    box-shadow: none !important;
    background: #fff !important;
    border-radius: 4px !important;
    break-inside: avoid;
    page-break-inside: avoid;
    margin-bottom: 6pt !important;
  }
  .sched-list-day * { color: #1e293b !important; }
  .sched-list-day-head { background: #f3f4f6 !important; border-bottom: 1px solid #d1d5db !important; }
  .sched-list-day-row  { background: #fff !important; border-bottom: 1px solid #f1f5f9 !important; }

  @page { margin: 1.5cm; size: A4 portrait; }
}
</style>
@endpush

@section('content')
@php
  $dayOrder   = ['monday','tuesday','wednesday','thursday','friday','saturday'];
  $dayLabels  = ['monday'=>'Monday','tuesday'=>'Tuesday','wednesday'=>'Wednesday','thursday'=>'Thursday','friday'=>'Friday','saturday'=>'Saturday'];
  $dayAbbrevs = ['monday'=>'Mon','tuesday'=>'Tue','wednesday'=>'Wed','thursday'=>'Thu','friday'=>'Fri','saturday'=>'Sat'];
  $todayName  = strtolower(now()->format('l'));

  // Grid: time slots + 2-D cell map
  $timeSlots   = [];   // start_time => end_time
  $grid        = [];   // start_time => [day => SectionSubject]
  $weekGrouped = [];   // day        => [SectionSubject, ...]

  foreach ($sectionSubjects ?? collect() as $ss) {
    $t = $ss->start_time ?? '';
    if (!$t) continue;
    if (!isset($timeSlots[$t])) {
      $timeSlots[$t] = $ss->end_time ?? '';
    }
    foreach ($ss->days ?? [] as $d) {
      $grid[$t][$d]    = $ss;
      $weekGrouped[$d][] = $ss;
    }
  }
  ksort($timeSlots);
  ksort($grid);
  $hasSchedule = !empty($timeSlots);

  // JSON payload for CSV export
  $scheduleForJs = collect($dayOrder)->mapWithKeys(function ($day) use ($weekGrouped, $dayLabels) {
    return [
      $dayLabels[$day] => collect($weekGrouped[$day] ?? [])
        ->sortBy('start_time')
        ->map(fn($ss) => [
          'time'    => $ss->time_range,
          'subject' => $ss->subject?->subject_name ?? '—',
          'teacher' => $ss->faculty?->full_name ?? '—',
          'room'    => $ss->room ?? '—',
        ])->values()->all(),
    ];
  });
@endphp

{{-- ── Print-only school header ────────────────────────────────── --}}
<div class="sched-print-header" style="margin-bottom:18px;padding-bottom:14px;border-bottom:2px solid #0f172a;">
  <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin-bottom:2px;">{{ config('app.name') }}</div>
  <div style="font-size:1.4rem;font-weight:800;color:#0f172a;">Weekly Class Schedule</div>
  <div style="font-size:.85rem;color:#475569;margin-top:6px;">
    <strong>{{ $studentInfo['full_name'] }}</strong>
    &nbsp;&middot;&nbsp; {{ $studentInfo['section'] }} ({{ $studentInfo['grade_level'] }})
    &nbsp;&middot;&nbsp; Printed {{ now()->format('F d, Y') }}
  </div>
</div>

{{-- ── Screen page header + controls ──────────────────────────── --}}
<div class="enc-page__header">
  <div class="enc-page__title-row" style="align-items:flex-start;gap:12px;flex-wrap:wrap;">
    <div>
      <h1 class="enc-page__title">Schedule & Assessment</h1>
      <p class="enc-page__subtitle">{{ $studentInfo['full_name'] }} — {{ now()->format('l, F d, Y') }}</p>
    </div>
    <div class="no-print" style="display:flex;gap:8px;align-items:center;flex-shrink:0;flex-wrap:wrap;">
      {{-- Grid / List toggle --}}
      <div style="display:flex;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:8px;overflow:hidden;">
        <button id="btn-grid" onclick="switchView('grid')"
                class="sched-btn-active"
                style="padding:.32rem .8rem;font-size:.78rem;font-weight:600;background:transparent;border:none;cursor:pointer;">Grid</button>
        <button id="btn-list" onclick="switchView('list')"
                style="padding:.32rem .8rem;font-size:.78rem;font-weight:600;color:rgba(255,255,255,.5);background:transparent;border:none;cursor:pointer;">List</button>
      </div>
      <button onclick="window.print()"
              style="display:inline-flex;align-items:center;gap:5px;padding:.32rem .8rem;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:8px;font-size:.78rem;font-weight:600;color:rgba(255,255,255,.8);cursor:pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 4.5h10.5M6 19.5h12a2.25 2.25 0 002.25-2.25v-7.5A2.25 2.25 0 0018 7.5H6a2.25 2.25 0 00-2.25 2.25v7.5A2.25 2.25 0 006 19.5zm.75-5.25h.008v.008H6.75v-.008zm3 0h.008v.008H9.75v-.008zm3 0h.008v.008h-.008v-.008z"/></svg>
        Print
      </button>
      <button onclick="downloadScheduleCSV()"
              style="display:inline-flex;align-items:center;gap:5px;padding:.32rem .8rem;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:8px;font-size:.78rem;font-weight:600;color:rgba(255,255,255,.8);cursor:pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
        CSV
      </button>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- GRID VIEW (default on screen, hidden in print)               --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="view-grid" class="enc-card student-glass-card" style="padding:1.25rem 1.5rem;">

  @if(!$hasSchedule)
    <div style="text-align:center;padding:3rem 1rem;color:rgba(255,255,255,.4);">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
           style="width:48px;height:48px;margin:0 auto 1rem;display:block;color:rgba(255,255,255,.2)">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
      </svg>
      <p style="font-size:.95rem;font-weight:600;">No Schedule Assigned</p>
      <p style="font-size:.82rem;margin-top:.3rem;">Your weekly schedule will appear here once configured.</p>
    </div>

  @else
    <div class="sched-grid-wrap">
      <table class="sched-grid-table">
        <colgroup>
          <col style="width:80px;">
          @foreach($dayOrder as $day)<col>@endforeach
        </colgroup>
        <thead>
          <tr>
            <th style="text-align:left;font-size:.67rem;color:rgba(255,255,255,.25);padding-bottom:10px;"></th>
            @foreach($dayOrder as $day)
            <th class="{{ $day === $todayName ? 'sched-col-today' : '' }}"
                style="text-align:center;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
                       color:{{ $day === $todayName ? '#a5b4fc' : 'rgba(255,255,255,.38)' }};padding-bottom:10px;">
              {{ $dayAbbrevs[$day] }}
              @if($day === $todayName)
              <div class="no-print" style="font-size:.58rem;font-weight:700;color:#818cf8;letter-spacing:.04em;margin-top:2px;">today</div>
              @endif
            </th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach($timeSlots as $startTime => $endTime)
          <tr>
            {{-- Time label --}}
            <td style="font-size:.67rem;color:rgba(255,255,255,.3);font-family:monospace;
                       white-space:nowrap;vertical-align:middle;text-align:right;padding-right:10px;">
              {{ substr($startTime, 0, 5) }}<br>
              <span style="color:rgba(255,255,255,.18);">{{ substr($endTime, 0, 5) }}</span>
            </td>
            {{-- One cell per day --}}
            @foreach($dayOrder as $day)
            @php $ss = $grid[$startTime][$day] ?? null; @endphp
            <td class="{{ $day === $todayName ? 'sched-col-today' : '' }}"
                style="padding:6px 5px;text-align:center;">
              @if($ss)
              <div style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);
                          border-radius:8px;padding:6px 7px;text-align:left;min-height:52px;">
                <div style="font-size:.76rem;font-weight:600;color:rgba(255,255,255,.88);line-height:1.3;">
                  {{ $ss->subject?->subject_name ?? '—' }}
                </div>
                @if($ss->room)
                <div style="font-size:.65rem;color:rgba(255,255,255,.45);margin-top:3px;">{{ $ss->room }}</div>
                @endif
                @if($ss->faculty)
                <div style="font-size:.62rem;color:rgba(255,255,255,.3);margin-top:1px;
                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                  {{ $ss->faculty->full_name }}
                </div>
                @endif
              </div>
              @else
              <div style="min-height:52px;"></div>
              @endif
            </td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- LIST VIEW — Today card (hidden by default, shown via toggle) --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="view-list-today" class="enc-card student-glass-card" style="display:none;padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Today's Schedule</div>
    <span class="enc-card__meta">{{ now()->format('l') }}'s class timetable</span>
  </div>
  <div class="enc-card__body">
    @if(empty($todaySchedule))
      <div style="text-align:center;padding:2.5rem 1rem;color:rgba(255,255,255,.4);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
             style="width:40px;height:40px;margin:0 auto .8rem;display:block;color:rgba(255,255,255,.2)">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
        </svg>
        <p style="font-size:.9rem;font-weight:600;">No Classes Today</p>
        <p style="font-size:.8rem;margin-top:.2rem;">No scheduled classes found for today.</p>
      </div>
    @else
      <div style="display:flex;flex-direction:column;gap:.6rem;">
        @foreach($todaySchedule as $class)
        <div style="display:flex;align-items:center;gap:1rem;background:rgba(255,255,255,.04);
                    border:1px solid rgba(255,255,255,.07);border-radius:10px;padding:.85rem 1rem;">
          <div style="min-width:110px;font-size:.8rem;font-weight:600;color:rgba(255,255,255,.5);font-family:monospace;">{{ $class['time'] }}</div>
          <div style="flex:1;">
            <div style="font-size:.92rem;font-weight:600;color:#fff;">{{ $class['subject'] }}</div>
            <div style="font-size:.8rem;color:rgba(255,255,255,.45);margin-top:.15rem;">{{ $class['teacher'] }}</div>
          </div>
          <div style="font-size:.82rem;color:rgba(255,255,255,.5);">{{ $class['room'] }}</div>
        </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- FULL WEEKLY LIST — shown in list mode on screen + always in   --}}
{{-- print (regardless of toggle state, via !important).           --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="sched-weekly-list"
     style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:1.25rem 1.5rem;">

  @if(!$hasSchedule)
    <div style="text-align:center;padding:1.5rem;color:rgba(255,255,255,.35);">
      <p style="font-size:.875rem;font-weight:600;">No schedule assigned yet.</p>
    </div>
  @else
  <div style="display:flex;flex-direction:column;gap:8px;">
    @foreach($dayOrder as $day)
    @php $classes = $weekGrouped[$day] ?? []; @endphp
    <div class="sched-list-day{{ $day === $todayName ? ' sched-today' : '' }}"
         style="border:1px solid {{ $day === $todayName ? '#6366f1' : 'rgba(255,255,255,.1)' }};
                border-radius:10px;overflow:hidden;
                {{ $day === $todayName ? 'box-shadow:0 0 0 2px rgba(99,102,241,.22);' : '' }}">
      <div class="sched-list-day-head"
           style="display:flex;align-items:center;gap:10px;padding:9px 16px;
                  border-bottom:1px solid rgba(255,255,255,.06);
                  {{ $day === $todayName ? 'background:rgba(99,102,241,.18);' : 'background:rgba(255,255,255,.04);' }}">
        <span style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;
                     color:{{ $day === $todayName ? '#a5b4fc' : 'rgba(255,255,255,.4)' }};
                     width:90px;flex-shrink:0;">{{ $dayLabels[$day] }}</span>
        @if($day === $todayName)
          <span class="no-print" style="font-size:.62rem;font-weight:700;padding:.1rem .45rem;
                border-radius:20px;background:#6366f1;color:#fff;">TODAY</span>
        @endif
        @if(empty($classes))
          <span style="font-size:.75rem;color:rgba(255,255,255,.22);">No classes</span>
        @else
          <span style="font-size:.75rem;color:rgba(255,255,255,.38);">
            {{ count($classes) }} {{ Str::plural('class', count($classes)) }}
          </span>
        @endif
      </div>
      @if(!empty($classes))
      <div>
        @foreach(collect($classes)->sortBy('start_time') as $ss)
        <div class="sched-list-day-row"
             style="display:flex;align-items:center;gap:12px;padding:9px 16px;
                    border-bottom:1px solid rgba(255,255,255,.04);">
          <div style="font-size:.75rem;color:rgba(255,255,255,.38);white-space:nowrap;
                      width:110px;flex-shrink:0;font-family:monospace;">{{ $ss->time_range }}</div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:.85rem;font-weight:600;color:rgba(255,255,255,.88);">
              {{ $ss->subject?->subject_name ?? '—' }}
            </div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.38);margin-top:2px;">
              {{ $ss->faculty?->full_name ?? '—' }}
            </div>
          </div>
          @if($ss->room)
          <div style="font-size:.7rem;font-weight:600;padding:.15rem .55rem;
                      background:rgba(255,255,255,.07);color:rgba(255,255,255,.5);
                      border-radius:5px;flex-shrink:0;">{{ $ss->room }}</div>
          @endif
        </div>
        @endforeach
      </div>
      @endif
    </div>
    @endforeach
  </div>
  @endif

</div>

{{-- ── Upcoming Assessments (always shown on screen, hidden in print) ── --}}
<div class="enc-card student-glass-card" style="padding:1.5rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Upcoming Assessments</div>
    <span class="enc-card__meta">Exams, quizzes, and projects due soon</span>
  </div>
  <div class="enc-card__body">
    <div style="text-align:center;padding:2.5rem 1rem;color:rgba(255,255,255,.45);">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
           style="width:40px;height:40px;margin:0 auto .75rem;display:block;color:rgba(255,255,255,.2)">
        <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75"/>
      </svg>
      <p style="font-size:.9rem;font-weight:600;">No Upcoming Assessments</p>
      <p style="font-size:.8rem;margin-top:.2rem;">Assessment schedules will appear here once posted by your teachers.</p>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
  var scheduleData = @json($scheduleForJs);

  /* ── View toggle ──────────────────────────────────────────────── */
  window.switchView = function (view) {
    var gridEl    = document.getElementById('view-grid');
    var todayEl   = document.getElementById('view-list-today');
    var listEl    = document.querySelector('.sched-weekly-list');
    var btnGrid   = document.getElementById('btn-grid');
    var btnList   = document.getElementById('btn-list');

    var isGrid = (view === 'grid');

    if (gridEl)  gridEl.style.display  = isGrid ? '' : 'none';
    if (todayEl) todayEl.style.display = isGrid ? 'none' : '';
    if (listEl)  listEl.style.display  = isGrid ? 'none' : 'block';

    if (btnGrid) {
      btnGrid.classList.toggle('sched-btn-active', isGrid);
      btnGrid.style.color = isGrid ? '' : 'rgba(255,255,255,.5)';
    }
    if (btnList) {
      btnList.classList.toggle('sched-btn-active', !isGrid);
      btnList.style.color = isGrid ? 'rgba(255,255,255,.5)' : '';
    }
  };

  /* ── CSV export ───────────────────────────────────────────────── */
  window.downloadScheduleCSV = function () {
    var rows = [['Day', 'Time', 'Subject', 'Teacher', 'Room']];
    for (var day in scheduleData) {
      var classes = scheduleData[day];
      for (var i = 0; i < classes.length; i++) {
        var c = classes[i];
        rows.push([day, c.time, c.subject, c.teacher, c.room]);
      }
    }
    var csv = rows.map(function (r) {
      return r.map(function (v) {
        return '"' + String(v == null ? '' : v).replace(/"/g, '""') + '"';
      }).join(',');
    }).join('\r\n');
    var a = document.createElement('a');
    a.href = 'data:text/csv;charset=utf-8,﻿' + encodeURIComponent(csv);
    a.download = 'schedule-{{ now()->format('Y-m-d') }}.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  };
})();
</script>
@endpush
