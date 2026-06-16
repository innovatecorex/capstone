@extends('layouts.app')
@section('title', 'Student Promotion')
@section('breadcrumb', 'Student Promotion')

@push('head')
<style>
/* ── Stat cards ─────────────────────────────────────────────────── */
.promo-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:1.25rem; }
@media(max-width:900px){ .promo-stats{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:520px){ .promo-stats{ grid-template-columns:1fr 1fr; } }

.promo-stat { background:#fff; border:1px solid rgba(15,23,42,.08); border-radius:12px;
  padding:1rem 1.1rem; display:flex; flex-direction:column; gap:.2rem; }
.promo-stat__num  { font-size:1.8rem; font-weight:900; line-height:1; }
.promo-stat__lbl  { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; }
.promo-stat--green  .promo-stat__num { color:#16a34a; }
.promo-stat--red    .promo-stat__num { color:#dc2626; }
.promo-stat--orange .promo-stat__num { color:#d97706; }
.promo-stat--gray   .promo-stat__num { color:#94a3b8; }
.promo-stat--grad   .promo-stat__num { color:#7c3aed; }

/* ── Pipeline mini bar ──────────────────────────────────────────── */
.pipeline { display:flex; gap:4px; align-items:center; }
.pipe-chip { display:inline-flex; align-items:center; gap:3px; padding:.18rem .48rem;
  border-radius:6px; font-size:.69rem; font-weight:700; white-space:nowrap; }
.pipe-submitted { background:#fef9c3; color:#854d0e; }
.pipe-finalized { background:#dbeafe; color:#1e40af; }
.pipe-locked    { background:#dcfce7; color:#166534; }
.pipe-none      { background:#f1f5f9; color:#94a3b8; }

/* ── Progress bar ───────────────────────────────────────────────── */
.grade-bar { width:100%; background:#f1f5f9; border-radius:999px; height:5px; margin-top:4px; overflow:hidden; }
.grade-bar__fill { height:100%; background:#4f46e5; border-radius:999px; transition:width .3s; }
.grade-bar__fill--full { background:#16a34a; }

/* ── Status badge ───────────────────────────────────────────────── */
.promo-badge { display:inline-block; padding:.22rem .65rem; border-radius:999px;
  font-size:.7rem; font-weight:800; text-transform:uppercase; letter-spacing:.04em; white-space:nowrap; }
.pb-promotable  { background:#dcfce7; color:#166534; }
.pb-graduated   { background:#ede9fe; color:#6d28d9; }
.pb-retention   { background:#fee2e2; color:#991b1b; }
.pb-nolock      { background:#fef3c7; color:#92400e; }
.pb-nogrades    { background:#f1f5f9; color:#64748b; }

/* ── Floating action bar ────────────────────────────────────────── */
.promo-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem;
  padding:14px 20px; background:#f8fafc; border-top:1px solid #e2e8f0; }
.promo-footer__info { font-size:.84rem; color:#64748b; }
.promo-footer__info strong { color:#0f172a; }
</style>
@endpush

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Student Promotion &amp; Advancement</h1>
      <p class="enc-page__subtitle">Advance students to the next grade level for the active academic year.</p>
    </div>
    @if($activeYear)
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.5rem 1rem;font-size:.8rem;color:#166534;font-weight:700;white-space:nowrap;">
      ● Active Year: {{ $activeYear->year_label }}
    </div>
    @else
    <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.5rem 1rem;font-size:.8rem;color:#991b1b;font-weight:700;">
      ✕ No active academic year
    </div>
    @endif
  </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div style="margin-bottom:16px;padding:13px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.875rem;font-weight:600;">
  ✓ {{ session('success') }}
</div>
@endif
@if($errors->has('promotion'))
<div style="margin-bottom:16px;padding:13px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.875rem;">
  {{ $errors->first('promotion') }}
</div>
@endif

{{-- Step 1: Filters ──────────────────────────────────────────────────── --}}
<div class="enc-card" style="margin-bottom:1.25rem;">
  <div class="enc-card__header">
    <div class="enc-card__title">Step 1 — Select Source Year &amp; Section</div>
  </div>
  <div class="enc-card__body" style="padding:18px 22px;">
    <form method="GET" action="{{ route('registrar.promotion') }}"
          style="display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:5px;min-width:220px;">
        <label style="font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Academic Year</label>
        <select name="academic_year_id" onchange="this.form.submit()"
                style="padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;min-width:220px;">
          <option value="">— Select Year —</option>
          @foreach($academicYears as $yr)
          <option value="{{ $yr->id }}" {{ optional($selectedYear)->id == $yr->id ? 'selected' : '' }}>
            {{ $yr->year_label }} {{ $yr->status === 'active' ? '(Active)' : '' }}
          </option>
          @endforeach
        </select>
      </div>

      @if($selectedYear && $sections->isNotEmpty())
      <div style="display:flex;flex-direction:column;gap:5px;min-width:220px;">
        <label style="font-size:.75rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Section</label>
        <select name="section_id" onchange="this.form.submit()"
                style="padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;min-width:220px;">
          <option value="">— Select Section —</option>
          @foreach($sections as $sec)
          <option value="{{ $sec->id }}" {{ optional($selectedSection)->id == $sec->id ? 'selected' : '' }}>
            {{ $sec->grade_level }} — {{ $sec->section_name }}
          </option>
          @endforeach
        </select>
      </div>
      @endif
    </form>
  </div>
</div>

{{-- Step 2: Student List ─────────────────────────────────────────────── --}}
@if($selectedSection && $selectedYear)
@php
  $nextGradeMap = [
    'Grade 7'  => 'Grade 8',
    'Grade 8'  => 'Grade 9',
    'Grade 9'  => 'Grade 10',
    'Grade 10' => null,
  ];
  $nextGrade     = $nextGradeMap[$selectedSection->grade_level] ?? null;
  $passingGrade  = config('academic.passing_grade', 75);

  // Aggregate counts from students collection
  $totalStudents    = $students->count();
  $promotableList   = $students->where('is_promotable', true);
  $promotableCount  = $promotableList->count();
  $retentionCount   = $students->where('has_locked_grades', true)->where('is_promotable', false)->count();
  $noGradesCount    = $students->where('has_locked_grades', false)->count();
  $graduatingCount  = $nextGrade === null ? $promotableCount : 0;

  $needsAction = ($gradeSummary['submitted'] + $gradeSummary['finalized']) > 0;
@endphp

{{-- Grade pipeline warning banner --}}
@if($needsAction)
<div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:10px;padding:.8rem 1.1rem;margin-bottom:1rem;font-size:.83rem;color:#92400e;display:flex;gap:.75rem;align-items:flex-start;">
  <span style="font-size:1.1rem;flex-shrink:0;">⚠</span>
  <div>
    <strong>Grades are not fully locked.</strong>
    @if($gradeSummary['submitted'] > 0)
      {{ $gradeSummary['submitted'] }} submitted grade(s) need to be <strong>finalized</strong>.
    @endif
    @if($gradeSummary['finalized'] > 0)
      {{ $gradeSummary['finalized'] }} finalized grade(s) need to be <strong>locked</strong>.
    @endif
    Go to <a href="{{ route('registrar.grades') }}" style="color:#92400e;font-weight:700;text-decoration:underline;">Grade Oversight</a> to complete the locking process before promoting.
  </div>
</div>
@endif

{{-- Stat cards --}}
<div class="promo-stats">
  <div class="promo-stat promo-stat--green">
    <div class="promo-stat__num">{{ $promotableCount }}</div>
    <div class="promo-stat__lbl">{{ $nextGrade ? 'Promotable' : 'For Graduation' }}</div>
  </div>
  <div class="promo-stat promo-stat--red">
    <div class="promo-stat__num">{{ $retentionCount }}</div>
    <div class="promo-stat__lbl">For Retention</div>
  </div>
  <div class="promo-stat promo-stat--orange">
    <div class="promo-stat__num">{{ $noGradesCount }}</div>
    <div class="promo-stat__lbl">No Locked Grades</div>
  </div>
  <div class="promo-stat promo-stat--gray">
    <div class="promo-stat__num">{{ $totalSubjects }}</div>
    <div class="promo-stat__lbl">Subjects / Student</div>
  </div>
</div>

{{-- Student Table --}}
<div class="enc-card">
  <div class="enc-card__header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
    <div>
      <div class="enc-card__title" style="margin-bottom:2px;">
        Step 2 — Review &amp; Confirm Promotion
      </div>
      <div style="font-size:.8rem;color:#64748b;">
        <strong>{{ $selectedSection->grade_level }} — {{ $selectedSection->section_name }}</strong>
        · {{ $selectedYear->year_label }}
        @if($nextGrade)
          → advancing to <strong style="color:#4f46e5;">{{ $nextGrade }}</strong>
        @else
          → <strong style="color:#7c3aed;">graduating (final level)</strong>
        @endif
      </div>
    </div>
    @if($totalSubjects > 0)
    <div style="font-size:.78rem;color:#64748b;text-align:right;">
      <div>Grade pipeline (this section)</div>
      <div style="display:flex;gap:.4rem;margin-top:3px;justify-content:flex-end;">
        @if($gradeSummary['submitted'] > 0)
        <span class="pipe-chip pipe-submitted">{{ $gradeSummary['submitted'] }} submitted</span>
        @endif
        @if($gradeSummary['finalized'] > 0)
        <span class="pipe-chip pipe-finalized">{{ $gradeSummary['finalized'] }} finalized</span>
        @endif
        <span class="pipe-chip pipe-locked">{{ $gradeSummary['locked'] }} locked</span>
      </div>
    </div>
    @endif
  </div>

  @if($students->isEmpty())
  <div style="padding:3.5rem;text-align:center;color:#94a3b8;">
    <div style="font-size:1.5rem;margin-bottom:.4rem;">👤</div>
    <div style="font-weight:600;color:#374151;margin-bottom:.25rem;">No enrolled students found.</div>
    <div style="font-size:.83rem;">No active enrollments exist for this section and year.</div>
  </div>
  @else
  <form id="promotionForm" method="POST" action="{{ route('registrar.promotion.promote') }}">
    @csrf
    <input type="hidden" name="source_year_id"    value="{{ $selectedYear->id }}">
    <input type="hidden" name="source_section_id" value="{{ $selectedSection->id }}">

    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <th style="padding:10px 14px;width:36px;">
              <input type="checkbox" id="selectAll" title="Select all promotable"
                     style="cursor:pointer;width:15px;height:15px;"
                     onchange="document.querySelectorAll('.student-cb:not(:disabled)').forEach(cb => cb.checked = this.checked); updateCount();">
            </th>
            <th style="padding:10px 14px;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;">Student</th>
            <th style="padding:10px 14px;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;">LRN</th>
            <th style="padding:10px 14px;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;">Grade Progress</th>
            <th style="padding:10px 14px;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;">General Avg</th>
            <th style="padding:10px 14px;text-align:center;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($students as $row)
          @php
            $avg       = $row->average;
            $promotable = $row->is_promotable;
            $pct = $row->total_subjects > 0
              ? min(100, round(($row->locked_count / $row->total_subjects) * 100))
              : 0;

            if (!$row->has_locked_grades && $row->all_grades_count === 0) {
              $statusLabel = 'No Grades';
              $statusClass = 'pb-nogrades';
            } elseif (!$row->has_locked_grades) {
              $statusLabel = 'Needs Lock';
              $statusClass = 'pb-nolock';
            } elseif ($promotable && $nextGrade === null) {
              $statusLabel = 'For Graduation';
              $statusClass = 'pb-graduated';
            } elseif ($promotable) {
              $statusLabel = 'Promotable';
              $statusClass = 'pb-promotable';
            } else {
              $statusLabel = 'For Retention';
              $statusClass = 'pb-retention';
            }
          @endphp
          <tr style="border-bottom:1px solid #f1f5f9;{{ $promotable ? 'background:#fafffe;' : '' }}">
            <td style="padding:10px 14px;">
              <input type="checkbox"
                     name="student_ids[]"
                     value="{{ $row->student->id }}"
                     class="student-cb"
                     {{ $promotable ? '' : 'disabled' }}
                     {{ $promotable ? 'checked' : '' }}
                     onchange="updateCount()"
                     style="cursor:{{ $promotable ? 'pointer' : 'not-allowed' }};width:15px;height:15px;">
            </td>
            <td style="padding:10px 14px;">
              <div style="font-weight:700;color:#0f172a;">
                {{ $row->student->last_name }}, {{ $row->student->first_name }}
              </div>
              @if($row->student->middle_name)
              <div style="font-size:.75rem;color:#94a3b8;">{{ $row->student->middle_name }}</div>
              @endif
            </td>
            <td style="padding:10px 14px;font-size:.83rem;color:#64748b;font-family:monospace;">
              {{ $row->student->lrn ?? '—' }}
            </td>
            <td style="padding:10px 14px;min-width:160px;">
              <div class="pipeline" style="margin-bottom:4px;">
                @if($row->submitted_count > 0)
                <span class="pipe-chip pipe-submitted">{{ $row->submitted_count }}S</span>
                @endif
                @if($row->finalized_count > 0)
                <span class="pipe-chip pipe-finalized">{{ $row->finalized_count }}F</span>
                @endif
                @if($row->locked_count > 0)
                <span class="pipe-chip pipe-locked">{{ $row->locked_count }}L</span>
                @endif
                @if($row->all_grades_count === 0)
                <span class="pipe-chip pipe-none">No grades</span>
                @endif
              </div>
              @if($row->total_subjects > 0)
              <div class="grade-bar">
                <div class="grade-bar__fill {{ $pct >= 100 ? 'grade-bar__fill--full' : '' }}"
                     style="width:{{ $pct }}%"></div>
              </div>
              <div style="font-size:.65rem;color:#94a3b8;margin-top:2px;">
                {{ $row->locked_count }}/{{ $row->total_subjects }} locked
              </div>
              @endif
            </td>
            <td style="padding:10px 14px;text-align:center;">
              @if($avg !== null)
              <span style="font-size:1rem;font-weight:800;color:{{ $promotable ? '#16a34a' : '#dc2626' }};">
                {{ number_format($avg, 2) }}
              </span>
              <div style="font-size:.68rem;color:#94a3b8;margin-top:1px;">
                {{ $promotable ? '≥ '.$passingGrade.' ✓' : '< '.$passingGrade.' ✗' }}
              </div>
              @else
              <span style="color:#94a3b8;font-size:.85rem;">—</span>
              @endif
            </td>
            <td style="padding:10px 14px;text-align:center;">
              <span class="promo-badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Footer action bar --}}
    <div class="promo-footer">
      <div class="promo-footer__info">
        <span id="selCount">{{ $promotableCount }}</span> of <strong>{{ $totalStudents }}</strong> selected for promotion
        @if(!$activeYear)
        <span style="color:#dc2626;font-weight:700;margin-left:.5rem;">— No active academic year. Cannot promote.</span>
        @elseif($noGradesCount > 0 && $promotableCount === 0)
        <span style="color:#d97706;font-weight:600;margin-left:.5rem;">— Lock grades first in Grade Oversight.</span>
        @endif
      </div>
      <div style="display:flex;gap:.6rem;align-items:center;">
        @if($needsAction)
        <a href="{{ route('registrar.grades') }}"
           style="padding:.55rem 1.1rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;color:#374151;text-decoration:none;font-size:.875rem;font-weight:600;">
          Go to Grade Oversight
        </a>
        @endif
        <button type="submit"
                id="promoteBtn"
                class="enc-btn enc-btn--primary"
                onclick="return confirm('Promote selected students? This will advance their grade level and create new enrollment records in {{ $activeYear?->year_label ?? 'the active year' }}.')"
                {{ (!$activeYear || $promotableCount === 0) ? 'disabled' : '' }}
                style="{{ (!$activeYear || $promotableCount === 0) ? 'opacity:.45;cursor:not-allowed;' : '' }}">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
               style="width:14px;height:14px;flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
          </svg>
          {{ $nextGrade ? 'Confirm Promotion' : 'Confirm Graduation' }}
        </button>
      </div>
    </div>
  </form>
  @endif
</div>

@elseif($selectedYear && $sections->isEmpty())
<div class="enc-card" style="padding:2.5rem;text-align:center;color:#94a3b8;">
  <div style="font-size:1.5rem;margin-bottom:.4rem;">🏫</div>
  <div style="font-weight:600;color:#374151;margin-bottom:.25rem;">No sections found for {{ $selectedYear->year_label }}.</div>
  <div style="font-size:.83rem;">Create sections for this academic year first.</div>
</div>
@endif

@endsection

@push('scripts')
<script>
function updateCount() {
  const checked = document.querySelectorAll('.student-cb:checked').length;
  const el = document.getElementById('selCount');
  const btn = document.getElementById('promoteBtn');
  if (el) el.textContent = checked;
  if (btn && !btn.dataset.forceDisabled) {
    btn.disabled = checked === 0;
    btn.style.opacity = checked === 0 ? '.45' : '1';
    btn.style.cursor  = checked === 0 ? 'not-allowed' : 'pointer';
  }
}
// Mark force-disabled buttons (no active year) so JS doesn't re-enable them
document.querySelectorAll('#promoteBtn[disabled]').forEach(b => b.dataset.forceDisabled = '1');
</script>
@endpush
