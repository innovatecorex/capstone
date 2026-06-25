@extends('layouts.app')

@section('title', 'File a Grade Complaint')
@section('breadcrumb', 'Grade Complaint')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">File a Grade Complaint</h1>
      <p class="enc-page__subtitle">Submit a formal concern about your grade for review.</p>
    </div>
  </div>
</div>

@if($errors->any())
<div class="enc-alert enc-alert--error" style="margin-bottom:1rem;">
  <ul style="margin:0;padding-left:1.2rem;">
    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
  </ul>
</div>
@endif

@unless($enrollment)
<div class="enc-card" style="padding:2rem;text-align:center;color:var(--gray-500);">
  You are not currently enrolled in an active academic year. Please contact your registrar.
</div>
@else
<form method="POST" action="{{ route('complaints.store') }}" enctype="multipart/form-data">
  @csrf
  <div class="enc-card" style="padding:1.5rem;margin-bottom:1.25rem;">
    <div class="enc-card__header"><div class="enc-card__title">Complaint Details</div></div>
    <div class="enc-card__body" style="display:grid;gap:1.1rem;">

      {{-- Subject --}}
      <div>
        <label class="enc-label" for="section_subject_id">Subject <span style="color:#e11d48;">*</span></label>
        <select id="section_subject_id" name="section_subject_id" class="enc-input" required>
          <option value="">— Select a subject —</option>
          @foreach($sectionSubjects as $ss)
          <option value="{{ $ss->id }}"
            {{ (old('section_subject_id', $preSection) == $ss->id) ? 'selected' : '' }}>
            {{ $ss->subject?->subject_name ?? 'Unknown Subject' }}
            @if($ss->faculty) · {{ $ss->faculty->full_name }} @endif
          </option>
          @endforeach
        </select>
      </div>

      {{-- Quarter --}}
      <div>
        <label class="enc-label" for="grading_quarter_id">Quarter (optional)</label>
        <select id="grading_quarter_id" name="grading_quarter_id" class="enc-input">
          <option value="">— All quarters / general concern —</option>
          @foreach($quarters as $q)
          <option value="{{ $q->id }}"
            {{ (old('grading_quarter_id', $preQuarter) == $q->id) ? 'selected' : '' }}>
            Quarter {{ $q->quarter_number }}
            @if($q->start_date) ({{ \Carbon\Carbon::parse($q->start_date)->format('M d') }} – {{ \Carbon\Carbon::parse($q->end_date)->format('M d, Y') }}) @endif
          </option>
          @endforeach
        </select>
      </div>

      {{-- Reason --}}
      <div>
        <label class="enc-label" for="reason">Reason / Description <span style="color:#e11d48;">*</span></label>
        <textarea id="reason" name="reason" class="enc-input" rows="5"
          placeholder="Describe your concern in detail. Include specific scores or components you believe are incorrect. Minimum 20 characters."
          required minlength="20" maxlength="2000">{{ old('reason') }}</textarea>
        <div style="font-size:.78rem;color:var(--gray-400);margin-top:.25rem;">
          Min 20 · Max 2,000 characters
        </div>
      </div>

      {{-- Attachments --}}
      <div>
        <label class="enc-label">Proof / Attachments <span style="color:var(--gray-400);font-weight:400;">(optional — up to 5 files)</span></label>
        <div style="border:2px dashed rgba(15,23,42,.14);border-radius:10px;padding:1rem 1.25rem;background:#fafafa;">
          <input type="file" name="attachments[]" id="gc-attachments" multiple
            accept=".jpg,.jpeg,.png,.gif,.webp,.pdf"
            onchange="previewAttachments(this)"
            style="font-size:.85rem;color:var(--navy);">
          <div style="font-size:.76rem;color:var(--gray-400);margin-top:.4rem;">
            Images (JPG, PNG, GIF, WebP) or PDF · Max 5 MB each · Up to 5 files
          </div>
          <div id="gc-attach-preview" style="display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.6rem;"></div>
        </div>
        @error('attachments')
          <div style="color:#dc2626;font-size:.8rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
        @error('attachments.*')
          <div style="color:#dc2626;font-size:.8rem;margin-top:.25rem;">{{ $message }}</div>
        @enderror
      </div>

    </div>
  </div>

  <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
    <button type="submit" class="enc-btn enc-btn--primary" style="background:#1e293b;color:#ffffff;border:1px solid #1e293b;">Submit Complaint</button>
    <a href="{{ route('complaints.index') }}" class="enc-btn enc-btn--ghost">Cancel</a>
  </div>
</form>
@endunless
@endsection

@push('head')
<style>
.enc-label { display:block; font-size:.8rem; font-weight:700; color:var(--gray-500); margin-bottom:.35rem; }
.enc-input { width:100%; padding:.6rem .9rem; border:1px solid rgba(15,23,42,.14); border-radius:8px; font-size:.9rem; background:#fff; color:var(--navy); outline:none; transition:border-color .15s; }
.enc-input:focus { border-color:var(--primary); }
textarea.enc-input { resize:vertical; font-family:inherit; }
.enc-btn { display:inline-flex; align-items:center; justify-content:center; padding:.65rem 1.4rem; border-radius:999px; font-weight:700; font-size:.9rem; cursor:pointer; border:none; text-decoration:none; }
.enc-btn--primary { background:var(--primary); color:#fff; }
.enc-btn--ghost { background:rgba(15,23,42,.07); color:var(--navy); }
.enc-alert--error { background:#fef2f2; border:1px solid #fca5a5; border-radius:10px; padding:.85rem 1rem; font-size:.87rem; color:#991b1b; }
</style>
<script>
function previewAttachments(input) {
  const box = document.getElementById('gc-attach-preview');
  box.innerHTML = '';
  if (!input.files || !input.files.length) return;
  Array.from(input.files).slice(0, 5).forEach(file => {
    const wrap = document.createElement('div');
    wrap.style.cssText = 'display:flex;align-items:center;gap:.4rem;padding:.3rem .6rem;background:#f1f5f9;border-radius:6px;font-size:.78rem;color:#374151;';
    const icon = file.type.startsWith('image/') ? '🖼️' : '📄';
    const size = file.size > 1048576 ? (file.size/1048576).toFixed(1)+' MB' : Math.round(file.size/1024)+' KB';
    wrap.textContent = icon + ' ' + file.name + ' (' + size + ')';
    box.appendChild(wrap);
  });
}
</script>
@endpush
