@extends('layouts.app')
@section('title', 'SF9 — Learner Progress Report Card')
@section('breadcrumb', 'SF Forms / SF9 Report Card')

@push('head')
<style>
.sf-header { background: linear-gradient(135deg,#7c3aed,#a78bfa); border-radius:16px; padding:24px 28px; color:#fff; margin-bottom:22px; }
.sf-header h2 { margin:0 0 4px; font-size:1.2rem; font-weight:800; }
.sf-filter-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px 22px; margin-bottom:22px; }
.sf-filter-row { display:flex; gap:14px; flex-wrap:wrap; align-items:flex-end; }
.sf-filter-card label { display:block; font-size:.78rem; font-weight:600; color:#475569; margin-bottom:5px; }
.sf-filter-card select { padding:9px 12px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:.86rem; background:#f8fafc; min-width:240px; }
.sf-btn { background:#7c3aed; color:#fff; border:none; border-radius:9px; padding:9px 20px; font-weight:700; cursor:pointer; font-size:.87rem; }
.sf-btn-dl { background:#059669; color:#fff; border:none; border-radius:9px; padding:9px 20px; font-weight:700; cursor:pointer; font-size:.87rem; text-decoration:none; display:inline-block; }
.sf9-table { width:100%; border-collapse:collapse; }
.sf9-table th { padding:10px 14px; background:#7c3aed; color:#fff; font-size:.76rem; font-weight:700; text-align:center; }
.sf9-table th:first-child { text-align:left; }
.sf9-table td { padding:10px 14px; font-size:.85rem; border-bottom:1px solid #f1f5f9; }
.sf9-table tr:last-child td { border-bottom:none; }
.sf9-table tr:nth-child(even) td { background:#f9fafb; }
.grade-cell { text-align:center; font-weight:700; }
.grade-pass { color:#059669; }
.grade-fail { color:#dc2626; }
</style>
@endpush

@section('content')
<div class="sf-header">
  <h2>📄 SF9 — Learner Progress Report Card</h2>
  <p>Official DepEd SF9 report card with quarterly grades.</p>
</div>

<div class="sf-filter-card">
  <form method="GET" action="{{ route('sf.sf9') }}">
    <div class="sf-filter-row">
      <div>
        <label>Section *</label>
        <select name="section_id" id="sectionSel" onchange="this.form.submit()">
          <option value="">— Choose a section —</option>
          @foreach($sections->groupBy('grade_level') as $grade => $secs)
            <optgroup label="Grade {{ $grade }}">
              @foreach($secs as $sec)
                <option value="{{ $sec->id }}" {{ $sectionId == $sec->id ? 'selected' : '' }}>{{ $sec->section_name }}</option>
              @endforeach
            </optgroup>
          @endforeach
        </select>
      </div>
      @if($students->isNotEmpty())
      <div>
        <label>Student *</label>
        <select name="student_id" onchange="this.form.submit()">
          <option value="">— Choose a student —</option>
          @foreach($students as $stu)
            <option value="{{ $stu->id }}" {{ $studentId == $stu->id ? 'selected' : '' }}>
              {{ $stu->last_name }}, {{ $stu->first_name }}
            </option>
          @endforeach
        </select>
      </div>
      @endif
      @if($student && $section)
        <a href="{{ route('sf.sf9') }}?section_id={{ $sectionId }}&student_id={{ $studentId }}&download=1" class="sf-btn-dl">⬇ Download PDF</a>
      @endif
    </div>
    <input type="hidden" name="section_id" value="{{ $sectionId }}">
    <input type="hidden" name="student_id" value="{{ $studentId }}">
  </form>
</div>

@if($student && $section)
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 22px;margin-bottom:18px;display:grid;grid-template-columns:repeat(4,1fr);gap:12px;flex-wrap:wrap;">
  <div><span style="font-size:.75rem;font-weight:700;color:#64748b;display:block;">Student Name</span><strong>{{ $student->last_name }}, {{ $student->first_name }}</strong></div>
  <div><span style="font-size:.75rem;font-weight:700;color:#64748b;display:block;">LRN</span><strong>{{ $student->lrn ?? 'N/A' }}</strong></div>
  <div><span style="font-size:.75rem;font-weight:700;color:#64748b;display:block;">Grade Level</span><strong>{{ $section->grade_level }}</strong></div>
  <div><span style="font-size:.75rem;font-weight:700;color:#64748b;display:block;">Section</span><strong>{{ $section->section_name }}</strong></div>
</div>

@if(empty($gradeData))
  <div style="background:#fffbeb;border:1px solid #fcd34d;border-left:4px solid #f59e0b;border-radius:12px;padding:20px 24px;display:flex;gap:14px;align-items:flex-start;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:22px;height:22px;color:#d97706;flex-shrink:0;margin-top:1px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
    </svg>
    <div style="font-size:.9rem;color:#92400e;line-height:1.65;">
      @if($pendingGrades > 0)
        <strong style="display:block;margin-bottom:3px;">No <em>finalized</em> grades yet for this student.</strong>
        This learner has <strong>{{ $pendingGrades }}</strong> subject grade{{ $pendingGrades > 1 ? 's' : '' }}
        still in progress (draft or submitted for verification). The SF9 shows only officially
        <strong>finalized</strong> grades — once the faculty finalize them, they will appear here automatically.
      @else
        <strong style="display:block;margin-bottom:3px;">No grades recorded for this student yet.</strong>
        No grades have been encoded for this learner in the selected section. Once faculty enter and
        finalize grades, the official report card will populate here.
      @endif
    </div>
  </div>
@else
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
  <div style="overflow-x:auto;">
    <table class="sf9-table">
      <thead>
        <tr>
          <th style="text-align:left;padding:12px 16px;">Learning Area</th>
          @foreach($quarters as $q)
            <th>Q{{ $q->quarter_number }}</th>
          @endforeach
          <th>Final</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        @foreach($gradeData as $subject => $qGrades)
          @php
            $vals = array_filter(array_values($qGrades), fn($v) => !is_null($v));
            $final = count($vals) > 0 ? round(array_sum($vals)/count($vals),2) : null;
            $passed = $final >= 75;
          @endphp
          <tr>
            <td style="font-weight:600;">{{ $subject }}</td>
            @foreach($quarters as $q)
              @php $g = $qGrades[$q->quarter_number] ?? null; @endphp
              <td class="grade-cell {{ $g !== null ? ($g >= 75 ? 'grade-pass' : 'grade-fail') : '' }}">
                {{ $g !== null ? $g : '—' }}
              </td>
            @endforeach
            <td class="grade-cell {{ $final !== null ? ($passed ? 'grade-pass' : 'grade-fail') : '' }}" style="font-size:.9rem;">
              {{ $final ?? '—' }}
            </td>
            <td style="text-align:center;">
              @if($final !== null)
                <span style="font-size:.75rem;font-weight:700;padding:2px 8px;border-radius:99px;background:{{ $passed ? '#d1fae5' : '#fee2e2' }};color:{{ $passed ? '#065f46' : '#991b1b' }};">
                  {{ $passed ? 'Passed' : 'Failed' }}
                </span>
              @endif
            </td>
          </tr>
        @endforeach
        <tr style="background:#f8fafc;border-top:2px solid #e2e8f0;">
          <td style="font-weight:800;font-size:.88rem;">General Average</td>
          @php
            $generalAvg = collect($gradeData)->map(function($qg) {
              $vals = array_filter(array_values($qg), fn($v) => !is_null($v));
              return count($vals) > 0 ? array_sum($vals)/count($vals) : null;
            })->filter()->avg();
          @endphp
          @foreach($quarters as $q)
            <td></td>
          @endforeach
          <td class="grade-cell" style="font-size:1rem;color:{{ ($generalAvg ?? 0) >= 75 ? '#059669' : '#dc2626' }};">
            {{ $generalAvg ? number_format($generalAvg,2) : '—' }}
          </td>
          <td style="text-align:center;">
            @if($generalAvg)
              <span style="font-size:.78rem;font-weight:800;padding:3px 10px;border-radius:99px;background:{{ $generalAvg >= 75 ? '#d1fae5' : '#fee2e2' }};color:{{ $generalAvg >= 75 ? '#065f46' : '#991b1b' }};">
                {{ $generalAvg >= 75 ? 'PROMOTED' : 'RETAINED' }}
              </span>
            @endif
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endif
@endif
@endsection
