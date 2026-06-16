@extends('layouts.app')

@section('title', 'Application — ' . $applicant->reference_number)
@section('breadcrumb', 'Application Detail')

@push('head')
<style>
/* ── Pipeline stepper ────────────────────────────────────────── */
.app-pipeline { display:flex; align-items:flex-start; gap:0; }
.app-pipeline-step {
  flex:1; display:flex; flex-direction:column; align-items:center; gap:5px;
  position:relative;
}
.app-pipeline-step:not(:last-child)::after {
  content:''; position:absolute;
  top:13px; left:calc(50% + 13px); right:calc(-50% + 13px);
  height:2px; background:#e2e8f0; z-index:0;
}
.app-pipeline-step.is-done:not(:last-child)::after    { background:#86efac; }
.app-pipeline-step.is-rejected:not(:last-child)::after{ background:#fca5a5; }

.app-step-dot {
  width:26px; height:26px; border-radius:50%;
  display:flex; align-items:center; justify-content:center;
  font-size:.68rem; font-weight:800; position:relative; z-index:1; flex-shrink:0;
}
.app-step-dot--done     { background:#16a34a; color:#fff; }
.app-step-dot--active   { background:#1d4ed8; color:#fff; box-shadow:0 0 0 3px rgba(29,78,216,.18); }
.app-step-dot--pending  { background:#f1f5f9; color:#94a3b8; border:2px solid #e2e8f0; }
.app-step-dot--rejected { background:#dc2626; color:#fff; }
.app-step-dot--violet   { background:#7c3aed; color:#fff; }

.app-step-label {
  font-size:.62rem; font-weight:700; text-align:center;
  color:#94a3b8; text-transform:uppercase; letter-spacing:.04em; line-height:1.3;
}
.app-step-label.is-done     { color:#16a34a; }
.app-step-label.is-active   { color:#1d4ed8; }
.app-step-label.is-rejected { color:#dc2626; }
.app-step-label.is-violet   { color:#7c3aed; }

/* ── Misc ───────────────────────────────────────────────────── */
.adm-status { display:inline-block; padding:.22rem .65rem; border-radius:999px; font-size:.73rem; font-weight:700; }
.adm-status--pending                { background:#fef9c3; color:#854d0e; }
.adm-status--under_review           { background:#dbeafe; color:#1e40af; }
.adm-status--accepted               { background:#dcfce7; color:#166534; }
.adm-status--rejected               { background:#fee2e2; color:#991b1b; }
.adm-status--enrolled               { background:#e0f2fe; color:#0369a1; }
.adm-status--eligible_for_enrollment{ background:#ede9fe; color:#5b21b6; }
.adm-label { display:block; font-size:.76rem; font-weight:700; color:var(--gray-500); margin-bottom:.3rem; }
.adm-input { width:100%; padding:.55rem .85rem; border:1px solid rgba(15,23,42,.14); border-radius:8px; font-size:.88rem; background:#fff; color:var(--navy); font-family:inherit; }
.adm-input:focus { outline:none; border-color:var(--primary); }
textarea.adm-input { resize:vertical; }
.app-hint { border-radius:8px; padding:.6rem .9rem; font-size:.8rem; line-height:1.55; }
.app-hint--blue   { background:#eff6ff; border:1px solid #bfdbfe; border-left:3px solid #2563eb; color:#1e40af; }
.app-hint--green  { background:#f0fdf4; border:1px solid #86efac; border-left:3px solid #16a34a; color:#166534; }
.app-hint--violet { background:#f5f3ff; border:1px solid #c4b5fd; border-left:3px solid #7c3aed; color:#5b21b6; }
.app-hint--red    { background:#fef2f2; border:1px solid #fca5a5; border-left:3px solid #dc2626; color:#991b1b; }
</style>
@endpush

@section('content')

<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">{{ $applicant->full_name }}</h1>
      <p class="enc-page__subtitle">Ref: {{ $applicant->reference_number }} · Submitted {{ $applicant->created_at->format('M d, Y') }}</p>
    </div>
    <a href="{{ route('registrar.applicants.index') }}" class="enc-btn enc-btn--ghost">← Back to List</a>
  </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.85rem 1.1rem;font-size:.87rem;color:#166534;margin-bottom:1.1rem;line-height:1.6;">
  {!! session('success') !!}
</div>
@endif
@if(session('error'))
<div style="background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;padding:.85rem 1.1rem;font-size:.87rem;color:#991b1b;margin-bottom:1.1rem;">
  {{ session('error') }}
</div>
@endif

@php
  $status = $applicant->status;
  $stepState = function(string $key) use ($status): string {
    return match($key) {
      'submitted' => 'done',
      'review'    => match($status) {
        'pending'                             => 'active',
        'under_review','accepted','rejected',
        'eligible_for_enrollment','enrolled'  => 'done',
        default => 'pending',
      },
      'decision'  => match($status) {
        'under_review'                        => 'active',
        'accepted','eligible_for_enrollment',
        'enrolled'                            => 'done',
        'rejected'                            => 'rejected',
        default => 'pending',
      },
      'account'   => match($status) {
        'accepted'               => 'active',
        'eligible_for_enrollment'=> 'violet',
        'enrolled'               => 'done',
        default => 'pending',
      },
      default => 'pending',
    };
  };

  $hints = [
    'pending'                => ['blue',   'Move to <strong>Under Review</strong> to begin evaluating this application.'],
    'under_review'           => ['blue',   'Review the applicant\'s details then set a decision: <strong>Accept</strong> or <strong>Reject</strong>.'],
    'accepted'               => ['green',  'Application accepted. Click <strong>Create Student Account</strong> below to finalize enrollment.'],
    'eligible_for_enrollment'=> ['violet', 'Applicant is eligible for enrollment. Confirm readiness before creating the student account.'],
    'rejected'               => ['red',    'This application was rejected. You may reopen it by updating the status.'],
    'enrolled'               => ['green',  'Enrollment is complete. The student account has been created.'],
  ];
  [$hintColor, $hintText] = $hints[$status] ?? ['blue', ''];
@endphp

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.25rem;align-items:start;">

  {{-- ── LEFT: applicant details ─────────────────────────────────── --}}
  <div style="display:grid;gap:1.1rem;">

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Personal Information</div></div>
      <div class="enc-card__body">
        @include('registrar.applicants._info-grid', ['rows' => [
          ['Date of Birth', $applicant->date_of_birth?->format('F d, Y') ?? '—'],
          ['Sex', $applicant->sex],
          ['LRN', $applicant->lrn ?? '—'],
          ['Nationality', $applicant->nationality ?? '—'],
        ]])
      </div>
    </div>

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Home Address</div></div>
      <div class="enc-card__body" style="font-size:.88rem;color:var(--navy);line-height:1.7;">
        {{ $applicant->address }}<br>
        @if($applicant->barangay) Brgy. {{ $applicant->barangay }}, @endif
        @if($applicant->municipality) {{ $applicant->municipality }}, @endif
        @if($applicant->province) {{ $applicant->province }} @endif
        @if($applicant->zip_code) {{ $applicant->zip_code }} @endif
      </div>
    </div>

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Previous School</div></div>
      <div class="enc-card__body">
        @include('registrar.applicants._info-grid', ['rows' => [
          ['School', $applicant->previous_school ?? '—'],
          ['Grade Completed', $applicant->previous_grade_level ?? '—'],
          ['School Year', $applicant->school_year_completed ?? '—'],
        ]])
      </div>
    </div>

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Parent / Guardian</div></div>
      <div class="enc-card__body">
        @include('registrar.applicants._info-grid', ['rows' => [
          ['Name', $applicant->parent_guardian_name],
          ['Relationship', $applicant->relationship],
          ['Contact', $applicant->parent_contact],
          ['Email', $applicant->parent_email ?? '—'],
        ]])
      </div>
    </div>

    @php $etr = $applicant->entranceTestResult; @endphp
    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Guidance &amp; Testing</div></div>
      <div class="enc-card__body">
        @if($etr)
          <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
            <span style="display:inline-block;padding:.3rem .9rem;border-radius:999px;font-size:.78rem;font-weight:800;
              background:{{ $etr->passed ? '#dcfce7' : '#fee2e2' }};color:{{ $etr->passed ? '#166534' : '#991b1b' }};">
              {{ $etr->passed ? 'PASSED' : 'FAILED' }}
            </span>
            <span style="font-size:.85rem;color:var(--gray-500);">{{ $etr->test_date->format('M d, Y') }}</span>
          </div>
          @include('registrar.applicants._info-grid', ['rows' => [
            ['Score',   number_format($etr->total_score,0).' / '.number_format($etr->max_score,0).' ('.$etr->percentage.'%)'],
            ['Passing', number_format($etr->passing_score,0)],
            ['NV',    $etr->nv_score !== null ? number_format($etr->nv_score,0).($etr->nv_pct !== null ? ' ('.$etr->nv_pct.'%)' : '').($etr->nv_descriptive ? ' · '.$etr->nv_descriptive : '') : '—'],
            ['Verbal',$etr->v_score  !== null ? number_format($etr->v_score, 0).($etr->v_pct  !== null ? ' ('.$etr->v_pct.'%)'  : '').($etr->v_descriptive  ? ' · '.$etr->v_descriptive  : '') : '—'],
          ]])
          @if($etr->acad_filipino_score !== null || $etr->acad_english_score !== null)
          <div style="margin-top:.65rem;padding:.6rem .8rem;background:#f8fafc;border-radius:8px;font-size:.8rem;color:var(--gray-500);display:grid;grid-template-columns:1fr 1fr;gap:.35rem;">
            @foreach(['filipino'=>'Filipino','english'=>'English','math'=>'Math','science'=>'Science'] as $k=>$lbl)
            @php $p = $etr->{'acad_'.$k.'_pct'}; $d = $etr->{'acad_'.$k.'_desc'}; @endphp
            @if($p !== null || $d)<div><strong>{{ $lbl }}:</strong> {{ $p !== null ? $p.'%' : '' }}{{ $d ? ' · '.$d : '' }}</div>@endif
            @endforeach
          </div>
          @endif
          @if($etr->notes)<div style="margin-top:.55rem;font-size:.8rem;color:var(--gray-400);font-style:italic;">{{ $etr->notes }}</div>@endif
        @else
          <div style="color:var(--gray-400);font-size:.88rem;">No test record yet.</div>
        @endif
      </div>
    </div>

  </div>

  {{-- ── RIGHT: pipeline + actions ────────────────────────────────── --}}
  <div style="display:grid;gap:1rem;">

    {{-- Onboarding pipeline --}}
    <div class="enc-card" style="padding:1.1rem 1.25rem 1.25rem;">
      <div style="font-size:.72rem;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.85rem;">Onboarding Pipeline</div>
      <div class="app-pipeline">
        @foreach([
          ['submitted','✓','Submitted'],
          ['review',   '2','Under Review'],
          ['decision', '3','Decision'],
          ['account',  '4','Account'],
        ] as [$key, $num, $lbl])
        @php
          $st = $stepState($key);
          $dotCls = match($st) {
            'done'     => 'app-step-dot--done',
            'active'   => 'app-step-dot--active',
            'rejected' => 'app-step-dot--rejected',
            'violet'   => 'app-step-dot--violet',
            default    => 'app-step-dot--pending',
          };
          $lblCls = match($st) {
            'done'     => 'is-done',
            'active'   => 'is-active',
            'rejected' => 'is-rejected',
            'violet'   => 'is-violet',
            default    => '',
          };
          $icon = match($st) { 'done' => '✓', 'rejected' => '✕', default => $num };
        @endphp
        <div class="app-pipeline-step {{ in_array($st,['done','rejected']) ? 'is-'.$st : '' }}">
          <div class="app-step-dot {{ $dotCls }}">{{ $icon }}</div>
          <div class="app-step-label {{ $lblCls }}">{{ $lbl }}</div>
        </div>
        @endforeach
      </div>
      @if($hintText)
      <div class="app-hint app-hint--{{ $hintColor }}" style="margin-top:.85rem;">{!! $hintText !!}</div>
      @endif
    </div>

    {{-- Current status --}}
    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Application Status</div></div>
      <div class="enc-card__body" style="display:grid;gap:.75rem;">
        <div>
          <span class="adm-status adm-status--{{ $applicant->status }}" style="font-size:.85rem;padding:.3rem .9rem;">
            {{ ucfirst(str_replace('_', ' ', $applicant->status)) }}
          </span>
        </div>
        @include('registrar.applicants._info-grid', ['rows' => [
          ['Applying For', $applicant->applying_for_grade . ($applicant->applying_for_year ? ' · ' . $applicant->applying_for_year : '')],
          ['Submitted', $applicant->created_at->format('M d, Y h:i A')],
          ['Reviewed By', $applicant->reviewedBy?->full_name ?? '—'],
          ['Reviewed At', $applicant->reviewed_at?->format('M d, Y') ?? '—'],
        ]])
        @if($applicant->remarks)
        <div style="background:#fef9c3;border-radius:8px;padding:.65rem .9rem;font-size:.82rem;color:#854d0e;">
          <strong>Remarks:</strong> {{ $applicant->remarks }}
        </div>
        @endif
      </div>
    </div>

    {{-- Update status form --}}
    @if($applicant->status !== 'enrolled')
    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Update Decision</div></div>
      <div class="enc-card__body">
        <form method="POST" action="{{ route('registrar.applicants.update-status', $applicant->id) }}"
              style="display:grid;gap:.75rem;">
          @csrf @method('PATCH')
          <div>
            <label class="adm-label">New Status</label>
            <select name="status" class="adm-input" required>
              @foreach(['pending','under_review','accepted','rejected','eligible_for_enrollment','enrolled'] as $s)
              <option value="{{ $s }}" {{ $applicant->status === $s ? 'selected' : '' }}>
                {{ ucfirst(str_replace('_',' ',$s)) }}
              </option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="adm-label">Remarks</label>
            <textarea name="remarks" class="adm-input" rows="3"
              placeholder="Notes or reason for this decision…">{{ old('remarks', $applicant->remarks) }}</textarea>
          </div>
          <button type="submit" class="enc-btn enc-btn--primary" style="width:100%;">Save Decision</button>
        </form>
      </div>
    </div>
    @endif

    {{-- Create student account --}}
    @if($applicant->status === 'accepted')
    <div class="enc-card" style="padding:1.25rem;border:2px solid #bbf7d0;">
      <div class="enc-card__header">
        <div class="enc-card__title" style="color:#166534;">Create Student Account</div>
      </div>
      <div class="enc-card__body">
        <p style="font-size:.82rem;color:var(--gray-500);margin-bottom:.9rem;line-height:1.6;">
          Generates a username, LRN, and temporary password for this applicant.
          @if($applicant->parent_email)
            Credentials will be emailed to <strong>{{ $applicant->parent_email }}</strong>.
          @else
            No parent email on file — share credentials manually.
          @endif
        </p>
        <form method="POST" action="{{ route('registrar.applicants.create-account', $applicant->id) }}"
              onsubmit="return confirm('Create a student account for {{ addslashes($applicant->full_name) }}?');">
          @csrf
          <button type="submit" class="enc-btn enc-btn--primary" style="width:100%;background:#16a34a;border-color:#16a34a;">
            Create Student Account
          </button>
        </form>
      </div>
    </div>
    @endif

    @if($applicant->status === 'enrolled')
    <div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:.85rem 1rem;font-size:.85rem;color:#166534;font-weight:600;display:flex;align-items:center;gap:.5rem;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Student account created. Enrollment is complete.
    </div>
    @endif

  </div>
</div>

@endsection
