@extends('layouts.app')
@section('title', 'Enrollment Management')
@section('breadcrumb', 'Enrollment')

@push('head')
<style>
/* ── Layout ─────────────────────────────── */
.enr-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
@media (max-width: 900px) { .enr-grid { grid-template-columns: 1fr; } }

/* ── Cards ──────────────────────────────── */
.enr-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.enr-card__head { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; background: #f8fafc; }
.enr-card__title { font-size: .9rem; font-weight: 800; color: #0f172a; margin: 0; }
.enr-card__desc { font-size: .78rem; color: #64748b; margin: 2px 0 0; }
.enr-card__body { padding: 20px; }

/* ── Stat chips ──────────────────────────── */
.enr-stat-chip { display:inline-flex; align-items:center; gap:8px; padding:.4rem .85rem; border-radius:8px; border:1px solid; }
.enr-stat-chip--green { background:#f0fdf4; border-color:#86efac; }
.enr-stat-chip--blue  { background:#eff6ff; border-color:#93c5fd; }
.enr-stat-chip--amber { background:#fffbeb; border-color:#fcd34d; }
.enr-stat-chip__num   { font-size:1.05rem; font-weight:800; color:#0f172a; line-height:1; }
.enr-stat-chip--green .enr-stat-chip__num { color:#166534; }
.enr-stat-chip--blue  .enr-stat-chip__num { color:#1d4ed8; }
.enr-stat-chip--amber .enr-stat-chip__num { color:#92400e; }
.enr-stat-chip__label { font-size:.72rem; font-weight:600; color:#64748b; white-space:nowrap; }

/* ── Form ───────────────────────────────── */
.enr-field { margin-bottom: 14px; }
.enr-label { display: block; font-size: .75rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 5px; }
.enr-input, .enr-select {
  width: 100%; padding: .55rem .85rem;
  border: 1px solid #e2e8f0; border-radius: 8px;
  font-size: .87rem; background: #fff; color: #0f172a;
  box-sizing: border-box;
}
.enr-input:focus, .enr-select:focus {
  outline: none; border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99,102,241,.1);
}
.enr-btn {
  width: 100%; padding: .65rem 1rem;
  background: #4f46e5; color: #fff;
  border: none; border-radius: 8px;
  font-size: .87rem; font-weight: 700;
  cursor: pointer; transition: background .15s;
}
.enr-btn:hover { background: #4338ca; }
.enr-btn:disabled { background: #94a3b8; cursor: not-allowed; }

/* ── Student dropdown ────────────────────── */
.enr-search-wrap { position: relative; }
.enr-dropdown-trigger {
  width: 100%; padding: .55rem .85rem;
  border: 1px solid #e2e8f0; border-radius: 8px;
  font-size: .87rem; background: #fff; color: #0f172a;
  box-sizing: border-box; cursor: pointer;
  display: flex; align-items: center; justify-content: space-between;
  user-select: none; transition: border-color .15s;
}
.enr-dropdown-trigger:hover { border-color: #6366f1; }
.enr-dropdown-trigger.open { border-color: #6366f1; border-radius: 8px 8px 0 0; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.enr-dropdown-trigger__text { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.enr-dropdown-trigger__arrow { flex-shrink: 0; margin-left: 8px; transition: transform .2s; color: #94a3b8; }
.enr-dropdown-trigger.open .enr-dropdown-trigger__arrow { transform: rotate(180deg); }
.enr-dropdown-panel {
  position: absolute; top: 100%; left: 0; right: 0; z-index: 200;
  border: 1px solid #6366f1; border-top: none; border-radius: 0 0 8px 8px;
  background: #fff; box-shadow: 0 8px 24px rgba(0,0,0,.12);
  display: none;
}
.enr-dropdown-panel.open { display: block; }
.enr-student-filter {
  width: 100%; padding: .5rem .85rem;
  border: none; border-bottom: 1px solid #e2e8f0;
  font-size: .85rem; box-sizing: border-box; background: #f8fafc;
}
.enr-student-filter:focus { outline: none; background: #eff6ff; }
.enr-student-list { max-height: 220px; overflow-y: auto; }
.enr-student-opt {
  padding: 9px 12px; cursor: pointer;
  border-bottom: 1px solid #f1f5f9;
  transition: background .1s; display: flex; align-items: center; justify-content: space-between;
}
.enr-student-opt:last-child { border-bottom: none; }
.enr-student-opt:hover { background: #f1f5f9; }
.enr-student-opt__name { font-weight: 700; font-size: .85rem; color: #0f172a; }
.enr-student-opt__lrn  { font-size: .75rem; color: #64748b; }
.enr-pay-badge { font-size: .65rem; font-weight: 700; border-radius: 4px; padding: 1px 5px; white-space: nowrap; }
.enr-pay-badge--paid   { background: #dcfce7; color: #166534; }
.enr-pay-badge--unpaid { background: #fee2e2; color: #991b1b; }
.enr-student-opt__tag  { font-size: .68rem; font-weight: 700; border-radius: 4px; padding: 1px 6px; background: #fef3c7; color: #92400e; white-space: nowrap; margin-left: 6px; }

/* ── Prereq inline badge ─────────────────── */
.enr-prereq-ok   { display:flex; align-items:center; gap:.45rem; padding:.5rem .75rem; background:#f0fdf4; border:1px solid #86efac; border-radius:7px; font-size:.8rem; color:#166534; font-weight:600; }
.enr-prereq-warn { padding:.55rem .75rem; background:#fffbeb; border:1px solid #fcd34d; border-radius:7px; font-size:.8rem; color:#92400e; }
.enr-prereq-warn strong { display:block; font-weight:700; margin-bottom:3px; }
.enr-prereq-checking { font-size:.8rem; color:#64748b; padding:.4rem 0; }

/* ── Section preview panel ──────────────── */
.section-preview { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-top: 12px; display: none; }
.section-preview__header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.section-preview__name { font-weight: 800; font-size: .92rem; color: #0f172a; }
.section-preview__capacity { font-size: .75rem; color: #64748b; }
.section-preview__teacher { font-size: .78rem; color: #475569; margin-bottom: 10px; }
.subject-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: .8rem; }
.subject-row:last-child { border-bottom: none; }
.subject-row__name { font-weight: 600; color: #0f172a; }
.subject-row__faculty { color: #64748b; }
.subject-row__schedule { font-size: .72rem; color: #94a3b8; }
.capacity-bar { height: 5px; background: #f1f5f9; border-radius: 3px; margin-top: 10px; }
.capacity-bar__fill { height: 100%; background: #22c55e; border-radius: 3px; transition: width .3s; }
.capacity-bar__fill.warn { background: #f59e0b; }
.capacity-bar__fill.full { background: #ef4444; }

/* ── Enrollment table ───────────────────── */
.enr-table { width: 100%; border-collapse: collapse; font-size: .84rem; }
.enr-table th { padding: 10px 12px; text-align: left; font-weight: 700; font-size: .7rem; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; background: #f8fafc; }
.enr-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.enr-table tr:hover td { background: #f8fafc; }
.enr-status { display: inline-block; padding: .2rem .55rem; border-radius: 5px; font-size: .68rem; font-weight: 700; text-transform: uppercase; }
.enr-status--enrolled   { background: #dcfce7; color: #166534; }
.enr-status--dropped    { background: #fee2e2; color: #991b1b; }
.enr-status--finalized  { background: #e0e7ff; color: #3730a3; }

/* ── Table filter bar ───────────────────── */
.enr-filter-bar { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.enr-filter-bar input, .enr-filter-bar select { padding: .42rem .75rem; border: 1px solid #e2e8f0; border-radius: 7px; font-size: .82rem; background: #fff; }

/* ── Alert ──────────────────────────────── */
.enr-alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: .85rem; }
.enr-alert.success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
.enr-alert.error   { background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; }
</style>
@endpush

@section('content')
<div style="max-width:1200px;">

  {{-- ── Page heading ───────────────────────────────────────────────────── --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <div>
      <h1 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Enrollment Management</h1>
      <p style="font-size:.82rem;color:#64748b;margin:0;">Assign students to sections and view faculty/subject assignments.</p>
    </div>
    @if($activeAcademicYear && $selectedYear && $selectedYear->id !== $activeAcademicYear->id)
    <div style="display:inline-flex;align-items:center;gap:6px;background:#fef3c7;border:1px solid #fcd34d;border-radius:999px;padding:.3rem .85rem;">
      <span style="font-size:.75rem;font-weight:700;color:#92400e;">Viewing historical year — {{ $selectedYear->year_label }}</span>
    </div>
    @elseif($activeAcademicYear)
    <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-radius:999px;padding:.35rem 1rem;">
      <div style="width:7px;height:7px;border-radius:50%;background:#10b981;"></div>
      <span style="font-size:.8rem;font-weight:700;color:#059669;">{{ $activeAcademicYear->year_label }}</span>
    </div>
    @endif
  </div>

  {{-- ── Term Selection Bar + Stats ─────────────────────────────────────── --}}
  <div class="enr-card" style="margin-bottom:20px;">
    <div class="enr-card__body" style="padding:14px 20px;">
      <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">

        <div style="display:flex;align-items:center;gap:8px;">
          <label class="enr-label" style="margin:0;white-space:nowrap;">Academic Year</label>
          <form method="GET" action="{{ route('registrar.enrollment') }}" style="display:contents;">
            @if(request('search'))      <input type="hidden" name="search"        value="{{ request('search') }}">      @endif
            @if(request('grade_filter'))<input type="hidden" name="grade_filter"  value="{{ request('grade_filter') }}">@endif
            @if(request('status_filter'))<input type="hidden" name="status_filter" value="{{ request('status_filter') }}">@endif
            <select name="year_id" class="enr-select" style="width:auto;min-width:190px;" onchange="this.form.submit()">
              @foreach($allAcademicYears as $yr)
              <option value="{{ $yr->id }}" {{ $selectedYearId == $yr->id ? 'selected' : '' }}>
                {{ $yr->year_label }}{{ $yr->status === 'active' ? ' (Active)' : '' }}
              </option>
              @endforeach
            </select>
          </form>
        </div>

        <div style="margin-left:auto;display:flex;gap:10px;flex-wrap:wrap;">
          <div class="enr-stat-chip enr-stat-chip--green">
            <span class="enr-stat-chip__num">{{ $totalEnrolled }}</span>
            <span class="enr-stat-chip__label">Total Enrolled</span>
          </div>
          <div class="enr-stat-chip enr-stat-chip--blue">
            <span class="enr-stat-chip__num">{{ $sectionsWithSeats }}</span>
            <span class="enr-stat-chip__label">Sections w/ Seats</span>
          </div>
          <div class="enr-stat-chip enr-stat-chip--amber">
            <span class="enr-stat-chip__num">{{ $unpaidCount }}</span>
            <span class="enr-stat-chip__label">Unpaid Students</span>
          </div>
        </div>

      </div>
    </div>
  </div>

  @if(session('success'))
  <div class="enr-alert success">{{ session('success') }}</div>
  @endif

  @if($errors->has('enrollment'))
  <div class="enr-alert error">{{ $errors->first('enrollment') }}</div>
  @endif

  {{-- ── Main Grid: Enroll Form + Prerequisite Checker ─────────────────── --}}
  <div class="enr-grid">

    {{-- ENROLL FORM --}}
    <div class="enr-card">
      <div class="enr-card__head">
        <div class="enr-card__title">Enroll a Student</div>
        <div class="enr-card__desc">Search student → pick grade level → select section → assign</div>
      </div>
      <div class="enr-card__body">
        <form method="POST" action="{{ route('registrar.enroll') }}" id="enrollForm">
          @csrf
          <input type="hidden" name="academic_year_id" value="{{ $selectedYear?->id }}">
          <input type="hidden" name="grade_level" id="hidden_grade_level">

          {{-- Student Dropdown --}}
          <div class="enr-field">
            <label class="enr-label">Select Student</label>
            <div class="enr-search-wrap">

              <div class="enr-dropdown-trigger" id="studentTrigger" onclick="toggleStudentDropdown()">
                <span class="enr-dropdown-trigger__text" id="triggerText" style="color:#94a3b8;">— Choose a student —</span>
                <svg class="enr-dropdown-trigger__arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
              </div>

              <div class="enr-dropdown-panel" id="studentDropdownPanel">
                <input type="text" id="studentFilter" class="enr-student-filter"
                       placeholder="🔍  Filter by name or LRN…"
                       oninput="filterStudents(this.value)"
                       autocomplete="off">
                <div class="enr-student-list" id="studentList">
                  @foreach($allStudents as $s)
                  @php $currentSection = $s->enrollments->first()?->section?->section_name; @endphp
                  <div class="enr-student-opt"
                       data-id="{{ $s->id }}"
                       data-name="{{ $s->full_name }}"
                       data-lrn="{{ $s->lrn ?? '' }}"
                       data-section="{{ $currentSection ?? '' }}"
                       data-paid="{{ ($studentPaymentStatus[$s->id] ?? false) ? '1' : '0' }}"
                       data-search="{{ strtolower($s->full_name . ' ' . ($s->lrn ?? '')) }}"
                       onclick="selectStudent(this)">
                    <div>
                      <div class="enr-student-opt__name">{{ $s->full_name }}</div>
                      <div class="enr-student-opt__lrn">LRN: {{ $s->lrn ?? 'No LRN' }}</div>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
                      @if($studentPaymentStatus[$s->id] ?? false)
                        <span class="enr-pay-badge enr-pay-badge--paid">Paid</span>
                      @else
                        <span class="enr-pay-badge enr-pay-badge--unpaid">Unpaid</span>
                      @endif
                      @if($currentSection)
                        <span class="enr-student-opt__tag">{{ $currentSection }}</span>
                      @endif
                    </div>
                  </div>
                  @endforeach
                  @if($allStudents->isEmpty())
                  <div style="padding:20px;text-align:center;color:#94a3b8;font-size:.82rem;">No active students found.</div>
                  @endif
                </div>
              </div>

            </div>
            <input type="hidden" name="student_id" id="selectedStudentId">
            <div id="selectedStudentInfo" style="margin-top:8px;font-size:.8rem;color:#475569;display:none;align-items:center;gap:6px;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;color:#22c55e;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <strong id="selectedStudentName" style="color:#0f172a;"></strong>
              <span id="selectedStudentLrn" style="color:#94a3b8;"></span>
              <span id="selectedStudentPay" style="font-size:.7rem;font-weight:700;border-radius:4px;padding:1px 5px;"></span>
              <span id="alreadyEnrolledWarning" style="display:none;background:#fef3c7;color:#92400e;font-size:.72rem;font-weight:700;border-radius:4px;padding:1px 6px;"></span>
            </div>
          </div>

          {{-- Grade Level --}}
          <div class="enr-field">
            <label class="enr-label">Grade Level</label>
            <select id="gradeLevelSelect" class="enr-select" onchange="onGradeChange(this.value)">
              <option value="">— Select grade level —</option>
              @foreach($standardGradeLevels as $lvl)
              <option value="{{ $lvl }}">{{ $lvl }}</option>
              @endforeach
            </select>

            {{-- Inline Prerequisite Status --}}
            <div id="prereqStatus" style="margin-top:8px;display:none;"></div>
          </div>

          {{-- Section --}}
          <div class="enr-field">
            <label class="enr-label">Section</label>
            <select id="sectionSelect" class="enr-select" name="section_id" onchange="onSectionChange(this.value)" disabled>
              <option value="">— Select grade level first —</option>
            </select>

            <div id="sectionPreview" class="section-preview">
              <div class="section-preview__header">
                <div class="section-preview__name" id="previewName">—</div>
                <div class="section-preview__capacity" id="previewCapacity">— / — students</div>
              </div>
              <div class="section-preview__teacher">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;display:inline-block;vertical-align:middle;margin-right:3px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                Adviser: <span id="previewAdviser">—</span>
              </div>
              <div id="previewSubjects"></div>
              <div class="capacity-bar"><div class="capacity-bar__fill" id="previewCapacityBar" style="width:0%"></div></div>
            </div>
          </div>

          <button type="submit" class="enr-btn" id="enrollBtn" disabled>Enroll Student</button>
        </form>
      </div>
    </div>

    {{-- PREREQUISITE CHECKER --}}
    <div class="enr-card">
      <div class="enr-card__head">
        <div class="enr-card__title">Prerequisite Checker</div>
        <div class="enr-card__desc">Verify if a student meets curriculum requirements before enrolling</div>
      </div>
      <div class="enr-card__body">
        <form method="GET" action="{{ route('registrar.enrollment') }}">
          <input type="hidden" name="year_id" value="{{ $selectedYearId }}">
          <div class="enr-field">
            <label class="enr-label">Student LRN</label>
            <input type="text" name="check_lrn" value="{{ request('check_lrn') }}"
              placeholder="12-digit LRN" class="enr-input">
          </div>
          <div class="enr-field">
            <label class="enr-label">Target Grade Level</label>
            <select name="check_grade_level" class="enr-select">
              <option value="">— Select —</option>
              @foreach($standardGradeLevels as $lvl)
              <option value="{{ $lvl }}" {{ request('check_grade_level') === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="enr-btn" style="background:#0ea5e9;">Check Prerequisites</button>
        </form>

        @if(request('check_lrn'))
        <div style="margin-top:16px;padding-top:14px;border-top:1px solid #f1f5f9;">
          @if(!$checkStudent)
            <div style="font-size:.87rem;color:#dc2626;font-weight:600;">No student found with LRN "{{ request('check_lrn') }}".</div>
          @elseif($unmetPrereqs === null)
            <div style="font-size:.87rem;color:#d97706;font-weight:600;">Please select a target grade level.</div>
          @elseif(empty($unmetPrereqs))
            <div style="display:flex;align-items:center;gap:.6rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.75rem 1rem;">
              <span style="font-size:1.1rem;color:#16a34a;">✓</span>
              <div>
                <div style="font-weight:700;color:#166534;font-size:.88rem;">All prerequisites met</div>
                <div style="font-size:.78rem;color:#15803d;">{{ $checkStudent->full_name }} is eligible for {{ $checkGrade }}.</div>
              </div>
            </div>
          @else
            <div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:.85rem 1rem;">
              <div style="font-weight:700;color:#991b1b;font-size:.88rem;margin-bottom:8px;">
                ⚠ {{ count($unmetPrereqs) }} unmet prerequisite{{ count($unmetPrereqs) > 1 ? 's' : '' }}
                for {{ $checkStudent->full_name }}
              </div>
              @foreach($unmetPrereqs as $p)
              <div style="font-size:.8rem;color:#7f1d1d;margin-bottom:4px;">
                <strong>{{ $p['subject'] }}</strong> requires {{ $p['requires'] }}
                (min {{ $p['min_grade'] }}, got {{ $p['student_grade'] ?? 'No record' }})
              </div>
              @endforeach
            </div>
          @endif
        </div>
        @endif
      </div>
    </div>

  </div>

  {{-- ── Payment Status (finance-confirmed) ────────────────────────────── --}}
  <div class="enr-card" style="margin-bottom:24px;">
    <div class="enr-card__head" style="margin-bottom:12px;">
      <div>
        <div class="enr-card__title">Payment Status</div>
        <div class="enr-card__desc">When finance confirms a student has paid, mark them here. This only makes the student eligible for enrollment — you still assign their section above.</div>
      </div>
    </div>
    @php
      $unpaidStudents = collect($allStudents)->filter(fn($s) => !($studentPaymentStatus[$s->id] ?? false));
    @endphp
    @if($unpaidStudents->isEmpty())
      <div style="padding:16px;text-align:center;color:#94a3b8;font-size:.84rem;">All students are marked as paid for {{ $selectedYear?->year_label ?? 'this year' }}.</div>
    @else
      <div style="overflow-x:auto;">
        <table class="enr-table">
          <thead>
            <tr><th>Student</th><th>LRN</th><th>Status</th><th style="text-align:right;">Action</th></tr>
          </thead>
          <tbody>
            @foreach($unpaidStudents as $s)
            <tr>
              <td style="font-weight:600;color:#0f172a;">{{ $s->full_name }}</td>
              <td style="color:#64748b;">{{ $s->lrn ?? '—' }}</td>
              <td><span class="enr-pay-badge enr-pay-badge--unpaid">Unpaid</span></td>
              <td style="text-align:right;">
                <form method="POST" action="{{ route('registrar.mark-paid') }}" style="margin:0;display:inline;">
                  @csrf
                  <input type="hidden" name="student_id" value="{{ $s->id }}">
                  <button type="submit" style="background:#166534;color:#fff;border:none;border-radius:6px;padding:5px 12px;font-size:.76rem;font-weight:700;cursor:pointer;"
                    title="Finance confirmed payment — mark this student as eligible for enrollment">Mark as Paid</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- ── Current Enrollments Table ─────────────────────────────────────── --}}
  <div class="enr-card">
    <div class="enr-card__head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
      <div>
        <div class="enr-card__title">Enrollments — {{ $selectedYear?->year_label ?? '—' }}</div>
        <div class="enr-card__desc">Filtered view of student enrollments with section and faculty assignments</div>
      </div>
      <form method="GET" action="{{ route('registrar.enrollment') }}" class="enr-filter-bar">
        <input type="hidden" name="year_id" value="{{ $selectedYearId }}">
        <input type="text" name="search" value="{{ $search }}" placeholder="Name or section…" style="width:180px;">
        <select name="grade_filter">
          <option value="">All grades</option>
          @foreach($standardGradeLevels as $lvl)
          <option value="{{ $lvl }}" {{ $gradeFilter === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
          @endforeach
        </select>
        <select name="status_filter">
          <option value="enrolled" {{ $statusFilter === 'enrolled' ? 'selected' : '' }}>Enrolled</option>
          <option value="dropped"  {{ $statusFilter === 'dropped'  ? 'selected' : '' }}>Dropped</option>
          <option value="all"      {{ $statusFilter === 'all'      ? 'selected' : '' }}>All statuses</option>
        </select>
        <button type="submit" style="padding:.42rem .9rem;background:#4f46e5;color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;">Filter</button>
        @if($search || $gradeFilter || $statusFilter !== 'enrolled')
        <a href="{{ route('registrar.enrollment', ['year_id' => $selectedYearId]) }}"
           style="padding:.42rem .75rem;background:#f1f5f9;color:#475569;border-radius:7px;font-size:.82rem;font-weight:600;text-decoration:none;">Clear</a>
        @endif
      </form>
    </div>
    <div style="overflow-x:auto;">
      <table class="enr-table">
        <thead>
          <tr>
            <th>Student</th>
            <th>LRN</th>
            <th>Section</th>
            <th>Grade</th>
            <th>Adviser</th>
            <th>Subjects / Faculty</th>
            <th>Enrolled On</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentEnrollments as $enr)
          <tr>
            <td style="font-weight:700;color:#0f172a;">{{ $enr->student?->full_name ?? '—' }}</td>
            <td style="font-size:.8rem;color:#64748b;">{{ $enr->student?->lrn ?? '—' }}</td>
            <td style="font-weight:600;">{{ $enr->section?->section_name ?? '—' }}</td>
            <td>{{ $enr->section?->grade_level ?? '—' }}</td>
            <td style="font-size:.8rem;color:#475569;">{{ $enr->section?->adviser?->full_name ?? 'None' }}</td>
            <td>
              <div style="display:flex;flex-direction:column;gap:3px;">
                @forelse($enr->section?->sectionSubjects ?? [] as $ss)
                <div style="font-size:.76rem;">
                  <span style="font-weight:600;color:#0f172a;">{{ $ss->subject?->subject_name }}</span>
                  <span style="color:#94a3b8;"> — {{ $ss->faculty?->full_name ?? 'Unassigned' }}</span>
                </div>
                @empty
                <span style="font-size:.76rem;color:#94a3b8;">No subjects assigned</span>
                @endforelse
              </div>
            </td>
            <td style="font-size:.78rem;color:#64748b;">{{ $enr->enrolled_at?->format('M d, Y') ?? '—' }}</td>
            <td>
              <span class="enr-status enr-status--{{ $enr->status }}">{{ ucfirst($enr->status) }}</span>
            </td>
            <td>
              @if($enr->status === 'enrolled')
                @if($enr->finalized_at)
                  <span class="enr-status enr-status--finalized" title="Confirmed {{ $enr->finalized_at->format('M d, Y') }}">
                    &#x1F512; Confirmed
                  </span>
                @else
                  <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                    <a href="{{ route('registrar.enrollment.finalize', $enr->student_id) }}?year_id={{ $selectedYearId }}"
                       style="padding:.3rem .65rem;font-size:.72rem;background:#e0e7ff;color:#3730a3;border-radius:5px;text-decoration:none;font-weight:700;">Finalize</a>
                    <form method="POST" action="{{ route('registrar.drop-enrollment') }}"
                          onsubmit="return confirmRemove('{{ addslashes($enr->student?->full_name ?? '') }}', '{{ addslashes($enr->section?->section_name ?? '') }}')">
                      @csrf
                      <input type="hidden" name="enrollment_id" value="{{ $enr->id }}">
                      <button type="submit" style="padding:.3rem .65rem;font-size:.72rem;background:#fee2e2;color:#991b1b;border:none;border-radius:5px;cursor:pointer;font-weight:700;">Remove</button>
                    </form>
                  </div>
                @endif
              @else
              <span style="font-size:.75rem;color:#94a3b8;">—</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" style="text-align:center;color:#94a3b8;padding:32px;">No enrollments found matching the current filters.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($recentEnrollments->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #e2e8f0;">
      {{ $recentEnrollments->links() }}
    </div>
    @endif
  </div>

</div>
@endsection

@push('scripts')
<script>
const AJAX_SECTIONS_URL = '{{ route('registrar.ajax.sections') }}';
const AJAX_SECTION_URL  = '{{ route('registrar.ajax.section-info') }}';
const AJAX_PREREQ_URL   = '{{ route('registrar.ajax.prereq-check') }}';
const SELECTED_YEAR_ID  = {{ $selectedYearId ?: 'null' }};
const CSRF_TOKEN        = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

let prereqUnmet      = false;
let prereqCheckTimer = null;

// ── Confirm removal dialog ────────────────────────────────────────────────
function confirmRemove(studentName, sectionName) {
  return confirm(
    'Remove ' + studentName + ' from ' + sectionName + '?\n\n' +
    'This cannot be undone if no finalized grades exist.'
  );
}

// ── Student Dropdown ──────────────────────────────────────────────────────
function toggleStudentDropdown() {
  const trigger = document.getElementById('studentTrigger');
  const panel   = document.getElementById('studentDropdownPanel');
  if (panel.classList.contains('open')) {
    closeStudentDropdown();
  } else {
    trigger.classList.add('open');
    panel.classList.add('open');
    document.getElementById('studentFilter').focus();
  }
}

function closeStudentDropdown() {
  document.getElementById('studentTrigger').classList.remove('open');
  document.getElementById('studentDropdownPanel').classList.remove('open');
}

document.addEventListener('click', e => {
  if (!e.target.closest('.enr-search-wrap')) closeStudentDropdown();
});

function filterStudents(q) {
  const term = q.toLowerCase().trim();
  document.querySelectorAll('#studentList .enr-student-opt').forEach(el => {
    el.style.display = (!term || el.dataset.search.includes(term)) ? '' : 'none';
  });
}

function selectStudent(el) {
  const name    = el.dataset.name;
  const lrn     = el.dataset.lrn;
  const section = el.dataset.section;
  const paid    = el.dataset.paid === '1';

  document.getElementById('selectedStudentId').value = el.dataset.id;
  document.getElementById('triggerText').textContent  = name + (lrn ? '  (' + lrn + ')' : '');
  document.getElementById('triggerText').style.color  = '#0f172a';
  document.getElementById('selectedStudentName').textContent = name;
  document.getElementById('selectedStudentLrn').textContent  = lrn ? '(' + lrn + ')' : '';

  const payBadge = document.getElementById('selectedStudentPay');
  payBadge.textContent = paid ? 'Paid' : 'Unpaid';
  payBadge.className   = paid ? 'enr-pay-badge enr-pay-badge--paid' : 'enr-pay-badge enr-pay-badge--unpaid';

  const warn = document.getElementById('alreadyEnrolledWarning');
  warn.textContent   = section ? 'Already in ' + section : '';
  warn.style.display = section ? 'inline' : 'none';

  document.getElementById('selectedStudentInfo').style.display = 'flex';
  closeStudentDropdown();

  firePrereqCheck();
  checkEnrollBtn();
}

// ── Grade Level Cascades → Sections ──────────────────────────────────────
function onGradeChange(grade) {
  document.getElementById('hidden_grade_level').value = grade;
  const sel = document.getElementById('sectionSelect');
  document.getElementById('sectionPreview').style.display = 'none';
  clearPrereqStatus();

  if (!grade) {
    sel.innerHTML = '<option value="">— Select grade level first —</option>';
    sel.disabled = true;
    prereqUnmet = false;
    checkEnrollBtn();
    return;
  }

  // Load sections
  let url = AJAX_SECTIONS_URL + '?grade_level=' + encodeURIComponent(grade);
  if (SELECTED_YEAR_ID) url += '&academic_year_id=' + SELECTED_YEAR_ID;

  sel.innerHTML = '<option value="">Loading…</option>';
  sel.disabled = true;

  fetch(url)
    .then(r => r.json())
    .then(data => {
      if (!data.length) {
        sel.innerHTML = '<option value="">No sections found for this grade</option>';
      } else {
        sel.innerHTML = '<option value="">— Choose a section —</option>' +
          data.map(s => {
            const left = Math.max(0, s.capacity - s.enrolled);
            const full = left === 0;
            const tail = full ? ' — FULL' : ` — ${left} slot${left === 1 ? '' : 's'} left`;
            return `<option value="${s.id}" ${full ? 'disabled' : ''}>${escHtml(s.section_name)} (${s.enrolled}/${s.capacity}${tail})</option>`;
          }).join('');
        sel.disabled = false;
      }
      firePrereqCheck();
      checkEnrollBtn();
    });
}

function onSectionChange(sectionId) {
  if (!sectionId) {
    document.getElementById('sectionPreview').style.display = 'none';
    checkEnrollBtn();
    return;
  }

  let url = AJAX_SECTION_URL + '?section_id=' + sectionId;
  if (SELECTED_YEAR_ID) url += '&academic_year_id=' + SELECTED_YEAR_ID;

  fetch(url)
    .then(r => r.json())
    .then(s => {
      if (!s) return;

      document.getElementById('previewName').textContent     = s.section_name;
      document.getElementById('previewAdviser').textContent  = s.adviser;
      document.getElementById('previewCapacity').textContent = s.enrolled + ' / ' + s.capacity + ' students';

      const pct = Math.min(100, Math.round((s.enrolled / s.capacity) * 100));
      const bar = document.getElementById('previewCapacityBar');
      bar.style.width = pct + '%';
      bar.className = 'capacity-bar__fill' + (pct >= 100 ? ' full' : pct >= 80 ? ' warn' : '');

      const subList = s.subjects.map(sub => `
        <div class="subject-row">
          <div>
            <div class="subject-row__name">${escHtml(sub.subject)}</div>
            <div class="subject-row__faculty">${escHtml(sub.faculty)}</div>
          </div>
          <div class="subject-row__schedule">${escHtml(sub.schedule ?? '')}</div>
        </div>
      `).join('');

      document.getElementById('previewSubjects').innerHTML = subList ||
        '<div style="font-size:.78rem;color:#94a3b8;">No subjects assigned to this section yet.</div>';
      document.getElementById('sectionPreview').style.display = 'block';
      checkEnrollBtn();
    });
}

// ── Inline Prerequisite Check ─────────────────────────────────────────────
function clearPrereqStatus() {
  const box = document.getElementById('prereqStatus');
  box.style.display = 'none';
  box.innerHTML = '';
  prereqUnmet = false;
}

function firePrereqCheck() {
  const studentId = document.getElementById('selectedStudentId').value;
  const grade     = document.getElementById('gradeLevelSelect').value;
  const box       = document.getElementById('prereqStatus');

  if (!studentId || !grade || !SELECTED_YEAR_ID) {
    clearPrereqStatus();
    checkEnrollBtn();
    return;
  }

  // Debounce — avoid double-firing when both fields change rapidly
  clearTimeout(prereqCheckTimer);
  prereqCheckTimer = setTimeout(() => {
    box.innerHTML = '<div class="enr-prereq-checking">Checking prerequisites…</div>';
    box.style.display = 'block';

    fetch(AJAX_PREREQ_URL +
      '?student_id=' + encodeURIComponent(studentId) +
      '&grade_level=' + encodeURIComponent(grade) +
      '&academic_year_id=' + encodeURIComponent(SELECTED_YEAR_ID))
      .then(r => r.json())
      .then(data => {
        if (data.met) {
          prereqUnmet = false;
          box.innerHTML = `
            <div class="enr-prereq-ok">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Prerequisites met
            </div>`;
        } else {
          prereqUnmet = true;
          const list = data.unmet.map(u =>
            `<div style="margin-top:3px;font-size:.78rem;"><strong>${escHtml(u.subject)}</strong> requires ${escHtml(u.requires)} (min ${u.min_grade}${u.student_grade != null ? ', got ' + u.student_grade : ', not taken'})</div>`
          ).join('');
          box.innerHTML = `
            <div class="enr-prereq-warn">
              <strong>⚠ ${data.unmet.length} unmet prerequisite${data.unmet.length > 1 ? 's' : ''}</strong>
              ${list}
            </div>`;
        }
        box.style.display = 'block';
        checkEnrollBtn();
      })
      .catch(() => {
        clearPrereqStatus();
        checkEnrollBtn();
      });
  }, 300);
}

// ── Enroll button gate ────────────────────────────────────────────────────
function checkEnrollBtn() {
  const btn        = document.getElementById('enrollBtn');
  const hasStudent = !!document.getElementById('selectedStudentId').value;
  const hasSection = !!document.getElementById('sectionSelect').value;
  const hasGrade   = !!document.getElementById('gradeLevelSelect').value;
  btn.disabled = !(hasStudent && hasSection && hasGrade) || prereqUnmet;
}

function escHtml(str) {
  return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush
