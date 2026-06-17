@extends('layouts.admin')

@section('title', 'Curriculum & Prerequisites')
@section('breadcrumb', 'Curriculum & Prerequisites')

@push('head')
<style>
/* ── Stats ──────────────────────────────────────────────────── */
.cm-stats {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: .75rem;
  margin-bottom: 1.25rem;
}
@media (max-width: 900px) { .cm-stats { grid-template-columns: repeat(3,1fr); } }

.cm-stat {
  background: #fff; border: 1px solid rgba(15,23,42,.08); border-radius: 12px;
  padding: .85rem 1rem; display: flex; align-items: center; gap: .75rem;
}
.cm-stat-icon { width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.cm-stat-icon svg { width:16px;height:16px; }
.cm-stat-icon--indigo { background:#eef2ff;color:#4338ca; }
.cm-stat-icon--green  { background:#dcfce7;color:#16a34a; }
.cm-stat-icon--amber  { background:#fef3c7;color:#b45309; }
.cm-stat-icon--blue   { background:#dbeafe;color:#1d4ed8; }
.cm-stat-icon--gray   { background:#f1f5f9;color:#64748b; }
.cm-stat-val   { font-size:1.3rem;font-weight:900;color:#0f172a;line-height:1; }
.cm-stat-label { font-size:.66rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-top:2px; }

/* ── Filter bar ─────────────────────────────────────────────── */
.cm-filters {
  background: #fff; border: 1px solid rgba(15,23,42,.08); border-radius: 12px;
  padding: 1rem 1.1rem; margin-bottom: 1.1rem;
  display: flex; gap: .65rem; flex-wrap: wrap; align-items: flex-end;
}
.cm-filter-group { display:flex;flex-direction:column;gap:.2rem; }
.cm-filter-label { font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em; }
.cm-filter-ctrl {
  padding:.42rem .8rem; border:1px solid rgba(15,23,42,.13); border-radius:8px;
  font-size:.85rem; background:#fff; color:#0f172a; font-family:inherit; height:34px; outline:none;
}
.cm-filter-ctrl:focus { border-color:#4f46e5; }
.cm-toggle-pill {
  display:inline-flex; align-items:center; gap:5px;
  padding:.35rem .85rem; border:1.5px solid rgba(15,23,42,.13); border-radius:999px;
  font-size:.78rem; font-weight:700; color:#64748b; cursor:pointer; text-decoration:none;
  background:#f8fafc; transition:all .15s; height:34px;
}
.cm-toggle-pill:hover { border-color:#4f46e5; color:#4338ca; background:#eef2ff; }
.cm-toggle-pill.active { border-color:#4f46e5; color:#4338ca; background:#eef2ff; }
.cm-toggle-pill svg { width:13px;height:13px; }

/* ── Grade-level section heading ─────────────────────────────── */
.cm-grade-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: .6rem 1rem; background: #f8fafc; border-bottom: 1px solid rgba(15,23,42,.07);
}
.cm-grade-label { font-size:.78rem; font-weight:800; color:#374151; }
.cm-grade-badge {
  display:inline-flex; align-items:center; gap:4px;
  font-size:.67rem; font-weight:700; padding:.18rem .55rem;
  border-radius:999px; white-space:nowrap;
}
.cm-grade-badge--blue   { background:#dbeafe;color:#1e40af; }
.cm-grade-badge--amber  { background:#fef3c7;color:#92400e; }
.cm-grade-badge--indigo { background:#e0e7ff;color:#3730a3; }

/* ── Table ──────────────────────────────────────────────────── */
.cm-th { padding:9px 14px;font-size:.68rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid rgba(15,23,42,.07);white-space:nowrap;text-align:left; }
.cm-td { padding:10px 14px;border-bottom:1px solid rgba(15,23,42,.04);vertical-align:middle; }
.cm-row:hover td { background:rgba(15,23,42,.012); }

/* Prerequisite chain chip */
.cm-prereq-chip {
  display:inline-flex;align-items:center;gap:4px;
  padding:.22rem .65rem;border-radius:6px;font-size:.73rem;font-weight:700;
}
.cm-prereq-chip--has  { background:#fef3c7;color:#92400e;border:1px solid #fde68a; }
.cm-prereq-chip--none { background:#f1f5f9;color:#94a3b8; }
.cm-arrow { color:#94a3b8;font-size:.8rem; }

/* Status pill */
.cm-pill {
  display:inline-block;padding:.18rem .55rem;border-radius:999px;
  font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;
}
.cm-pill--active   { background:#dcfce7;color:#166534; }
.cm-pill--inactive { background:#f1f5f9;color:#64748b; }
.cm-pill--required { background:#dbeafe;color:#1e40af; }
.cm-pill--elective { background:#ede9fe;color:#5b21b6; }

/* Bulk toolbar */
.cm-bulk-bar {
  display:none; align-items:center; gap:.75rem;
  background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px;
  padding:.6rem 1rem; margin-bottom:.85rem;
}
.cm-bulk-bar.visible { display:flex; }

/* Copy modal */
.cm-modal-backdrop {
  display:none; position:fixed; inset:0; background:rgba(15,23,42,.45);
  z-index:1000; align-items:center; justify-content:center;
}
.cm-modal-backdrop.open { display:flex; }
.cm-modal {
  background:#fff; border-radius:16px; padding:1.75rem;
  width:100%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,.2);
}
.cm-modal-title { font-size:1rem;font-weight:800;color:#0f172a;margin-bottom:.4rem; }
.cm-modal-sub   { font-size:.82rem;color:#64748b;margin-bottom:1.1rem;line-height:1.5; }
</style>
@endpush

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Curriculum &amp; Prerequisites</h1>
      <p class="enc-page__subtitle">
        Define which subjects are required per grade level and configure prerequisite chains.
        @if($selectedYear) · <strong>{{ $selectedYear->year_label }}</strong> @endif
      </p>
    </div>
    <div style="display:flex;gap:.6rem;">
      <button onclick="document.getElementById('copyModal').classList.add('open')"
              class="enc-btn enc-btn--ghost" style="font-size:.82rem;">
        Copy from Year
      </button>
      <a href="{{ route('admin.curriculum-mappings.create', $academicYearId ? ['academic_year_id'=>$academicYearId] : []) }}"
         class="enc-btn enc-btn--primary" style="font-size:.82rem;">
        + Add Mapping
      </a>
    </div>
  </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.86rem;color:#166534;margin-bottom:1rem;">{!! session('success') !!}</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.75rem 1rem;font-size:.86rem;color:#991b1b;margin-bottom:1rem;">{{ session('error') }}</div>
@endif

{{-- Stats --}}
<div class="cm-stats">
  <div class="cm-stat">
    <div class="cm-stat-icon cm-stat-icon--indigo">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/></svg>
    </div>
    <div><div class="cm-stat-val">{{ $stats['total'] }}</div><div class="cm-stat-label">Total</div></div>
  </div>
  <div class="cm-stat">
    <div class="cm-stat-icon cm-stat-icon--green">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <div><div class="cm-stat-val">{{ $stats['active'] }}</div><div class="cm-stat-label">Active</div></div>
  </div>
  <div class="cm-stat">
    <div class="cm-stat-icon cm-stat-icon--amber">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
    </div>
    <div><div class="cm-stat-val">{{ $stats['with_prerequisite'] }}</div><div class="cm-stat-label">With Prereqs</div></div>
  </div>
  <div class="cm-stat">
    <div class="cm-stat-icon cm-stat-icon--blue">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
    </div>
    <div><div class="cm-stat-val">{{ $stats['required'] }}</div><div class="cm-stat-label">Required</div></div>
  </div>
  <div class="cm-stat">
    <div class="cm-stat-icon cm-stat-icon--gray">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
    </div>
    <div><div class="cm-stat-val">{{ $stats['elective'] }}</div><div class="cm-stat-label">Elective</div></div>
  </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.curriculum-mappings.index') }}" class="cm-filters">
  <div class="cm-filter-group">
    <span class="cm-filter-label">Academic Year</span>
    <select name="academic_year_id" class="cm-filter-ctrl" style="width:155px;">
      <option value="">All Years</option>
      @foreach($academicYears as $year)
      <option value="{{ $year->id }}" {{ $academicYearId == $year->id ? 'selected' : '' }}>{{ $year->year_label }}</option>
      @endforeach
    </select>
  </div>
  <div class="cm-filter-group">
    <span class="cm-filter-label">Grade Level</span>
    <select name="grade_level" class="cm-filter-ctrl" style="width:140px;">
      <option value="">All Grades</option>
      @foreach($gradeLevels as $lvl)
      <option value="{{ $lvl }}" {{ $gradeLevel === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
      @endforeach
    </select>
  </div>
  <div class="cm-filter-group">
    <span class="cm-filter-label">Status</span>
    <select name="status" class="cm-filter-ctrl" style="width:120px;">
      <option value="">All</option>
      <option value="active"   {{ $status === 'active'   ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
  </div>
  <div class="cm-filter-group" style="justify-content:flex-end;">
    <span class="cm-filter-label">&nbsp;</span>
    <a href="{{ route('admin.curriculum-mappings.index', array_merge(request()->except('prereq_only','page'), $prereqOnly ? [] : ['prereq_only'=>1])) }}"
       class="cm-toggle-pill {{ $prereqOnly ? 'active' : '' }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
      With Prereqs Only
    </a>
  </div>
  <div style="display:flex;gap:.5rem;align-items:flex-end;">
    <button type="submit" class="enc-btn enc-btn--primary" style="height:34px;font-size:.82rem;">Filter</button>
    @if(request()->hasAny(['academic_year_id','grade_level','status','prereq_only']))
    <a href="{{ route('admin.curriculum-mappings.index') }}" class="enc-btn enc-btn--ghost" style="height:34px;font-size:.82rem;">Clear</a>
    @endif
  </div>
</form>

{{-- Bulk action bar --}}
<div id="bulk-bar" class="cm-bulk-bar">
  <span id="bulk-count" style="font-size:.84rem;font-weight:700;color:#1e40af;">0 selected</span>
  <form id="bulk-form" method="POST" action="{{ route('admin.curriculum-mappings.bulk-action') }}" style="display:flex;gap:.5rem;align-items:center;">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input">
    <div id="bulk-ids-container"></div>
    <select id="bulk-action-select" style="padding:.3rem .7rem;border:1px solid #93c5fd;border-radius:6px;font-size:.82rem;background:#fff;">
      <option value="">-- Bulk Action --</option>
      <option value="activate">Activate</option>
      <option value="deactivate">Deactivate</option>
      <option value="delete">Delete</option>
    </select>
    <button type="button" onclick="applyBulk()"
            style="padding:.3rem .85rem;background:#1d4ed8;color:#fff;border:none;border-radius:6px;font-size:.82rem;font-weight:700;cursor:pointer;">
      Apply
    </button>
  </form>
  <button onclick="clearSelection()" style="padding:.3rem .7rem;background:transparent;border:1px solid #93c5fd;border-radius:6px;font-size:.78rem;color:#1e40af;cursor:pointer;margin-left:auto;">
    Deselect All
  </button>
</div>

{{-- Mappings grouped by grade level --}}
@if($grouped->isEmpty())
<div class="enc-card" style="padding:3.5rem;text-align:center;">
  <div style="font-size:2rem;margin-bottom:.5rem;">📚</div>
  <div style="font-weight:700;color:#374151;margin-bottom:.3rem;">No curriculum mappings found</div>
  <div style="font-size:.84rem;color:#94a3b8;margin-bottom:1.25rem;">
    @if($prereqOnly) No mappings with prerequisites match the current filters. @else Add your first mapping to get started. @endif
  </div>
  <a href="{{ route('admin.curriculum-mappings.create') }}" class="enc-btn enc-btn--primary" style="font-size:.84rem;">+ Add Mapping</a>
</div>
@else

@foreach($grouped as $grade => $items)
@php
  $withPrereq   = $items->whereNotNull('prerequisite_subject_id')->count();
  $activeCount  = $items->where('status','active')->count();
  $requiredCount= $items->where('is_required',true)->count();
@endphp
<div class="enc-card" style="padding:0;overflow:hidden;margin-bottom:1rem;">
  {{-- Grade heading --}}
  <div class="cm-grade-head">
    <div style="display:flex;align-items:center;gap:.65rem;">
      <div style="width:8px;height:8px;border-radius:50%;background:#4f46e5;flex-shrink:0;"></div>
      <span class="cm-grade-label">{{ $grade }}</span>
      <span class="cm-grade-badge cm-grade-badge--blue">{{ $items->count() }} subject{{ $items->count()!==1?'s':'' }}</span>
      @if($withPrereq > 0)
      <span class="cm-grade-badge cm-grade-badge--amber">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:10px;height:10px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
        {{ $withPrereq }} with prereq
      </span>
      @endif
      @if($requiredCount > 0)
      <span class="cm-grade-badge cm-grade-badge--indigo">{{ $requiredCount }} required</span>
      @endif
    </div>
    <div style="font-size:.72rem;color:#94a3b8;">{{ $activeCount }}/{{ $items->count() }} active</div>
  </div>

  {{-- Table --}}
  <div style="overflow-x:auto;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr>
          <th class="cm-th" style="width:36px;">
            <input type="checkbox" class="grade-select-all" data-grade="{{ $grade }}"
                   style="cursor:pointer;accent-color:#4f46e5;">
          </th>
          <th class="cm-th">#</th>
          <th class="cm-th">Subject</th>
          <th class="cm-th">Type</th>
          <th class="cm-th">Prerequisite</th>
          <th class="cm-th">Min Grade</th>
          <th class="cm-th">Status</th>
          <th class="cm-th"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $mapping)
        <tr class="cm-row">
          <td class="cm-td">
            <input type="checkbox" class="row-checkbox" value="{{ $mapping->id }}"
                   style="cursor:pointer;accent-color:#4f46e5;">
          </td>
          <td class="cm-td" style="color:#94a3b8;font-size:.78rem;font-weight:600;">{{ $mapping->sequence_order ?: '—' }}</td>
          <td class="cm-td">
            <div style="font-weight:700;color:#0f172a;font-size:.87rem;">{{ $mapping->subject?->subject_name ?? '—' }}</div>
            <div style="font-size:.73rem;color:#94a3b8;font-family:monospace;">{{ $mapping->subject?->subject_code }}</div>
          </td>
          <td class="cm-td">
            <span class="cm-pill {{ $mapping->is_required ? 'cm-pill--required' : 'cm-pill--elective' }}">
              {{ $mapping->is_required ? 'Required' : 'Elective' }}
            </span>
          </td>
          <td class="cm-td">
            @if($mapping->prerequisiteSubject)
            <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
              <span class="cm-prereq-chip cm-prereq-chip--has">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:10px;height:10px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                {{ $mapping->prerequisiteSubject->subject_code }}
              </span>
              <span style="font-size:.72rem;color:#64748b;max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                    title="{{ $mapping->prerequisiteSubject->subject_name }}">
                {{ $mapping->prerequisiteSubject->subject_name }}
              </span>
            </div>
            @else
            <span class="cm-prereq-chip cm-prereq-chip--none">None</span>
            @endif
          </td>
          <td class="cm-td" style="font-size:.82rem;font-weight:700;color:#374151;text-align:center;">
            @if($mapping->prerequisiteSubject)
              <span style="background:#fef9c3;color:#92400e;padding:.15rem .5rem;border-radius:6px;font-size:.78rem;">
                ≥ {{ $mapping->prerequisite_min_grade ?? 75 }}
              </span>
            @else
              <span style="color:#cbd5e1;">—</span>
            @endif
          </td>
          <td class="cm-td">
            <span class="cm-pill {{ $mapping->status === 'active' ? 'cm-pill--active' : 'cm-pill--inactive' }}">
              {{ ucfirst($mapping->status) }}
            </span>
          </td>
          <td class="cm-td" style="text-align:right;white-space:nowrap;">
            <a href="{{ route('admin.curriculum-mappings.edit', $mapping) }}"
               style="font-size:.82rem;color:#4f46e5;font-weight:700;text-decoration:none;margin-right:.75rem;">Edit</a>
            <form method="POST" action="{{ route('admin.curriculum-mappings.destroy', $mapping) }}" style="display:inline;"
                  onsubmit="return confirm('Delete this mapping? This cannot be undone.')">
              @csrf @method('DELETE')
              <button type="submit" style="font-size:.82rem;color:#dc2626;font-weight:700;background:none;border:none;cursor:pointer;padding:0;">Delete</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endforeach

@endif

{{-- Copy-from-year modal --}}
<div id="copyModal" class="cm-modal-backdrop" onclick="if(event.target===this)this.classList.remove('open')">
  <div class="cm-modal">
    <div class="cm-modal-title">Copy Curriculum from Another Year</div>
    <div class="cm-modal-sub">Copies all subject mappings and prerequisite links from the selected source year to the target year. Existing entries in the target are skipped.</div>
    <form method="POST" action="{{ route('admin.curriculum-mappings.copy-from-year') }}" style="display:grid;gap:.75rem;">
      @csrf
      <div>
        <label style="display:block;font-size:.75rem;font-weight:700;color:#374151;margin-bottom:.3rem;">Source Year <span style="color:#dc2626">*</span></label>
        <select name="source_year_id" required style="width:100%;padding:.5rem .85rem;border:1px solid #d1d5db;border-radius:8px;font-size:.87rem;background:#fff;">
          <option value="">Select source year</option>
          @foreach($academicYears as $year)
          <option value="{{ $year->id }}">{{ $year->year_label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label style="display:block;font-size:.75rem;font-weight:700;color:#374151;margin-bottom:.3rem;">Target Year <span style="color:#dc2626">*</span></label>
        <select name="target_year_id" required style="width:100%;padding:.5rem .85rem;border:1px solid #d1d5db;border-radius:8px;font-size:.87rem;background:#fff;">
          <option value="">Select target year</option>
          @foreach($academicYears as $year)
          <option value="{{ $year->id }}" {{ $academicYearId == $year->id ? 'selected' : '' }}>{{ $year->year_label }}</option>
          @endforeach
        </select>
      </div>
      <div style="display:flex;gap:.65rem;margin-top:.25rem;">
        <button type="submit" class="enc-btn enc-btn--primary" style="flex:1;">Copy Mappings</button>
        <button type="button" onclick="document.getElementById('copyModal').classList.remove('open')"
                class="enc-btn enc-btn--ghost" style="flex:1;">Cancel</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
(function () {
  const bar      = document.getElementById('bulk-bar');
  const countEl  = document.getElementById('bulk-count');

  function getChecked() {
    return Array.from(document.querySelectorAll('.row-checkbox:checked'));
  }

  function syncBar() {
    const checked = getChecked();
    bar.classList.toggle('visible', checked.length > 0);
    if (checked.length > 0) countEl.textContent = checked.length + ' selected';
  }

  // Row checkboxes
  document.querySelectorAll('.row-checkbox').forEach(cb => cb.addEventListener('change', syncBar));

  // Per-grade "select all" checkboxes
  document.querySelectorAll('.grade-select-all').forEach(sa => {
    sa.addEventListener('change', function () {
      const grade = this.dataset.grade;
      document.querySelectorAll('.row-checkbox').forEach(cb => {
        if (cb.closest('table').closest('.enc-card').querySelector('.cm-grade-label')?.textContent.trim() === grade) {
          cb.checked = sa.checked;
        }
      });
      syncBar();
    });
  });

  window.clearSelection = function () {
    document.querySelectorAll('.row-checkbox,.grade-select-all').forEach(cb => cb.checked = false);
    syncBar();
  };

  window.applyBulk = function () {
    const action  = document.getElementById('bulk-action-select').value;
    const checked = getChecked();
    if (!action)         { alert('Please select a bulk action.'); return; }
    if (!checked.length) { alert('No rows selected.'); return; }
    if (action === 'delete' && !confirm('Delete ' + checked.length + ' mapping(s)? This cannot be undone.')) return;

    document.getElementById('bulk-action-input').value = action;
    const container = document.getElementById('bulk-ids-container');
    container.innerHTML = '';
    checked.forEach(cb => {
      const inp = document.createElement('input');
      inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
      container.appendChild(inp);
    });
    document.getElementById('bulk-form').submit();
  };
})();
</script>
@endpush

@endsection
