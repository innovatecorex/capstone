@extends('layouts.app')

@section('title', 'Enrollment Advising')
@section('breadcrumb', 'Enrollment Advising')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Enrollment Advising</h1>
      <p class="enc-page__subtitle">Build and confirm subject plans for student enrollment.</p>
    </div>
  </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#166534;margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#991b1b;margin-bottom:1rem;">{{ session('error') }}</div>
@endif

{{-- Search bar --}}
<form method="GET" action="{{ route('registrar.advising.index') }}"
      style="display:flex;gap:.65rem;align-items:flex-end;margin-bottom:1.25rem;flex-wrap:wrap;">
  <div style="flex:1;min-width:240px;max-width:380px;">
    <label style="display:block;font-size:.72rem;font-weight:700;color:var(--gray-500);margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.04em;">Search Student</label>
    <input type="text" name="search" value="{{ $search }}" placeholder="Name or LRN…"
      style="width:100%;padding:.5rem .85rem;border:1px solid rgba(15,23,42,.14);border-radius:8px;font-size:.87rem;">
  </div>
  <button type="submit"
    style="padding:.5rem 1.1rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:.87rem;cursor:pointer;">
    Search
  </button>
  @if($search)
  <a href="{{ route('registrar.advising.index') }}"
    style="padding:.5rem .9rem;background:rgba(15,23,42,.07);color:var(--navy);border-radius:8px;font-size:.87rem;text-decoration:none;font-weight:600;">
    Clear
  </a>
  @endif
</form>

<div class="enc-card" style="padding:0;overflow:hidden;">
  @if($students->isEmpty())
    <div style="padding:3rem;text-align:center;color:var(--gray-400);font-size:.9rem;">
      No students found{{ $search ? ' matching "'.e($search).'"' : '' }}.
    </div>
  @else
  <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
    <thead>
      <tr style="background:#f8fafc;border-bottom:1px solid rgba(15,23,42,.08);">
        <th class="adv-th" style="text-align:left;">Student</th>
        <th class="adv-th">Grade Level</th>
        <th class="adv-th">LRN</th>
        @if($activeYear)
        <th class="adv-th">Plan ({{ $activeYear->year_label }})</th>
        @endif
        <th class="adv-th"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($students as $s)
      <tr class="adv-row">
        <td class="adv-td">
          <div style="font-weight:700;color:var(--navy);">{{ $s->last_name }}, {{ $s->first_name }}</div>
        </td>
        <td class="adv-td" style="text-align:center;">
          <span style="font-size:.83rem;color:var(--gray-500);">{{ $s->grade_level ?? '—' }}</span>
        </td>
        <td class="adv-td" style="text-align:center;font-family:monospace;font-size:.8rem;color:var(--gray-500);">
          {{ $s->lrn ?? '—' }}
        </td>
        @if($activeYear)
        <td class="adv-td" style="text-align:center;">
          @php $cnt = $planCounts[$s->id] ?? 0; @endphp
          @if($cnt > 0)
            <span style="display:inline-block;padding:.18rem .6rem;border-radius:99px;background:#ede9fe;color:#5b21b6;font-size:.72rem;font-weight:700;">{{ $cnt }} subject{{ $cnt !== 1 ? 's' : '' }}</span>
          @else
            <span style="font-size:.78rem;color:var(--gray-300);">No plan</span>
          @endif
        </td>
        @endif
        <td class="adv-td" style="text-align:right;">
          <a href="{{ route('registrar.advising.show', $s->id) }}"
             style="font-size:.82rem;font-weight:700;color:var(--primary);text-decoration:none;padding:.3rem .75rem;border:1px solid var(--primary);border-radius:6px;white-space:nowrap;"
             onmouseenter="this.style.background='var(--primary)';this.style.color='#fff'"
             onmouseleave="this.style.background='';this.style.color='var(--primary)'">
            Advise →
          </a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @if($students->hasPages())
  <div style="padding:.85rem 1.1rem;border-top:1px solid rgba(15,23,42,.06);">
    {{ $students->links() }}
  </div>
  @endif
  @endif
</div>
@endsection

@push('head')
<style>
.adv-th { padding:10px 14px; font-size:.73rem; font-weight:700; color:var(--gray-500); text-transform:uppercase; letter-spacing:.05em; text-align:center; }
.adv-td { padding:12px 14px; border-bottom:1px solid rgba(15,23,42,.04); vertical-align:middle; }
.adv-row:hover td { background:rgba(15,23,42,.015); }
</style>
@endpush
