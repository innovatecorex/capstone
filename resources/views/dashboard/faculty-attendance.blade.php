@extends('layouts.app')
@section('title', 'Attendance')
@section('breadcrumb', 'Attendance')

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Attendance</h1>
      <p class="enc-page__subtitle">Mark daily attendance for each of your assigned classes.</p>
    </div>
  </div>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div style="margin-bottom:20px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;color:#166534;font-size:.9rem;font-weight:500;">
  {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="margin-bottom:20px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;color:#991b1b;font-size:.9rem;font-weight:500;">
  @foreach($errors->all() as $err)
    <div>{{ $err }}</div>
  @endforeach
</div>
@endif

@if($allSchedules->isEmpty())
<div class="enc-card">
  <div class="enc-card__body" style="padding:48px 32px;text-align:center;">
    <div style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:8px;">No assigned classes</div>
    <p style="font-size:.875rem;color:#64748b;margin:0 auto;max-width:420px;">
      You don't have any section-subject assignments for the active academic year yet.
      Please contact the Registrar to set up your teaching load.
    </p>
  </div>
</div>
@else

{{-- Class & date picker --}}
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__header">
    <div class="enc-card__title">Select Class &amp; Date</div>
  </div>
  <div class="enc-card__body" style="padding:20px 24px;">
    <form method="GET" action="{{ route('faculty.attendance') }}" style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
      <div style="display:flex;flex-direction:column;gap:6px;min-width:280px;flex:1;">
        <label style="font-size:.8rem;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Class</label>
        <select name="section_subject_id" onchange="this.form.submit()" style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
          <option value="">— Select a class —</option>
          @foreach($allSchedules as $sched)
            <option value="{{ $sched->id }}" {{ optional($selectedSchedule)->id == $sched->id ? 'selected' : '' }}>
              {{ $sched->subject->subject_name ?? $sched->subject_name ?? 'Subject' }}
              — {{ $sched->section->section_name ?? 'No Section' }}
              ({{ $sched->schedule_days_label ?? '' }} {{ $sched->time_range ?? '' }})
            </option>
          @endforeach
        </select>
      </div>

      <div style="display:flex;flex-direction:column;gap:6px;min-width:180px;">
        <label style="font-size:.8rem;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:.04em;">Date</label>
        <input type="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}"
               onchange="this.form.submit()"
               style="padding:8px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:.9rem;">
      </div>
    </form>
  </div>
</div>

{{-- Session History (past recorded dates for this class) --}}
@if($selectedSchedule && $sessionDates->isNotEmpty())
<div class="enc-card" style="margin-bottom:24px;">
  <div class="enc-card__header" style="display:flex;align-items:center;justify-content:space-between;">
    <div class="enc-card__title">Session History</div>
    <span style="font-size:.78rem;color:#64748b;">Click a date to view or edit that session's attendance</span>
  </div>
  <div class="enc-card__body" style="padding:14px 20px;">
    <div style="display:flex;flex-wrap:wrap;gap:.45rem;">
      @foreach($sessionDates as $sd)
      @php $isActive = $sd->toDateString() === $date; @endphp
      <a href="{{ route('faculty.attendance', ['section_subject_id' => $selectedSchedule->id, 'date' => $sd->toDateString()]) }}"
         style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .75rem;border-radius:999px;font-size:.78rem;font-weight:600;text-decoration:none;transition:all .15s;
                {{ $isActive ? 'background:#1d4ed8;color:#fff;border:1px solid #1d4ed8;' : 'background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;' }}">
        <span style="font-weight:700;">{{ $sd->format('D') }}</span>
        <span>{{ $sd->format('M d') }}</span>
      </a>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- Roster --}}
@if($selectedSchedule)
  @if($roster->isEmpty())
  <div class="enc-card">
    <div class="enc-card__body" style="padding:40px 32px;text-align:center;">
      <div style="font-size:.95rem;color:#64748b;">
        No students are currently enrolled in this section.
      </div>
    </div>
  </div>
  @else
  <form method="POST" action="{{ route('faculty.attendance.store') }}">
    @csrf
    <input type="hidden" name="section_subject_id" value="{{ $selectedSchedule->id }}">
    <input type="hidden" name="date" value="{{ $date }}">

    <div class="enc-card">
      <div class="enc-card__header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <div>
          <div class="enc-card__title">Roster — {{ \Carbon\Carbon::parse($date)->format('F j, Y (l)') }}</div>
          <div style="font-size:.8rem;color:#64748b;margin-top:4px;">
            {{ $roster->count() }} student(s) enrolled
            @php $marked = $roster->filter(fn($r) => $r->has_record)->count(); @endphp
            @if($marked > 0) · {{ $marked }} already marked for this date @endif
          </div>
        </div>

        {{-- Bulk-action helper buttons --}}
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <button type="button" onclick="markAll('present')"
                  style="padding:.4rem .85rem;border:1px solid #86efac;border-radius:8px;background:#f0fdf4;color:#166534;font-size:.8rem;font-weight:600;cursor:pointer;">
            All Present
          </button>
          <button type="button" onclick="markAll('absent')"
                  style="padding:.4rem .85rem;border:1px solid #fca5a5;border-radius:8px;background:#fef2f2;color:#991b1b;font-size:.8rem;font-weight:600;cursor:pointer;">
            All Absent
          </button>
          <button type="button" onclick="markAll('late')"
                  style="padding:.4rem .85rem;border:1px solid #fcd34d;border-radius:8px;background:#fffbeb;color:#92400e;font-size:.8rem;font-weight:600;cursor:pointer;">
            All Late
          </button>
          <button type="button" onclick="markAll('excused')"
                  style="padding:.4rem .85rem;border:1px solid #93c5fd;border-radius:8px;background:#eff6ff;color:#1e40af;font-size:.8rem;font-weight:600;cursor:pointer;">
            All Excused
          </button>
          <button type="button" onclick="clearAll()"
                  style="padding:.4rem .85rem;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;color:#64748b;font-size:.8rem;font-weight:600;cursor:pointer;"
                  title="Remove all status markings for this date">
            Clear All
          </button>
        </div>
      </div>

      <div class="enc-card__body" style="padding:0;">
        <div style="overflow-x:auto;">
          <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
            <thead>
              <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <th style="padding:12px 16px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Student</th>
                <th style="padding:12px 16px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">LRN</th>
                <th style="padding:12px 16px;text-align:center;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">Status</th>
                <th style="padding:12px 16px;text-align:left;font-size:.72rem;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;">
                  Remarks
                  <span style="color:#e11d48;margin-left:2px;" title="Required for absent, late, or excused">*</span>
                  <span style="font-weight:400;color:#94a3b8;margin-left:4px;">(required for absent/late/excused)</span>
                </th>
              </tr>
            </thead>
            <tbody>
              @foreach($roster as $i => $row)
              <tr style="border-bottom:1px solid #f1f5f9;" id="att-row-{{ $i }}">
                <td style="padding:12px 16px;color:#0f172a;font-weight:500;">
                  {{ $row->student->last_name }}, {{ $row->student->first_name }}
                  <input type="hidden" name="attendance[{{ $i }}][enrollment_id]" value="{{ $row->enrollment->id }}">
                </td>
                <td style="padding:12px 16px;color:#64748b;font-family:monospace;font-size:.82rem;">
                  {{ $row->student->lrn }}
                </td>
                <td style="padding:12px 16px;text-align:center;">
                  <div style="display:inline-flex;gap:6px;flex-wrap:wrap;justify-content:center;align-items:center;">
                    @foreach(['present' => ['#166534','#86efac','#f0fdf4'], 'absent' => ['#991b1b','#fca5a5','#fef2f2'], 'late' => ['#92400e','#fcd34d','#fffbeb'], 'excused' => ['#1e40af','#93c5fd','#eff6ff']] as $opt => $colors)
                      <label style="cursor:pointer;">
                        <input type="radio" name="attendance[{{ $i }}][status]" value="{{ $opt }}"
                               {{ ($row->status ?? '') === $opt ? 'checked' : '' }}
                               class="att-status att-status-{{ $i }}"
                               style="display:none;"
                               onchange="updateLabel({{ $i }}); updateRemarks({{ $i }})">
                        <span class="att-label att-label-{{ $i }}-{{ $opt }}"
                              data-status="{{ $opt }}"
                              style="display:inline-block;padding:.3rem .7rem;border-radius:6px;font-size:.74rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;cursor:pointer;
                                     color:{{ ($row->status ?? '') === $opt ? $colors[0] : '#94a3b8' }};
                                     background:{{ ($row->status ?? '') === $opt ? $colors[2] : '#f8fafc' }};
                                     border:1px solid {{ ($row->status ?? '') === $opt ? $colors[1] : '#e2e8f0' }};">{{ $opt }}</span>
                      </label>
                    @endforeach

                    {{-- Clear/remove button --}}
                    <button type="button" onclick="clearRow({{ $i }})"
                            title="Remove attendance for this student"
                            style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:6px;border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;font-size:.85rem;cursor:pointer;line-height:1;padding:0;"
                            id="clear-btn-{{ $i }}">✕</button>
                  </div>
                </td>
                <td style="padding:12px 16px;">
                  <input type="text" name="attendance[{{ $i }}][remarks]"
                         id="remarks-{{ $i }}"
                         value="{{ $row->remarks }}"
                         maxlength="255"
                         placeholder="e.g. doctor's note"
                         style="width:100%;padding:6px 10px;border:1px solid #e2e8f0;border-radius:6px;font-size:.82rem;transition:border-color .15s;">
                  <div id="remarks-hint-{{ $i }}" style="font-size:.72rem;color:#e11d48;margin-top:3px;display:none;">
                    Remarks required for this status.
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div style="padding:16px 24px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;">
        <button type="submit" style="padding:.6rem 1.4rem;border:none;border-radius:8px;background:#065f46;color:#fff;font-size:.875rem;font-weight:700;cursor:pointer;">
          Save Attendance
        </button>
      </div>
    </div>
  </form>
  @endif
@else
  <div class="enc-card">
    <div class="enc-card__body" style="padding:40px 32px;text-align:center;">
      <div style="font-size:.95rem;color:#64748b;">
        Select a class above to start marking attendance.
      </div>
    </div>
  </div>
@endif

@endif

@endsection

@push('scripts')
<script>
const ATT_COLORS = {
  present: ['#166534','#86efac','#f0fdf4'],
  absent:  ['#991b1b','#fca5a5','#fef2f2'],
  late:    ['#92400e','#fcd34d','#fffbeb'],
  excused: ['#1e40af','#93c5fd','#eff6ff'],
};
const REMARKS_REQUIRED = ['absent','late','excused'];

function markAll(status) {
  document.querySelectorAll('.att-status').forEach(el => {
    const m = el.name.match(/attendance\[(\d+)\]/);
    if (!m) return;
    const radio = document.querySelector(`input[name="${el.name}"][value="${status}"]`);
    if (radio) {
      radio.checked = true;
      const idx = parseInt(m[1], 10);
      updateLabel(idx);
      updateRemarks(idx);
    }
  });
}

function clearAll() {
  const rows = document.querySelectorAll('[id^="att-row-"]');
  rows.forEach(row => {
    const m = row.id.match(/att-row-(\d+)/);
    if (m) clearRow(parseInt(m[1], 10));
  });
}

function clearRow(rowIdx) {
  document.querySelectorAll(`input[name="attendance[${rowIdx}][status]"]`).forEach(r => r.checked = false);
  updateLabel(rowIdx);
  updateRemarks(rowIdx);
}

function updateLabel(rowIdx) {
  const checkedRadio = document.querySelector(`input[name="attendance[${rowIdx}][status]"]:checked`);
  const selectedStatus = checkedRadio ? checkedRadio.value : null;

  ['present','absent','late','excused'].forEach(opt => {
    const labels = document.querySelectorAll(`.att-label-${rowIdx}-${opt}`);
    const c = ATT_COLORS[opt];
    labels.forEach(lbl => {
      if (opt === selectedStatus) {
        lbl.style.color       = c[0];
        lbl.style.background  = c[2];
        lbl.style.borderColor = c[1];
      } else {
        lbl.style.color       = '#94a3b8';
        lbl.style.background  = '#f8fafc';
        lbl.style.borderColor = '#e2e8f0';
      }
    });
  });
}

function updateRemarks(rowIdx) {
  const checkedRadio = document.querySelector(`input[name="attendance[${rowIdx}][status]"]:checked`);
  const status = checkedRadio ? checkedRadio.value : null;
  const remarkInput = document.getElementById(`remarks-${rowIdx}`);
  const hint = document.getElementById(`remarks-hint-${rowIdx}`);
  if (!remarkInput) return;
  const required = REMARKS_REQUIRED.includes(status);
  remarkInput.required = required;
  remarkInput.style.borderColor = required ? '#fca5a5' : '#e2e8f0';
  if (hint) hint.style.display = required ? 'block' : 'none';
  // If re-filled, remove red border
  remarkInput.oninput = () => {
    if (remarkInput.value.trim()) remarkInput.style.borderColor = '#86efac';
    else if (required) remarkInput.style.borderColor = '#fca5a5';
  };
}

// Initialise all rows on page load
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[id^="att-row-"]').forEach(row => {
    const m = row.id.match(/att-row-(\d+)/);
    if (m) {
      const idx = parseInt(m[1], 10);
      updateLabel(idx);
      updateRemarks(idx);
    }
  });
});
</script>
@endpush
