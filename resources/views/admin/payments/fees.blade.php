@extends('layouts.app')
@section('title', 'Enrollment Fees')
@section('breadcrumb', 'Enrollment Fees')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Enrollment Fees</h1>
      <p class="enc-page__subtitle">Set the fee each grade level must pay before a student can be enlisted into a section.</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.payments.index') }}" style="background:#f1f5f9;color:#0f172a;padding:.55rem 1.1rem;border-radius:8px;font-size:.875rem;font-weight:600;text-decoration:none;display:inline-block;border:1px solid #e2e8f0;">← Payments</a>
    </div>
  </div>
</div>

@if(session('success'))
<div style="margin-bottom:20px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.9rem;font-weight:500;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;">
  @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
</div>
@endif

{{-- Year selector --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__body" style="padding:16px 20px;">
    <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:4px;min-width:240px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Academic Year</label>
        <select name="academic_year_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;cursor:pointer;">
          <option value="">— Select year —</option>
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ $yearId == $yr->id ? 'selected' : '' }}>
              {{ $yr->year_label }} ({{ ucfirst($yr->status) }})
            </option>
          @endforeach
        </select>
      </div>
    </form>
  </div>
</div>

@if($yearId)

@php
  // $gradeLevels is passed from PaymentController::fees() via StudentController::GRADE_LEVELS
  // Canonical list lives in config('academic.grade_levels') — Grade 7-12 only (JHS + SHS)
  $feesByGrade = $fees->keyBy('grade_level');
  $selectedYear = $academicYears->firstWhere('id', $yearId);
@endphp

{{-- Summary chips --}}
<div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
  <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:10px 16px;font-size:.82rem;color:#166534;font-weight:600;">
    <span style="font-size:1.2rem;font-weight:800;">{{ $fees->count() }}</span> / {{ count($gradeLevels) }} grades configured
  </div>
  <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:8px;padding:10px 16px;font-size:.82rem;color:#9a3412;font-weight:600;">
    {{ count($gradeLevels) - $fees->count() }} grades not yet set
  </div>
  @if($fees->count())
  <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px 16px;font-size:.82rem;color:#475569;font-weight:600;">
    Avg fee: ₱{{ number_format($fees->avg('amount'), 2) }}
  </div>
  @endif
</div>

{{-- Fees table --}}
<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 01-.75.75h-.75M3.75 18.75h16.5"/>
      </svg>
      Fee Schedule — {{ $selectedYear?->year_label }}
    </div>
  </div>
  <div class="enc-card__body" style="padding:0;">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
            <th style="padding:11px 18px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;width:160px;">Grade Level</th>
            <th style="padding:11px 18px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Current Fee</th>
            <th style="padding:11px 18px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Last Updated</th>
            <th style="padding:11px 18px;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;min-width:260px;">Set / Update Amount</th>
          </tr>
        </thead>
        <tbody>
          @foreach($gradeLevels as $grade)
          @php $fee = $feesByGrade->get($grade); @endphp
          <tr style="border-bottom:1px solid #f1f5f9;">

            {{-- Grade label --}}
            <td style="padding:12px 18px;font-weight:700;color:#0f172a;">
              {{ $grade }}
              @if(in_array($grade, ['Grade 11','Grade 12']))
                <span style="display:inline-block;margin-left:6px;padding:.1rem .45rem;border-radius:4px;font-size:.65rem;font-weight:700;background:#ede9fe;color:#6d28d9;text-transform:uppercase;letter-spacing:.04em;">SHS</span>
              @else
                <span style="display:inline-block;margin-left:6px;padding:.1rem .45rem;border-radius:4px;font-size:.65rem;font-weight:700;background:#dbeafe;color:#1d4ed8;text-transform:uppercase;letter-spacing:.04em;">JHS</span>
              @endif
            </td>

            {{-- Current amount --}}
            <td style="padding:12px 18px;">
              @if($fee)
                <span style="font-size:1rem;font-weight:700;color:#0f172a;">₱{{ number_format($fee->amount, 2) }}</span>
                <span style="color:#94a3b8;font-size:.78rem;margin-left:4px;">PHP</span>
              @else
                <span style="color:#94a3b8;font-size:.85rem;font-style:italic;">Not set</span>
              @endif
            </td>

            {{-- Last updated --}}
            <td style="padding:12px 18px;color:#64748b;font-size:.82rem;">
              {{ $fee ? $fee->updated_at->format('M d, Y') : '—' }}
            </td>

            {{-- Inline set/update form --}}
            <td style="padding:10px 18px;text-align:right;">
              <form method="POST" action="{{ route('admin.payments.fees.store') }}"
                    style="display:flex;gap:8px;align-items:center;justify-content:flex-end;">
                @csrf
                <input type="hidden" name="academic_year_id" value="{{ $yearId }}">
                <input type="hidden" name="grade_level" value="{{ $grade }}">
                <input type="number" name="amount" step="0.01" min="1" max="999999"
                       value="{{ $fee ? $fee->amount : '' }}"
                       placeholder="0.00"
                       style="width:130px;padding:7px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:.875rem;text-align:right;background:#fff;"
                       required>
                <button type="submit"
                        style="padding:7px 14px;background:{{ $fee ? '#0f172a' : '#4f46e5' }};color:#fff;border:none;border-radius:8px;font-size:.82rem;font-weight:700;cursor:pointer;white-space:nowrap;">
                  {{ $fee ? 'Update' : 'Set Fee' }}
                </button>
              </form>
            </td>

          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@else
<div class="enc-card">
  <div class="enc-card__body" style="padding:48px 24px;text-align:center;color:#94a3b8;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 01-.75.75h-.75M3.75 18.75h16.5"/>
    </svg>
    <p style="font-size:.95rem;font-weight:500;color:#475569;">Select an academic year above to view and manage enrollment fees.</p>
  </div>
</div>
@endif

@endsection
