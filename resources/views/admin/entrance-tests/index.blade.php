@extends('layouts.app')

@section('title', 'Entrance Test Results')
@section('breadcrumb', 'Entrance Test Results')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Entrance Test Results</h1>
      <p class="enc-page__subtitle">Record and review admission examination scores.</p>
    </div>
  </div>
</div>

{{-- Summary stat cards --}}
<div class="enc-stats">

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--navy">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['total'] }}</div>
      <div class="enc-stat-label">Total Applicants</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--indigo">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['tested'] }}</div>
      <div class="enc-stat-label">Tests Recorded</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['passed'] }}</div>
      <div class="enc-stat-label">Passed</div>
    </div>
  </div>

  <div class="enc-stat-card">
    <div class="enc-stat-icon enc-stat-icon--red">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div class="enc-stat-body">
      <div class="enc-stat-value">{{ $counts['failed'] }}</div>
      <div class="enc-stat-label">Failed</div>
    </div>
  </div>

</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.entrance-tests.index') }}"
      style="display:flex;gap:.65rem;flex-wrap:wrap;margin-bottom:1.1rem;align-items:flex-end;">
  <div>
    <label style="display:block;font-size:.72rem;font-weight:700;color:var(--gray-500);margin-bottom:.25rem;">Result</label>
    <select name="result"
      style="padding:.5rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.87rem;background:#fff;">
      <option value="">All</option>
      <option value="passed"  {{ request('result') === 'passed'  ? 'selected' : '' }}>Passed</option>
      <option value="failed"  {{ request('result') === 'failed'  ? 'selected' : '' }}>Failed</option>
      <option value="pending" {{ request('result') === 'pending' ? 'selected' : '' }}>Not yet tested</option>
    </select>
  </div>
  <div>
    <label style="display:block;font-size:.72rem;font-weight:700;color:var(--gray-500);margin-bottom:.25rem;">Grade</label>
    <select name="grade"
      style="padding:.5rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.87rem;background:#fff;">
      <option value="">All grades</option>
      @foreach($grades as $g)
      <option value="{{ $g }}" {{ request('grade') === $g ? 'selected' : '' }}>{{ $g }}</option>
      @endforeach
    </select>
  </div>
  <button type="submit"
    style="padding:.5rem 1.1rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:.87rem;cursor:pointer;">
    Filter
  </button>
  @if(request()->hasAny(['result','grade']))
  <a href="{{ route('admin.entrance-tests.index') }}"
    style="padding:.5rem .9rem;background:rgba(15,23,42,.07);color:var(--navy);border-radius:8px;font-size:.87rem;text-decoration:none;font-weight:600;">
    Clear
  </a>
  @endif
</form>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#166534;margin-bottom:1rem;">
  {{ session('success') }}
</div>
@endif

<div class="enc-card" style="padding:0;overflow:hidden;">
  @if($applicants->isEmpty())
    <div style="padding:3rem;text-align:center;color:var(--gray-400);font-size:.9rem;">
      No applicants found.
    </div>
  @else
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th class="etbl-th" style="text-align:left;">Applicant</th>
          <th class="etbl-th">Grade</th>
          <th class="etbl-th">Test Date</th>
          <th class="etbl-th">Score</th>
          <th class="etbl-th">Result</th>
          <th class="etbl-th"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($applicants as $a)
        @php $r = $a->entranceTestResult; @endphp
        <tr class="etbl-row">
          <td class="etbl-td">
            <div style="font-weight:700;color:var(--navy);">{{ $a->full_name }}</div>
            <div style="font-size:.76rem;color:var(--gray-400);">{{ $a->reference_number }}</div>
          </td>
          <td class="etbl-td" style="text-align:center;font-size:.85rem;">{{ $a->applying_for_grade }}</td>
          <td class="etbl-td" style="text-align:center;font-size:.82rem;color:var(--gray-500);">
            {{ $r ? $r->test_date->format('M d, Y') : '—' }}
          </td>
          <td class="etbl-td" style="text-align:center;">
            @if($r)
              <span style="font-weight:800;font-size:.95rem;color:var(--navy);">
                {{ number_format($r->total_score, 0) }}
              </span>
              <span style="font-size:.75rem;color:var(--gray-400);">/ {{ number_format($r->max_score, 0) }}</span>
              <div style="font-size:.72rem;color:var(--gray-400);">{{ $r->percentage }}%</div>
            @else
              <span style="color:var(--gray-400);font-size:.82rem;">—</span>
            @endif
          </td>
          <td class="etbl-td" style="text-align:center;">
            @if($r)
              <span class="result-chip {{ $r->passed ? 'chip-pass' : 'chip-fail' }}">
                {{ $r->passed ? 'PASSED' : 'FAILED' }}
              </span>
            @else
              <span class="result-chip chip-pending">Not Tested</span>
            @endif
          </td>
          <td class="etbl-td" style="text-align:right;">
            <a href="{{ route('admin.entrance-tests.create', $a->id) }}"
               style="font-size:.82rem;color:var(--primary);font-weight:700;text-decoration:none;">
              {{ $r ? 'Edit' : 'Record' }} →
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if($applicants->hasPages())
  <div style="padding:1rem 1.25rem;border-top:1px solid rgba(15,23,42,.06);">
    {{ $applicants->links() }}
  </div>
  @endif
  @endif
</div>
@endsection

@push('head')
<style>
.etbl-th { padding:10px 14px; font-size:.73rem; font-weight:700; color:var(--gray-500); text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid rgba(15,23,42,.08); text-align:center; }
.etbl-td { padding:12px 14px; border-bottom:1px solid rgba(15,23,42,.04); vertical-align:middle; }
.etbl-row:hover td { background:rgba(15,23,42,.015); }
.result-chip { display:inline-block; padding:.22rem .7rem; border-radius:999px; font-size:.72rem; font-weight:800; letter-spacing:.04em; }
.chip-pass    { background:#dcfce7; color:#166534; }
.chip-fail    { background:#fee2e2; color:#991b1b; }
.chip-pending { background:#f1f5f9; color:#64748b; }
</style>
@endpush
