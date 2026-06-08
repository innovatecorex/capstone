@extends('layouts.app')
@section('title', 'Document Requests')
@section('breadcrumb', 'Document Requests')

@push('head')
<style>
.req-filter-bar { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; }
.req-filter-btn { padding:.35rem .85rem; border:1px solid var(--sd-border); border-radius:999px; font-size:.78rem; font-weight:600; background:#fff; color:var(--sd-muted); cursor:pointer; transition:all .15s; }
.req-filter-btn.active, .req-filter-btn:hover { background:var(--sd-primary); color:#fff; border-color:var(--sd-primary); }

.req-stats { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:20px; }
@media(max-width:768px){ .req-stats{ grid-template-columns:repeat(2,1fr); } }
.req-stat { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 16px; }
.req-stat__num  { font-size:1.55rem; font-weight:800; line-height:1; margin-bottom:4px; }
.req-stat__lbl  { font-size:.72rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em; }

.req-table { width:100%; border-collapse:collapse; }
.req-table th { padding:10px 14px; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#64748b; background:#f8fafc; border-bottom:1px solid #e2e8f0; text-align:left; }
.req-table td { padding:13px 14px; font-size:.84rem; color:#1e293b; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
.req-table tr:last-child td { border-bottom:none; }
.req-table tr:hover td { background:#fafbff; }

.req-badge { display:inline-flex; align-items:center; gap:5px; padding:.22rem .7rem; border-radius:999px; font-size:.72rem; font-weight:700; white-space:nowrap; }
.req-badge--pending    { background:#fef9c3; color:#854d0e; border:1px solid #fde68a; }
.req-badge--review     { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.req-badge--ready      { background:#f0fdf4; color:#166534; border:1px solid #86efac; }
.req-badge--completed  { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; }
.req-badge--cancelled  { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

.req-action { padding:.25rem .65rem; border-radius:6px; font-size:.75rem; font-weight:600; border:1px solid; cursor:pointer; text-decoration:none; display:inline-block; transition:all .15s; }
.req-action--view     { border-color:#cbd5e1; color:#475569; background:#fff; }
.req-action--view:hover { background:#f8fafc; }
.req-action--approve  { border-color:#86efac; color:#166534; background:#f0fdf4; }
.req-action--approve:hover { background:#dcfce7; }
.req-action--release  { border-color:#6ee7b7; color:#065f46; background:#ecfdf5; }
.req-action--release:hover { background:#d1fae5; }
</style>
@endpush

@push('scripts')
<script>
(function(){
  var activeFilter = 'all';

  window.switchFilter = function(el, filter) {
    activeFilter = filter;
    document.querySelectorAll('.req-filter-btn').forEach(function(b){ b.classList.remove('active'); });
    el.classList.add('active');
    document.querySelectorAll('.req-row').forEach(function(row){
      var status = row.dataset.status;
      row.style.display = (activeFilter === 'all' || status === activeFilter) ? '' : 'none';
    });
    var visible = document.querySelectorAll('.req-row:not([style*="display: none"])').length;
    var emptyEl = document.getElementById('req-empty');
    if (emptyEl) emptyEl.style.display = visible === 0 ? '' : 'none';
  };
})();
</script>
@endpush

@section('content')
<div style="max-width:1100px;">

  {{-- Page header --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.2rem;font-weight:800;color:var(--sd-navy);margin:0 0 4px;">Document Requests</h1>
      <p style="font-size:.82rem;color:var(--sd-muted);margin:0;">Review and process student document and record requests.</p>
    </div>
    <button type="button" style="padding:.5rem 1.1rem;background:#1d4ed8;color:#fff;border:none;border-radius:8px;font-size:.82rem;font-weight:700;cursor:pointer;">
      + New Request
    </button>
  </div>

  {{-- Stat strip --}}
  <div class="req-stats">
    <div class="req-stat">
      <div class="req-stat__num" style="color:#1e293b;">12</div>
      <div class="req-stat__lbl">Total</div>
    </div>
    <div class="req-stat">
      <div class="req-stat__num" style="color:#d97706;">4</div>
      <div class="req-stat__lbl">Pending</div>
    </div>
    <div class="req-stat">
      <div class="req-stat__num" style="color:#2563eb;">3</div>
      <div class="req-stat__lbl">Under Review</div>
    </div>
    <div class="req-stat">
      <div class="req-stat__num" style="color:#16a34a;">2</div>
      <div class="req-stat__lbl">Ready for Release</div>
    </div>
    <div class="req-stat">
      <div class="req-stat__num" style="color:#64748b;">3</div>
      <div class="req-stat__lbl">Completed</div>
    </div>
  </div>

  {{-- Filter bar --}}
  <div class="req-filter-bar">
    <button type="button" class="req-filter-btn active" onclick="switchFilter(this,'all')">All Requests</button>
    <button type="button" class="req-filter-btn" onclick="switchFilter(this,'pending')">Pending</button>
    <button type="button" class="req-filter-btn" onclick="switchFilter(this,'review')">Under Review</button>
    <button type="button" class="req-filter-btn" onclick="switchFilter(this,'ready')">Ready for Release</button>
    <button type="button" class="req-filter-btn" onclick="switchFilter(this,'completed')">Completed</button>
  </div>

  {{-- Table --}}
  <div class="sd-card" style="overflow:hidden;">
    <table class="req-table">
      <thead>
        <tr>
          <th>Req #</th>
          <th>Student</th>
          <th>Document Type</th>
          <th>Requested</th>
          <th>Purpose</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>

        @php
        $requests = [
          ['REQ-2026-001', 'Reyes, Maria Cristina',    '2024-7',   'Transcript of Records',            'Jun 2, 2026', 'Scholarship application',       'pending'],
          ['REQ-2026-002', 'Santos, Juan Miguel',       '2024-8',   'Certificate of Enrollment',        'Jun 2, 2026', 'Bank account opening',          'pending'],
          ['REQ-2026-003', 'Garcia, Ana Lorraine',      '2024-9',   'Good Moral Character Certificate', 'Jun 1, 2026', 'College application',           'review'],
          ['REQ-2026-004', 'Dela Cruz, Paolo Andrei',   '2024-10',  'Diploma / Completion Certificate', 'Jun 1, 2026', 'Employment requirement',        'review'],
          ['REQ-2026-005', 'Mendoza, Claire Bianca',    '2024-11',  'Form 137 (Permanent Record)',      'May 30, 2026','Transfer to another school',    'review'],
          ['REQ-2026-006', 'Torres, Lhance Gabriel',    '2024-12',  'Certificate of Enrollment',        'May 29, 2026','Government ID application',     'ready'],
          ['REQ-2026-007', 'Lim, Sophia Angelica',      '2024-7',   'Transcript of Records',            'May 28, 2026','PSA record correction',         'ready'],
          ['REQ-2026-008', 'Villanueva, Mark Anthony',  '2024-8',   'Good Moral Character Certificate', 'May 27, 2026','Job application',               'completed'],
          ['REQ-2026-009', 'Ramos, Jennilyn Grace',     '2024-9',   'Certificate of Enrollment',        'May 26, 2026','CHED scholarship requirement',  'completed'],
          ['REQ-2026-010', 'Cruz, Benedict Jerome',     '2024-10',  'Form 138 (Report Card)',            'May 25, 2026','College admission requirement', 'completed'],
          ['REQ-2026-011', 'Aquino, Patrizia Mae',      '2024-11',  'Transcript of Records',            'Jun 3, 2026', 'Graduate school application',   'pending'],
          ['REQ-2026-012', 'Buenaventura, Karl Luis',   '2024-12',  'Diploma / Completion Certificate', 'Jun 3, 2026', 'Board exam requirement',        'pending'],
        ];

        $badgeClass = [
          'pending'   => 'req-badge--pending',
          'review'    => 'req-badge--review',
          'ready'     => 'req-badge--ready',
          'completed' => 'req-badge--completed',
        ];
        $badgeDot = [
          'pending'   => '🟡',
          'review'    => '🔵',
          'ready'     => '🟢',
          'completed' => '⚪',
        ];
        $badgeLabel = [
          'pending'   => 'Pending',
          'review'    => 'Under Review',
          'ready'     => 'Ready for Release',
          'completed' => 'Completed',
        ];
        @endphp

        @foreach($requests as [$reqNo, $student, $lrn, $docType, $date, $purpose, $status])
        <tr class="req-row" data-status="{{ $status }}">
          <td>
            <span style="font-family:monospace;font-size:.8rem;font-weight:700;color:#1d4ed8;">{{ $reqNo }}</span>
          </td>
          <td>
            <div style="font-weight:700;color:#0f172a;">{{ $student }}</div>
            <div style="font-size:.72rem;color:#94a3b8;font-family:monospace;">LRN: {{ $lrn }}</div>
          </td>
          <td style="color:#334155;">{{ $docType }}</td>
          <td style="white-space:nowrap;color:#64748b;font-size:.8rem;">{{ $date }}</td>
          <td style="color:#64748b;font-size:.8rem;max-width:160px;">{{ $purpose }}</td>
          <td>
            <span class="req-badge {{ $badgeClass[$status] }}">
              {{ $badgeLabel[$status] }}
            </span>
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap;">
              <a href="#" class="req-action req-action--view">View</a>
              @if($status === 'pending')
                <a href="#" class="req-action req-action--approve">Review</a>
              @elseif($status === 'review')
                <a href="#" class="req-action req-action--approve">Approve</a>
              @elseif($status === 'ready')
                <a href="#" class="req-action req-action--release">Release</a>
              @endif
            </div>
          </td>
        </tr>
        @endforeach

      </tbody>
    </table>

    <div id="req-empty" style="display:none;padding:48px;text-align:center;color:#94a3b8;font-size:.875rem;">
      No requests match the selected filter.
    </div>
  </div>

</div>
@endsection
