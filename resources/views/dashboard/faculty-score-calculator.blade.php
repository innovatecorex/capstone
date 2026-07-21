@extends('layouts.app')
@section('title', 'Score Calculator')
@section('breadcrumb', 'Score Calculator')

@section('content')
<style>
  .sc-wrap { max-width: 1100px; margin: 0 auto; }
  .sc-head { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px 24px; margin-bottom:20px; }
  .sc-title { font-size:1.15rem; font-weight:800; color:#0f172a; }
  .sc-sub { font-size:.85rem; color:#64748b; margin-top:4px; }
  .sc-note { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; font-size:.8rem; border-radius:10px; padding:10px 14px; margin-top:14px; line-height:1.5; }
  .sc-tabs { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:18px; }
  .sc-tab { padding:7px 14px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; font-size:.8rem; font-weight:600; color:#475569; cursor:pointer; }
  .sc-tab.active { background:#4f46e5; color:#fff; border-color:#4f46e5; }
  .sc-panel { display:none; }
  .sc-panel.active { display:block; }
  .sc-student { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px 18px; margin-bottom:12px; }
  .sc-student__name { font-weight:700; color:#0f172a; font-size:.92rem; margin-bottom:10px; }
  .sc-items { display:flex; flex-wrap:wrap; gap:8px; align-items:flex-end; }
  .sc-item { display:flex; flex-direction:column; gap:3px; }
  .sc-item label { font-size:.68rem; color:#94a3b8; font-weight:600; }
  .sc-item input { width:78px; padding:5px 8px; border:1px solid #cbd5e1; border-radius:7px; font-size:.85rem; }
  .sc-item input.lbl { width:70px; }
  .sc-avg { margin-left:auto; text-align:right; }
  .sc-avg__lbl { font-size:.68rem; color:#94a3b8; font-weight:600; text-transform:uppercase; }
  .sc-avg__val { font-size:1.25rem; font-weight:800; color:#4f46e5; }
  .sc-add { background:#eef2ff; color:#4338ca; border:1px dashed #a5b4fc; border-radius:7px; padding:5px 10px; font-size:.75rem; font-weight:700; cursor:pointer; }
  .sc-del { background:none; border:none; color:#dc2626; cursor:pointer; font-size:.9rem; padding:0 2px; }
  .sc-save { position:sticky; bottom:16px; margin-top:20px; display:flex; justify-content:flex-end; }
  .sc-save button { background:#16a34a; color:#fff; border:none; border-radius:10px; padding:11px 26px; font-size:.9rem; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(22,163,74,.3); }
  .sc-qsel { padding:7px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:.85rem; }
</style>

<div class="sc-wrap">
  <div class="sc-head">
    <div class="sc-title">Score Calculator — {{ $ss->subject->subject_name ?? 'Subject' }}</div>
    <div class="sc-sub">{{ $ss->section->grade_level ?? '' }} · {{ $ss->section->section_name ?? '' }}</div>
    <div class="sc-note">
      Enter each student's individual scores under a component (e.g. four homeworks). The average is computed automatically.
      Then <strong>manually type that average into the Gradebook</strong>. This worksheet saves and persists, but does not
      change gradebook grades on its own.
    </div>
    <form method="GET" style="margin-top:14px;">
      <label style="font-size:.8rem;color:#475569;font-weight:600;margin-right:8px;">Quarter:</label>
      <select name="quarter_id" class="sc-qsel" onchange="this.form.submit()">
        @foreach($allQuarters as $q)
        <option value="{{ $q->id }}" {{ $quarter && $q->id === $quarter->id ? 'selected' : '' }}>{{ $q->quarter_name }}</option>
        @endforeach
      </select>
    </form>
  </div>

  @if(!$quarter)
    <div class="sc-note">No active grading quarter. Set one to use the calculator.</div>
  @elseif($enrollments->isEmpty())
    <div class="sc-note">No enrolled students in this section yet.</div>
  @else
  <form method="POST" action="{{ route('faculty.gradebook.calculator.save', $ss) }}" id="calc-form">
    @csrf
    <input type="hidden" name="quarter_id" value="{{ $quarter->id }}">

    {{-- Component tabs --}}
    <div class="sc-tabs">
      @foreach($components as $key => $label)
      <div class="sc-tab {{ $loop->first ? 'active' : '' }}" data-tab="{{ $key }}" onclick="scSwitch('{{ $key }}')">{{ $label }}</div>
      @endforeach
    </div>

    @foreach($components as $key => $label)
    <div class="sc-panel {{ $loop->first ? 'active' : '' }}" id="panel-{{ $key }}">
      @foreach($enrollments as $enr)
        @php $rows = $saved->get($enr->id . '|' . $key); @endphp
      <div class="sc-student" data-student="{{ $enr->id }}" data-comp="{{ $key }}">
        <div class="sc-student__name">{{ $enr->student->full_name ?? 'Student #' . $enr->id }}</div>
        <div class="sc-items">
          <div class="sc-rows" style="display:flex;flex-wrap:wrap;gap:8px;">
            @if($rows)
              @foreach($rows as $r)
              <div class="sc-item">
                <label>Item</label>
                <input type="text" class="lbl" name="rows[{{ $enr->id }}][{{ $key }}][{{ $loop->index }}][label]" value="{{ $r->item_label }}" placeholder="e.g. HW1">
                <input type="number" step="0.01" min="0" max="100" class="sc-score" name="rows[{{ $enr->id }}][{{ $key }}][{{ $loop->index }}][score]" value="{{ $r->score }}" placeholder="0-100" oninput="scAvg(this)">
                <button type="button" class="sc-del" onclick="scDel(this)">✕</button>
              </div>
              @endforeach
            @endif
          </div>
          <button type="button" class="sc-add" onclick="scAdd(this,'{{ $enr->id }}','{{ $key }}')">+ Add score</button>
          <div class="sc-avg">
            <div class="sc-avg__lbl">Average</div>
            <div class="sc-avg__val">—</div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endforeach

    <div class="sc-save">
      <button type="submit">Save Scores</button>
    </div>
  </form>
  @endif
</div>

<script>
  function scSwitch(key) {
    document.querySelectorAll('.sc-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === key));
    document.querySelectorAll('.sc-panel').forEach(p => p.classList.toggle('active', p.id === 'panel-' + key));
  }

  function scAvg(input) {
    const card = input.closest('.sc-student');
    const scores = [...card.querySelectorAll('.sc-score')]
      .map(i => parseFloat(i.value))
      .filter(v => !isNaN(v));
    const avgEl = card.querySelector('.sc-avg__val');
    if (scores.length === 0) { avgEl.textContent = '—'; return; }
    const avg = scores.reduce((a, b) => a + b, 0) / scores.length;
    avgEl.textContent = (Math.round(avg * 100) / 100).toFixed(2);
  }

  function scAdd(btn, enrId, comp) {
    const card = btn.closest('.sc-student');
    const rows = card.querySelector('.sc-rows');
    const idx = rows.querySelectorAll('.sc-item').length;
    const div = document.createElement('div');
    div.className = 'sc-item';
    div.innerHTML =
      '<label>Item</label>' +
      '<input type="text" class="lbl" name="rows[' + enrId + '][' + comp + '][' + idx + '][label]" placeholder="e.g. HW1">' +
      '<input type="number" step="0.01" min="0" max="100" class="sc-score" name="rows[' + enrId + '][' + comp + '][' + idx + '][score]" placeholder="0-100" oninput="scAvg(this)">' +
      '<button type="button" class="sc-del" onclick="scDel(this)">\u2715</button>';
    rows.appendChild(div);
  }

  function scDel(btn) {
    const card = btn.closest('.sc-student');
    btn.closest('.sc-item').remove();
    const anyScore = card.querySelector('.sc-score');
    scAvg(anyScore || document.createElement('input'));
    // recompute even if none left
    const avgEl = card.querySelector('.sc-avg__val');
    const scores = [...card.querySelectorAll('.sc-score')].map(i => parseFloat(i.value)).filter(v => !isNaN(v));
    avgEl.textContent = scores.length ? (Math.round(scores.reduce((a,b)=>a+b,0)/scores.length*100)/100).toFixed(2) : '—';
  }

  // Compute averages for saved data on load.
  document.querySelectorAll('.sc-student').forEach(card => {
    const s = card.querySelector('.sc-score');
    if (s) scAvg(s);
  });
</script>
@endsection
