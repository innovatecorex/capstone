@extends('layouts.app')
@section('title', 'My Schedule')
@section('breadcrumb', 'My Schedule')

@push('head')
<style>
/* ── Faculty schedule: print support ──────────────────────────── */
.sched-print-header { display: none; }

@media print {
  .enc-sidebar, #enc-sidebar, .enc-header, .no-print { display: none !important; }
  .sched-print-header { display: block !important; }

  html, body { background: #fff !important; }
  .enc-main, [class*="enc-main"] { margin-left: 0 !important; padding-left: 0 !important; }

  .sched-day {
    border: 1px solid #d1d5db !important;
    box-shadow: none !important;
    border-radius: 4px !important;
    break-inside: avoid;
    page-break-inside: avoid;
    margin-bottom: 6pt !important;
  }
  .sched-day-head { background: #f3f4f6 !important; box-shadow: none !important; }

  @page { margin: 1.5cm; size: A4 portrait; }
}
</style>
@endpush

@section('content')
@php
  $dayOrder  = ['monday','tuesday','wednesday','thursday','friday','saturday'];
  $dayLabels = ['monday'=>'Monday','tuesday'=>'Tuesday','wednesday'=>'Wednesday','thursday'=>'Thursday','friday'=>'Friday','saturday'=>'Saturday'];
  $grouped   = [];
  foreach ($allSchedules as $s) {
    foreach ($s->days ?? [] as $d) { $grouped[$d][] = $s; }
  }
  $scheduleForJs = collect($dayOrder)->mapWithKeys(function ($day) use ($grouped, $dayLabels) {
    return [
      $dayLabels[$day] => collect($grouped[$day] ?? [])
        ->sortBy('start_time')
        ->map(fn($cls) => [
          'time'    => $cls->time_range,
          'subject' => $cls->subject_name ?? '',
          'section' => $cls->section_name ?? '',
          'room'    => $cls->room ?? '',
        ])->values()->all(),
    ];
  });
@endphp

<div style="max-width:960px;">

  {{-- Print-only school header (hidden on screen) --}}
  <div class="sched-print-header" style="margin-bottom:18px;padding-bottom:14px;border-bottom:2px solid #0f172a;">
    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#64748b;margin-bottom:2px;">{{ config('app.name') }}</div>
    <div style="font-size:1.4rem;font-weight:800;color:#0f172a;">Weekly Teaching Schedule</div>
    <div style="font-size:.85rem;color:#475569;margin-top:6px;">
      <strong>{{ $user->full_name }}</strong>
      @if($activeAcademicYear) &nbsp;&middot;&nbsp; S.Y. {{ $activeAcademicYear->year_label }} @endif
      &nbsp;&middot;&nbsp; Printed {{ now()->format('F d, Y') }}
    </div>
  </div>

  {{-- Screen header with print/export toolbar --}}
  <div style="margin-bottom:24px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <h1 style="font-size:1.35rem;font-weight:800;color:#0f172a;margin:0 0 4px;">My Schedule</h1>
      <p style="font-size:.875rem;color:#94a3b8;margin:0;">Your weekly teaching schedule at a glance.</p>
    </div>
    <div class="no-print" style="display:flex;gap:8px;align-items:center;flex-shrink:0;padding-top:2px;">
      <button onclick="window.print()"
              style="display:inline-flex;align-items:center;gap:6px;padding:.4rem .9rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-weight:600;color:#374151;cursor:pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 4.5h10.5M6 19.5h12a2.25 2.25 0 002.25-2.25v-7.5A2.25 2.25 0 0018 7.5H6a2.25 2.25 0 00-2.25 2.25v7.5A2.25 2.25 0 006 19.5zm.75-5.25h.008v.008H6.75v-.008zm3 0h.008v.008H9.75v-.008zm3 0h.008v.008h-.008v-.008z"/></svg>
        Print
      </button>
      <button onclick="downloadScheduleCSV()"
              style="display:inline-flex;align-items:center;gap:6px;padding:.4rem .9rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-weight:600;color:#374151;cursor:pointer;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
        CSV
      </button>
    </div>
  </div>

  @if($allSchedules->isEmpty())
  <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:60px 24px;text-align:center;">
    <div style="width:56px;height:56px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:24px;height:24px;color:#94a3b8;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/></svg>
    </div>
    <div style="font-size:.95rem;font-weight:700;color:#374151;margin-bottom:6px;">No schedule assigned yet</div>
    <div style="font-size:.82rem;color:#94a3b8;">Your schedule will appear here once the admin assigns classes to you.</div>
  </div>
  @else
  <div style="display:flex;flex-direction:column;gap:12px;">
    @foreach($dayOrder as $day)
    @php $classes = $grouped[$day] ?? []; @endphp
    <div class="sched-day{{ $day === $todayName ? ' sched-today' : '' }}"
         style="background:#fff;border:1px solid {{ $day === $todayName ? '#6366f1' : '#e5e7eb' }};border-radius:14px;overflow:hidden;{{ $day === $todayName ? 'box-shadow:0 0 0 3px rgba(99,102,241,.08);' : '' }}">
      <div class="sched-day-head"
           style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f1f5f9;{{ $day === $todayName ? 'background:#eef2ff;' : 'background:#f8fafc;' }}">
        <div style="font-size:.82rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:{{ $day === $todayName ? '#4338ca' : '#94a3b8' }};width:100px;flex-shrink:0;">
          {{ $dayLabels[$day] }}
        </div>
        @if($day === $todayName)
          <span class="no-print" style="font-size:.65rem;font-weight:700;padding:.15rem .55rem;border-radius:20px;background:#6366f1;color:#fff;">TODAY</span>
        @endif
        @if(empty($classes))
          <span style="font-size:.78rem;color:#cbd5e1;">No classes</span>
        @else
          <span style="font-size:.78rem;color:#64748b;">{{ count($classes) }} {{ Str::plural('class', count($classes)) }}</span>
        @endif
      </div>
      @if(!empty($classes))
      <div style="padding:4px 0;">
        @foreach(collect($classes)->sortBy('start_time') as $cls)
        <div style="display:flex;align-items:center;gap:16px;padding:10px 20px;border-bottom:1px solid #f8fafc;">
          <div style="font-size:.78rem;color:#64748b;white-space:nowrap;width:120px;flex-shrink:0;">{{ $cls->time_range }}</div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:.875rem;font-weight:600;color:#0f172a;">{{ $cls->subject_name }}</div>
            @if($cls->section)
            <div style="font-size:.75rem;color:#94a3b8;margin-top:1px;">{{ $cls->section_name }}</div>
            @endif
          </div>
          @if($cls->room)
          <div style="font-size:.75rem;font-weight:600;padding:.2rem .65rem;background:#f1f5f9;color:#475569;border-radius:6px;flex-shrink:0;">
            {{ $cls->room }}
          </div>
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
@endsection

@push('scripts')
<script>
(function () {
  var scheduleData = @json($scheduleForJs);
  window.downloadScheduleCSV = function () {
    var rows = [['Day', 'Time', 'Subject', 'Section', 'Room']];
    for (var day in scheduleData) {
      var classes = scheduleData[day];
      for (var i = 0; i < classes.length; i++) {
        var c = classes[i];
        rows.push([day, c.time, c.subject, c.section, c.room]);
      }
    }
    var csv = rows.map(function (r) {
      return r.map(function (v) {
        return '"' + String(v === null || v === undefined ? '' : v).replace(/"/g, '""') + '"';
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
