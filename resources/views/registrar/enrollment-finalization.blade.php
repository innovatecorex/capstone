@extends('layouts.app')

@section('title', 'Enrollment Finalization')
@section('breadcrumb', 'Enrollment Finalization')

@push('head')
<style>
.ef-page-header { margin-bottom: 1.5rem; }
.ef-page-title  { font-size: 1.45rem; font-weight: 800; color: #0f172a; margin: 0 0 .15rem; }
.ef-page-sub    { font-size: .88rem; color: #64748b; margin: 0; }

.ef-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
    margin-bottom: 1.25rem;
}
.ef-card__head {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: .6rem;
}
.ef-card__title { font-size: .9rem; font-weight: 700; color: #0f172a; }
.ef-card__body  { padding: 1.25rem; }

/* Year selector */
.ef-year-bar {
    display: flex;
    align-items: center;
    gap: .75rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: .6rem 1rem;
    margin-bottom: 1.25rem;
}
.ef-year-bar label { font-size: .8rem; font-weight: 600; color: #475569; white-space: nowrap; }
.ef-year-select {
    flex: 1; max-width: 260px;
    border: 1px solid #e2e8f0; border-radius: 7px;
    padding: .35rem .65rem; font-size: .84rem; color: #0f172a;
    background: #fff;
}

/* Student info strip */
.ef-student-strip {
    display: flex; gap: 1.5rem; align-items: flex-start;
    background: #f8fafc; border-radius: 10px;
    padding: .9rem 1.1rem; margin-bottom: 1.25rem;
    border: 1px solid #e2e8f0;
}
.ef-student-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    background: #6366f1; color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; font-weight: 800; flex-shrink: 0;
}
.ef-student-name  { font-size: 1rem; font-weight: 700; color: #0f172a; }
.ef-student-meta  { font-size: .78rem; color: #64748b; margin-top: 2px; display: flex; gap: .9rem; flex-wrap: wrap; }
.ef-student-meta span::before { content: '•'; margin-right: .4rem; color: #cbd5e1; }
.ef-student-meta span:first-child::before { display: none; }

/* Section info */
.ef-section-info {
    display: flex; gap: 1.5rem; flex-wrap: wrap;
    padding: .75rem 0; border-bottom: 1px solid #f1f5f9; margin-bottom: 1rem;
}
.ef-info-item { display: flex; flex-direction: column; gap: 2px; }
.ef-info-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; font-weight: 700; }
.ef-info-value { font-size: .9rem; font-weight: 600; color: #0f172a; }

/* Subjects table */
.ef-table { width: 100%; border-collapse: collapse; font-size: .84rem; }
.ef-table th {
    padding: 9px 12px; text-align: left;
    font-size: .69rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .04em; color: #64748b;
    border-bottom: 2px solid #e2e8f0; background: #f8fafc;
}
.ef-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.ef-table tr:hover td { background: #f8fafc; }
.ef-table .ef-code { font-family: monospace; font-size: .79rem; color: #6366f1; background: #eef2ff; padding: .15rem .4rem; border-radius: 4px; }
.ef-table .ef-units { font-weight: 700; color: #0f172a; }
.ef-table tfoot td { background: #f8fafc; font-weight: 700; border-top: 2px solid #e2e8f0; padding: 10px 12px; }

/* Badges */
.ef-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .3rem .75rem; border-radius: 999px;
    font-size: .75rem; font-weight: 700;
}
.ef-badge--confirmed { background: #dcfce7; color: #15803d; }
.ef-badge--pending   { background: #fef9c3; color: #a16207; }

/* Confirm button */
.ef-confirm-btn {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .65rem 1.4rem; border-radius: 8px;
    background: #16a34a; color: #fff;
    border: none; cursor: pointer;
    font-size: .9rem; font-weight: 700;
    transition: background .15s;
}
.ef-confirm-btn:hover { background: #15803d; }
.ef-confirm-btn:disabled { background: #94a3b8; cursor: not-allowed; }

.ef-back-link {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .82rem; color: #6366f1; font-weight: 600;
    text-decoration: none; margin-bottom: 1rem;
}
.ef-back-link:hover { text-decoration: underline; }

/* Alert */
.ef-alert { border-radius: 9px; padding: .75rem 1rem; font-size: .85rem; margin-bottom: 1rem; }
.ef-alert--info    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.ef-alert--success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.ef-alert--warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
</style>
@endpush

@section('content')
<div class="ef-page-header">
  <a href="{{ route('registrar.enrollment') }}" class="ef-back-link">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;"><path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 4.158a.75.75 0 11-1.06 1.06l-5.5-5.5a.75.75 0 010-1.06l5.5-5.5a.75.75 0 011.06 1.06L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd"/></svg>
    Back to Enrollment
  </a>
  <h1 class="ef-page-title">Enrollment Finalization</h1>
  <p class="ef-page-sub">Review and confirm a student's enrollment for the term.</p>
</div>

{{-- Flash messages --}}
@if(session('success'))
  <div class="ef-alert ef-alert--success">{{ session('success') }}</div>
@endif
@if(session('info'))
  <div class="ef-alert ef-alert--info">{{ session('info') }}</div>
@endif

{{-- Academic year selector --}}
<form method="GET" action="{{ route('registrar.enrollment.finalize', $student) }}" class="ef-year-bar">
  <label for="ef-year">Academic Year</label>
  <select id="ef-year" name="year_id" class="ef-year-select" onchange="this.form.submit()">
    @foreach($academicYears as $ay)
      <option value="{{ $ay->id }}" @selected($ay->id == $yearId)>{{ $ay->year_label }}</option>
    @endforeach
  </select>
</form>

{{-- Student info strip --}}
<div class="ef-student-strip">
  <div class="ef-student-avatar">{{ strtoupper(substr($student->first_name, 0, 1)) }}</div>
  <div>
    <div class="ef-student-name">{{ $student->full_name }}</div>
    <div class="ef-student-meta">
      <span>LRN: {{ $student->lrn ?? 'N/A' }}</span>
      @if($enrollment)
        <span>{{ $enrollment->section?->grade_level }}</span>
        <span>{{ $enrollment->academicYear?->year_label }}</span>
        <span>Enrolled {{ $enrollment->enrolled_at?->format('M d, Y') }}</span>
      @endif
    </div>
  </div>
  @if($enrollment?->isFinalized())
    <div style="margin-left:auto;">
      <span class="ef-badge ef-badge--confirmed">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
        Enrollment Confirmed
      </span>
    </div>
  @endif
</div>

@if(!$enrollment)
  {{-- No enrollment found for selected year --}}
  <div class="ef-card">
    <div class="ef-card__body" style="text-align:center;padding:3rem 1.5rem;color:#94a3b8;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:48px;height:48px;margin:0 auto 1rem;display:block;color:#cbd5e1;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
      </svg>
      <p style="font-size:.95rem;font-weight:700;color:#475569;margin:0 0 .4rem;">No Enrollment Found</p>
      <p style="font-size:.83rem;">{{ $student->full_name }} has no enrollment record for the selected academic year. Enroll the student first.</p>
    </div>
  </div>

@else

  {{-- Enrollment Summary card --}}
  <div class="ef-card">
    <div class="ef-card__head">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#6366f1" style="width:18px;height:18px;flex-shrink:0;">
        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h4a1 1 0 100-2H7z" clip-rule="evenodd"/>
      </svg>
      <span class="ef-card__title">Enrollment Summary</span>
      @if($enrollment->isFinalized())
        <span class="ef-badge ef-badge--confirmed" style="margin-left:auto;font-size:.7rem;">
          Confirmed {{ $enrollment->finalized_at->format('M d, Y g:i A') }}
          @if($enrollment->finalizedBy)
            &nbsp;by {{ $enrollment->finalizedBy->full_name }}
          @endif
        </span>
      @else
        <span class="ef-badge ef-badge--pending" style="margin-left:auto;font-size:.7rem;">Pending Confirmation</span>
      @endif
    </div>
    <div class="ef-card__body">

      {{-- Section info --}}
      <div class="ef-section-info">
        <div class="ef-info-item">
          <span class="ef-info-label">Section</span>
          <span class="ef-info-value">{{ $enrollment->section?->section_name ?? '—' }}</span>
        </div>
        <div class="ef-info-item">
          <span class="ef-info-label">Grade Level</span>
          <span class="ef-info-value">{{ $enrollment->section?->grade_level ?? '—' }}</span>
        </div>
        <div class="ef-info-item">
          <span class="ef-info-label">Adviser</span>
          <span class="ef-info-value">{{ $enrollment->section?->adviser?->full_name ?? 'None' }}</span>
        </div>
        <div class="ef-info-item">
          <span class="ef-info-label">Academic Year</span>
          <span class="ef-info-value">{{ $enrollment->academicYear?->year_label ?? '—' }}</span>
        </div>
        <div class="ef-info-item">
          <span class="ef-info-label">Total Units</span>
          <span class="ef-info-value" style="color:#6366f1;">{{ $totalUnits > 0 ? $totalUnits . ' units' : '—' }}</span>
        </div>
      </div>

      {{-- Subjects table --}}
      @if($enrollment->section?->sectionSubjects->isNotEmpty())
      <div style="overflow-x:auto;">
        <table class="ef-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Course Code</th>
              <th>Subject Name</th>
              <th>Faculty</th>
              <th>Schedule</th>
              <th style="text-align:center;">Units</th>
            </tr>
          </thead>
          <tbody>
            @foreach($enrollment->section->sectionSubjects as $i => $ss)
            <tr>
              <td style="color:#94a3b8;font-size:.78rem;">{{ $i + 1 }}</td>
              <td><span class="ef-code">{{ $ss->subject?->subject_code ?? '—' }}</span></td>
              <td style="font-weight:600;color:#0f172a;">{{ $ss->subject?->subject_name ?? '—' }}</td>
              <td style="font-size:.82rem;color:#475569;">{{ $ss->faculty?->full_name ?? 'Unassigned' }}</td>
              <td style="font-size:.8rem;color:#64748b;">
                @if($ss->schedule_days_label)
                  {{ $ss->schedule_days_label }}
                  <span style="color:#94a3b8;">&nbsp;{{ $ss->time_range }}</span>
                @else
                  <span style="color:#94a3b8;">—</span>
                @endif
              </td>
              <td style="text-align:center;" class="ef-units">{{ $ss->subject?->credits ?? '—' }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5" style="text-align:right;color:#475569;">Total Registered Units</td>
              <td style="text-align:center;color:#6366f1;font-size:.95rem;">{{ $totalUnits ?: '—' }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
      @else
        <div class="ef-alert ef-alert--warning">
          This section has no subjects assigned yet. Assign section subjects before confirming enrollment.
        </div>
      @endif

    </div>
  </div>

  {{-- Confirm / Lock action --}}
  @if($enrollment->isFinalized())
    <div class="ef-alert ef-alert--success" style="display:flex;align-items:center;gap:.75rem;">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:20px;height:20px;flex-shrink:0;">
        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
      </svg>
      <div>
        <strong>Schedule Locked.</strong> This enrollment was confirmed on {{ $enrollment->finalized_at->format('F d, Y \a\t g:i A') }}.
        Grade records have been pushed to the grading module. No further changes can be made.
      </div>
    </div>
  @else
    <div class="ef-card">
      <div class="ef-card__head">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#f59e0b" style="width:18px;height:18px;flex-shrink:0;">
          <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
        </svg>
        <span class="ef-card__title">Confirm &amp; Lock Enrollment</span>
      </div>
      <div class="ef-card__body">
        <p style="font-size:.86rem;color:#475569;margin:0 0 1rem;">
          Clicking <strong>Confirm Enrollment</strong> will:
        </p>
        <ul style="font-size:.85rem;color:#475569;margin:0 0 1.25rem;padding-left:1.2rem;line-height:1.8;">
          <li>Lock <strong>{{ $student->full_name }}'s</strong> schedule for <strong>{{ $enrollment->academicYear?->year_label }}</strong>.</li>
          <li>Change institutional status to <strong>Officially Enrolled</strong>.</li>
          <li>Push subject records to the grading module ({{ $enrollment->section?->sectionSubjects->count() ?? 0 }} subjects × 4 quarters).</li>
        </ul>
        @if($enrollment->section?->sectionSubjects->isEmpty())
          <div class="ef-alert ef-alert--warning" style="margin-bottom:1rem;">
            Cannot confirm: no subjects are assigned to this section.
          </div>
        @endif
        <form method="POST" action="{{ route('registrar.enrollment.confirm', $student) }}"
              onsubmit="return confirm('Confirm enrollment for {{ addslashes($student->full_name) }}?\n\nThis will lock the schedule and push grade records to the grading module. This action cannot be undone.')">
          @csrf
          <input type="hidden" name="year_id" value="{{ $yearId }}">
          <button type="submit" class="ef-confirm-btn"
                  @if($enrollment->section?->sectionSubjects->isEmpty()) disabled @endif>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;">
              <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
            </svg>
            Confirm Enrollment
          </button>
        </form>
      </div>
    </div>
  @endif

@endif
@endsection
