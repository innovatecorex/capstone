@extends('layouts.app')
@section('title', 'SF2 — Daily Attendance Register')
@section('breadcrumb', 'SF Forms / SF2 Attendance')

@push('head')
<style>
.sf-header { background: linear-gradient(135deg,#065f46,#059669); border-radius:16px; padding:24px 28px; color:#fff; margin-bottom:22px; }
.sf-header h2 { margin:0 0 4px; font-size:1.2rem; font-weight:800; }
.sf-filter-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px 22px; margin-bottom:22px; display:flex; gap:14px; flex-wrap:wrap; align-items:flex-end; }
.sf-filter-card label { display:block; font-size:.78rem; font-weight:600; color:#475569; margin-bottom:5px; }
.sf-filter-card select,.sf-filter-card input { padding:9px 12px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:.86rem; background:#f8fafc; }
.sf-btn { background:#065f46; color:#fff; border:none; border-radius:9px; padding:9px 20px; font-weight:700; cursor:pointer; font-size:.87rem; }
.sf-btn-dl { background:#3b82f6; color:#fff; border:none; border-radius:9px; padding:9px 20px; font-weight:700; cursor:pointer; font-size:.87rem; text-decoration:none; display:inline-block; }
.att-cell { text-align:center; font-size:.75rem; font-weight:700; padding:3px; }
.att-P { color:#059669; } .att-A { color:#dc2626; } .att-L { color:#d97706; } .att-E { color:#7c3aed; }
</style>
@endpush

@section('content')
<div class="sf-header">
  <h2>📅 SF2 — Daily Attendance Register</h2>
  <p>Monthly attendance register per section.</p>
</div>

<div class="sf-filter-card">
  <form method="GET" action="{{ route('sf.sf2') }}" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;width:100%;">
    <div>
      <label>Section *</label>
      <select name="section_id" required>
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
    <div>
      <label>Month</label>
      <select name="month">
        @for($m=1; $m<=12; $m++)
          <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null,$m,1)->format('F') }}</option>
        @endfor
      </select>
    </div>
    <div>
      <label>Year</label>
      <input type="number" name="year" value="{{ $year }}" min="2020" max="2030" style="width:90px;">
    </div>
    <button type="submit" class="sf-btn">Load</button>
    @if($section && $students->isNotEmpty())
      <a href="{{ route('sf.sf2') }}?section_id={{ $sectionId }}&month={{ $month }}&year={{ $year }}&download=1" class="sf-btn-dl">⬇ Download PDF</a>
    @endif
  </form>
</div>

@if($section && $students->isNotEmpty())
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px 20px;margin-bottom:18px;">
  <strong>{{ $section->section_name }}</strong> · Grade {{ $section->grade_level }} ·
  {{ \Carbon\Carbon::create($year,$month,1)->format('F Y') }} ·
  {{ $students->count() }} students
</div>

{{-- The register below is valid and correctly built, but every mark will be
     blank if attendance was never recorded for this month. Say so explicitly,
     otherwise an empty grid reads as a broken form rather than as missing data. --}}
@if($attendance->isEmpty())
<div style="background:#fffbeb;border:1px solid #fcd34d;border-left:4px solid #f59e0b;border-radius:10px;padding:14px 18px;margin-bottom:18px;display:flex;gap:12px;align-items:flex-start;">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:#d97706;flex-shrink:0;margin-top:1px;">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
  </svg>
  <div style="font-size:.85rem;color:#92400e;line-height:1.6;">
    <strong style="display:block;margin-bottom:2px;">
      No attendance records for this section in {{ \Carbon\Carbon::create($year,$month,1)->format('F Y') }}.
    </strong>
    The register below lists the enrolled students correctly, but the daily marks are blank
    because attendance has not been encoded for this month yet. Choose another month, or have
    the adviser record attendance first.
  </div>
</div>
@endif

<div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;font-size:.78rem;">
      <thead>
        <tr style="background:#065f46;color:#fff;">
          <th style="padding:10px 12px;text-align:left;position:sticky;left:0;background:#065f46;min-width:180px;">#  Name</th>
          @for($d=1; $d<=$daysInMonth; $d++)
            <th style="padding:8px 4px;text-align:center;min-width:28px;">{{ $d }}</th>
          @endfor
          <th style="padding:8px 10px;text-align:center;">P</th>
          <th style="padding:8px 10px;text-align:center;">A</th>
          <th style="padding:8px 10px;text-align:center;">L</th>
        </tr>
      </thead>
      <tbody>
        @foreach($students as $i => $stu)
        @php
          $stuAtt = $attendance->get($stu->id, collect());
          $present = 0; $absent = 0; $late = 0;
        @endphp
        <tr style="{{ $i%2==0 ? '' : 'background:#f9fafb;' }}">
          <td style="padding:8px 12px;font-weight:600;position:sticky;left:0;background:inherit;">{{ $i+1 }}. {{ $stu->last_name }}, {{ $stu->first_name }}</td>
          @for($d=1; $d<=$daysInMonth; $d++)
            @php
              $dateStr = sprintf('%04d-%02d-%02d',$year,$month,$d);
              $rec = $stuAtt->first(fn($a) => \Carbon\Carbon::parse($a->date)->format('Y-m-d') === $dateStr);
              $status = $rec?->status ?? '';
              if($status==='present') $present++;
              elseif($status==='absent') $absent++;
              elseif($status==='late') $late++;
            @endphp
            <td class="att-cell att-{{ strtoupper(substr($status,0,1)) }}">
              {{ $status ? strtoupper(substr($status,0,1)) : '' }}
            </td>
          @endfor
          <td style="text-align:center;font-weight:700;color:#059669;">{{ $present }}</td>
          <td style="text-align:center;font-weight:700;color:#dc2626;">{{ $absent }}</td>
          <td style="text-align:center;font-weight:700;color:#d97706;">{{ $late }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="padding:12px 18px;background:#f8fafc;border-top:1px solid #e2e8f0;font-size:.78rem;color:#475569;display:flex;gap:18px;">
    <span><strong style="color:#059669;">P</strong> = Present</span>
    <span><strong style="color:#dc2626;">A</strong> = Absent</span>
    <span><strong style="color:#d97706;">L</strong> = Late</span>
    <span><strong style="color:#7c3aed;">E</strong> = Excused</span>
  </div>
</div>
@elseif($sectionId && $students->isEmpty())
  <div style="text-align:center;padding:40px;background:#fff;border:1px solid #e2e8f0;border-radius:16px;color:#94a3b8;">
    <p style="font-weight:600;">No students found for the selected section.</p>
  </div>
@endif
@endsection
