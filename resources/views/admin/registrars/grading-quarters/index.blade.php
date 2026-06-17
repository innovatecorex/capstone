@extends('layouts.app')
@section('title', 'Grading Quarters')
@section('breadcrumb', 'Grading Quarters')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Grading Quarters</h1>
      <p class="enc-page__subtitle">Manage grading periods per academic year. <strong>Activate</strong> a period to open it for faculty grade submission.</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.grading-quarters.create') }}"
         style="background:#1d4ed8;color:#fff;padding:.55rem 1.1rem;border-radius:8px;font-size:.875rem;font-weight:700;text-decoration:none;display:inline-block;">
        + Add Grading Period
      </a>
    </div>
  </div>
</div>

@if(session('success'))
<div style="margin-bottom:20px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.9rem;font-weight:500;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;">{{ session('error') }}</div>
@endif
@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;">
  @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
</div>
@endif

{{-- Tip banner --}}
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:.75rem 1.1rem;margin-bottom:1.25rem;font-size:.83rem;color:#1e40af;display:flex;gap:.6rem;align-items:flex-start;">
  <span style="font-size:1rem;flex-shrink:0;">💡</span>
  <span>Only <strong>one period per academic year</strong> can be active at a time. Activating a period automatically deactivates all others in the same year. Faculty can only submit grades while a period is active.</span>
</div>

{{-- Filters --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__body" style="padding:16px 20px;">
    <form method="GET" action="{{ route('admin.grading-quarters.index') }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:4px;min-width:220px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Academic Year</label>
        <select name="academic_year_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">All Years</option>
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ request('academic_year_id') == $yr->id ? 'selected' : '' }}>
              {{ $yr->year_label }}{{ $yr->status === 'active' ? ' (Active Year)' : '' }}
            </option>
          @endforeach
        </select>
      </div>
      <div style="display:flex;flex-direction:column;gap:4px;min-width:160px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Status</label>
        <select name="status" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">All Statuses</option>
          <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
          <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
      </div>
    </form>
  </div>
</div>

{{-- Table --}}
<div class="enc-card">
  <div class="enc-card__body" style="padding:0;">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">#</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Period</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Academic Year</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Start</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">End</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
            <th style="padding:12px 14px;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($quarters as $quarter)
          <tr style="border-bottom:1px solid #f1f5f9;{{ $quarter->status === 'active' ? 'background:#f0fdf4;' : '' }}">
            <td style="padding:12px 14px;color:#94a3b8;font-weight:700;">{{ $quarter->quarter_number }}</td>
            <td style="padding:12px 14px;color:#0f172a;font-weight:700;">{{ $quarter->quarter_name }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ $quarter->academicYear?->year_label ?? '—' }}</td>
            <td style="padding:12px 14px;color:#64748b;font-size:.83rem;">{{ optional($quarter->start_date)->format('M d, Y') ?? '—' }}</td>
            <td style="padding:12px 14px;color:#64748b;font-size:.83rem;">{{ optional($quarter->end_date)->format('M d, Y') ?? '—' }}</td>
            <td style="padding:12px 14px;">
              @php
                $colors = [
                  'active'   => ['#166534','#86efac','#f0fdf4'],
                  'inactive' => ['#92400e','#fcd34d','#fffbeb'],
                  'archived' => ['#475569','#cbd5e1','#f8fafc'],
                ];
                $c = $colors[$quarter->status] ?? $colors['inactive'];
              @endphp
              <span style="display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">
                @if($quarter->status === 'active')● @endif{{ $quarter->status }}
              </span>
            </td>
            <td style="padding:12px 14px;text-align:right;white-space:nowrap;">
              <div style="display:flex;gap:.4rem;justify-content:flex-end;align-items:center;flex-wrap:wrap;">
                @if($quarter->status !== 'active')
                <form method="POST" action="{{ route('admin.grading-quarters.activate', $quarter) }}" style="display:inline;">
                  @csrf @method('PATCH')
                  <button type="submit"
                    style="padding:.3rem .75rem;border:none;border-radius:6px;background:#4f46e5;color:#fff;font-size:.78rem;font-weight:700;cursor:pointer;"
                    onclick="return confirm('Activate \'{{ addslashes($quarter->quarter_name) }}\'? All other periods in {{ $quarter->academicYear?->year_label }} will be deactivated.')">
                    ✓ Set Active
                  </button>
                </form>
                @else
                <span style="font-size:.78rem;color:#16a34a;font-weight:800;">● ACTIVE</span>
                @endif
                <a href="{{ route('admin.grading-quarters.edit', $quarter) }}"
                   style="padding:.3rem .75rem;border:1px solid #cbd5e1;border-radius:6px;background:#fff;color:#374151;text-decoration:none;font-size:.78rem;font-weight:600;">Edit</a>
                @if($quarter->status !== 'active')
                <form method="POST" action="{{ route('admin.grading-quarters.destroy', $quarter) }}" style="display:inline;"
                      onsubmit="return confirm('Delete \'{{ addslashes($quarter->quarter_name) }}\'? This cannot be undone.');">
                  @csrf @method('DELETE')
                  <button type="submit" style="padding:.3rem .75rem;border:1px solid #fca5a5;border-radius:6px;background:#fef2f2;color:#dc2626;font-size:.78rem;font-weight:600;cursor:pointer;">Delete</button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" style="padding:48px;text-align:center;color:#94a3b8;">
              <div style="font-size:1.5rem;margin-bottom:.5rem;">📅</div>
              <div style="font-weight:600;color:#374151;margin-bottom:.25rem;">No grading periods found.</div>
              <div style="font-size:.83rem;">Periods are auto-created when you save an Academic Year, or you can add them manually.</div>
              <a href="{{ route('admin.grading-quarters.create') }}"
                 style="display:inline-block;margin-top:12px;padding:.5rem 1.1rem;background:#1d4ed8;color:#fff;border-radius:8px;font-size:.85rem;font-weight:700;text-decoration:none;">
                Add Grading Period
              </a>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@if($quarters->hasPages())
<div style="margin-top:16px;">{{ $quarters->links() }}</div>
@endif

@endsection
