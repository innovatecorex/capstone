@extends('layouts.admin')

@section('title', 'Add Curriculum Mapping')
@section('breadcrumb', 'Add Mapping')

@push('head')
<style>
.cm-form-card { background:#fff;border:1px solid rgba(15,23,42,.08);border-radius:14px;overflow:hidden;max-width:640px; }
.cm-form-section { padding:1.25rem 1.5rem;border-bottom:1px solid rgba(15,23,42,.06); }
.cm-form-section:last-child { border-bottom:none; }
.cm-section-title { font-size:.72rem;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.9rem;display:flex;align-items:center;gap:6px; }
.cm-section-title span { display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:50%;background:#4f46e5;color:#fff;font-size:.65rem;font-weight:900; }
.cm-field { display:flex;flex-direction:column;gap:.3rem;margin-bottom:.85rem; }
.cm-field:last-child { margin-bottom:0; }
.cm-label { font-size:.73rem;font-weight:700;color:#374151; }
.cm-label .req { color:#dc2626;margin-left:2px; }
.cm-label .opt { font-size:.67rem;font-weight:500;color:#94a3b8;margin-left:5px; }
.cm-ctrl {
  width:100%;height:42px;padding:0 12px;
  border:1.5px solid #e2e8f0;border-radius:9px;
  font-size:.875rem;color:#0f172a;background:#f8fafc;font-family:inherit;
  outline:none;transition:border-color .15s,box-shadow .15s;
  -webkit-appearance:none;appearance:none;
}
.cm-ctrl:focus { border-color:#4f46e5;background:#fff;box-shadow:0 0 0 3px rgba(79,70,229,.1); }
.cm-ctrl.is-err { border-color:#dc2626;box-shadow:0 0 0 3px rgba(220,38,38,.08); }
select.cm-ctrl {
  background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:right 10px center;background-size:14px;
  padding-right:32px;cursor:pointer;
}
.cm-hint { font-size:.71rem;color:#94a3b8;line-height:1.4; }
.cm-err  { font-size:.72rem;color:#dc2626;font-weight:600; }
.cm-grid-2 { display:grid;grid-template-columns:1fr 1fr;gap:.85rem; }

/* Prereq block hidden until toggled */
.cm-prereq-block { display:none;margin-top:.85rem;padding:.9rem 1rem;background:#fffbeb;border:1px solid #fde68a;border-radius:10px; }
.cm-prereq-block.visible { display:block; }
.cm-prereq-toggle { display:flex;align-items:center;gap:8px;cursor:pointer;user-select:none; }
.cm-prereq-toggle input[type=checkbox] { width:16px;height:16px;accent-color:#f59e0b;cursor:pointer; }
.cm-prereq-toggle span { font-size:.82rem;font-weight:700;color:#374151; }
</style>
@endpush

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Add Curriculum Mapping</h1>
      <p class="enc-page__subtitle">Assign a subject to a grade level and optionally configure a prerequisite.</p>
    </div>
    <a href="{{ route('admin.curriculum-mappings.index') }}" class="enc-btn enc-btn--ghost">← Back</a>
  </div>
</div>

<form action="{{ route('admin.curriculum-mappings.store') }}" method="POST">
@csrf
<div class="cm-form-card">

  {{-- Section 1: Assignment --}}
  <div class="cm-form-section">
    <div class="cm-section-title"><span>1</span> Assignment</div>

    <div class="cm-field">
      <label class="cm-label">Academic Year <span class="req">*</span></label>
      <select name="academic_year_id" class="cm-ctrl {{ $errors->has('academic_year_id') ? 'is-err' : '' }}" required>
        <option value="">Select Academic Year</option>
        @foreach($academicYears as $year)
        <option value="{{ $year->id }}" {{ old('academic_year_id', $academicYear?->id) == $year->id ? 'selected' : '' }}>{{ $year->year_label }}</option>
        @endforeach
      </select>
      @error('academic_year_id')<div class="cm-err">{{ $message }}</div>@enderror
    </div>

    <div class="cm-grid-2">
      <div class="cm-field">
        <label class="cm-label">Grade Level <span class="req">*</span></label>
        <select name="grade_level" class="cm-ctrl {{ $errors->has('grade_level') ? 'is-err' : '' }}" required>
          <option value="">Select Grade Level</option>
          @foreach($standardGradeLevels as $level)
          <option value="{{ $level }}" {{ old('grade_level', $gradeLevel) === $level ? 'selected' : '' }}>{{ $level }}</option>
          @endforeach
        </select>
        @error('grade_level')<div class="cm-err">{{ $message }}</div>@enderror
      </div>

      <div class="cm-field">
        <label class="cm-label">Subject Type <span class="req">*</span></label>
        <select name="is_required" class="cm-ctrl {{ $errors->has('is_required') ? 'is-err' : '' }}" required>
          <option value="">Select Type</option>
          <option value="1" {{ old('is_required') === '1' ? 'selected' : '' }}>Required</option>
          <option value="0" {{ old('is_required') === '0' ? 'selected' : '' }}>Elective</option>
        </select>
        @error('is_required')<div class="cm-err">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="cm-field">
      <label class="cm-label">Subject <span class="req">*</span></label>
      <select name="subject_id" class="cm-ctrl {{ $errors->has('subject_id') ? 'is-err' : '' }}" required>
        <option value="">Select Subject</option>
        @foreach($subjects as $subject)
        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
          {{ $subject->subject_code }} – {{ $subject->subject_name }}
        </option>
        @endforeach
      </select>
      @error('subject_id')<div class="cm-err">{{ $message }}</div>@enderror
    </div>
  </div>

  {{-- Section 2: Prerequisite --}}
  <div class="cm-form-section">
    <div class="cm-section-title"><span>2</span> Prerequisite Configuration</div>

    <label class="cm-prereq-toggle">
      <input type="checkbox" id="prereq-toggle"
             {{ old('prerequisite_subject_id') ? 'checked' : '' }}
             onchange="document.getElementById('prereq-block').classList.toggle('visible', this.checked)">
      <span>This subject has a prerequisite</span>
    </label>

    <div id="prereq-block" class="cm-prereq-block {{ old('prerequisite_subject_id') ? 'visible' : '' }}">
      <div class="cm-field">
        <label class="cm-label">Prerequisite Subject <span class="opt">(student must have passed this first)</span></label>
        <select name="prerequisite_subject_id" id="prereq-subject" class="cm-ctrl">
          <option value="">— None (no prerequisite) —</option>
          @foreach($subjects as $subject)
          <option value="{{ $subject->id }}" {{ old('prerequisite_subject_id') == $subject->id ? 'selected' : '' }}>
            {{ $subject->subject_code }} – {{ $subject->subject_name }}
          </option>
          @endforeach
        </select>
        @error('prerequisite_subject_id')<div class="cm-err">{{ $message }}</div>@enderror
      </div>

      <div class="cm-field" style="max-width:200px;">
        <label class="cm-label">Minimum Passing Grade <span class="opt">(default: 75)</span></label>
        <input type="number" name="prerequisite_min_grade" id="prereq-grade"
               value="{{ old('prerequisite_min_grade', 75) }}"
               min="0" max="100" step="0.5" class="cm-ctrl">
        <div class="cm-hint">Student must have achieved at least this grade in the prerequisite subject.</div>
        @error('prerequisite_min_grade')<div class="cm-err">{{ $message }}</div>@enderror
      </div>
    </div>
  </div>

  {{-- Section 3: Settings --}}
  <div class="cm-form-section">
    <div class="cm-section-title"><span>3</span> Settings</div>
    <div class="cm-grid-2">
      <div class="cm-field">
        <label class="cm-label">Sequence Order <span class="opt">(display order)</span></label>
        <input type="number" name="sequence_order" value="{{ old('sequence_order', 0) }}"
               min="0" class="cm-ctrl" placeholder="0">
        <div class="cm-hint">Lower numbers appear first within the same grade.</div>
        @error('sequence_order')<div class="cm-err">{{ $message }}</div>@enderror
      </div>
      <div class="cm-field">
        <label class="cm-label">Status <span class="req">*</span></label>
        <select name="status" class="cm-ctrl {{ $errors->has('status') ? 'is-err' : '' }}" required>
          <option value="active"   {{ old('status','active') === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ old('status','active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')<div class="cm-err">{{ $message }}</div>@enderror
      </div>
    </div>
  </div>

  {{-- Actions --}}
  <div style="padding:1.1rem 1.5rem;background:#f8fafc;display:flex;gap:.65rem;">
    <button type="submit" class="enc-btn enc-btn--primary">Create Mapping</button>
    <a href="{{ route('admin.curriculum-mappings.index') }}" class="enc-btn enc-btn--ghost">Cancel</a>
  </div>

</div>
</form>

@push('scripts')
<script>
// Keep prereq subject select in sync when toggle is unchecked
document.getElementById('prereq-toggle')?.addEventListener('change', function () {
  if (!this.checked) {
    document.getElementById('prereq-subject').value = '';
    document.getElementById('prereq-grade').value  = 75;
  }
});
</script>
@endpush

@endsection
