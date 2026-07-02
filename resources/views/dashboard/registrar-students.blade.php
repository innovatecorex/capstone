@extends('layouts.app')
@section('title', 'Student Records')
@section('breadcrumb', 'Student Records')

@push('head')
<style>
/* ── Table ── */
.reg-table { width:100%; border-collapse:collapse; }
.reg-table th { padding:10px 14px; font-size:.72rem; font-weight:700; color:var(--sd-muted); text-transform:uppercase; letter-spacing:.07em; border-bottom:2px solid var(--sd-border); text-align:left; white-space:nowrap; }
.reg-table td { padding:11px 14px; font-size:.875rem; border-bottom:1px solid #f1f5f9; color:var(--sd-navy); vertical-align:middle; }
.reg-table tr:last-child td { border-bottom:none; }
.reg-table tr:hover td { background:#f8fafc; }
.reg-avatar { width:32px; height:32px; border-radius:8px; background:linear-gradient(135deg,#4f46e5,#7c3aed); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; flex-shrink:0; }

/* ── Badges ── */
.rs-badge { display:inline-flex; align-items:center; gap:4px; padding:.2rem .65rem; border-radius:999px; font-size:.7rem; font-weight:700; white-space:nowrap; }
.rs-badge--active   { background:rgba(16,185,129,.1); color:#059669; }
.rs-badge--inactive { background:#f1f5f9; color:#64748b; }
.rs-badge--enrolled { background:rgba(16,185,129,.1); color:#059669; }
.rs-badge--pending  { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
.rs-badge--none     { background:#f1f5f9; color:#94a3b8; }

/* ── Stats strip ── */
.rs-stats { display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.rs-stat  { flex:1; min-width:120px; background:#fff; border:1px solid var(--sd-border); border-radius:12px; padding:12px 16px; display:flex; flex-direction:column; gap:2px; }
.rs-stat__val  { font-size:1.4rem; font-weight:900; color:var(--sd-navy); line-height:1; }
.rs-stat__lbl  { font-size:.72rem; font-weight:600; color:var(--sd-muted); text-transform:uppercase; letter-spacing:.05em; }
.rs-stat--green .rs-stat__val { color:#059669; }
.rs-stat--amber .rs-stat__val { color:#d97706; }
.rs-stat--gray  .rs-stat__val { color:#94a3b8; }

/* ── Filter bar ── */
.rs-filters { display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-bottom:18px; }
.rs-search-wrap { flex:1; min-width:200px; position:relative; }
.rs-search-wrap svg { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; }
.rs-search-input { width:100%; padding:.52rem .9rem .52rem 2.1rem; border:1px solid var(--sd-border); border-radius:999px; font-size:.855rem; outline:none; transition:border-color .15s,box-shadow .15s; background:#fff; box-sizing:border-box; }
.rs-search-input:focus { border-color:var(--sd-primary); box-shadow:0 0 0 3px rgba(79,70,229,.08); }
.rs-select { padding:.5rem .9rem; border:1px solid var(--sd-border); border-radius:999px; font-size:.84rem; background:#fff; color:var(--sd-navy); outline:none; cursor:pointer; transition:border-color .15s; }
.rs-select:focus { border-color:var(--sd-primary); }
.rs-select--active { border-color:#4f46e5; background:#eff6ff; color:#1d4ed8; font-weight:700; }
.rs-filter-btn  { padding:.5rem 1.1rem; border:none; border-radius:999px; background:var(--sd-primary); color:#fff; font-size:.84rem; font-weight:700; cursor:pointer; transition:opacity .15s; white-space:nowrap; }
.rs-filter-btn:hover { opacity:.88; }
.rs-clear-btn   { padding:.5rem 1rem; border:1px solid var(--sd-border); border-radius:999px; background:#fff; color:#64748b; font-size:.84rem; font-weight:600; cursor:pointer; text-decoration:none; white-space:nowrap; transition:border-color .15s; }
.rs-clear-btn:hover { border-color:#94a3b8; }

/* ── Active filter pills ── */
.rs-active-filters { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:14px; }
.rs-pill { display:inline-flex; align-items:center; gap:5px; padding:.22rem .7rem; border-radius:999px; font-size:.74rem; font-weight:700; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
</style>
@endpush

@section('content')

@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-left:4px solid #16a34a;border-radius:8px;color:#166534;font-size:.85rem;font-weight:600;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fca5a5;border-left:4px solid #dc2626;border-radius:8px;color:#991b1b;font-size:.85rem;font-weight:600;">{{ session('error') }}</div>
@endif
@if($errors->any())
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fca5a5;border-left:4px solid #dc2626;border-radius:8px;color:#991b1b;font-size:.85rem;">
  @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

{{-- Page header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
  <div>
    <h1 style="font-size:1.2rem;font-weight:800;color:var(--sd-navy);margin:0 0 3px;">Student Records</h1>
    <p style="font-size:.82rem;color:var(--sd-muted);margin:0;">
      Master registry of all student accounts
      @if($activeYear) &mdash; active year: <strong>{{ $activeYear->year_label }}</strong>@endif
    </p>
  </div>
  <button type="button" onclick="document.getElementById('importModal').style.display='flex'"
          class="enc-button enc-button--primary enc-button--sm"
          style="display:inline-flex;align-items:center;gap:6px;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
    </svg>
    Import Master List
  </button>
</div>

{{-- Stats strip --}}
@if($activeYear)
<div class="rs-stats">
  <div class="rs-stat">
    <div class="rs-stat__val">{{ number_format($stats['total']) }}</div>
    <div class="rs-stat__lbl">Total Students</div>
  </div>
  <div class="rs-stat rs-stat--green">
    <div class="rs-stat__val">{{ number_format($stats['enrolled']) }}</div>
    <div class="rs-stat__lbl">Enrolled · {{ $activeYear->year_label }}</div>
  </div>
  <div class="rs-stat rs-stat--amber">
    <div class="rs-stat__val">{{ number_format($stats['pending_payment']) }}</div>
    <div class="rs-stat__lbl">Pending Payment</div>
  </div>
  <div class="rs-stat rs-stat--gray">
    <div class="rs-stat__val">{{ number_format($stats['not_enrolled']) }}</div>
    <div class="rs-stat__lbl">Not Yet Enrolled</div>
  </div>
</div>
@endif

{{-- Filter bar --}}
<form method="GET" action="{{ route('registrar.students') }}" id="filterForm">
  <div class="rs-filters">
    {{-- Search --}}
    <div class="rs-search-wrap">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
      </svg>
      <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or LRN…" class="rs-search-input">
    </div>

    {{-- Grade level --}}
    <select name="grade" class="rs-select {{ $gradeFilter ? 'rs-select--active' : '' }}" onchange="this.form.submit()">
      <option value="">All Grades</option>
      @foreach($gradeLevels as $gl)
        <option value="{{ $gl }}" {{ $gradeFilter === $gl ? 'selected' : '' }}>{{ $gl }}</option>
      @endforeach
    </select>

    {{-- Account status --}}
    <select name="status" class="rs-select {{ $statusFilter ? 'rs-select--active' : '' }}" onchange="this.form.submit()">
      <option value="">All Status</option>
      <option value="active"   {{ $statusFilter === 'active'   ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>

    {{-- Enrollment status --}}
    @if($activeYear)
    <select name="enroll" class="rs-select {{ $enrollFilter ? 'rs-select--active' : '' }}" onchange="this.form.submit()">
      <option value="">All Enrollment</option>
      <option value="enrolled"        {{ $enrollFilter === 'enrolled'        ? 'selected' : '' }}>Enrolled</option>
      <option value="pending_payment" {{ $enrollFilter === 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
      <option value="none"            {{ $enrollFilter === 'none'            ? 'selected' : '' }}>Not Enrolled</option>
    </select>
    @endif

    <button type="submit" class="rs-filter-btn">Search</button>

    @if($search || $gradeFilter || $statusFilter || $enrollFilter)
      <a href="{{ route('registrar.students') }}" class="rs-clear-btn">Clear</a>
    @endif
  </div>

  {{-- Active filter pills --}}
  @php $hasFilters = $search || $gradeFilter || $statusFilter || $enrollFilter; @endphp
  @if($hasFilters)
  <div class="rs-active-filters">
    @if($search)
      <span class="rs-pill">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:10px;height:10px"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
        "{{ $search }}"
      </span>
    @endif
    @if($gradeFilter)
      <span class="rs-pill">{{ $gradeFilter }}</span>
    @endif
    @if($statusFilter)
      <span class="rs-pill">{{ ucfirst($statusFilter) }}</span>
    @endif
    @if($enrollFilter)
      <span class="rs-pill">
        {{ $enrollFilter === 'pending_payment' ? 'Pending Payment' : ($enrollFilter === 'none' ? 'Not Enrolled' : 'Enrolled') }}
      </span>
    @endif
    <span style="font-size:.75rem;color:var(--sd-muted);align-self:center;">
      {{ $students->total() }} {{ Str::plural('result', $students->total()) }}
    </span>
  </div>
  @else
  <div style="font-size:.78rem;color:var(--sd-muted);margin-bottom:14px;">
    Showing {{ $students->total() }} {{ Str::plural('student', $students->total()) }}
  </div>
  @endif
</form>

{{-- Table --}}
<div class="sd-card">
  <div class="sd-card__body" style="padding:0;">
    @if($students->isEmpty())
      <div style="text-align:center;padding:56px 24px;color:var(--sd-muted);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:40px;height:40px;margin:0 auto 12px;display:block;color:#cbd5e1;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
        <div style="font-weight:600;font-size:.9rem;margin-bottom:4px;">No students found</div>
        @if($hasFilters)
          <div style="font-size:.8rem;">Try adjusting your filters or <a href="{{ route('registrar.students') }}" style="color:var(--sd-primary);font-weight:600;">clear them</a>.</div>
        @endif
      </div>
    @else
      <div style="overflow-x:auto;">
        <table class="reg-table">
          <thead>
            <tr>
              <th>Student</th>
              <th>LRN</th>
              <th>Grade</th>
              <th>Section</th>
              <th>Enrollment</th>
              <th>Account</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $s)
            @php
              $enr        = $s->enrollments->first();
              $enrStatus  = $enr?->status;
              $sectionName = $enr?->section?->section_name;
            @endphp
            <tr>
              {{-- Name --}}
              <td>
                <div style="display:flex;align-items:center;gap:10px;">
                  <div class="reg-avatar">{{ strtoupper(substr($s->first_name,0,1)) }}{{ strtoupper(substr($s->last_name,0,1)) }}</div>
                  <div>
                    <div style="font-weight:700;line-height:1.2;">{{ $s->last_name }}, {{ $s->first_name }}</div>
                    @if($s->email)
                      <div style="font-size:.74rem;color:var(--sd-muted);margin-top:2px;">{{ $s->email }}</div>
                    @endif
                  </div>
                </div>
              </td>

              {{-- LRN --}}
              <td style="font-family:monospace;font-size:.82rem;color:var(--sd-muted);">
                {{ $s->lrn ?? '—' }}
              </td>

              {{-- Grade --}}
              <td style="font-size:.84rem;font-weight:600;">
                {{ $s->grade_level ?? '—' }}
              </td>

              {{-- Section --}}
              <td style="font-size:.84rem;">
                @if($sectionName)
                  <span style="color:var(--sd-navy);font-weight:600;">{{ $sectionName }}</span>
                @else
                  <span style="color:#cbd5e1;">—</span>
                @endif
              </td>

              {{-- Enrollment status --}}
              <td>
                @if($enrStatus === 'enrolled')
                  <span class="rs-badge rs-badge--enrolled">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:10px;height:10px"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                    Enrolled
                  </span>
                @elseif($enrStatus === 'pending_payment')
                  <span class="rs-badge rs-badge--pending">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:10px;height:10px"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd"/></svg>
                    Pending Payment
                  </span>
                @else
                  <span class="rs-badge rs-badge--none">Not Enrolled</span>
                @endif
              </td>

              {{-- Account status --}}
              <td>
                <span class="rs-badge {{ $s->status === 'active' ? 'rs-badge--active' : 'rs-badge--inactive' }}">
                  {{ ucfirst($s->status ?? 'inactive') }}
                </span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if($students->hasPages())
      <div style="padding:14px 20px;border-top:1px solid var(--sd-border);">
        {{ $students->links() }}
      </div>
      @endif
    @endif
  </div>
</div>

{{-- ── Import Master List Modal ─────────────────────────────────────── --}}
<div id="importModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:1000;align-items:center;justify-content:center;padding:20px;">
  <div style="background:#fff;border-radius:14px;max-width:520px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
    <div style="padding:18px 22px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
      <h3 style="margin:0;font-size:1.05rem;font-weight:800;color:#0f172a;">Import Student Master List</h3>
      <button type="button" onclick="document.getElementById('importModal').style.display='none'"
              style="background:none;border:none;font-size:1.4rem;line-height:1;color:#94a3b8;cursor:pointer;">&times;</button>
    </div>
    <form method="POST" action="{{ route('registrar.students.import') }}" enctype="multipart/form-data" style="padding:22px;">
      @csrf
      <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px 16px;margin-bottom:18px;font-size:.82rem;color:#1e40af;line-height:1.55;">
        Upload a <strong>.csv</strong> file using the institutional template. Required columns:
        <strong>LRN, First Name, Last Name, Email</strong>. The system checks each LRN and email
        against existing records — if any duplicate is found, the entire import is halted to
        prevent overwriting.
      </div>
      <a href="{{ route('registrar.students.import.template') }}"
         style="display:inline-flex;align-items:center;gap:6px;font-size:.82rem;font-weight:700;color:#1d4ed8;text-decoration:none;margin-bottom:16px;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v13.5m0 0l-4.5-4.5M12 16.5l4.5-4.5M3.75 21h16.5"/>
        </svg>
        Download CSV Template
      </a>
      <div style="margin-bottom:20px;">
        <label style="display:block;font-size:.78rem;font-weight:700;color:#475569;margin-bottom:6px;">CSV File</label>
        <input type="file" name="master_list" accept=".csv,text/csv" required
               style="width:100%;font-size:.85rem;padding:8px;border:1px solid #cbd5e1;border-radius:8px;background:#f8fafc;">
      </div>
      <div style="display:flex;justify-content:flex-end;gap:10px;">
        <button type="button" onclick="document.getElementById('importModal').style.display='none'"
                style="padding:.5rem 1rem;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#475569;font-size:.85rem;font-weight:700;cursor:pointer;">Cancel</button>
        <button type="submit"
                style="padding:.5rem 1.2rem;border:none;border-radius:8px;background:#1d4ed8;color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;">Import</button>
      </div>
    </form>
  </div>
</div>
<script>
  document.getElementById('importModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
  });
</script>

@endsection
