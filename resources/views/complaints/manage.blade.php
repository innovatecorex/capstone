@extends('layouts.app')

@section('title', 'Grade Complaints')
@section('breadcrumb', 'Grade Complaints Management')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">Grade Complaints</h1>
      <p class="enc-page__subtitle">Review and respond to student grade concerns.</p>
    </div>
  </div>
</div>

@if(session('success'))
<div class="enc-alert enc-alert--success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif

{{-- ── Filters ─────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('complaints.manage') }}" class="gc-filters">
  <div class="gc-filter-group">
    <label class="gc-filter-label">Search student</label>
    <input type="text" name="search" value="{{ request('search') }}"
      placeholder="Name or LRN…" class="gc-filter-ctrl" style="width:200px;">
  </div>
  <div class="gc-filter-group">
    <label class="gc-filter-label">Status</label>
    <select name="status" class="gc-filter-ctrl" style="width:190px;">
      <option value="">All statuses</option>
      @foreach(['pending','under_review','forwarded_to_teacher','resolved','dismissed'] as $s)
      <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
        {{ ucfirst(str_replace('_',' ',$s)) }}
      </option>
      @endforeach
    </select>
  </div>
  <div class="gc-filter-group">
    <label class="gc-filter-label">Subject</label>
    <select name="subject" class="gc-filter-ctrl" style="width:220px;">
      <option value="">All subjects</option>
      @foreach($subjects as $subj)
      <option value="{{ $subj->id }}" {{ request('subject') == $subj->id ? 'selected' : '' }}>
        {{ $subj->subject_name }}
      </option>
      @endforeach
    </select>
  </div>
  <div style="display:flex;gap:.5rem;align-items:flex-end;">
    <button type="submit" class="enc-btn enc-btn--primary" style="height:36px;font-size:.83rem;">Filter</button>
    @if(request()->hasAny(['search','status','subject']))
    <a href="{{ route('complaints.manage') }}" class="enc-btn enc-btn--ghost" style="height:36px;font-size:.83rem;">Clear</a>
    @endif
  </div>
</form>

@if($complaints->isEmpty())
<div class="enc-card" style="padding:2.5rem;text-align:center;color:var(--gray-400);">
  No grade complaints found matching the current filters.
</div>
@else

@foreach($complaints as $c)
<div class="enc-card complaint-card" style="padding:0;margin-bottom:1.1rem;overflow:hidden;">

  {{-- Card header --}}
  <div style="display:flex;align-items:center;justify-content:space-between;padding:.9rem 1.25rem;border-bottom:1px solid rgba(15,23,42,.06);flex-wrap:wrap;gap:.5rem;">
    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
      <span class="status-badge status-{{ $c->status }}">{{ ucfirst(str_replace('_', ' ', $c->status)) }}</span>
      <strong style="color:var(--navy);">{{ $c->student?->full_name ?? '—' }}</strong>
      @if($c->student?->lrn)
        <span style="color:var(--gray-400);font-size:.82rem;">{{ $c->student->lrn }}</span>
      @endif
      <span style="color:var(--gray-400);">·</span>
      <span style="font-size:.85rem;color:var(--gray-500);">{{ $c->sectionSubject?->subject?->subject_name ?? '—' }}</span>
      @if($c->gradingQuarter)
        <span style="font-size:.82rem;background:rgba(15,23,42,.06);padding:.15rem .55rem;border-radius:999px;color:var(--navy);">Q{{ $c->gradingQuarter->quarter_number }}</span>
      @endif
    </div>
    <span style="font-size:.78rem;color:var(--gray-400);">{{ $c->created_at->format('M d, Y · h:i A') }}</span>
  </div>

  <div style="padding:1rem 1.25rem;">

    {{-- Teacher & grade snapshot row --}}
    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:.9rem;">
      <div style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;color:var(--gray-500);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
        <span>Teacher:</span>
        <strong style="color:var(--navy);">{{ $c->sectionSubject?->faculty?->full_name ?? 'Unassigned' }}</strong>
      </div>
      @if($c->grade)
      <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(15,23,42,.04);border-radius:8px;padding:.35rem .75rem;font-size:.82rem;">
        <span style="color:var(--gray-500);">Disputed grade:</span>
        <strong style="color:var(--navy);font-size:.95rem;">{{ number_format($c->grade->final_grade, 0) }}</strong>
        <span style="color:{{ $c->grade->final_grade >= 75 ? '#166534' : '#991b1b' }};font-size:.78rem;">
          {{ $c->grade->descriptor ?? ($c->grade->final_grade >= 75 ? 'Passed' : 'Failed') }}
        </span>
      </div>
      @endif
      @if($c->corrected_grade)
      <div style="display:inline-flex;align-items:center;gap:.5rem;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:.35rem .75rem;font-size:.82rem;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#166534" stroke-width="2" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span style="color:#166534;">Corrected to:</span>
        <strong style="color:#166534;">{{ number_format($c->corrected_grade, 0) }}</strong>
        @if($c->grade_corrected_at)
        <span style="color:#86efac;font-size:.76rem;">{{ $c->grade_corrected_at->format('M d') }}</span>
        @endif
      </div>
      @endif
    </div>

    {{-- Student's reason --}}
    <div style="margin-bottom:1rem;">
      <div style="font-size:.75rem;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">Student's Concern</div>
      <div style="font-size:.87rem;color:var(--navy);line-height:1.55;white-space:pre-wrap;">{{ $c->reason }}</div>
    </div>

    {{-- Attachments --}}
    @if($c->attachments->isNotEmpty())
    <div style="margin-bottom:1rem;">
      <div style="font-size:.75rem;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.5rem;">
        Proof / Attachments ({{ $c->attachments->count() }})
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
        @foreach($c->attachments as $att)
        @if($att->isImage())
        <a href="{{ route('complaints.attachment.download', $att->id) }}" target="_blank"
           style="display:block;border-radius:8px;border:1px solid rgba(15,23,42,.12);overflow:hidden;text-decoration:none;">
          <img src="{{ route('complaints.attachment.download', $att->id) }}"
               alt="{{ $att->original_name }}"
               style="width:90px;height:70px;object-fit:cover;display:block;">
          <div style="padding:.25rem .4rem;font-size:.68rem;color:var(--gray-500);max-width:90px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
            {{ $att->original_name }}
          </div>
        </a>
        @else
        <a href="{{ route('complaints.attachment.download', $att->id) }}" target="_blank"
           style="display:flex;align-items:center;gap:.4rem;padding:.45rem .75rem;border:1px solid rgba(15,23,42,.12);border-radius:8px;text-decoration:none;font-size:.8rem;color:var(--navy);background:#f8fafc;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;color:#e11d48;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
          <span>{{ Str::limit($att->original_name, 28) }}</span>
          <span style="color:var(--gray-400);font-size:.72rem;">{{ $att->file_size_formatted }}</span>
        </a>
        @endif
        @endforeach
      </div>
    </div>
    @endif

    {{-- Existing response --}}
    @if($c->response)
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;">
      <div style="font-size:.75rem;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem;">
        Response by {{ $c->respondedBy?->full_name ?? '—' }}
        @if($c->responded_at) · {{ $c->responded_at->format('M d, Y') }} @endif
      </div>
      <div style="font-size:.87rem;color:#14532d;line-height:1.55;">{{ $c->response }}</div>
    </div>
    @endif

    {{-- Response form (shown for open complaints) --}}
    @if($c->isOpen())
    <form method="POST" action="{{ route('complaints.respond', $c->id) }}"
          style="border-top:1px solid rgba(15,23,42,.06);padding-top:.9rem;display:grid;gap:.75rem;">
      @csrf
      @method('PATCH')

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
        <div>
          <label class="enc-label">Update Status</label>
          <select name="status" class="enc-input" required
                  onchange="toggleCorrectedGrade(this, 'cg-{{ $c->id }}')">
            <option value="under_review">Under Review</option>
            @if(auth()->user()->role_id !== '02')
            <option value="forwarded_to_teacher">Forwarded to Teacher</option>
            @endif
            <option value="resolved">Resolved</option>
            <option value="dismissed">Dismissed</option>
          </select>
        </div>

        {{-- Corrected grade (registrar/admin only, visible when Resolved is selected) --}}
        @if(in_array(auth()->user()->role_id, ['03','04']) && $c->grade_id)
        <div id="cg-{{ $c->id }}" style="display:none;">
          <label class="enc-label">Corrected Final Grade <span style="color:var(--gray-400);font-weight:400;">(optional — applies to grade record)</span></label>
          <input type="number" name="corrected_grade" class="enc-input"
                 min="0" max="100" step="1" placeholder="e.g. 85"
                 value="{{ old('corrected_grade') }}">
        </div>
        @endif
      </div>

      <div>
        <label class="enc-label">Response / Remarks</label>
        <textarea name="response" class="enc-input" rows="3"
          placeholder="Provide a clear explanation or resolution…"
          required minlength="10" maxlength="2000">{{ old('response') }}</textarea>
      </div>

      <div>
        <button type="submit" class="enc-btn enc-btn--primary">Save Response</button>
      </div>
    </form>
    @endif

  </div>
</div>
@endforeach

@if($complaints->hasPages())
<div style="margin-top:1rem;">{{ $complaints->links() }}</div>
@endif
@endif
@endsection

@push('head')
<style>
/* ── Filters ─────────────────────────────────────────────────────────── */
.gc-filters { display:flex; gap:.65rem; flex-wrap:wrap; align-items:flex-end; margin-bottom:1.1rem; }
.gc-filter-group { display:flex; flex-direction:column; gap:.2rem; }
.gc-filter-label { font-size:.68rem; font-weight:700; color:var(--gray-500); text-transform:uppercase; letter-spacing:.05em; }
.gc-filter-ctrl {
  padding:.45rem .85rem; border:1px solid rgba(15,23,42,.14);
  border-radius:8px; font-size:.86rem; background:#fff; color:#0f172a;
  font-family:inherit; outline:none; height:36px;
}
.gc-filter-ctrl:focus { border-color:var(--primary); }

/* ── Card + form ─────────────────────────────────────────────────────── */
.complaint-card { border:1px solid rgba(15,23,42,.07); }
.enc-label { display:block; font-size:.78rem; font-weight:700; color:var(--gray-500); margin-bottom:.3rem; }
.enc-input { width:100%; padding:.55rem .85rem; border:1px solid rgba(15,23,42,.14); border-radius:8px; font-size:.88rem; background:#fff; color:var(--navy); outline:none; font-family:inherit; }
.enc-input:focus { border-color:var(--primary); }
textarea.enc-input { resize:vertical; }
.enc-btn { display:inline-flex; align-items:center; justify-content:center; padding:.6rem 1.25rem; border-radius:999px; font-weight:700; font-size:.87rem; cursor:pointer; border:none; text-decoration:none; }
.enc-btn--primary { background:var(--primary); color:#fff; }
.enc-btn--ghost   { background:rgba(15,23,42,.07); color:var(--navy); }

/* ── Status badges ──────────────────────────────────────────────────── */
.status-badge { display:inline-block; padding:.22rem .65rem; border-radius:999px; font-size:.74rem; font-weight:700; }
.status-pending               { background:#fef9c3; color:#854d0e; }
.status-under_review          { background:#dbeafe; color:#1e40af; }
.status-forwarded_to_teacher  { background:#ede9fe; color:#5b21b6; }
.status-resolved              { background:#dcfce7; color:#166534; }
.status-dismissed             { background:#f3f4f6; color:#6b7280; }

.enc-alert--success { background:#f0fdf4; border:1px solid #86efac; border-radius:10px; padding:.85rem 1rem; font-size:.87rem; color:#166534; }
</style>
<script>
function toggleCorrectedGrade(select, boxId) {
  const box = document.getElementById(boxId);
  if (!box) return;
  box.style.display = select.value === 'resolved' ? '' : 'none';
}
</script>
@endpush
