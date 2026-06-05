@extends('layouts.app')
@section('title', 'Edit Grading Period')
@section('breadcrumb', 'Edit Grading Period')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Edit Grading Period</h1>
      <p class="enc-page__subtitle">Update the details for <strong>{{ $quarter->quarter_name }}</strong>.</p>
    </div>
    <div class="enc-page__actions">
      <a href="{{ route('admin.grading-quarters.index') }}" style="background:#0f172a;color:#fff;padding:.55rem 1.1rem;border-radius:8px;font-size:.875rem;font-weight:700;text-decoration:none;display:inline-block;">← Back</a>
    </div>
  </div>
</div>

@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;">
  @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
</div>
@endif

<div class="enc-card" style="max-width:720px;">
  <form method="POST" action="{{ route('admin.grading-quarters.update', $quarter) }}">
    @csrf
    @method('PUT')

    <div class="enc-card__header"><div class="enc-card__title">Grading Period Details</div></div>

    <div class="enc-card__body" style="padding:24px;display:flex;flex-direction:column;gap:18px;">

      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Academic Year <span style="color:#dc2626;">*</span></label>
        <select name="academic_year_id" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ old('academic_year_id', $quarter->academic_year_id) == $yr->id ? 'selected' : '' }}>{{ $yr->year_label }}</option>
          @endforeach
        </select>
      </div>

      <div style="display:grid;grid-template-columns:1fr 2fr;gap:18px;">
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Period No. <span style="color:#dc2626;">*</span></label>
          <select name="quarter_number" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
            @for($i = 1; $i <= 4; $i++)
              <option value="{{ $i }}" {{ old('quarter_number', $quarter->quarter_number) == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
          </select>
        </div>
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Period Name <span style="color:#dc2626;">*</span></label>
          <input type="text" name="quarter_name" required maxlength="100"
                 value="{{ old('quarter_name', $quarter->quarter_name) }}"
                 style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Start Date <span style="color:#dc2626;">*</span></label>
          <input type="date" name="start_date" required
                 value="{{ old('start_date', optional($quarter->start_date)->format('Y-m-d')) }}"
                 style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
        </div>
        <div>
          <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">End Date <span style="color:#dc2626;">*</span></label>
          <input type="date" name="end_date" required
                 value="{{ old('end_date', optional($quarter->end_date)->format('Y-m-d')) }}"
                 style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
        </div>
      </div>

      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Status <span style="color:#dc2626;">*</span></label>
        <select name="status" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="active"   {{ old('status', $quarter->status) === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status', $quarter->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
          <option value="archived" {{ old('status', $quarter->status) === 'archived' ? 'selected' : '' }}>Archived</option>
        </select>
      </div>

    </div>

    <div style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;">
      <a href="{{ route('admin.grading-quarters.index') }}" style="padding:.6rem 1.4rem;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#475569;text-decoration:none;font-size:.875rem;font-weight:600;">Cancel</a>
      <button type="submit" style="padding:.6rem 1.4rem;border:none;border-radius:8px;background:#1d4ed8;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;">Save Changes</button>
    </div>
  </form>
</div>

@endsection
