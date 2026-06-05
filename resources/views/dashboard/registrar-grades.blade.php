@extends('layouts.app')
@section('title', 'Grades & Records')
@section('breadcrumb', 'Grades & Records')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Master Grade Sheet</h1>
      <p class="enc-page__subtitle">Read-only view of all submitted grades. To lock grades or handle unlock requests, use Grade Lock.</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('registrar.grade-lock.index') }}" style="background:#0f172a;color:#fff;padding:.55rem 1.1rem;border-radius:8px;font-size:.875rem;font-weight:700;text-decoration:none;display:inline-block;">Grade Lock →</a>
    </div>
  </div>
</div>

@if(!$activeYear)
<div class="enc-card"><div class="enc-card__body" style="padding:40px;text-align:center;color:#64748b;">No active academic year. Set one under Academic Years first.</div></div>
@else

{{-- Filters --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__body" style="padding:16px 20px;">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:4px;min-width:200px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Grading Period</label>
        <select name="quarter_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          @forelse($quarters as $q)
            <option value="{{ $q->id }}" {{ $selectedQuarterId == $q->id ? 'selected' : '' }}>{{ $q->quarter_name }}</option>
          @empty
            <option value="">No periods</option>
          @endforelse
        </select>
      </div>
      <div style="display:flex;flex-direction:column;gap:4px;min-width:260px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Section &amp; Subject</label>
        <select name="section_subject_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">All Sections &amp; Subjects</option>
          @foreach($sectionSubjects as $ss)
            <option value="{{ $ss->id }}" {{ $selectedSectionSubjectId == $ss->id ? 'selected' : '' }}>
              {{ $ss->section?->section_name }} — {{ $ss->subject?->subject_name }}
            </option>
          @endforeach
        </select>
      </div>
    </form>
  </div>
</div>

{{-- Master sheet --}}
<div class="enc-card">
  <div class="enc-card__body" style="padding:0;">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Section</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Subject</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Student</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Faculty</th>
            <th style="padding:12px 14px;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Final Grade</th>
            <th style="padding:12px 14px;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Remark</th>
            <th style="padding:12px 14px;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($grades as $g)
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:11px 14px;color:#64748b;">{{ $g->sectionSubject?->section?->section_name ?? '—' }}</td>
            <td style="padding:11px 14px;color:#0f172a;">{{ $g->sectionSubject?->subject?->subject_name ?? '—' }}</td>
            <td style="padding:11px 14px;color:#0f172a;font-weight:600;">{{ $g->student?->last_name }}, {{ $g->student?->first_name }}</td>
            <td style="padding:11px 14px;color:#64748b;">
              @if($g->sectionSubject?->faculty){{ $g->sectionSubject->faculty->last_name }}, {{ $g->sectionSubject->faculty->first_name }}@else <span style="color:#94a3b8;">TBA</span>@endif
            </td>
            <td style="padding:11px 14px;text-align:center;font-weight:700;color:#0f172a;">{{ !is_null($g->final_grade) ? number_format($g->final_grade, 0) : '—' }}</td>
            <td style="padding:11px 14px;text-align:center;">
              @if(is_null($g->final_grade))
                <span style="color:#94a3b8;font-size:.8rem;">Pending</span>
              @else
                @php $pass = $g->isPassing(); $c = $pass ? ['#166534','#86efac','#f0fdf4'] : ['#991b1b','#fca5a5','#fef2f2']; @endphp
                <span style="display:inline-block;padding:.2rem .6rem;border-radius:6px;font-size:.72rem;font-weight:700;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">{{ $pass ? 'Passed' : 'Failed' }}</span>
              @endif
            </td>
            <td style="padding:11px 14px;text-align:center;">
              @php $sc = ['draft'=>['#92400e','#fcd34d','#fffbeb'],'submitted'=>['#1e40af','#bfdbfe','#eff6ff'],'finalized'=>['#166534','#86efac','#f0fdf4'],'locked'=>['#475569','#cbd5e1','#f8fafc']][$g->status] ?? ['#475569','#cbd5e1','#f8fafc']; @endphp
              <span style="display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.68rem;font-weight:700;text-transform:uppercase;color:{{ $sc[0] }};background:{{ $sc[2] }};border:1px solid {{ $sc[1] }};">{{ $g->status }}</span>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" style="padding:40px;text-align:center;color:#94a3b8;">No grades found for this filter. Grades appear here once faculty save or submit them.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@if($grades->isNotEmpty())
<p style="margin-top:12px;font-size:.78rem;color:#94a3b8;">Showing {{ $grades->count() }} grade record(s).</p>
@endif

@endif

@endsection
