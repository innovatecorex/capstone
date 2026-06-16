@extends('layouts.app')
@section('title', 'Report Generation')
@section('breadcrumb', 'Report Generation')

@push('head')
<style>
/* ── Stat cards ─────────────────────────────────────────── */
.rg-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:1.25rem; }
@media(max-width:640px){ .rg-stats{ grid-template-columns:1fr 1fr; } }

.rg-stat { background:#fff; border:1px solid rgba(15,23,42,.08); border-radius:12px; padding:1rem 1.1rem; }
.rg-stat__num { font-size:1.9rem; font-weight:900; line-height:1; margin-bottom:2px; }
.rg-stat__lbl { font-size:.71rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; }
.rg-stat--blue   .rg-stat__num { color:#2563eb; }
.rg-stat--green  .rg-stat__num { color:#16a34a; }
.rg-stat--purple .rg-stat__num { color:#7c3aed; }

/* ── Filters card ───────────────────────────────────────── */
.rg-filters { background:#fff; border:1px solid rgba(15,23,42,.08); border-radius:12px;
  padding:1rem 1.25rem; margin-bottom:1.25rem; }
.rg-filters__row { display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
.rg-label { font-size:.73rem; font-weight:700; color:#475569; text-transform:uppercase;
  letter-spacing:.04em; display:block; margin-bottom:5px; }
.rg-input { padding:8px 11px; border:1px solid #cbd5e1; border-radius:8px;
  background:#fff; font-size:.875rem; min-width:200px; }
.rg-input:focus { outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.1); }

/* ── Table ──────────────────────────────────────────────── */
.rg-card { background:#fff; border:1px solid rgba(15,23,42,.08); border-radius:12px;
  overflow:hidden; margin-bottom:1.25rem; }
.rg-table { width:100%; border-collapse:collapse; font-size:.875rem; }
.rg-table th { padding:9px 14px; background:#f8fafc; text-align:left;
  font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em;
  color:#94a3b8; border-bottom:1px solid rgba(15,23,42,.06); }
.rg-table td { padding:11px 14px; border-bottom:1px solid rgba(15,23,42,.04); vertical-align:middle; }
.rg-table tr:last-child td { border-bottom:none; }
.rg-table tr:hover td { background:#fafbff; }

/* Grade pipeline chips */
.rg-pipe { display:flex; gap:4px; flex-wrap:wrap; }
.rg-chip { display:inline-block; padding:.16rem .5rem; border-radius:5px;
  font-size:.67rem; font-weight:800; text-transform:uppercase; }
.rg-chip--locked   { background:#dcfce7; color:#166534; }
.rg-chip--final    { background:#dbeafe; color:#1e40af; }
.rg-chip--none     { background:#f1f5f9; color:#94a3b8; }

/* Average badge */
.rg-avg { font-size:.95rem; font-weight:800; }
.rg-avg--pass { color:#16a34a; }
.rg-avg--fail { color:#dc2626; }
.rg-avg--none { color:#94a3b8; font-size:.82rem; }

/* Download button */
.rg-dl-btn { display:inline-flex; align-items:center; gap:.3rem; padding:.35rem .85rem;
  background:#4f46e5; color:#fff; border-radius:8px; font-size:.77rem; font-weight:700;
  text-decoration:none; white-space:nowrap; transition:background .12s; }
.rg-dl-btn:hover { background:#4338ca; }
.rg-dl-btn--disabled { background:#e2e8f0; color:#94a3b8; cursor:not-allowed; pointer-events:none; }

/* ── Side panel (quick links) ───────────────────────────── */
.rg-layout { display:grid; grid-template-columns:1fr 260px; gap:1.25rem; }
@media(max-width:900px){ .rg-layout{ grid-template-columns:1fr; } }

.rg-side-card { background:#fff; border:1px solid rgba(15,23,42,.08); border-radius:12px;
  padding:1rem 1.1rem; align-self:start; }
.rg-side-card h3 { font-size:.8rem; font-weight:800; text-transform:uppercase;
  letter-spacing:.05em; color:#64748b; margin-bottom:.75rem; }
.rg-side-link { display:flex; align-items:center; gap:.6rem; padding:.6rem .75rem;
  border-radius:8px; text-decoration:none; color:#374151; font-size:.84rem;
  font-weight:600; transition:background .12s; }
.rg-side-link:hover { background:#f1f5f9; }
.rg-side-link__icon { width:30px; height:30px; border-radius:8px; display:flex;
  align-items:center; justify-content:center; flex-shrink:0; }

/* ── Empty / prompt state ───────────────────────────────── */
.rg-empty { padding:3.5rem 2rem; text-align:center; color:#94a3b8; }
.rg-empty__icon  { font-size:2rem; margin-bottom:.5rem; }
.rg-empty__title { font-size:.95rem; font-weight:700; color:#374151; margin-bottom:.3rem; }
.rg-empty__sub   { font-size:.82rem; }
</style>
@endpush

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Report Generation</h1>
      <p class="enc-page__subtitle">Generate, download, and verify student report cards for the active academic year.</p>
    </div>
    @if($activeYear)
    <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.5rem 1rem;font-size:.8rem;color:#166534;font-weight:700;white-space:nowrap;">
      ● {{ $activeYear->year_label }}
    </div>
    @endif
  </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.8rem 1rem;margin-bottom:1rem;font-size:.85rem;color:#166534;font-weight:600;">
  ✓ {{ session('success') }}
</div>
@endif

{{-- Stat cards --}}
<div class="rg-stats">
  <div class="rg-stat rg-stat--blue">
    <div class="rg-stat__num">{{ number_format($stats['total_enrolled']) }}</div>
    <div class="rg-stat__lbl">Enrolled Students</div>
  </div>
  <div class="rg-stat rg-stat--green">
    <div class="rg-stat__num">{{ number_format($stats['with_grades']) }}</div>
    <div class="rg-stat__lbl">With Finalized Grades</div>
  </div>
  <div class="rg-stat rg-stat--purple">
    <div class="rg-stat__num">{{ number_format($stats['tokens_generated']) }}</div>
    <div class="rg-stat__lbl">Report Cards Generated</div>
  </div>
</div>

{{-- Note about grade requirement --}}
@if($stats['with_grades'] < $stats['total_enrolled'])
<div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:10px;padding:.75rem 1.1rem;margin-bottom:1.25rem;font-size:.82rem;color:#92400e;display:flex;gap:.6rem;align-items:flex-start;">
  <span style="flex-shrink:0;font-size:1rem;">💡</span>
  <span>
    <strong>{{ $stats['total_enrolled'] - $stats['with_grades'] }}</strong> enrolled student(s) have no finalized grades yet.
    Report cards are generated from <strong>finalized or locked</strong> grades.
    Go to <a href="{{ route('registrar.grades') }}" style="color:#92400e;text-decoration:underline;font-weight:700;">Grade Oversight</a> to finalize grades first.
  </span>
</div>
@endif

<div class="rg-layout">
  {{-- Main column ─────────────────────────────────────────── --}}
  <div>
    {{-- Filters --}}
    <div class="rg-filters">
      <form method="GET" action="{{ route('registrar.report-cards') }}" class="rg-filters__row">
        <div>
          <label class="rg-label" for="rc-year">Academic Year</label>
          <select name="academic_year_id" id="rc-year" class="rg-input" onchange="this.form.submit()">
            <option value="">— All Years —</option>
            @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ optional($selectedYear)->id == $yr->id ? 'selected' : '' }}>
              {{ $yr->year_label }}{{ $yr->status === 'active' ? ' (Active)' : '' }}
            </option>
            @endforeach
          </select>
        </div>

        @if($selectedYear && $sections->isNotEmpty())
        <div>
          <label class="rg-label" for="rc-sec">Section</label>
          <select name="section_id" id="rc-sec" class="rg-input" onchange="this.form.submit()">
            <option value="">— All Sections —</option>
            @foreach($sections as $sec)
            <option value="{{ $sec->id }}" {{ optional($selectedSection)->id == $sec->id ? 'selected' : '' }}>
              {{ $sec->grade_level }} — {{ $sec->section_name }}
            </option>
            @endforeach
          </select>
        </div>
        @endif

        @if($selectedYear)
        <div>
          <label class="rg-label" for="rc-search">Search Student</label>
          <div style="display:flex;gap:6px;">
            <input type="text" name="search" id="rc-search" class="rg-input"
                   placeholder="Name or LRN…" value="{{ $search }}"
                   style="min-width:180px;">
            @if($selectedYear)
            <input type="hidden" name="academic_year_id" value="{{ optional($selectedYear)->id }}">
            @endif
            @if($selectedSection)
            <input type="hidden" name="section_id" value="{{ optional($selectedSection)->id }}">
            @endif
            <button type="submit" style="padding:8px 14px;border:none;border-radius:8px;background:#4f46e5;color:#fff;font-size:.82rem;font-weight:700;cursor:pointer;">Search</button>
          </div>
        </div>
        @endif
      </form>
    </div>

    {{-- Student list --}}
    @if($students->isNotEmpty())
    @php $canGenerateCount = $students->where('can_generate', true)->count(); @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem;flex-wrap:wrap;gap:.4rem;">
      <div style="font-size:.82rem;color:#64748b;">
        <strong style="color:#0f172a;">{{ $students->count() }}</strong> student(s)
        @if($selectedSection) in <strong>{{ $selectedSection->grade_level }} — {{ $selectedSection->section_name }}</strong>@endif
        @if($search) matching "<strong>{{ $search }}</strong>"@endif
        · <span style="color:#16a34a;font-weight:700;">{{ $canGenerateCount }}</span> ready for report card
      </div>
    </div>

    <div class="rg-card">
      <div style="overflow-x:auto;">
        <table class="rg-table">
          <thead>
            <tr>
              <th>Student</th>
              <th>LRN</th>
              @if(!$selectedSection)<th>Section</th>@endif
              <th>Grade Status</th>
              <th style="text-align:center;">General Avg</th>
              <th style="text-align:right;">Report Card</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $row)
            @php
              $avg       = $row->general_avg;
              $passing   = config('academic.passing_grade', 75);
              $canGen    = $row->can_generate;
            @endphp
            <tr>
              <td>
                <div style="font-weight:700;color:#0f172a;">
                  {{ optional($row->student)->last_name }}, {{ optional($row->student)->first_name }}
                </div>
                @if(optional($row->student)->middle_name)
                <div style="font-size:.72rem;color:#94a3b8;">{{ optional($row->student)->middle_name }}</div>
                @endif
              </td>
              <td style="font-size:.82rem;color:#64748b;font-family:monospace;">
                {{ optional($row->student)->lrn ?? '—' }}
              </td>
              @if(!$selectedSection)
              <td style="font-size:.82rem;color:#64748b;">
                {{ optional($row->section)->grade_level }} — {{ optional($row->section)->section_name ?? '—' }}
              </td>
              @endif
              <td>
                <div class="rg-pipe">
                  @if($row->locked_count > 0)
                  <span class="rg-chip rg-chip--locked">{{ $row->locked_count }} Locked</span>
                  @endif
                  @if($row->finalized_count > 0)
                  <span class="rg-chip rg-chip--final">{{ $row->finalized_count }} Finalized</span>
                  @endif
                  @if($row->total_grades === 0)
                  <span class="rg-chip rg-chip--none">No Grades</span>
                  @endif
                </div>
              </td>
              <td style="text-align:center;">
                @if($avg !== null)
                <span class="rg-avg {{ $avg >= $passing ? 'rg-avg--pass' : 'rg-avg--fail' }}">
                  {{ number_format($avg, 2) }}
                </span>
                <div style="font-size:.67rem;color:#94a3b8;margin-top:1px;">
                  {{ $avg >= $passing ? 'Passing ✓' : 'Below Passing' }}
                </div>
                @else
                <span class="rg-avg rg-avg--none">—</span>
                @endif
              </td>
              <td style="text-align:right;">
                @if($canGen && $row->student)
                <a href="{{ route('report-card.download', $row->student) }}"
                   class="rg-dl-btn" target="_blank">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                  </svg>
                  Download PDF
                </a>
                @else
                <span class="rg-dl-btn rg-dl-btn--disabled">No Grades</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Footer --}}
      <div style="padding:10px 14px;background:#f8fafc;border-top:1px solid rgba(15,23,42,.05);font-size:.75rem;color:#94a3b8;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.4rem;">
        <span>Report cards are generated as QR-verified PDFs using current finalized/locked grade data.</span>
        <span>Each download is logged and fingerprinted for tamper detection.</span>
      </div>
    </div>

    @elseif($selectedYear && ($selectedSection || $search))
    <div class="rg-card">
      <div class="rg-empty">
        <div class="rg-empty__icon">🔍</div>
        <div class="rg-empty__title">No students found</div>
        <div class="rg-empty__sub">
          @if($search)
            No enrolled students match "<strong>{{ $search }}</strong>" in {{ optional($selectedYear)->year_label }}.
          @else
            No enrolled students found in this section.
          @endif
        </div>
      </div>
    </div>

    @elseif($selectedYear)
    <div class="rg-card">
      <div class="rg-empty">
        <div class="rg-empty__icon">📄</div>
        <div class="rg-empty__title">Select a section or search for a student</div>
        <div class="rg-empty__sub">
          Choose a section from the filter above, or type a student name / LRN in the search box.
          Only enrolled students with finalized or locked grades can have report cards generated.
        </div>
      </div>
    </div>

    @else
    <div class="rg-card">
      <div class="rg-empty">
        <div class="rg-empty__icon">📋</div>
        <div class="rg-empty__title">Select an academic year to begin</div>
        <div class="rg-empty__sub">Use the Academic Year filter above to load sections and students.</div>
      </div>
    </div>
    @endif
  </div>

  {{-- Side panel ──────────────────────────────────────────── --}}
  <div>
    <div class="rg-side-card" style="margin-bottom:1rem;">
      <h3>Quick Access</h3>
      <a href="{{ route('registrar.reports.aggregate') }}" class="rg-side-link">
        <div class="rg-side-link__icon" style="background:#f0fdf4;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2" style="width:16px;height:16px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
          </svg>
        </div>
        <div>
          <div>Honor Roll &amp; Intervention</div>
          <div style="font-size:.72rem;color:#94a3b8;font-weight:400;">Aggregate academic reports</div>
        </div>
      </a>
      <a href="{{ route('registrar.grades') }}" class="rg-side-link">
        <div class="rg-side-link__icon" style="background:#eff6ff;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2" style="width:16px;height:16px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <div>Grade Oversight</div>
          <div style="font-size:.72rem;color:#94a3b8;font-weight:400;">Finalize &amp; lock grades</div>
        </div>
      </a>
      <a href="{{ route('registrar.promotion') }}" class="rg-side-link">
        <div class="rg-side-link__icon" style="background:#faf5ff;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2" style="width:16px;height:16px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"/>
          </svg>
        </div>
        <div>
          <div>Student Promotion</div>
          <div style="font-size:.72rem;color:#94a3b8;font-weight:400;">Advance to next grade level</div>
        </div>
      </a>
    </div>

    <div class="rg-side-card">
      <h3>About Report Cards</h3>
      <div style="font-size:.79rem;color:#64748b;line-height:1.55;display:flex;flex-direction:column;gap:.5rem;">
        <p>Each PDF includes a <strong>QR code</strong> that anyone can scan to verify the document's authenticity.</p>
        <p>The QR links to a public verification page that checks a <strong>SHA-256 fingerprint</strong> of the grade data — any tampering is immediately detected.</p>
        <p>Grades must be in <strong>Finalized</strong> or <strong>Locked</strong> status to appear on the report card.</p>
        <p>Downloading a report card is automatically <strong>logged</strong> in the audit trail.</p>
      </div>
    </div>
  </div>
</div>

@endsection
