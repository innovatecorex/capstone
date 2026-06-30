@extends('layouts.app')
@section('title', 'Sections')
@section('breadcrumb', 'Sections')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Sections</h1>
      <p class="enc-page__subtitle">Manage class sections (e.g. Grade 7 — St. Joseph). Sections are scoped per academic year.</p>
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

<div style="margin-bottom:24px;">

  <div class="enc-card">
    <div class="enc-card__header"><div class="enc-card__title">+ Add Section</div></div>
    <div class="enc-card__body" style="padding:20px;">
      <form method="POST" action="{{ route('admin.sections.store') }}" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        @csrf
        <input type="hidden" name="academic_year_id" value="{{ $yearId }}">

        <select name="grade_level" id="grade_level" required onchange="loadSectionNames()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
          <option value="">— Grade Level —</option>
          @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $gl)
            <option value="{{ $gl }}" {{ old('grade_level') === $gl ? 'selected' : '' }}>{{ $gl }}</option>
          @endforeach
        </select>

        <div>
          <select name="section_name" id="section_name_select" required onchange="toggleCustomName()" style="width:100%;padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
            <option value="">— Select grade level first —</option>
          </select>
          <input type="text" name="section_name_custom" id="section_name_custom" placeholder="Type section name" maxlength="100" style="display:none;width:100%;margin-top:8px;padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
        </div>

        <select name="adviser_id" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
          <option value="">— Adviser (optional) —</option>
          @foreach($faculty as $f)
            <option value="{{ $f->id }}">{{ $f->last_name }}, {{ $f->first_name }}</option>
          @endforeach
        </select>

        <input type="number" name="capacity" placeholder="Capacity" required min="1" max="200" value="40" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">

        <select name="status" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>

        <button type="submit" {{ !$yearId ? 'disabled' : '' }} style="padding:.5rem 1rem;border:none;border-radius:8px;background:{{ $yearId ? '#1d4ed8' : '#94a3b8' }};color:#fff;font-size:.875rem;font-weight:700;cursor:{{ $yearId ? 'pointer' : 'not-allowed' }};">Add Section</button>

        @if(!$yearId)
          <p style="font-size:.75rem;color:#92400e;margin:0;grid-column:1/-1;">Pick an academic year first.</p>
        @endif
      </form>
    </div>
  </div>
</div>

{{-- ── Filter Bar ──────────────────────────────────────────────────────── --}}
<div class="enc-card" style="margin-bottom:20px;">
  <div class="enc-card__body" style="padding:16px 20px;">
    <form method="GET" action="{{ route('admin.sections.index') }}"
          style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;">

      {{-- Search --}}
      <div style="display:flex;flex-direction:column;gap:4px;min-width:200px;flex:1;">
        <label style="font-size:.68rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Search</label>
        <div style="position:relative;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
               style="position:absolute;left:9px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
          </svg>
          <input type="text" name="search" value="{{ $search }}"
                 placeholder="Section name…"
                 style="width:100%;padding:7px 10px 7px 30px;border:1px solid #cbd5e1;border-radius:8px;font-size:.875rem;background:#fff;">
        </div>
      </div>

      {{-- Academic Year --}}
      <div style="display:flex;flex-direction:column;gap:4px;min-width:170px;">
        <label style="font-size:.68rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Academic Year</label>
        <select name="academic_year_id"
                style="padding:7px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:.875rem;background:#fff;">
          <option value="">— All Years —</option>
          @foreach($academicYears as $yr)
            <option value="{{ $yr->id }}" {{ $yearId == $yr->id ? 'selected' : '' }}>
              {{ $yr->year_label }} @if($yr->status === 'active')(Active)@endif
            </option>
          @endforeach
        </select>
      </div>

      {{-- Grade Level --}}
      <div style="display:flex;flex-direction:column;gap:4px;min-width:130px;">
        <label style="font-size:.68rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Grade</label>
        <select name="grade"
                style="padding:7px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:.875rem;background:#fff;">
          <option value="">All Grades</option>
          @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $gl)
            <option value="{{ $gl }}" {{ $grade === $gl ? 'selected' : '' }}>{{ $gl }}</option>
          @endforeach
        </select>
      </div>

      {{-- Status --}}
      <div style="display:flex;flex-direction:column;gap:4px;min-width:120px;">
        <label style="font-size:.68rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</label>
        <select name="status"
                style="padding:7px 10px;border:1px solid #cbd5e1;border-radius:8px;font-size:.875rem;background:#fff;">
          <option value="">All</option>
          <option value="active"   {{ $status === 'active'   ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>

      {{-- Buttons --}}
      <div style="display:flex;gap:8px;align-items:flex-end;">
        <button type="submit"
                style="padding:.45rem 1.1rem;border:none;border-radius:8px;background:#1d4ed8;color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;height:36px;">
          Filter
        </button>
        @if($search || $grade || $status || $yearId)
        <a href="{{ route('admin.sections.index') }}"
           style="display:inline-flex;align-items:center;height:36px;padding:0 .85rem;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;color:#64748b;font-size:.82rem;font-weight:600;text-decoration:none;">
          Clear
        </a>
        @endif
      </div>

    </form>
  </div>
</div>

<div class="enc-card">
  <div class="enc-card__header">
    <div class="enc-card__title">Sections List</div>
    <span class="enc-card__meta">
      {{ $sections->total() }} section(s)
      @if($grade) · {{ $grade }} @endif
      @if($status) · {{ ucfirst($status) }} @endif
      @if($search) · "{{ $search }}" @endif
    </span>
  </div>
  <div class="enc-card__body" style="padding:0;">
    <div style="overflow-x:auto;">
      <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
          <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Grade</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Section</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Adviser</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Capacity</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Year</th>
            <th style="padding:12px 14px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
            <th style="padding:12px 14px;text-align:right;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sections as $s)
          <tr style="border-bottom:1px solid #f1f5f9;">
            <td style="padding:12px 14px;color:#0f172a;font-weight:600;">{{ $s->grade_level }}</td>
            <td style="padding:12px 14px;color:#0f172a;">{{ $s->section_name }}</td>
            <td style="padding:12px 14px;color:#64748b;">
              @if($s->adviser)
                {{ $s->adviser->last_name }}, {{ $s->adviser->first_name }}
              @else
                <span style="color:#94a3b8;font-style:italic;">— None —</span>
              @endif
            </td>
            <td style="padding:12px 14px;color:#64748b;">{{ $s->capacity }}</td>
            <td style="padding:12px 14px;color:#64748b;">{{ $s->academicYear?->year_label ?? '—' }}</td>
            <td style="padding:12px 14px;">
              @php $c = $s->status === 'active' ? ['#166534','#86efac','#f0fdf4'] : ['#475569','#cbd5e1','#f8fafc']; @endphp
              <span style="display:inline-block;padding:.2rem .55rem;border-radius:6px;font-size:.7rem;font-weight:700;text-transform:uppercase;color:{{ $c[0] }};background:{{ $c[2] }};border:1px solid {{ $c[1] }};">{{ $s->status }}</span>
            </td>
            <td style="padding:12px 14px;text-align:right;white-space:nowrap;">
              @php
                $editData = [
                  'id'           => $s->id,
                  'grade_level'  => $s->grade_level,
                  'section_name' => $s->section_name,
                  'adviser_id'   => $s->adviser_id,
                  'capacity'     => $s->capacity,
                  'status'       => $s->status,
                  'url'          => route('admin.sections.update', $s),
                ];
              @endphp
              <button type="button"
                onclick="openEditSection({{ Js::from($editData) }})"
                style="background:none;border:none;color:#1d4ed8;font-size:.82rem;font-weight:600;cursor:pointer;margin-right:14px;padding:0;">Edit</button>
              <a href="{{ route('admin.sections.roster', $s) }}" style="color:#1d4ed8;font-size:.82rem;font-weight:600;text-decoration:none;margin-right:14px;">Manage Students</a>
              <form action="{{ route('admin.sections.destroy', $s) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete section \'{{ $s->grade_level }} — {{ $s->section_name }}\'?');">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#dc2626;font-size:.82rem;font-weight:600;cursor:pointer;">Delete</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" style="padding:40px;text-align:center;color:#94a3b8;">No sections in this academic year. Add one above.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<div style="margin-top:16px;">{{ $sections->links() }}</div>

@push('scripts')
<script>
// Suggested section names per grade level (mirrors SectionSeeder).
const SECTION_NAMES = {
  'Grade 7':  ['St. Joseph', 'St. Mary', 'St. Michael', 'St. Peter', 'St. Paul'],
  'Grade 8':  ['Sampaguita', 'Gumamela', 'Rosal', 'Camia', 'Ilang-Ilang'],
  'Grade 9':  ['Emerald', 'Sapphire', 'Ruby', 'Diamond', 'Topaz'],
  'Grade 10': ['Newton', 'Einstein', 'Galileo', 'Darwin', 'Tesla'],
  'Grade 11': ['Mabini', 'Rizal', 'Bonifacio', 'Luna', 'Aguinaldo'],
  'Grade 12': ['Aristotle', 'Plato', 'Socrates', 'Descartes', 'Kant'],
};

function loadSectionNames() {
  const grade = document.getElementById('grade_level').value;
  const sel   = document.getElementById('section_name_select');
  const custom = document.getElementById('section_name_custom');

  custom.style.display = 'none';
  custom.removeAttribute('required');
  sel.setAttribute('name', 'section_name');

  if (!grade) {
    sel.innerHTML = '<option value="">— Select grade level first —</option>';
    return;
  }

  const names = SECTION_NAMES[grade] || [];
  let html = '<option value="">— Select Section —</option>';
  names.forEach(function (n) {
    html += '<option value="' + n + '">' + n + '</option>';
  });
  html += '<option value="__custom__">Other (type a name)…</option>';
  sel.innerHTML = html;
}

function toggleCustomName() {
  const sel    = document.getElementById('section_name_select');
  const custom = document.getElementById('section_name_custom');

  if (sel.value === '__custom__') {
    // Hand the field name over to the text input so it gets submitted.
    sel.removeAttribute('name');
    custom.setAttribute('name', 'section_name');
    custom.setAttribute('required', 'required');
    custom.style.display = 'block';
    custom.focus();
  } else {
    sel.setAttribute('name', 'section_name');
    custom.removeAttribute('name');
    custom.removeAttribute('required');
    custom.style.display = 'none';
  }
}

// Populate on load if a grade level is already chosen (e.g. after a validation error).
document.addEventListener('DOMContentLoaded', loadSectionNames);
</script>
@endpush

{{-- ── Edit Section Modal ───────────────────────────────────────────── --}}
<div id="editSectionOverlay" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:1000;overflow-y:auto;padding:20px;">
  <div style="background:#fff;border-radius:14px;max-width:460px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.25);margin:40px auto;max-height:calc(100vh - 80px);overflow-y:auto;">
    <div style="padding:18px 22px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
      <h3 style="margin:0;font-size:1.05rem;font-weight:800;color:#0f172a;">Edit Section</h3>
      <button type="button" onclick="closeEditSection()" style="background:none;border:none;font-size:1.4rem;line-height:1;color:#94a3b8;cursor:pointer;">&times;</button>
    </div>
    <form id="editSectionForm" method="POST" style="padding:22px;display:flex;flex-direction:column;gap:14px;">
      @csrf
      @method('PUT')

      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;margin-bottom:5px;">Grade Level</label>
        <select name="grade_level" id="edit_grade_level" required style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
          @foreach(['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $gl)
            <option value="{{ $gl }}">{{ $gl }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;margin-bottom:5px;">Section Name</label>
        <input type="text" name="section_name" id="edit_section_name" required maxlength="100" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
      </div>

      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;margin-bottom:5px;">Adviser</label>
        <select name="adviser_id" id="edit_adviser_id" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
          <option value="">— None —</option>
          @foreach($faculty as $f)
            <option value="{{ $f->id }}">{{ $f->last_name }}, {{ $f->first_name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;margin-bottom:5px;">Capacity</label>
        <input type="number" name="capacity" id="edit_capacity" required min="1" max="200" style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
      </div>

      <div>
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;margin-bottom:5px;">Status</label>
        <select name="status" id="edit_status" required style="width:100%;padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:.88rem;">
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>

      <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:4px;">
        <button type="button" onclick="closeEditSection()" style="padding:.5rem 1rem;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#475569;font-size:.85rem;font-weight:700;cursor:pointer;">Cancel</button>
        <button type="submit" style="padding:.5rem 1.1rem;border:none;border-radius:8px;background:#1d4ed8;color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditSection(data) {
  document.getElementById('editSectionForm').action = data.url;
  document.getElementById('edit_grade_level').value  = data.grade_level;
  document.getElementById('edit_section_name').value = data.section_name;
  document.getElementById('edit_adviser_id').value   = data.adviser_id ?? '';
  document.getElementById('edit_capacity').value     = data.capacity;
  document.getElementById('edit_status').value       = data.status;
  document.getElementById('editSectionOverlay').style.display = 'block';
}
function closeEditSection() {
  document.getElementById('editSectionOverlay').style.display = 'none';
}
document.getElementById('editSectionOverlay').addEventListener('click', function(e){
  if (e.target === this) closeEditSection();
});
</script>

@endsection
