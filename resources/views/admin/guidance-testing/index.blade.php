@extends('layouts.app')

@section('title', 'Guidance & Testing — EncryptEd')
@section('breadcrumb', 'Guidance & Testing')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Guidance &amp; Testing</h1>
      <p class="enc-page__subtitle">
        Admission and academic test records for applicants
        @if($activeYear)
          &mdash; <strong>S.Y. {{ $activeYear->year_label }}</strong>
        @endif
      </p>
    </div>
  </div>
</div>

{{-- ── Stats ── --}}
<div class="enc-stats">

  <a href="{{ route('admin.guidance-testing.index') }}" class="enc-stat-card" style="text-decoration:none;color:inherit;">
    <div class="enc-stat-icon enc-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['total'] }}</div>
      <div class="enc-stat-label">Total Applicants</div>
    </div>
  </a>

  <a href="{{ route('admin.guidance-testing.index') }}" class="enc-stat-card" style="text-decoration:none;color:inherit;">
    <div class="enc-stat-icon enc-stat-icon--indigo">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m2 7H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v11a2 2 0 01-2 2z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['tested'] }}</div>
      <div class="enc-stat-label">Tests Recorded</div>
    </div>
  </a>

  <a href="{{ route('admin.guidance-testing.index', ['result' => 'passed']) }}" class="enc-stat-card" style="text-decoration:none;color:inherit;">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['passed'] }}</div>
      <div class="enc-stat-label">Passed</div>
    </div>
  </a>

  <a href="{{ route('admin.guidance-testing.index', ['result' => 'failed']) }}" class="enc-stat-card" style="text-decoration:none;color:inherit;">
    <div class="enc-stat-icon enc-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['failed'] }}</div>
      <div class="enc-stat-label">Failed</div>
    </div>
  </a>

  <a href="{{ route('admin.guidance-testing.index', ['result' => 'eligible']) }}" class="enc-stat-card" style="text-decoration:none;color:inherit;">
    <div class="enc-stat-icon enc-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['eligible'] }}</div>
      <div class="enc-stat-label">Eligible for Enrollment</div>
    </div>
  </a>

</div>

{{-- ── Filters ── --}}
<form method="GET" style="display:flex;gap:.65rem;align-items:center;margin-bottom:1.1rem;flex-wrap:wrap;">
  <select name="result" class="gt-select" style="width:160px;" onchange="this.form.submit()">
    <option value="">All Results</option>
    <option value="passed"     {{ request('result')==='passed'     ? 'selected':'' }}>Passed</option>
    <option value="failed"     {{ request('result')==='failed'     ? 'selected':'' }}>Failed</option>
    <option value="not_tested" {{ request('result')==='not_tested' ? 'selected':'' }}>Not Yet Tested</option>
  </select>
  <select name="grade" class="gt-select" style="width:160px;" onchange="this.form.submit()">
    <option value="">All Grade Levels</option>
    @foreach($grades as $g)
    <option value="{{ $g }}" {{ request('grade')===$g ? 'selected':'' }}>{{ $g }}</option>
    @endforeach
  </select>
  @if(request('result') || request('grade'))
  <a href="{{ route('admin.guidance-testing.index') }}" style="font-size:.82rem;color:var(--gray-400);text-decoration:none;">Clear filters</a>
  @endif
</form>

{{-- ── Table ── --}}
<div class="enc-card" style="padding:0;overflow:hidden;">
  <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
    <thead>
      <tr style="background:#f8fafc;border-bottom:1px solid rgba(15,23,42,.08);">
        <th style="text-align:left;padding:10px 14px;font-weight:700;color:var(--gray-500);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Applicant</th>
        <th style="text-align:left;padding:10px 14px;font-weight:700;color:var(--gray-500);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Grade Level</th>
        <th style="text-align:center;padding:10px 14px;font-weight:700;color:var(--gray-500);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Test Result</th>
        <th style="text-align:center;padding:10px 14px;font-weight:700;color:var(--gray-500);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Score</th>
        <th style="text-align:center;padding:10px 14px;font-weight:700;color:var(--gray-500);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Academic</th>
        <th style="text-align:center;padding:10px 14px;font-weight:700;color:var(--gray-500);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Status</th>
        <th style="text-align:right;padding:10px 14px;font-weight:700;color:var(--gray-500);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($applicants as $app)
      @php $etr = $app->entranceTestResult; @endphp
      <tr style="border-bottom:1px solid rgba(15,23,42,.05);transition:background .1s;" onmouseenter="this.style.background='#f8fafc'" onmouseleave="this.style.background=''">
        <td style="padding:11px 14px;">
          <div style="font-weight:700;color:var(--navy);">{{ $app->full_name }}</div>
          <div style="font-size:.75rem;color:var(--gray-400);margin-top:1px;">{{ $app->reference_number }}</div>
        </td>
        <td style="padding:11px 14px;color:var(--gray-500);">{{ $app->applying_for_grade }}</td>
        <td style="padding:11px 14px;text-align:center;">
          @if($etr)
            <span style="display:inline-block;padding:.2rem .75rem;border-radius:999px;font-size:.73rem;font-weight:800;
              background:{{ $etr->passed ? '#dcfce7' : '#fee2e2' }};
              color:{{ $etr->passed ? '#166534' : '#991b1b' }};">
              {{ $etr->passed ? 'PASSED' : 'FAILED' }}
            </span>
          @else
            <span style="display:inline-block;padding:.2rem .75rem;border-radius:999px;font-size:.73rem;font-weight:700;background:#f1f5f9;color:var(--gray-400);">No Test</span>
          @endif
        </td>
        <td style="padding:11px 14px;text-align:center;font-size:.82rem;color:var(--gray-500);">
          @if($etr)
            {{ number_format($etr->total_score, 0) }}/{{ number_format($etr->max_score, 0) }}
            <span style="color:var(--gray-400);">({{ $etr->percentage }}%)</span>
          @else
            <span style="color:var(--gray-300);">—</span>
          @endif
        </td>
        <td style="padding:11px 14px;text-align:center;">
          @if($etr && ($etr->acad_filipino_score !== null || $etr->acad_english_score !== null))
            <span style="display:inline-block;padding:.2rem .65rem;border-radius:999px;font-size:.72rem;font-weight:700;background:#ede9fe;color:#5b21b6;">Recorded</span>
          @else
            <span style="font-size:.78rem;color:var(--gray-300);">—</span>
          @endif
        </td>
        <td style="padding:11px 14px;text-align:center;">
          <span class="status-chip status-{{ $app->status }}" style="font-size:.73rem;">
            {{ ucfirst(str_replace('_', ' ', $app->status)) }}
          </span>
        </td>
        <td style="padding:11px 14px;text-align:right;">
          <a href="{{ route('admin.guidance-testing.create', $app->id) }}"
             style="font-size:.8rem;font-weight:700;color:var(--primary);text-decoration:none;padding:.3rem .75rem;border:1px solid var(--primary);border-radius:6px;white-space:nowrap;transition:all .1s;"
             onmouseenter="this.style.background='var(--primary)';this.style.color='#fff'"
             onmouseleave="this.style.background='';this.style.color='var(--primary)'">
            {{ $etr ? 'Edit Record' : 'Record Test' }}
          </a>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="7" style="padding:2.5rem;text-align:center;color:var(--gray-400);font-size:.88rem;">
          No applicants found matching these filters.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>

  @if($applicants->hasPages())
  <div style="padding:.85rem 1.1rem;border-top:1px solid rgba(15,23,42,.06);">
    {{ $applicants->links() }}
  </div>
  @endif
</div>
@endsection

@push('head')
<style>
.gt-select {
  padding: .45rem .75rem;
  border: 1px solid rgba(15,23,42,.14);
  border-radius: 8px;
  font-size: .84rem;
  color: var(--navy);
  background: #fff;
  font-family: inherit;
  outline: none;
  cursor: pointer;
}
.gt-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.status-chip { display:inline-block; padding:.22rem .65rem; border-radius:999px; font-size:.73rem; font-weight:700; }
.status-pending                 { background:#fef9c3; color:#854d0e; }
.status-under_review            { background:#dbeafe; color:#1e40af; }
.status-accepted                { background:#dcfce7; color:#166534; }
.status-rejected                { background:#fee2e2; color:#991b1b; }
.status-enrolled                { background:#e0f2fe; color:#0369a1; }
.status-eligible_for_enrollment { background:#fffbeb; color:#92400e; }
</style>
@endpush
