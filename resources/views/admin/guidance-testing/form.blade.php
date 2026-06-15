@extends('layouts.app')

@section('title', ($result ? 'Edit' : 'Record') . ' Test — ' . $applicant->reference_number)
@section('breadcrumb', 'Guidance & Testing')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">{{ $result ? 'Edit' : 'Record' }} Test</h1>
      <p class="enc-page__subtitle">
        {{ $applicant->full_name }} · {{ $applicant->reference_number }} · {{ $applicant->applying_for_grade }}
      </p>
    </div>
    <a href="{{ route('admin.guidance-testing.index') }}" class="gt-btn gt-btn--ghost">← Back to List</a>
  </div>
</div>

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.85rem 1rem;margin-bottom:1rem;font-size:.87rem;color:#991b1b;">
  <strong>Please fix the following:</strong>
  <ul style="margin:.35rem 0 0 1.1rem;">
    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
  </ul>
</div>
@endif

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.85rem 1rem;margin-bottom:1rem;font-size:.87rem;color:#166534;">
  {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('admin.guidance-testing.store', $applicant->id) }}">
  @csrf

  <div style="display:grid;grid-template-columns:1fr 340px;gap:1.25rem;align-items:start;">

    {{-- ══════════════════════════════════════════════════════ LEFT ══════ --}}
    <div style="display:grid;gap:1.1rem;">

      {{-- ── Section 1: Admission Test ─────────────────────────────────── --}}
      <div class="enc-card" style="padding:1.25rem;">
        <div class="enc-card__header">
          <div class="enc-card__title">
            <span class="gt-section-icon" style="background:#eff6ff;color:#1d4ed8;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6m2 7H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v11a2 2 0 01-2 2z"/></svg>
            </span>
            Admission Test
          </div>
        </div>
        <div class="enc-card__body" style="display:grid;gap:1rem;">

          {{-- Date + Incoming Level --}}
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
            <div>
              <label class="gt-label">Test Date <span class="gt-req">*</span></label>
              <input type="date" name="test_date" class="gt-input" required
                value="{{ old('test_date', $result?->test_date?->format('Y-m-d') ?? date('Y-m-d')) }}">
              @error('test_date')<div class="gt-err">{{ $message }}</div>@enderror
            </div>
            <div>
              <label class="gt-label">Incoming Level</label>
              <input type="text" name="incoming_level" class="gt-input"
                placeholder="e.g. Grade 7 New Student"
                value="{{ old('incoming_level', $result?->incoming_level ?? '') }}">
            </div>
          </div>

          {{-- NV + Verbal rows --}}
          <div style="border:1px solid rgba(15,23,42,.08);border-radius:10px;overflow:hidden;">
            <div style="background:#f8fafc;padding:.55rem 1rem;font-size:.72rem;font-weight:800;color:var(--gray-500);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid rgba(15,23,42,.06);">
              Test Breakdown
            </div>
            <div style="padding:.85rem 1rem;display:grid;gap:.75rem;">

              {{-- Non-Verbal --}}
              <div>
                <div style="font-size:.73rem;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.45rem;">Non-Verbal (NV)</div>
                <div style="display:grid;grid-template-columns:100px 90px 90px 1fr;gap:.55rem;align-items:start;">
                  <div>
                    <label class="gt-label">Score</label>
                    <input type="number" id="nvScore" name="nv_score" class="gt-input" step="0.01" min="0"
                      value="{{ old('nv_score', $result?->nv_score ?? '') }}" placeholder="—">
                  </div>
                  <div>
                    <label class="gt-label">Max</label>
                    <input type="number" id="nvMax" name="nv_max" class="gt-input" step="0.01" min="1"
                      value="{{ old('nv_max', $result?->nv_max ?? 50) }}" placeholder="50">
                  </div>
                  <div>
                    <label class="gt-label">Percentage (%)</label>
                    <input type="number" id="nvPct" name="nv_pct" class="gt-input gt-input--computed" step="0.01" min="0" max="100"
                      value="{{ old('nv_pct', $result?->nv_pct ?? '') }}" placeholder="auto">
                  </div>
                  <div>
                    <label class="gt-label">Descriptive Equivalent</label>
                    <select name="nv_descriptive" class="gt-input">
                      <option value="">— Select —</option>
                      @foreach($descriptiveOptions as $opt)
                      <option value="{{ $opt }}" {{ old('nv_descriptive', $result?->nv_descriptive) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div style="border-top:1px solid rgba(15,23,42,.06);"></div>

              {{-- Verbal --}}
              <div>
                <div style="font-size:.73rem;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.45rem;">Verbal (V)</div>
                <div style="display:grid;grid-template-columns:100px 90px 90px 1fr;gap:.55rem;align-items:start;">
                  <div>
                    <label class="gt-label">Score</label>
                    <input type="number" id="vScore" name="v_score" class="gt-input" step="0.01" min="0"
                      value="{{ old('v_score', $result?->v_score ?? '') }}" placeholder="—">
                  </div>
                  <div>
                    <label class="gt-label">Max</label>
                    <input type="number" id="vMax" name="v_max" class="gt-input" step="0.01" min="1"
                      value="{{ old('v_max', $result?->v_max ?? 50) }}" placeholder="50">
                  </div>
                  <div>
                    <label class="gt-label">Percentage (%)</label>
                    <input type="number" id="vPct" name="v_pct" class="gt-input gt-input--computed" step="0.01" min="0" max="100"
                      value="{{ old('v_pct', $result?->v_pct ?? '') }}" placeholder="auto">
                  </div>
                  <div>
                    <label class="gt-label">Descriptive Equivalent</label>
                    <select name="v_descriptive" class="gt-input">
                      <option value="">— Select —</option>
                      @foreach($descriptiveOptions as $opt)
                      <option value="{{ $opt }}" {{ old('v_descriptive', $result?->v_descriptive) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

            </div>
          </div>

          {{-- Overall admission result --}}
          <div style="border:1px solid rgba(15,23,42,.08);border-radius:10px;overflow:hidden;">
            <div style="background:#f8fafc;padding:.55rem 1rem;font-size:.72rem;font-weight:800;color:var(--gray-500);text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid rgba(15,23,42,.06);">
              Overall Admission Result
            </div>
            <div style="padding:.85rem 1rem;display:grid;grid-template-columns:1fr 1fr 1fr;gap:.65rem;">
              <div>
                <label class="gt-label">Total Score <span class="gt-req">*</span></label>
                <input type="number" name="total_score" id="totalScore" class="gt-input" required
                  step="0.01" min="0"
                  value="{{ old('total_score', $result?->total_score ?? '') }}" placeholder="0">
                @error('total_score')<div class="gt-err">{{ $message }}</div>@enderror
              </div>
              <div>
                <label class="gt-label">Max Score</label>
                <input type="number" name="max_score" id="maxScore" class="gt-input"
                  step="0.01" min="1"
                  value="{{ old('max_score', $result?->max_score ?? 100) }}">
              </div>
              <div>
                <label class="gt-label">Passing Score</label>
                <input type="number" name="passing_score" id="passingScore" class="gt-input"
                  step="0.01" min="0"
                  value="{{ old('passing_score', $result?->passing_score ?? 75) }}">
              </div>
            </div>
          </div>

        </div>
      </div>

      {{-- ── Section 2: Academic Test ──────────────────────────────────── --}}
      <div class="enc-card" style="padding:1.25rem;">
        <div class="enc-card__header">
          <div class="enc-card__title">
            <span class="gt-section-icon" style="background:#f0fdf4;color:#059669;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
            </span>
            Academic Test
          </div>
          <span class="enc-card__meta">Leave blank if not applicable</span>
        </div>
        <div class="enc-card__body">
          {{-- Max score per subject (used for auto-pct calculation) --}}
          <div style="display:flex;align-items:center;gap:.65rem;margin-bottom:.85rem;padding:.55rem .75rem;background:#f8fafc;border-radius:8px;border:1px solid rgba(15,23,42,.07);">
            <label class="gt-label" style="margin:0;white-space:nowrap;">Max Score per Subject:</label>
            <input type="number" id="acadMax" name="acad_max" class="gt-input" style="width:90px;" step="1" min="1"
              value="{{ old('acad_max', 100) }}" placeholder="100">
            <span style="font-size:.75rem;color:var(--gray-400);">Percentages auto-calculate when you enter scores.</span>
          </div>
          <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
            <thead>
              <tr style="background:#f8fafc;border-bottom:1px solid rgba(15,23,42,.07);">
                <th style="text-align:left;padding:8px 10px;color:var(--gray-500);font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;width:110px;">Subject</th>
                <th style="text-align:center;padding:8px 10px;color:var(--gray-500);font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;width:100px;">Score</th>
                <th style="text-align:center;padding:8px 10px;color:var(--gray-500);font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;width:90px;">%</th>
                <th style="text-align:left;padding:8px 10px;color:var(--gray-500);font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;">Descriptive Equivalent</th>
              </tr>
            </thead>
            <tbody>
              @foreach([
                ['key'=>'filipino', 'label'=>'Filipino',     'color'=>'#d97706'],
                ['key'=>'english',  'label'=>'English',      'color'=>'#1d4ed8'],
                ['key'=>'math',     'label'=>'Mathematics',  'color'=>'#7c3aed'],
                ['key'=>'science',  'label'=>'Science',      'color'=>'#059669'],
              ] as $subj)
              <tr style="border-bottom:1px solid rgba(15,23,42,.04);">
                <td style="padding:9px 10px;">
                  <span style="font-weight:700;color:{{ $subj['color'] }};font-size:.83rem;">{{ $subj['label'] }}</span>
                </td>
                <td style="padding:9px 10px;">
                  <input type="number" id="acad_{{ $subj['key'] }}_score" name="acad_{{ $subj['key'] }}_score" class="gt-input acad-score" step="0.01" min="0"
                    style="text-align:center;"
                    value="{{ old('acad_'.$subj['key'].'_score', $result?->{'acad_'.$subj['key'].'_score'} ?? '') }}"
                    placeholder="—">
                </td>
                <td style="padding:9px 10px;">
                  <input type="number" id="acad_{{ $subj['key'] }}_pct" name="acad_{{ $subj['key'] }}_pct" class="gt-input gt-input--computed acad-pct" step="0.01" min="0" max="100"
                    style="text-align:center;"
                    value="{{ old('acad_'.$subj['key'].'_pct', $result?->{'acad_'.$subj['key'].'_pct'} ?? '') }}"
                    placeholder="auto">
                </td>
                <td style="padding:9px 10px;">
                  <select name="acad_{{ $subj['key'] }}_desc" class="gt-input">
                    <option value="">— Select —</option>
                    @foreach($descriptiveOptions as $opt)
                    <option value="{{ $opt }}" {{ old('acad_'.$subj['key'].'_desc', $result?->{'acad_'.$subj['key'].'_desc'}) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                  </select>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>

    {{-- ══════════════════════════════════════════════════════ RIGHT ═════ --}}
    <div style="display:grid;gap:1rem;">

      {{-- ── Live Pass/Fail ────────────────────────────────────────────── --}}
      <div id="liveResult" style="display:none;border-radius:12px;padding:.85rem 1rem;text-align:center;"></div>

      {{-- ── Prior result badge ────────────────────────────────────────── --}}
      @if($result)
      <div style="padding:.85rem 1rem;border-radius:12px;background:{{ $result->passed ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $result->passed ? '#bbf7d0' : '#fca5a5' }};">
        <div style="font-size:.68rem;font-weight:800;color:{{ $result->passed ? '#166534' : '#991b1b' }};text-transform:uppercase;letter-spacing:.07em;margin-bottom:.3rem;">Current Test Result</div>
        <div style="font-size:1.15rem;font-weight:900;color:{{ $result->passed ? '#15803d' : '#dc2626' }};">
          {{ $result->passed ? '✓ PASSED' : '✗ FAILED' }}
        </div>
        <div style="font-size:.8rem;color:{{ $result->passed ? '#166534' : '#991b1b' }};margin-top:.2rem;">
          {{ number_format($result->total_score, 0) }}/{{ number_format($result->max_score, 0) }}
          ({{ $result->percentage }}%) · {{ $result->test_date->format('M d, Y') }}
        </div>
      </div>
      @endif

      {{-- ── Interview & Evaluation ────────────────────────────────────── --}}
      <div class="enc-card" style="padding:1.1rem;">
        <div class="enc-card__header">
          <div class="enc-card__title">
            <span class="gt-section-icon" style="background:#fdf4ff;color:#9333ea;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
            </span>
            Interview &amp; Evaluation
          </div>
        </div>
        <div class="enc-card__body" style="display:grid;gap:.75rem;">
          <div>
            <label class="gt-label">Interviewer Name</label>
            <input type="text" name="interviewer_name" class="gt-input"
              placeholder="Guidance Counselor / Teacher"
              value="{{ old('interviewer_name', $result?->interviewer_name ?? '') }}">
          </div>
          <div>
            <label class="gt-label">Interview Date</label>
            <input type="date" name="interview_date" class="gt-input"
              value="{{ old('interview_date', $result?->interview_date?->format('Y-m-d') ?? '') }}">
          </div>
          <div>
            <label class="gt-label">Notes / Remarks</label>
            <textarea name="notes" class="gt-input" rows="4"
              placeholder="Observations, recommendations, or remarks…">{{ old('notes', $result?->notes ?? '') }}</textarea>
          </div>
        </div>
      </div>

      {{-- ── Status Control ────────────────────────────────────────────── --}}
      <div class="enc-card" style="padding:1.1rem;">
        <div class="enc-card__header">
          <div class="enc-card__title">
            <span class="gt-section-icon" style="background:#fffbeb;color:#d97706;">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
            </span>
            Status Control
          </div>
        </div>
        <div class="enc-card__body" style="display:grid;gap:.75rem;">

          <div>
            <div style="font-size:.7rem;font-weight:700;color:var(--gray-400);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Current Status</div>
            <span class="status-chip status-{{ $applicant->status }}" style="font-size:.82rem;">
              {{ ucfirst(str_replace('_', ' ', $applicant->status)) }}
            </span>
          </div>

          <div id="eligibleWrap" style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:.75rem .9rem;display:none;">
            <label style="display:flex;align-items:flex-start;gap:.6rem;cursor:pointer;">
              <input type="checkbox" name="set_eligible" value="1"
                {{ old('set_eligible') ? 'checked' : '' }}
                style="width:16px;height:16px;margin-top:2px;flex-shrink:0;accent-color:#d97706;">
              <span style="font-size:.82rem;line-height:1.5;">
                <strong style="color:#92400e;display:block;margin-bottom:.15rem;">Set Eligible for Enrollment</strong>
                <span style="color:#78350f;">Marks this applicant as cleared by guidance and ready to proceed to enrollment. Only available when test is passed.</span>
              </span>
            </label>
          </div>

          @if($applicant->status === 'eligible_for_enrollment')
          <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:.65rem .9rem;font-size:.8rem;color:#92400e;display:flex;align-items:center;gap:.5rem;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;flex-shrink:0;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
            Already marked as <strong>Eligible for Enrollment</strong>
          </div>
          @endif

        </div>
      </div>

      {{-- ── Actions ───────────────────────────────────────────────────── --}}
      <div style="display:grid;gap:.55rem;">
        <button type="submit" class="gt-btn gt-btn--primary" style="width:100%;justify-content:center;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;"><path d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.295 8.235a.75.75 0 10-1.09 1.03l4.25 4.5a.75.75 0 001.09 0l4.25-4.5a.75.75 0 00-1.09-1.03l-2.955 3.129V2.75z"/><path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z"/></svg>
          {{ $result ? 'Update Record' : 'Save Record' }}
        </button>
        <a href="{{ route('admin.applicants.show', $applicant->id) }}" class="gt-btn gt-btn--ghost" style="width:100%;justify-content:center;">
          View Full Application
        </a>
      </div>

    </div>
  </div>
</form>
@endsection

@push('head')
<style>
.gt-label       { display:block; font-size:.74rem; font-weight:700; color:var(--gray-500); margin-bottom:.28rem; }
.gt-req         { color:#e11d48; }
.gt-input       { width:100%; padding:.5rem .8rem; border:1px solid rgba(15,23,42,.13); border-radius:8px; font-size:.875rem; background:#fff; color:var(--navy); font-family:inherit; outline:none; }
.gt-input:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(37,99,235,.1); }
textarea.gt-input { resize:vertical; }
.gt-err         { font-size:.73rem; color:#dc2626; font-weight:600; margin-top:.2rem; }
.gt-btn         { display:inline-flex; align-items:center; gap:.45rem; padding:.6rem 1.1rem; border-radius:999px; font-weight:700; font-size:.87rem; cursor:pointer; border:none; text-decoration:none; transition:all .15s; }
.gt-btn--primary { background:var(--primary); color:#fff; }
.gt-btn--primary:hover { filter:brightness(1.1); }
.gt-btn--ghost   { background:rgba(15,23,42,.07); color:var(--navy); }
.gt-btn--ghost:hover { background:rgba(15,23,42,.12); }
.gt-section-icon { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:7px; margin-right:.35rem; flex-shrink:0; }
.gt-section-icon svg { width:14px; height:14px; }
.enc-card__title { display:flex; align-items:center; }
.gt-input--computed { background:#f0f9ff !important; color:#0369a1; font-weight:600; }
.status-chip { display:inline-block; padding:.22rem .65rem; border-radius:999px; font-size:.73rem; font-weight:700; }
.status-pending                 { background:#fef9c3; color:#854d0e; }
.status-under_review            { background:#dbeafe; color:#1e40af; }
.status-accepted                { background:#dcfce7; color:#166534; }
.status-rejected                { background:#fee2e2; color:#991b1b; }
.status-enrolled                { background:#e0f2fe; color:#0369a1; }
.status-eligible_for_enrollment { background:#fffbeb; color:#92400e; }
</style>
@endpush

@push('scripts')
<script>
(function () {
  const total   = document.getElementById('totalScore');
  const maxEl   = document.getElementById('maxScore');
  const passing = document.getElementById('passingScore');
  const box     = document.getElementById('liveResult');
  const eligWrap= document.getElementById('eligibleWrap');

  // Admission test components
  const nvScore = document.getElementById('nvScore');
  const nvMax   = document.getElementById('nvMax');
  const nvPct   = document.getElementById('nvPct');
  const vScore  = document.getElementById('vScore');
  const vMax    = document.getElementById('vMax');
  const vPct    = document.getElementById('vPct');

  // Academic components
  const acadMax   = document.getElementById('acadMax');
  const acadKeys  = ['filipino', 'english', 'math', 'science'];

  function toPct(score, max) {
    const s = parseFloat(score), m = parseFloat(max);
    if (isNaN(s) || isNaN(m) || m <= 0) return '';
    return Math.min(100, (s / m) * 100).toFixed(2);
  }

  function syncAdmission() {
    // Auto-calc NV %
    nvPct.value = toPct(nvScore.value, nvMax.value);

    // Auto-calc Verbal %
    vPct.value = toPct(vScore.value, vMax.value);

    // Auto-compute total and max from NV + Verbal
    const nS = parseFloat(nvScore.value) || 0;
    const nM = parseFloat(nvMax.value)   || 0;
    const vS = parseFloat(vScore.value)  || 0;
    const vM = parseFloat(vMax.value)    || 0;

    if (nS > 0 || vS > 0) {
      total.value  = (nS + vS).toFixed(0);
      maxEl.value  = (nM + vM).toFixed(0);
    }
    updatePassFail();
  }

  function syncAcademic() {
    const m = parseFloat(acadMax.value) || 100;
    acadKeys.forEach(key => {
      const scoreEl = document.getElementById('acad_' + key + '_score');
      const pctEl   = document.getElementById('acad_' + key + '_pct');
      if (scoreEl && pctEl) pctEl.value = toPct(scoreEl.value, m);
    });
  }

  function updatePassFail() {
    const t = parseFloat(total.value);
    const m = parseFloat(maxEl.value)   || 100;
    const p = parseFloat(passing.value) || 75;
    if (isNaN(t)) { box.style.display = 'none'; eligWrap.style.display = 'none'; return; }
    const passed = t >= p;
    const pct    = m > 0 ? ((t / m) * 100).toFixed(1) : '—';
    box.style.cssText = 'display:block;border-radius:12px;padding:.75rem 1rem;text-align:center;font-weight:800;font-size:.95rem;' +
      'background:' + (passed ? '#f0fdf4' : '#fef2f2') + ';' +
      'border:1px solid ' + (passed ? '#bbf7d0' : '#fca5a5') + ';' +
      'color:' + (passed ? '#15803d' : '#dc2626') + ';';
    box.textContent = (passed ? '✓ PASSED' : '✗ FAILED') + '  —  ' + t.toFixed(0) + ' / ' + m.toFixed(0) + ' (' + pct + '%)';
    @if($applicant->status !== 'eligible_for_enrollment')
    eligWrap.style.display = passed ? 'block' : 'none';
    @endif
  }

  // Bind admission inputs
  [nvScore, nvMax].forEach(el => el && el.addEventListener('input', syncAdmission));
  [vScore, vMax].forEach(el => el && el.addEventListener('input', syncAdmission));

  // Bind academic inputs
  acadKeys.forEach(key => {
    const el = document.getElementById('acad_' + key + '_score');
    if (el) el.addEventListener('input', syncAcademic);
  });
  acadMax && acadMax.addEventListener('input', syncAcademic);

  // Manual total/max/passing also update the pass/fail banner
  [total, maxEl, passing].forEach(el => el && el.addEventListener('input', updatePassFail));

  // Run on load
  syncAdmission();
  syncAcademic();
})();
</script>
@endpush
