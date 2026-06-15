@extends('layouts.app')
@section('title', 'Grades & Records')
@section('breadcrumb', 'Grades & Records')

@push('head')
<style>
/* ── Stat cards ── */
.gr-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
.gr-stat  { background:#fff; border:1px solid rgba(15,23,42,.08); border-radius:14px; padding:1.1rem 1.25rem; display:flex; align-items:center; gap:.9rem; }
.gr-stat__icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.gr-stat__icon svg { width:20px; height:20px; }
.gr-stat__value { font-size:1.6rem; font-weight:900; line-height:1; color:#0f172a; }
.gr-stat__label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-top:3px; }

/* ── Filter bar ── */
.gr-filters { display:flex; flex-wrap:wrap; gap:.6rem; align-items:flex-end; margin-bottom:1.1rem; }
.gr-select  { padding:.45rem .75rem; border:1px solid rgba(15,23,42,.13); border-radius:8px; font-size:.84rem; color:#0f172a; background:#fff; font-family:inherit; outline:none; cursor:pointer; }
.gr-select:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.1); }

/* ── Table ── */
.gr-table { width:100%; border-collapse:collapse; font-size:.875rem; }
.gr-table thead tr { background:#f8fafc; border-bottom:1px solid rgba(15,23,42,.08); }
.gr-table th { padding:10px 14px; text-align:left; font-size:.7rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }
.gr-table th.center, .gr-table td.center { text-align:center; }
.gr-table tbody tr { border-bottom:1px solid rgba(15,23,42,.04); transition:background .1s; }
.gr-table tbody tr:hover { background:#f8fafc; }
.gr-table td { padding:10px 14px; color:#374151; vertical-align:middle; }

/* ── Status badges ── */
.st-chip { display:inline-block; padding:.2rem .6rem; border-radius:999px; font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:.04em; }
.st-draft      { background:#fef9c3; color:#854d0e; }
.st-submitted  { background:#dbeafe; color:#1e40af; }
.st-finalized  { background:#dcfce7; color:#166534; }
.st-locked     { background:#f1f5f9; color:#475569; }

/* ── Grade badge ── */
.grade-val { font-size:1.05rem; font-weight:900; }
.grade-pass { color:#15803d; }
.grade-fail { color:#dc2626; }

/* ── Action buttons ── */
.btn-sm { display:inline-flex; align-items:center; gap:.3rem; padding:.3rem .75rem; border-radius:7px; font-size:.75rem; font-weight:700; text-decoration:none; border:none; cursor:pointer; transition:all .12s; white-space:nowrap; }
.btn-finalize { background:#1d4ed8; color:#fff; }
.btn-finalize:hover { background:#1e40af; }
.btn-lock     { background:#0f172a; color:#fff; }
.btn-lock:hover { filter:brightness(1.2); }
.btn-view     { background:rgba(15,23,42,.06); color:#374151; }
.btn-view:hover { background:rgba(15,23,42,.12); }

/* ── Bulk bar ── */
.bulk-bar { display:none; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.6rem;
  background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:.65rem 1rem; margin-bottom:.75rem; }
.bulk-bar.active { display:flex; }
.bulk-count { font-size:.84rem; font-weight:700; color:#1e40af; }
.btn-bulk-finalize { background:#1d4ed8; color:#fff; padding:.45rem 1rem; border-radius:8px; font-size:.82rem; font-weight:700; border:none; cursor:pointer; }
.btn-bulk-finalize:hover { background:#1e40af; }
</style>
@endpush

@section('content')

{{-- ── Page header ── --}}
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Grades &amp; Records</h1>
      <p class="enc-page__subtitle">Review and verify faculty grade submissions for the active quarter.</p>
    </div>
    <div style="display:flex;gap:.6rem;align-items:center;">
      @if($pendingUnlockCount > 0)
      <a href="{{ route('registrar.grade-lock.index') }}"
         style="background:#fef9c3;border:1px solid #fde68a;color:#92400e;padding:.45rem .9rem;border-radius:8px;font-size:.8rem;font-weight:700;text-decoration:none;">
        ⚠ {{ $pendingUnlockCount }} Unlock Request{{ $pendingUnlockCount > 1 ? 's' : '' }}
      </a>
      @endif
      <a href="{{ route('registrar.grade-lock.index') }}"
         style="background:#0f172a;color:#fff;padding:.5rem 1rem;border-radius:8px;font-size:.82rem;font-weight:700;text-decoration:none;">
        🔒 Grade Lock
      </a>
    </div>
  </div>
</div>

{{-- ── Flash messages ── --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.8rem 1rem;margin-bottom:1rem;font-size:.87rem;color:#166534;font-weight:600;">
  {{ session('success') }}
</div>
@endif
@if(session('error') || $errors->any())
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.8rem 1rem;margin-bottom:1rem;font-size:.87rem;color:#991b1b;">
  {{ session('error') ?? $errors->first() }}
</div>
@endif

@if(!$activeYear)
{{-- No active year state ── --}}
<div class="enc-card" style="padding:3rem;text-align:center;">
  <div style="font-size:2rem;margin-bottom:.5rem;">📅</div>
  <div style="font-weight:700;color:#374151;margin-bottom:.35rem;">No Active Academic Year</div>
  <div style="font-size:.87rem;color:#94a3b8;">Set an active academic year under Academic Years to manage grades.</div>
</div>
@else

{{-- ── Stat cards ── --}}
<div class="gr-stats">

  <div class="gr-stat">
    <div class="gr-stat__icon" style="background:#eff6ff;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#1d4ed8" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <div>
      <div class="gr-stat__value">{{ $stats['pending_review'] }}</div>
      <div class="gr-stat__label">Pending Review</div>
    </div>
  </div>

  <div class="gr-stat">
    <div class="gr-stat__icon" style="background:#f0fdf4;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div>
      <div class="gr-stat__value">{{ $stats['total_finalized'] }}</div>
      <div class="gr-stat__label">Finalized</div>
    </div>
  </div>

  <div class="gr-stat">
    <div class="gr-stat__icon" style="background:#f8fafc;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#475569" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
    </div>
    <div>
      <div class="gr-stat__value">{{ $stats['total_locked'] }}</div>
      <div class="gr-stat__label">Locked</div>
    </div>
  </div>

  <div class="gr-stat">
    <div class="gr-stat__icon" style="background:#fdf4ff;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#9333ea" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
    </div>
    <div>
      <div class="gr-stat__value" style="font-size:1rem;">{{ $activeQuarter?->quarter_name ?? 'None' }}</div>
      <div class="gr-stat__label">Active Quarter</div>
    </div>
  </div>

</div>

@if(!$activeQuarter && $stats['pending_review'] === 0)
<div class="enc-card" style="padding:2.5rem;text-align:center;color:#94a3b8;">
  <div style="font-size:1.4rem;margin-bottom:.5rem;">📋</div>
  <div style="font-weight:600;color:#374151;margin-bottom:.25rem;">No submitted grades to review.</div>
  <div style="font-size:.85rem;">No active grading quarter found. Set a quarter to <strong>active</strong> under Academic Years.</div>
</div>
@else

{{-- ── Filters ── --}}
<form method="GET" id="filterForm">
  <div class="gr-filters">
    <div>
      <div style="font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Quarter</div>
      <select name="quarter_id" class="gr-select" onchange="document.getElementById('filterForm').submit()">
        @forelse($quarters as $q)
        <option value="{{ $q->id }}" {{ $selectedQuarterId == $q->id ? 'selected' : '' }}>
          {{ $q->quarter_name }}{{ $q->status === 'active' ? ' ★' : '' }}
        </option>
        @empty
        <option value="">No quarters</option>
        @endforelse
      </select>
    </div>
    <div>
      <div style="font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Section &amp; Subject</div>
      <select name="section_subject_id" class="gr-select" style="min-width:200px;" onchange="document.getElementById('filterForm').submit()">
        <option value="">All Classes</option>
        @foreach($sectionSubjects as $ss)
        <option value="{{ $ss->id }}" {{ $selectedSectionSubjectId == $ss->id ? 'selected' : '' }}>
          {{ $ss->section?->section_name }} — {{ $ss->subject?->subject_name }}
        </option>
        @endforeach
      </select>
    </div>
    <div>
      <div style="font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.25rem;">Status</div>
      <select name="status" class="gr-select" onchange="document.getElementById('filterForm').submit()">
        <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>Pending Review</option>
        <option value="finalized" {{ $statusFilter === 'finalized' ? 'selected' : '' }}>Finalized</option>
        <option value="locked"    {{ $statusFilter === 'locked'    ? 'selected' : '' }}>Locked</option>
        <option value="all"       {{ $statusFilter === 'all'       ? 'selected' : '' }}>All Statuses</option>
      </select>
    </div>
    @if(request('quarter_id') || request('section_subject_id') || (request('status') && request('status') !== 'submitted'))
    <div style="padding-top:1.2rem;">
      <a href="{{ route('registrar.grades') }}" style="font-size:.8rem;color:#94a3b8;text-decoration:none;">Clear filters</a>
    </div>
    @endif
  </div>
</form>

{{-- ── Bulk action bar (shown when checkboxes selected) ── --}}
<form method="POST" action="{{ route('registrar.grades.bulk-finalize') }}" id="bulkForm">
  @csrf
  <div class="bulk-bar" id="bulkBar">
    <span class="bulk-count" id="bulkCount">0 selected</span>
    <button type="submit" class="btn-bulk-finalize"
            onclick="return confirm('Finalize all selected submitted grades?')">
      ✓ Finalize Selected
    </button>
  </div>

  {{-- ── Grades table ── --}}
  <div class="enc-card" style="padding:0;overflow:hidden;">
    <table class="gr-table">
      <thead>
        <tr>
          @if($statusFilter === 'submitted' || $statusFilter === 'all')
          <th style="width:36px;">
            <input type="checkbox" id="selectAll" style="cursor:pointer;accent-color:#1d4ed8;">
          </th>
          @endif
          <th>Section</th>
          <th>Subject</th>
          <th>Student</th>
          <th>Faculty</th>
          <th class="center">Written</th>
          <th class="center">Perf.</th>
          <th class="center">Q.A.</th>
          <th class="center">Final Grade</th>
          <th class="center">Status</th>
          <th class="center">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($grades as $g)
        @php
          $isSubmitted = $g->status === 'submitted';
          $isFinalized = $g->status === 'finalized';
          $pass = !is_null($g->final_grade) ? $g->isPassing() : null;
        @endphp
        <tr>
          @if($statusFilter === 'submitted' || $statusFilter === 'all')
          <td>
            @if($isSubmitted)
            <input type="checkbox" name="grade_ids[]" value="{{ $g->id }}"
                   class="grade-check" style="cursor:pointer;accent-color:#1d4ed8;">
            @endif
          </td>
          @endif
          <td>{{ $g->sectionSubject?->section?->section_name ?? '—' }}</td>
          <td style="font-weight:600;color:#0f172a;">{{ $g->sectionSubject?->subject?->subject_name ?? '—' }}</td>
          <td>
            <div style="font-weight:600;color:#0f172a;">{{ $g->enrollment?->student?->last_name }}, {{ $g->enrollment?->student?->first_name }}</div>
            @if($g->enrollment?->student?->lrn)
            <div style="font-size:.72rem;color:#94a3b8;">{{ $g->enrollment->student->lrn }}</div>
            @endif
          </td>
          <td style="color:#64748b;font-size:.82rem;">{{ $g->sectionSubject?->faculty?->last_name ?? 'TBA' }}</td>
          <td class="center" style="color:#64748b;font-size:.82rem;">{{ $g->written_work ?? '—' }}</td>
          <td class="center" style="color:#64748b;font-size:.82rem;">{{ $g->performance_task ?? '—' }}</td>
          <td class="center" style="color:#64748b;font-size:.82rem;">{{ $g->quarterly_assessment ?? '—' }}</td>
          <td class="center">
            @if(!is_null($g->final_grade))
            <span class="grade-val {{ $pass ? 'grade-pass' : 'grade-fail' }}">
              {{ number_format($g->final_grade, 0) }}
            </span>
            @if($pass !== null)
            <div style="font-size:.65rem;font-weight:700;color:{{ $pass ? '#16a34a' : '#dc2626' }};">{{ $pass ? 'PASSED' : 'FAILED' }}</div>
            @endif
            @else
            <span style="color:#cbd5e1;">—</span>
            @endif
          </td>
          <td class="center">
            <span class="st-chip st-{{ $g->status }}">{{ ucfirst($g->status) }}</span>
            @if($g->submitted_at)
            <div style="font-size:.65rem;color:#94a3b8;margin-top:2px;">{{ $g->submitted_at->diffForHumans() }}</div>
            @endif
          </td>
          <td class="center">
            <div style="display:flex;gap:.35rem;justify-content:center;flex-wrap:wrap;">
              @if($isSubmitted)
              <form method="POST" action="{{ route('registrar.grades.finalize', $g) }}">
                @csrf
                <button type="submit" class="btn-sm btn-finalize"
                        onclick="return confirm('Finalize grade for {{ addslashes($g->enrollment?->student?->full_name ?? 'this student') }}?')">
                  ✓ Finalize
                </button>
              </form>
              @endif

              @if($isFinalized)
              <form method="POST" action="{{ route('registrar.grades.lock', $g) }}">
                @csrf
                <button type="submit" class="btn-sm btn-lock"
                        onclick="return confirm('Lock this grade? It will be immutable until unlocked.')">
                  🔒 Lock
                </button>
              </form>
              @endif

              <a href="{{ route('registrar.grades.show', $g) }}" class="btn-sm btn-view">View</a>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="{{ ($statusFilter === 'submitted' || $statusFilter === 'all') ? 11 : 10 }}"
              style="padding:3rem;text-align:center;color:#94a3b8;">
            @if($statusFilter === 'submitted')
              No submitted grades pending review.
              @if($stats['total_finalized'] > 0)
                <br><span style="font-size:.8rem;">{{ $stats['total_finalized'] }} grade(s) already finalized.</span>
              @endif
            @elseif($statusFilter === 'finalized')
              No finalized grades. Use <a href="{{ route('registrar.grade-lock.index') }}" style="color:var(--primary);">Grade Lock</a> to lock them globally.
            @elseif($statusFilter === 'locked')
              No locked grades yet.
            @else
              No grades found for this filter.
            @endif
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    @if($grades instanceof \Illuminate\Pagination\LengthAwarePaginator && $grades->hasPages())
    <div style="padding:.85rem 1.1rem;border-top:1px solid rgba(15,23,42,.06);">
      {{ $grades->links() }}
    </div>
    @endif
  </div>
</form>

@if($grades->isNotEmpty())
<div style="margin-top:.6rem;font-size:.77rem;color:#94a3b8;">
  Showing {{ $grades->count() }}{{ $grades instanceof \Illuminate\Pagination\LengthAwarePaginator ? ' of ' . $grades->total() : '' }} grade record(s).
</div>
@endif

{{-- ── Quick link to global lock ── --}}
@if($stats['total_finalized'] > 0)
<div style="margin-top:1.25rem;background:#f0fdf4;border:1px solid #86efac;border-radius:12px;padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
  <div>
    <div style="font-weight:700;color:#166534;font-size:.9rem;">{{ $stats['total_finalized'] }} grade(s) ready to lock</div>
    <div style="font-size:.78rem;color:#16a34a;margin-top:2px;">Go to Grade Lock to apply section-level or global locks.</div>
  </div>
  <a href="{{ route('registrar.grade-lock.index') }}"
     style="background:#15803d;color:#fff;padding:.5rem 1rem;border-radius:8px;font-size:.82rem;font-weight:700;text-decoration:none;">
    Grade Lock →
  </a>
</div>
@endif

@endif {{-- end activeQuarter check --}}
@endif {{-- end activeYear check --}}

@endsection

@push('scripts')
<script>
(function () {
  const selectAll  = document.getElementById('selectAll');
  const bulkBar    = document.getElementById('bulkBar');
  const bulkCount  = document.getElementById('bulkCount');
  const checkboxes = document.querySelectorAll('.grade-check');

  function updateBulk() {
    const checked = document.querySelectorAll('.grade-check:checked').length;
    if (checked > 0) {
      bulkBar.classList.add('active');
      bulkCount.textContent = checked + ' selected';
    } else {
      bulkBar.classList.remove('active');
    }
    if (selectAll) selectAll.indeterminate = checked > 0 && checked < checkboxes.length;
  }

  if (selectAll) {
    selectAll.addEventListener('change', function () {
      checkboxes.forEach(cb => cb.checked = this.checked);
      updateBulk();
    });
  }

  checkboxes.forEach(cb => cb.addEventListener('change', updateBulk));
})();
</script>
@endpush
