@extends('layouts.app')
@section('title', 'Assessment & Finalization')
@section('breadcrumb', 'Assessment & Finalization')

@push('head')
<style>
/* ── Quarter tabs ────────────────────────── */
.acm-qtabs { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:20px; }
.acm-qtab {
  padding:.38rem 1rem; border-radius:999px; font-size:.8rem; font-weight:700;
  border:1px solid #e2e8f0; background:#fff; color:#64748b;
  text-decoration:none; transition:all .15s; white-space:nowrap;
}
.acm-qtab:hover { border-color:#6366f1; color:#4f46e5; }
.acm-qtab.active { background:#4f46e5; color:#fff; border-color:#4f46e5; }

/* ── Stat strip ──────────────────────────── */
.acm-stats { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:20px; }
@media(max-width:860px){ .acm-stats{ grid-template-columns:repeat(3,1fr); } }
.acm-stat { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; }
.acm-stat__num { font-size:1.5rem; font-weight:800; line-height:1.1; margin-bottom:4px; }
.acm-stat__lbl { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#64748b; }

/* ── Action bar ──────────────────────────── */
.acm-action-bar {
  display:flex; align-items:center; justify-content:space-between;
  flex-wrap:wrap; gap:10px; background:#fff;
  border:1px solid #e2e8f0; border-radius:12px; padding:14px 20px; margin-bottom:20px;
}
.acm-action-bar__info { font-size:.84rem; color:#475569; }
.acm-action-bar__info strong { color:#0f172a; }

/* ── Table card ──────────────────────────── */
.acm-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
.acm-table { width:100%; border-collapse:collapse; }
.acm-table th {
  padding:10px 14px; text-align:left; font-size:.7rem; font-weight:700;
  text-transform:uppercase; letter-spacing:.05em; color:#64748b;
  background:#f8fafc; border-bottom:1px solid #e2e8f0;
}
.acm-table th.center, .acm-table td.center { text-align:center; }
.acm-table td { padding:12px 14px; border-bottom:1px solid #f1f5f9; vertical-align:middle; font-size:.84rem; color:#1e293b; }
.acm-table tr:last-child td { border-bottom:none; }
.acm-table tr:hover td { background:#fafbff; }

/* ── Progress bar ────────────────────────── */
.acm-bar { height:5px; background:#f1f5f9; border-radius:3px; margin-top:5px; min-width:80px; }
.acm-bar__fill { height:100%; border-radius:3px; transition:width .3s; }

/* ── Status badge ────────────────────────── */
.acm-badge {
  display:inline-block; padding:.2rem .65rem; border-radius:999px;
  font-size:.68rem; font-weight:800; white-space:nowrap; text-transform:uppercase; letter-spacing:.04em;
}

/* ── Buttons ─────────────────────────────── */
.acm-btn {
  padding:.28rem .72rem; border-radius:7px; font-size:.75rem; font-weight:700;
  border:1px solid; cursor:pointer; white-space:nowrap;
  text-decoration:none; display:inline-block; transition:all .15s;
}
.acm-btn--finalize { border-color:#86efac; color:#166534; background:#f0fdf4; }
.acm-btn--finalize:hover { background:#dcfce7; }
.acm-btn--lock     { border-color:#bfdbfe; color:#1d4ed8; background:#eff6ff; }
.acm-btn--lock:hover { background:#dbeafe; }
.acm-btn--danger   { border-color:#fca5a5; color:#991b1b; background:#fef2f2; }
.acm-btn--danger:hover { background:#fee2e2; }
.acm-btn--primary  { border-color:#6366f1; color:#fff; background:#4f46e5; padding:.4rem 1rem; }
.acm-btn--primary:hover { background:#4338ca; }

/* ── Empty / no-year state ───────────────── */
.acm-empty { padding:52px 24px; text-align:center; color:#94a3b8; font-size:.875rem; }
.acm-empty svg { width:40px; height:40px; margin:0 auto 12px; display:block; color:#cbd5e1; }
</style>
@endpush

@section('content')
<div style="max-width:1200px;">

  {{-- ── Page header ──────────────────────────────────────────────────── --}}
  <div style="margin-bottom:20px;">
    <h1 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Assessment &amp; Finalization</h1>
    <p style="font-size:.82rem;color:#64748b;margin:0;">
      Review submitted grades by section, finalize them, and lock the record for the quarter.
    </p>
  </div>

  {{-- Flash messages --}}
  @if(session('success'))
  <div style="padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;margin-bottom:16px;font-size:.85rem;color:#166534;">
    {{ session('success') }}
  </div>
  @endif
  @if(session('error'))
  <div style="padding:12px 16px;background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;margin-bottom:16px;font-size:.85rem;color:#991b1b;">
    {{ session('error') }}
  </div>
  @endif

  @if(!$activeYear)
  <div class="acm-card">
    <div class="acm-empty">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.645c-1.73 0-2.813-1.874-1.948-3.374l7.547-13.015c.866-1.5 3.032-1.5 3.898 0l5.16 8.898z"/>
      </svg>
      No active academic year found. Activate an academic year to begin finalization.
    </div>
  </div>
  @else

  {{-- ── Quarter selector ──────────────────────────────────────────────── --}}
  @if($quarters->isNotEmpty())
  <div class="acm-qtabs">
    @foreach($quarters as $q)
      <a href="{{ route('registrar.assessment', ['quarter_id' => $q->id]) }}"
         class="acm-qtab {{ optional($selectedQuarter)->id === $q->id ? 'active' : '' }}">
        {{ $q->quarter_name }}
        @if($q->status === 'active')
          <span style="font-size:.65rem;opacity:.8;margin-left:3px;">● active</span>
        @endif
      </a>
    @endforeach
  </div>
  @endif

  @if(!$selectedQuarter)
  <div class="acm-card">
    <div class="acm-empty">No grading periods configured for this academic year.</div>
  </div>
  @else

  {{-- ── Quarter-level stats ───────────────────────────────────────────── --}}
  <div class="acm-stats">
    <div class="acm-stat">
      <div class="acm-stat__num" style="color:#94a3b8;">{{ $quarterStats['draft'] }}</div>
      <div class="acm-stat__lbl">Draft</div>
    </div>
    <div class="acm-stat">
      <div class="acm-stat__num" style="color:#d97706;">{{ $quarterStats['submitted'] }}</div>
      <div class="acm-stat__lbl">Submitted</div>
    </div>
    <div class="acm-stat">
      <div class="acm-stat__num" style="color:#16a34a;">{{ $quarterStats['finalized'] }}</div>
      <div class="acm-stat__lbl">Finalized</div>
    </div>
    <div class="acm-stat">
      <div class="acm-stat__num" style="color:#2563eb;">{{ $quarterStats['locked'] }}</div>
      <div class="acm-stat__lbl">Locked</div>
    </div>
    <div class="acm-stat">
      <div class="acm-stat__num" style="color:#dc2626;">{{ $quarterStats['missing'] }}</div>
      <div class="acm-stat__lbl">Missing</div>
    </div>
  </div>

  {{-- ── Quarter-level action bar ───────────────────────────────────────── --}}
  <div class="acm-action-bar">
    <div class="acm-action-bar__info">
      <strong>{{ $selectedQuarter->quarter_name }}</strong>
      &nbsp;·&nbsp; {{ $activeYear->year_label }}
      @php $total = array_sum([$quarterStats['draft'],$quarterStats['submitted'],$quarterStats['finalized'],$quarterStats['locked']]); @endphp
      @if($total > 0)
        &nbsp;·&nbsp; {{ $total }} grade entr{{ $total === 1 ? 'y' : 'ies' }} recorded
      @endif
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      @if($quarterStats['submitted'] > 0)
      <form method="POST" action="{{ route('registrar.assessment.finalize-quarter') }}"
            onsubmit="return confirm('Finalize all {{ $quarterStats['submitted'] }} submitted grade(s) for the entire quarter?')">
        @csrf
        <input type="hidden" name="quarter_id" value="{{ $selectedQuarter->id }}">
        <button type="submit" class="acm-btn acm-btn--primary">
          Finalize All Submitted ({{ $quarterStats['submitted'] }})
        </button>
      </form>
      @endif
      @if($quarterStats['locked'] === 0 && ($quarterStats['draft'] + $quarterStats['submitted'] + $quarterStats['missing']) === 0 && $quarterStats['finalized'] > 0)
      <span style="font-size:.82rem;color:#16a34a;font-weight:700;padding:.4rem 0;">
        ✓ All grades finalized — use Grade Lock to lock the quarter.
      </span>
      @endif
    </div>
  </div>

  {{-- ── Sections table ─────────────────────────────────────────────────── --}}
  <div class="acm-card">
    @if($sections->isEmpty())
    <div class="acm-empty">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
      </svg>
      No active sections found for this academic year.
    </div>
    @else
    <div style="overflow-x:auto;">
      <table class="acm-table">
        <thead>
          <tr>
            <th>Section</th>
            <th>Grade</th>
            <th class="center">Enrolled</th>
            <th class="center">Draft</th>
            <th class="center">Submitted</th>
            <th class="center">Finalized</th>
            <th class="center">Locked</th>
            <th class="center">Missing</th>
            <th>Progress</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        @foreach($sections as $sec)
          @php
            $c = $sec->acm_counts;
            $st = $sec->acm_status;
            $total = $c['entered'];
            $lockedPct = $total > 0 ? round(($c['locked'] / $total) * 100) : 0;
            $finalPct  = $total > 0 ? round((($c['finalized'] + $c['locked']) / $total) * 100) : 0;

            $badgeDef = match($st) {
              'locked'       => ['Locked',       '#1d4ed8', '#dbeafe'],
              'finalized'    => ['Finalized',     '#166534', '#dcfce7'],
              'all_submitted'=> ['All Submitted', '#92400e', '#fef3c7'],
              'in_progress'  => ['In Progress',   '#7c3aed', '#ede9fe'],
              default        => ['Not Started',   '#64748b', '#f1f5f9'],
            };
            [$badgeLabel, $badgeClr, $badgeBg] = $badgeDef;

            $barColor = match(true) {
              $lockedPct >= 100 => '#2563eb',
              $finalPct  >= 100 => '#16a34a',
              $finalPct  >  50  => '#22c55e',
              $finalPct  >   0  => '#f59e0b',
              default           => '#e2e8f0',
            };
          @endphp
          <tr>
            <td style="font-weight:700;color:#0f172a;">{{ $sec->section_name }}</td>
            <td style="color:#475569;font-size:.82rem;">{{ $sec->grade_level }}</td>
            <td class="center" style="color:#64748b;">{{ $sec->enrollments_count }}</td>
            <td class="center" style="color:{{ $c['draft'] ? '#94a3b8' : '#e2e8f0' }};font-weight:{{ $c['draft'] ? '700' : '400' }};">
              {{ $c['draft'] ?: '—' }}
            </td>
            <td class="center" style="color:{{ $c['submitted'] ? '#d97706' : '#e2e8f0' }};font-weight:{{ $c['submitted'] ? '700' : '400' }};">
              {{ $c['submitted'] ?: '—' }}
            </td>
            <td class="center" style="color:{{ $c['finalized'] ? '#16a34a' : '#e2e8f0' }};font-weight:{{ $c['finalized'] ? '700' : '400' }};">
              {{ $c['finalized'] ?: '—' }}
            </td>
            <td class="center" style="color:{{ $c['locked'] ? '#2563eb' : '#e2e8f0' }};font-weight:{{ $c['locked'] ? '700' : '400' }};">
              {{ $c['locked'] ?: '—' }}
            </td>
            <td class="center" style="color:{{ $c['missing'] ? '#dc2626' : '#e2e8f0' }};font-weight:{{ $c['missing'] ? '700' : '400' }};">
              {{ $c['missing'] ?: '—' }}
            </td>
            <td style="min-width:100px;">
              <div style="font-size:.7rem;color:#64748b;margin-bottom:3px;">
                {{ $finalPct }}% finalized
              </div>
              <div class="acm-bar">
                <div class="acm-bar__fill" style="width:{{ $finalPct }}%;background:{{ $barColor }};"></div>
              </div>
            </td>
            <td>
              <span class="acm-badge" style="background:{{ $badgeBg }};color:{{ $badgeClr }};">
                {{ $badgeLabel }}
              </span>
            </td>
            <td>
              <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                @if($c['submitted'] > 0)
                <form method="POST" action="{{ route('registrar.assessment.finalize-section') }}"
                      onsubmit="return confirm('Finalize {{ $c['submitted'] }} submitted grade(s) for {{ $sec->section_name }}?')">
                  @csrf
                  <input type="hidden" name="section_id" value="{{ $sec->id }}">
                  <input type="hidden" name="quarter_id" value="{{ $selectedQuarter->id }}">
                  <button type="submit" class="acm-btn acm-btn--finalize">
                    Finalize ({{ $c['submitted'] }})
                  </button>
                </form>
                @endif

                @if($c['finalized'] > 0)
                <form method="POST" action="{{ route('registrar.assessment.lock-section') }}"
                      onsubmit="return confirm('Lock {{ $c['finalized'] }} finalized grade(s) for {{ $sec->section_name }}? This cannot be undone without an unlock request.')">
                  @csrf
                  <input type="hidden" name="section_id" value="{{ $sec->id }}">
                  <input type="hidden" name="quarter_id" value="{{ $selectedQuarter->id }}">
                  <button type="submit" class="acm-btn acm-btn--lock">
                    Lock ({{ $c['finalized'] }})
                  </button>
                </form>
                @endif

                @if($c['submitted'] === 0 && $c['finalized'] === 0)
                  @if($st === 'locked')
                    <span style="font-size:.75rem;color:#2563eb;font-weight:700;">All Locked</span>
                  @else
                    <span style="font-size:.75rem;color:#cbd5e1;">—</span>
                  @endif
                @endif
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

  {{-- ── Grade Lock link ──────────────────────────────────────────────────── --}}
  <div style="margin-top:14px;font-size:.8rem;color:#64748b;text-align:right;">
    Need to manage unlock requests or do a global lock?
    <a href="{{ route('registrar.grade-lock.index') }}" style="color:#4f46e5;font-weight:700;text-decoration:none;">
      Go to Grade Lock Management →
    </a>
  </div>

  @endif {{-- selectedQuarter --}}
  @endif {{-- activeYear --}}

</div>
@endsection
