@extends('layouts.app')
@section('title', 'Document Requests')
@section('breadcrumb', 'Document Requests')

@push('head')
<style>
/* ── Stat strip ─────────────────────────── */
.dreq-stats { display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:20px; }
@media(max-width:860px){ .dreq-stats{ grid-template-columns:repeat(2,1fr); } }
.dreq-stat { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; }
.dreq-stat__num { font-size:1.55rem; font-weight:800; line-height:1.1; margin-bottom:4px; }
.dreq-stat__lbl { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#64748b; }

/* ── Toolbar ────────────────────────────── */
.dreq-toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:16px; }
.dreq-tabs { display:flex; gap:6px; flex-wrap:wrap; }
.dreq-tab {
  padding:.32rem .85rem; border-radius:999px; font-size:.78rem; font-weight:600;
  border:1px solid #e2e8f0; background:#fff; color:#64748b;
  text-decoration:none; transition:all .15s; white-space:nowrap;
}
.dreq-tab:hover { border-color:#6366f1; color:#4f46e5; }
.dreq-tab.active { background:#4f46e5; color:#fff; border-color:#4f46e5; }

/* ── Search bar ─────────────────────────── */
.dreq-search { display:flex; gap:6px; align-items:center; }
.dreq-search input {
  padding:.42rem .85rem; border:1px solid #e2e8f0; border-radius:8px;
  font-size:.84rem; width:240px; color:#0f172a; box-sizing:border-box;
}
.dreq-search input:focus { outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.1); }
.dreq-search-btn {
  padding:.42rem .9rem; background:#4f46e5; color:#fff;
  border:none; border-radius:8px; font-size:.82rem; font-weight:700;
  cursor:pointer; transition:background .15s; white-space:nowrap;
}
.dreq-search-btn:hover { background:#4338ca; }
.dreq-clear-btn {
  padding:.42rem .75rem; background:#f1f5f9; color:#475569;
  border:1px solid #e2e8f0; border-radius:8px; font-size:.82rem; font-weight:600;
  text-decoration:none; white-space:nowrap; transition:all .15s;
}
.dreq-clear-btn:hover { background:#e2e8f0; }

/* ── Table ──────────────────────────────── */
.dreq-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
.dreq-table { width:100%; border-collapse:collapse; }
.dreq-table th {
  padding:10px 14px; text-align:left; font-size:.7rem; font-weight:700;
  text-transform:uppercase; letter-spacing:.05em; color:#64748b;
  background:#f8fafc; border-bottom:1px solid #e2e8f0;
}
.dreq-table td { padding:12px 14px; border-bottom:1px solid #f1f5f9; vertical-align:middle; font-size:.84rem; color:#1e293b; }
.dreq-table tr.req-data-row:hover td { background:#fafbff; }
.dreq-table tr:last-child td { border-bottom:none; }

/* ── Badge ──────────────────────────────── */
.dreq-badge {
  display:inline-block; padding:.22rem .75rem; border-radius:999px;
  font-size:.7rem; font-weight:700; white-space:nowrap;
}

/* ── Action buttons ─────────────────────── */
.dreq-btn {
  padding:.25rem .7rem; border-radius:6px; font-size:.75rem; font-weight:700;
  border:1px solid; cursor:pointer; text-decoration:none; display:inline-block;
  transition:all .15s; background:#fff; white-space:nowrap;
}
.dreq-btn--view    { border-color:#cbd5e1; color:#475569; }
.dreq-btn--view:hover { background:#f8fafc; }
.dreq-btn--review  { border-color:#86efac; color:#166534; background:#f0fdf4; }
.dreq-btn--review:hover  { background:#dcfce7; }
.dreq-btn--approve { border-color:#bfdbfe; color:#1d4ed8; background:#eff6ff; }
.dreq-btn--approve:hover { background:#dbeafe; }
.dreq-btn--release { border-color:#cbd5e1; color:#475569; background:#f8fafc; }
.dreq-btn--release:hover { background:#e2e8f0; }
.dreq-btn--cancel  { border-color:#e2e8f0; color:#64748b; }
.dreq-btn--cancel:hover { background:#f1f5f9; }
.dreq-btn--confirm { border-color:#6366f1; color:#fff; background:#4f46e5; }
.dreq-btn--confirm:hover { background:#4338ca; }

/* ── Inline action row ──────────────────── */
.req-action-row { display:none; }
.req-action-row td { background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:14px 20px; }
.dreq-inline-form { display:flex; align-items:flex-start; gap:10px; flex-wrap:wrap; }
.dreq-inline-form textarea {
  flex:1; min-width:220px; padding:.5rem .75rem;
  border:1px solid #e2e8f0; border-radius:8px; font-size:.82rem;
  resize:vertical; min-height:56px; color:#0f172a; font-family:inherit;
}
.dreq-inline-form textarea:focus { outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.1); }
.dreq-inline-btns { display:flex; gap:6px; align-items:center; margin-top:2px; }

/* ── Empty state ────────────────────────── */
.dreq-empty { padding:52px 24px; text-align:center; color:#94a3b8; font-size:.875rem; }
.dreq-empty svg { width:40px; height:40px; margin:0 auto 12px; display:block; color:#cbd5e1; }
</style>
@endpush

@section('content')
<div style="max-width:1160px;">

  {{-- ── Page header ──────────────────────────────────────────────────── --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.2rem;font-weight:800;color:#0f172a;margin:0 0 4px;">Document Requests</h1>
      <p style="font-size:.82rem;color:#64748b;margin:0;">Review and process student document and record requests.</p>
    </div>
    <a href="{{ route('documents.student.index') }}"
       style="padding:.5rem 1.1rem;background:#4f46e5;color:#fff;border-radius:8px;font-size:.82rem;font-weight:700;text-decoration:none;display:inline-block;transition:background .15s;"
       onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'">
      + New Request
    </a>
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

  {{-- ── Stat cards ──────────────────────────────────────────────────── --}}
  <div class="dreq-stats">
    <div class="dreq-stat">
      <div class="dreq-stat__num" style="color:#0f172a;">{{ $counts['total'] }}</div>
      <div class="dreq-stat__lbl">Total</div>
    </div>
    <div class="dreq-stat">
      <div class="dreq-stat__num" style="color:#d97706;">{{ $counts['pending'] }}</div>
      <div class="dreq-stat__lbl">Pending</div>
    </div>
    <div class="dreq-stat">
      <div class="dreq-stat__num" style="color:#2563eb;">{{ $counts['processing'] }}</div>
      <div class="dreq-stat__lbl">Under Review</div>
    </div>
    <div class="dreq-stat">
      <div class="dreq-stat__num" style="color:#16a34a;">{{ $counts['ready'] }}</div>
      <div class="dreq-stat__lbl">Ready for Release</div>
    </div>
    <div class="dreq-stat">
      <div class="dreq-stat__num" style="color:#64748b;">{{ $counts['released'] }}</div>
      <div class="dreq-stat__lbl">Completed</div>
    </div>
  </div>

  {{-- ── Toolbar: tabs + search ───────────────────────────────────────── --}}
  <div class="dreq-toolbar">

    {{-- Filter tabs --}}
    <div class="dreq-tabs">
      @php
        $tabSearch = $search ? ['search' => $search] : [];
      @endphp
      <a href="{{ route('registrar.requests', $tabSearch) }}"
         class="dreq-tab {{ !$status ? 'active' : '' }}">All Requests</a>
      <a href="{{ route('registrar.requests', array_merge(['status' => 'pending'],    $tabSearch)) }}"
         class="dreq-tab {{ $status === 'pending'    ? 'active' : '' }}">Pending</a>
      <a href="{{ route('registrar.requests', array_merge(['status' => 'processing'], $tabSearch)) }}"
         class="dreq-tab {{ $status === 'processing' ? 'active' : '' }}">Under Review</a>
      <a href="{{ route('registrar.requests', array_merge(['status' => 'ready'],      $tabSearch)) }}"
         class="dreq-tab {{ $status === 'ready'      ? 'active' : '' }}">Ready for Release</a>
      <a href="{{ route('registrar.requests', array_merge(['status' => 'released'],   $tabSearch)) }}"
         class="dreq-tab {{ $status === 'released'   ? 'active' : '' }}">Completed</a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('registrar.requests') }}" class="dreq-search">
      @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
      <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or LRN…" autocomplete="off">
      <button type="submit" class="dreq-search-btn">Search</button>
      @if($search)
        <a href="{{ route('registrar.requests', array_filter(['status' => $status])) }}" class="dreq-clear-btn">Clear</a>
      @endif
    </form>

  </div>

  {{-- ── Table ────────────────────────────────────────────────────────── --}}
  <div class="dreq-card">
    @if($requests->isEmpty())
      <div class="dreq-empty">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
        No requests found.
      </div>
    @else
    <div style="overflow-x:auto;">
      <table class="dreq-table">
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
          $badgeLabel = [
            'pending'    => 'Pending',
            'processing' => 'Under Review',
            'ready'      => 'Ready for Release',
            'released'   => 'Completed',
            'rejected'   => 'Rejected',
          ];
          $nextStatus = [
            'pending'    => ['status' => 'processing', 'label' => 'Review',        'class' => 'dreq-btn--review'],
            'processing' => ['status' => 'ready',      'label' => 'Approve',       'class' => 'dreq-btn--approve'],
            'ready'      => ['status' => 'released',   'label' => 'Mark Released', 'class' => 'dreq-btn--release'],
          ];
        @endphp

        @foreach($requests as $req)
          @php
            [$clr, $bg] = $req->status_color;
            $reqNum = 'REQ-' . $req->created_at->format('Y') . '-' . str_pad($req->id, 3, '0', STR_PAD_LEFT);
            $purpose = mb_strlen($req->purpose) > 40 ? mb_substr($req->purpose, 0, 40) . '…' : $req->purpose;
            $action  = $nextStatus[$req->status] ?? null;
          @endphp

          {{-- Data row --}}
          <tr class="req-data-row">
            <td>
              <a href="{{ route('documents.registrar.index', ['search' => $req->student?->lrn]) }}"
                 style="font-family:monospace;font-size:.8rem;font-weight:700;color:#2563eb;text-decoration:none;">
                {{ $reqNum }}
              </a>
            </td>
            <td>
              <div style="font-weight:700;color:#0f172a;">
                {{ $req->student ? $req->student->last_name . ', ' . $req->student->first_name : '—' }}
              </div>
              <div style="font-size:.72rem;color:#94a3b8;font-family:monospace;">LRN: {{ $req->student?->lrn ?? '—' }}</div>
            </td>
            <td style="color:#334155;">{{ $req->document_label }}</td>
            <td style="white-space:nowrap;color:#64748b;font-size:.8rem;">{{ $req->created_at->format('M d, Y') }}</td>
            <td style="color:#64748b;font-size:.82rem;max-width:170px;">{{ $purpose }}</td>
            <td>
              <span class="dreq-badge" style="background:{{ $bg }};color:{{ $clr }};">
                {{ $badgeLabel[$req->status] ?? ucfirst($req->status) }}
              </span>
            </td>
            <td>
              <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                <a href="{{ route('documents.registrar.index', ['search' => $req->student?->lrn]) }}"
                   class="dreq-btn dreq-btn--view">View</a>
                @if($action)
                  <button type="button"
                          class="dreq-btn {{ $action['class'] }}"
                          onclick="toggleActionRow({{ $req->id }})">
                    {{ $action['label'] }}
                  </button>
                @endif
              </div>
            </td>
          </tr>

          {{-- Inline action row --}}
          @if($action)
          <tr class="req-action-row" id="action-row-{{ $req->id }}">
            <td colspan="7">
              <form method="POST" action="{{ route('documents.update-status', $req->id) }}"
                    class="dreq-inline-form"
                    @if($action['status'] === 'released') onsubmit="return confirmRelease()" @endif>
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="{{ $action['status'] }}">
                <textarea name="remarks" placeholder="Add notes for student (optional)…"></textarea>
                <div class="dreq-inline-btns">
                  <button type="submit" class="dreq-btn dreq-btn--confirm">Confirm</button>
                  <button type="button" class="dreq-btn dreq-btn--cancel"
                          onclick="cancelAction({{ $req->id }})">Cancel</button>
                </div>
              </form>
            </td>
          </tr>
          @endif

        @endforeach
        </tbody>
      </table>
    </div>

    @if($requests->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #e2e8f0;">
      {{ $requests->links() }}
    </div>
    @endif
    @endif
  </div>

</div>
@endsection

@push('scripts')
<script>
function toggleActionRow(id) {
  var row = document.getElementById('action-row-' + id);
  if (!row) return;
  // Close all other open rows first
  document.querySelectorAll('.req-action-row').forEach(function(r) {
    if (r.id !== 'action-row-' + id) r.style.display = 'none';
  });
  row.style.display = (row.style.display === 'table-row') ? 'none' : 'table-row';
  if (row.style.display === 'table-row') {
    var textarea = row.querySelector('textarea');
    if (textarea) textarea.focus();
  }
}

function cancelAction(id) {
  var row = document.getElementById('action-row-' + id);
  if (row) row.style.display = 'none';
}

function confirmRelease() {
  return confirm('Mark this document as released/claimed by the student?');
}
</script>
@endpush
