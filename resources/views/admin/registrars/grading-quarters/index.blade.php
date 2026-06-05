@extends('layouts.app')
@section('title', 'Grading Quarters')
@section('breadcrumb', 'Grading Quarters')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Grading Quarters</h1>
      <p class="enc-page__subtitle">Manage grading periods within each academic year. Quarterly years have 4 periods; semestral years have 2.</p>
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
@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;">
  @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
</div>
@endif

{{-- Filters --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__body" style="padding:16px 20px;">
    <form method="GET" action="{{ route('admin.grading-quarters.index') }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:4px;min-width:220px;">
        <label style="font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Academic Year</label>
        <select name="academic_year_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">All Years</option>
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ request('academic_year_id') == $yr->id ? 'selected' : '' }}>{{ $yr->year_label }}</option>
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
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:12px 14px;color:#0f172a;font-weight:700;">{{ $quarter->quarter_name }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ $quarter->academicYear?->year_label ?? '—' }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ optional($quarter->start_date)->format('M d, Y') ?? '—' }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ optional($quarter->end_date)->format('M d, Y') ?? '—' }}</td>
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
              <a href="{{ route('admin.grading-quarters.edit', $quarter) }}" style="color:#1d4ed8;text-decoration:none;font-size:.82rem;font-weight:600;margin-right:10px;">Edit</a>
              <form method="POST" action="{{ route('admin.grading-quarters.destroy', $quarter) }}" style="display:inline;" onsubmit="return confirm('Delete grading period \'{{ $quarter->quarter_name }}\'? This cannot be undone.');">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#dc2626;font-size:.82rem;font-weight:600;cursor:pointer;">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" style="padding:40px;text-align:center;color:#94a3b8;">No grading periods found. They're auto-created when you save an academic year, or click "+ Add Grading Period".</td></tr>
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
