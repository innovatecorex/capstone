@extends('layouts.app')

@section('title', 'Enrollment Advising')
@section('breadcrumb', 'Enrollment Advising')

@section('content')

{{-- Page header --}}
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <a href="{{ route('registrar.advising.index') }}"
         style="font-size:.8rem;color:var(--gray-400);text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;margin-bottom:.4rem;">
        ← Back to Students
      </a>
      <h1 class="enc-page__title">{{ $student->last_name }}, {{ $student->first_name }}</h1>
      <p class="enc-page__subtitle">
        Enrollment Advising
        @if($student->lrn) · LRN: {{ $student->lrn }} @endif
        @if($student->grade_level) · Current Grade: {{ $student->grade_level }} @endif
      </p>
    </div>
  </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#166534;margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#991b1b;margin-bottom:1rem;">{{ session('error') }}</div>
@endif

{{-- Term selection bar --}}
<form method="GET" class="enc-card" style="margin-bottom:1.25rem;">
  <div style="display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap;padding:1rem 1.25rem;">
    <div>
      <label class="adv-label">Academic Year</label>
      <select name="year_id" class="adv-select" onchange="this.form.submit()">
        @foreach($academicYears as $yr)
          <option value="{{ $yr->id }}" {{ $selectedYearId == $yr->id ? 'selected' : '' }}>
            {{ $yr->year_label }} ({{ ucfirst($yr->status) }})
          </option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="adv-label">Target Grade Level</label>
      <select name="grade_level" class="adv-select" onchange="this.form.submit()">
        @foreach(['Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $gl)
          <option value="{{ $gl }}" {{ $selectedGrade === $gl ? 'selected' : '' }}>{{ $gl }}</option>
        @endforeach
      </select>
    </div>
    <div style="margin-left:auto;display:flex;align-items:center;gap:.5rem;">
      @if($plan->isNotEmpty())
        @php $confirmedCount = $plan->where('status','confirmed')->count(); $pendingCount = $plan->where('status','pending')->count(); @endphp
        @if($confirmedCount > 0)
          <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.8rem;color:#166534;font-weight:700;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $confirmedCount }} confirmed
          </span>
        @endif
        @if($pendingCount > 0)
          <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.8rem;color:#92400e;font-weight:700;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $pendingCount }} pending
          </span>
        @endif
      @endif
    </div>
  </div>
</form>

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.25rem;align-items:start;">

  {{-- ── Left: Subject Grid ── --}}
  <div>

    @if(!$selectedYear)
      <div class="enc-card" style="padding:2rem;text-align:center;color:var(--gray-400);">No active academic year found.</div>
    @elseif($mappings->isEmpty())
      <div class="enc-card" style="padding:2rem;text-align:center;color:var(--gray-400);">
        No subjects found for {{ $selectedGrade }} in {{ $selectedYear->year_label }}.<br>
        <span style="font-size:.8rem;">Configure curriculum mappings in Admin → Curriculum first.</span>
      </div>
    @else

      {{-- Unmet prerequisite notice --}}
      @if(!empty($unmetNames))
      <div style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:.75rem 1rem;font-size:.84rem;color:#92400e;margin-bottom:1rem;">
        <strong>Prerequisite issues:</strong> {{ count($unmetNames) }} subject(s) have unmet prerequisites and cannot be added to the plan.
      </div>
      @endif

      {{-- Required subjects --}}
      @php $required = $mappings->where('is_required', true); $elective = $mappings->where('is_required', false); @endphp

      @if($required->isNotEmpty())
      <div class="enc-card" style="margin-bottom:1rem;padding:0;overflow:hidden;">
        <div style="padding:.75rem 1.1rem;border-bottom:1px solid rgba(15,23,42,.07);display:flex;align-items:center;gap:.5rem;">
          <span style="font-size:.8rem;font-weight:800;color:var(--navy);text-transform:uppercase;letter-spacing:.05em;">Required Subjects</span>
          <span style="display:inline-block;background:#dbeafe;color:#1e40af;border-radius:99px;font-size:.68rem;font-weight:700;padding:.1rem .45rem;">{{ $required->count() }}</span>
        </div>
        <table style="width:100%;border-collapse:collapse;">
          @foreach($required as $m)
          @php
            $subjectId  = $m->subject?->id;
            $subjName   = $m->subject?->subject_name ?? '—';
            $inPlan     = isset($planSubjectIds[$subjectId]);
            $planEntry  = $plan->firstWhere('subject_id', $subjectId);
            $isBlocked  = isset($unmetNames[$subjName]);
            $prereqInfo = $isBlocked ? $unmetNames[$subjName] : null;
          @endphp
          <tr style="border-bottom:1px solid rgba(15,23,42,.04);">
            <td style="padding:11px 14px;width:100%;">
              <div style="font-weight:700;color:var(--navy);font-size:.88rem;">{{ $subjName }}</div>
              <div style="font-size:.75rem;color:var(--gray-400);margin-top:2px;">{{ $m->subject?->subject_code }}</div>
              @if($m->prerequisiteSubject)
              <div style="font-size:.73rem;color:{{ $isBlocked ? '#dc2626' : '#6b7280' }};margin-top:3px;">
                @if($isBlocked)
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;display:inline;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                  Prereq not met: {{ $prereqInfo['requires'] }} (need {{ $prereqInfo['min_grade'] }}{{ $prereqInfo['student_grade'] !== null ? ', got '.$prereqInfo['student_grade'] : ', not taken' }})
                @else
                  Prereq: {{ $m->prerequisiteSubject->subject_name }}
                @endif
              </div>
              @endif
            </td>
            <td style="padding:11px 14px;text-align:right;white-space:nowrap;">
              @if($inPlan)
                <span class="adv-in-plan" data-plan-id="{{ $planEntry?->id }}" data-subject-id="{{ $subjectId }}">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                  In Plan
                </span>
                @if(optional($planEntry)->status !== 'confirmed')
                <button type="button" class="adv-remove-btn" data-plan-id="{{ $planEntry?->id }}" style="margin-left:.5rem;">Remove</button>
                @endif
              @elseif($isBlocked)
                <span style="font-size:.77rem;color:#dc2626;font-weight:600;opacity:.7;">Blocked</span>
              @else
                <button type="button" class="adv-add-btn"
                  data-subject-id="{{ $subjectId }}"
                  data-year-id="{{ $selectedYearId }}"
                  data-grade="{{ $selectedGrade }}">
                  + Add
                </button>
              @endif
            </td>
          </tr>
          @endforeach
        </table>
      </div>
      @endif

      {{-- Elective subjects --}}
      @if($elective->isNotEmpty())
      <div class="enc-card" style="padding:0;overflow:hidden;">
        <div style="padding:.75rem 1.1rem;border-bottom:1px solid rgba(15,23,42,.07);display:flex;align-items:center;gap:.5rem;">
          <span style="font-size:.8rem;font-weight:800;color:var(--navy);text-transform:uppercase;letter-spacing:.05em;">Elective Subjects</span>
          <span style="display:inline-block;background:#fef3c7;color:#92400e;border-radius:99px;font-size:.68rem;font-weight:700;padding:.1rem .45rem;">{{ $elective->count() }}</span>
        </div>
        <table style="width:100%;border-collapse:collapse;">
          @foreach($elective as $m)
          @php
            $subjectId  = $m->subject?->id;
            $subjName   = $m->subject?->subject_name ?? '—';
            $inPlan     = isset($planSubjectIds[$subjectId]);
            $planEntry  = $plan->firstWhere('subject_id', $subjectId);
            $isBlocked  = isset($unmetNames[$subjName]);
            $prereqInfo = $isBlocked ? $unmetNames[$subjName] : null;
          @endphp
          <tr style="border-bottom:1px solid rgba(15,23,42,.04);">
            <td style="padding:11px 14px;width:100%;">
              <div style="font-weight:700;color:var(--navy);font-size:.88rem;">{{ $subjName }}</div>
              <div style="font-size:.75rem;color:var(--gray-400);margin-top:2px;">{{ $m->subject?->subject_code }} · Elective</div>
              @if($m->prerequisiteSubject)
              <div style="font-size:.73rem;color:{{ $isBlocked ? '#dc2626' : '#6b7280' }};margin-top:3px;">
                @if($isBlocked)
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;display:inline;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                  Prereq not met: {{ $prereqInfo['requires'] }} (need {{ $prereqInfo['min_grade'] }}{{ $prereqInfo['student_grade'] !== null ? ', got '.$prereqInfo['student_grade'] : ', not taken' }})
                @else
                  Prereq: {{ $m->prerequisiteSubject->subject_name }}
                @endif
              </div>
              @endif
            </td>
            <td style="padding:11px 14px;text-align:right;white-space:nowrap;">
              @if($inPlan)
                <span class="adv-in-plan" data-plan-id="{{ $planEntry?->id }}" data-subject-id="{{ $subjectId }}">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                  In Plan
                </span>
                @if(optional($planEntry)->status !== 'confirmed')
                <button type="button" class="adv-remove-btn" data-plan-id="{{ $planEntry?->id }}" style="margin-left:.5rem;">Remove</button>
                @endif
              @elseif($isBlocked)
                <span style="font-size:.77rem;color:#dc2626;font-weight:600;opacity:.7;">Blocked</span>
              @else
                <button type="button" class="adv-add-btn"
                  data-subject-id="{{ $subjectId }}"
                  data-year-id="{{ $selectedYearId }}"
                  data-grade="{{ $selectedGrade }}">
                  + Add
                </button>
              @endif
            </td>
          </tr>
          @endforeach
        </table>
      </div>
      @endif

    @endif
  </div>

  {{-- ── Right: Plan Sidebar ── --}}
  <div style="position:sticky;top:80px;">

    {{-- Plan summary card --}}
    <div class="enc-card" style="padding:0;overflow:hidden;margin-bottom:1rem;">
      <div style="padding:.75rem 1.1rem;border-bottom:1px solid rgba(15,23,42,.07);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:.8rem;font-weight:800;color:var(--navy);text-transform:uppercase;letter-spacing:.05em;">Enrollment Plan</span>
        <span id="planCount" style="display:inline-block;background:#f1f5f9;color:var(--gray-500);border-radius:99px;font-size:.68rem;font-weight:700;padding:.1rem .45rem;">{{ $plan->count() }}</span>
      </div>

      <div id="planList" style="max-height:340px;overflow-y:auto;">
        @forelse($plan as $entry)
        <div class="adv-plan-item" data-plan-id="{{ $entry->id }}" style="padding:10px 14px;border-bottom:1px solid rgba(15,23,42,.04);display:flex;align-items:center;gap:.5rem;">
          <div style="flex:1;min-width:0;">
            <div style="font-size:.82rem;font-weight:700;color:var(--navy);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $entry->subject?->subject_name ?? '—' }}</div>
            <div style="font-size:.71rem;color:var(--gray-400);">{{ $entry->subject?->subject_code }}</div>
          </div>
          @if($entry->status === 'confirmed')
            <span style="font-size:.7rem;font-weight:700;color:#166534;background:#dcfce7;padding:.15rem .45rem;border-radius:99px;flex-shrink:0;">✓</span>
          @else
            <button type="button" class="adv-remove-btn" data-plan-id="{{ $entry->id }}"
              style="font-size:.7rem;color:#dc2626;background:none;border:none;cursor:pointer;flex-shrink:0;padding:.15rem .3rem;"
              title="Remove">✕</button>
          @endif
        </div>
        @empty
        <div id="planEmptyMsg" style="padding:1.5rem;text-align:center;color:var(--gray-400);font-size:.82rem;">
          No subjects in plan yet.<br>
          <span style="font-size:.75rem;">Click "+ Add" on any subject.</span>
        </div>
        @endforelse
      </div>
    </div>

    {{-- Confirm plan action --}}
    @php $pendingPlanCount = $plan->where('status','pending')->count(); @endphp
    @if($pendingPlanCount > 0)
    <form method="POST" action="{{ route('registrar.advising.confirm', $student->id) }}"
          onsubmit="return confirm('Confirm the enrollment plan? This will lock {{ $pendingPlanCount }} pending subject(s).');">
      @csrf
      <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">
      <button type="submit"
        style="width:100%;padding:.65rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:700;font-size:.88rem;cursor:pointer;">
        Confirm Plan ({{ $pendingPlanCount }} pending)
      </button>
    </form>
    @elseif($plan->isNotEmpty())
    <div style="text-align:center;font-size:.8rem;color:#166534;font-weight:700;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:.65rem;">
      All subjects confirmed
    </div>
    @endif

    {{-- Info box --}}
    <div style="margin-top:.85rem;padding:.85rem 1rem;background:#f8fafc;border-radius:8px;border:1px solid rgba(15,23,42,.07);">
      <div style="font-size:.75rem;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.04em;margin-bottom:.4rem;">How It Works</div>
      <ol style="margin:0;padding-left:1.1rem;font-size:.78rem;color:var(--gray-500);line-height:1.7;">
        <li>Select academic year &amp; target grade level.</li>
        <li>Add subjects to the plan (blocked = prereq not met).</li>
        <li>Confirm the plan to lock it in.</li>
        <li>Use Enrollment to assign the student to a section.</li>
      </ol>
    </div>

  </div>
</div>
@endsection

@push('head')
<style>
.adv-label {
  display: block;
  font-size: .72rem;
  font-weight: 700;
  color: var(--gray-500);
  text-transform: uppercase;
  letter-spacing: .04em;
  margin-bottom: .3rem;
}
.adv-select {
  padding: .45rem .75rem;
  border: 1px solid rgba(15,23,42,.14);
  border-radius: 8px;
  font-size: .87rem;
  background: #fff;
  font-family: inherit;
  outline: none;
  cursor: pointer;
}
.adv-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.adv-add-btn {
  font-size: .78rem;
  font-weight: 700;
  color: var(--primary);
  background: none;
  border: 1px solid var(--primary);
  border-radius: 6px;
  padding: .3rem .65rem;
  cursor: pointer;
  transition: all .1s;
  white-space: nowrap;
}
.adv-add-btn:hover { background: var(--primary); color: #fff; }
.adv-add-btn:disabled { opacity: .45; cursor: default; pointer-events: none; }
.adv-remove-btn {
  font-size: .75rem;
  font-weight: 700;
  color: #dc2626;
  background: none;
  border: 1px solid #fca5a5;
  border-radius: 6px;
  padding: .25rem .55rem;
  cursor: pointer;
  transition: all .1s;
  white-space: nowrap;
}
.adv-remove-btn:hover { background: #fee2e2; }
.adv-in-plan {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  font-size: .78rem;
  font-weight: 700;
  color: #166534;
  background: #dcfce7;
  border-radius: 6px;
  padding: .28rem .6rem;
  white-space: nowrap;
}
.adv-plan-item { transition: background .1s; }
.adv-plan-item:hover { background: rgba(15,23,42,.015); }
</style>
@endpush

@push('scripts')
<script>
(function () {
  const studentId = {{ $student->id }};
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  // Flash a toast message
  function toast(msg, ok = true) {
    const div = document.createElement('div');
    div.textContent = msg;
    div.style.cssText = `position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;padding:.65rem 1rem;border-radius:8px;font-size:.84rem;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,.12);background:${ok?'#f0fdf4':'#fef2f2'};color:${ok?'#166534':'#991b1b'};border:1px solid ${ok?'#86efac':'#fca5a5'};`;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
  }

  // Update the plan count badge
  function syncCount() {
    const items = document.querySelectorAll('.adv-plan-item');
    const badge = document.getElementById('planCount');
    if (badge) badge.textContent = items.length;
  }

  // Handle Add Subject
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.adv-add-btn');
    if (!btn) return;

    btn.disabled = true;
    const orig = btn.textContent;
    btn.textContent = '…';

    fetch(`/registrar/advising/${studentId}/add-subject`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      body: JSON.stringify({
        subject_id:       parseInt(btn.dataset.subjectId),
        academic_year_id: parseInt(btn.dataset.yearId),
        grade_level:      btn.dataset.grade,
      }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        // Replace the add button with an "In Plan" badge + remove button in the grid
        const cell = btn.parentElement;
        const subjName = btn.closest('tr')?.querySelector('td:first-child div')?.textContent?.trim() ?? '';
        const subjCode = btn.closest('tr')?.querySelector('td:first-child div:nth-child(2)')?.textContent?.trim() ?? '';

        cell.innerHTML = `
          <span class="adv-in-plan" data-plan-id="${data.plan_id}" data-subject-id="${btn.dataset.subjectId}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            In Plan
          </span>
          <button type="button" class="adv-remove-btn" data-plan-id="${data.plan_id}" style="margin-left:.5rem;">Remove</button>`;

        // Add to plan list sidebar
        const emptyMsg = document.getElementById('planEmptyMsg');
        if (emptyMsg) emptyMsg.remove();

        const planList = document.getElementById('planList');
        if (planList) {
          const item = document.createElement('div');
          item.className = 'adv-plan-item';
          item.dataset.planId = data.plan_id;
          item.style.cssText = 'padding:10px 14px;border-bottom:1px solid rgba(15,23,42,.04);display:flex;align-items:center;gap:.5rem;';
          item.innerHTML = `
            <div style="flex:1;min-width:0;">
              <div style="font-size:.82rem;font-weight:700;color:var(--navy);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${subjName}</div>
              <div style="font-size:.71rem;color:var(--gray-400);">${subjCode}</div>
            </div>
            <button type="button" class="adv-remove-btn" data-plan-id="${data.plan_id}"
              style="font-size:.7rem;color:#dc2626;background:none;border:none;cursor:pointer;flex-shrink:0;padding:.15rem .3rem;" title="Remove">✕</button>`;
          planList.appendChild(item);
          syncCount();
        }

        toast('Subject added to plan.');
      } else {
        btn.disabled = false;
        btn.textContent = orig;
        toast(data.message || 'Could not add subject.', false);
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = orig;
      toast('Network error. Please try again.', false);
    });
  });

  // Handle Remove Subject
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.adv-remove-btn');
    if (!btn) return;

    const planId = btn.dataset.planId;
    if (!confirm('Remove this subject from the plan?')) return;

    fetch(`/registrar/advising/${studentId}/remove-subject`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      body: JSON.stringify({ plan_id: parseInt(planId) }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        // Remove from plan sidebar
        const sidebarItem = document.querySelector(`.adv-plan-item[data-plan-id="${planId}"]`);
        if (sidebarItem) sidebarItem.remove();

        // If plan list is now empty, show empty message
        const planList = document.getElementById('planList');
        if (planList && planList.children.length === 0) {
          planList.innerHTML = `<div id="planEmptyMsg" style="padding:1.5rem;text-align:center;color:var(--gray-400);font-size:.82rem;">No subjects in plan yet.<br><span style="font-size:.75rem;">Click "+ Add" on any subject.</span></div>`;
        }

        syncCount();

        // In the grid, find the row by plan-id badge and restore the add button
        const gridBadge = document.querySelector(`.adv-in-plan[data-plan-id="${planId}"]`);
        if (gridBadge) {
          const subjectId = gridBadge.dataset.subjectId;
          const yearId    = document.querySelector('select[name="year_id"]')?.value ?? '';
          const grade     = document.querySelector('select[name="grade_level"]')?.value ?? '';
          const cell      = gridBadge.closest('td');
          if (cell) {
            cell.innerHTML = `<button type="button" class="adv-add-btn" data-subject-id="${subjectId}" data-year-id="${yearId}" data-grade="${grade}">+ Add</button>`;
          }
        }

        // Also handle remove buttons that are separate from the badge
        const gridRemove = document.querySelector(`button.adv-remove-btn[data-plan-id="${planId}"]`);
        if (gridRemove) gridRemove.remove();

        toast('Subject removed from plan.');
      } else {
        toast(data.message || 'Could not remove subject.', false);
      }
    })
    .catch(() => toast('Network error. Please try again.', false));
  });
})();
</script>
@endpush
