@extends('layouts.app')

@section('title', 'Application — ' . $applicant->reference_number)
@section('breadcrumb', 'Application Detail')

@push('head')
<style>
@keyframes camIn {
  from { opacity:0; transform:scale(.93) translateY(12px); }
  to   { opacity:1; transform:scale(1)   translateY(0);    }
}
@keyframes camBarFlow {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
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
.adm-status--waitlisted             { background:#fef3c7; color:#92400e; }
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
.app-hint--amber  { background:#fffbeb; border:1px solid #fde68a; border-left:3px solid #d97706; color:#92400e; }
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
        'pending'                                      => 'active',
        'under_review','waitlisted','accepted',
        'rejected','eligible_for_enrollment','enrolled' => 'done',
        default => 'pending',
      },
      'decision'  => match($status) {
        'under_review'                                 => 'active',
        'waitlisted'                                   => 'active',
        'accepted','eligible_for_enrollment','enrolled' => 'done',
        'rejected'                                     => 'rejected',
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
    'under_review'           => ['blue',   'Review the applicant\'s details then set a decision: <strong>Accept</strong>, <strong>Waitlist</strong>, or <strong>Reject</strong>.'],
    'waitlisted'             => ['amber',  'This applicant is on the waitlist. A notification email was sent. Upgrade to <strong>Accepted</strong> when a slot opens.'],
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

    {{-- Uploaded Documents --}}
    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Uploaded Documents</div></div>
      <div class="enc-card__body">
        @if($applicant->documents->isEmpty())
          <p style="font-size:.85rem;color:var(--gray-400);">No documents uploaded with this application.</p>
        @else
          <div style="display:grid;gap:.65rem;">
            @foreach($applicant->documents as $doc)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem .9rem;background:#f8fafc;border-radius:8px;border:1px solid rgba(15,23,42,.08);">
              <div>
                <div style="font-size:.85rem;font-weight:700;color:var(--navy);">{{ $doc->label }}</div>
                <div style="font-size:.76rem;color:var(--gray-400);">{{ $doc->original_name }} · {{ $doc->file_size_formatted }}</div>
              </div>
              <a href="{{ route('applicant.document.download', $doc->id) }}" target="_blank"
                 style="font-size:.78rem;font-weight:700;color:var(--primary);text-decoration:none;white-space:nowrap;padding:.35rem .8rem;background:rgba(79,70,229,.08);border-radius:6px;">
                View ↗
              </a>
            </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    {{-- Required Documents Checklist --}}
    @php
      $allRequiredChecked = collect($requirements)
        ->filter(fn($r) => $r['required'])
        ->keys()
        ->every(fn($k) => optional($requirementChecks->get($k))->is_submitted);
      $anyRequired = collect($requirements)->contains(fn($r) => $r['required']);
    @endphp
    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.85rem;">
        <div class="enc-card__title">Required Documents Checklist</div>
        @if($anyRequired)
          @if($allRequiredChecked)
          <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.22rem .7rem;border-radius:999px;font-size:.72rem;font-weight:800;background:#dcfce7;color:#166534;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:12px;height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            All Confirmed
          </span>
          @else
          <span style="padding:.22rem .7rem;border-radius:999px;font-size:.72rem;font-weight:800;background:#fef9c3;color:#854d0e;">Incomplete</span>
          @endif
        @endif
      </div>
      <div class="enc-card__body">
        <form method="POST" action="{{ route('registrar.applicants.save-requirements', $applicant->id) }}">
          @csrf
          <div style="display:grid;gap:.5rem;margin-bottom:.9rem;">
            @foreach($requirements as $key => $req)
            @php
              $check     = $requirementChecks->get($key);
              $ticked    = $check && $check->is_submitted;
            @endphp
            <label style="display:flex;align-items:flex-start;gap:.7rem;padding:.6rem .8rem;border-radius:8px;cursor:pointer;
                          border:1px solid {{ $ticked ? '#86efac' : 'rgba(15,23,42,.1)' }};
                          background:{{ $ticked ? '#f0fdf4' : '#fafafa' }};">
              <input type="checkbox" name="requirements[{{ $key }}]" value="1"
                     {{ $ticked ? 'checked' : '' }}
                     style="margin-top:.18rem;flex-shrink:0;accent-color:#16a34a;width:15px;height:15px;">
              <div style="flex:1;min-width:0;">
                <div style="font-size:.85rem;font-weight:700;color:var(--navy);display:flex;align-items:center;gap:.4rem;">
                  {{ $req['label'] }}
                  @if($req['required'])
                  <span style="font-size:.67rem;font-weight:800;color:#dc2626;letter-spacing:.03em;">REQUIRED</span>
                  @endif
                </div>
                @if($ticked && $check->checkedBy)
                <div style="font-size:.72rem;color:#16a34a;margin-top:.12rem;">
                  Confirmed by {{ $check->checkedBy->full_name }} &middot; {{ $check->checked_at?->format('M d, Y g:i A') }}
                </div>
                @elseif($ticked)
                <div style="font-size:.72rem;color:#16a34a;margin-top:.12rem;">Confirmed</div>
                @endif
              </div>
            </label>
            @endforeach
          </div>

          @if($applicant->status !== 'enrolled')
          <button type="submit" class="enc-btn enc-btn--ghost" style="width:100%;font-size:.82rem;">
            Save Checklist
          </button>
          @endif
        </form>

        @if(!$allRequiredChecked && in_array($applicant->status, ['pending','under_review','waitlisted']))
        <div class="app-hint app-hint--amber" style="margin-top:.75rem;font-size:.79rem;">
          The required documents (PSA Birth Certificate and Form 138) must be confirmed before this applicant can be accepted.
        </div>
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

        {{-- Application status moves FORWARD only. 'enrolled' and 'rejected' are
             terminal, so a closed application offers no options at all. The
             server enforces this regardless — the dropdown just stops the
             registrar from attempting a move that would be refused. --}}
        @if(empty($allowedStatuses))
          <div style="padding:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;font-size:.85rem;color:#475569;line-height:1.6;">
            <strong style="color:#0f172a;">This application is {{ str_replace('_', ' ', $applicant->status) }}.</strong><br>
            It is final — its status can no longer be changed.
          </div>
        @else
        <form method="POST" action="{{ route('registrar.applicants.update-status', $applicant->id) }}"
              style="display:grid;gap:.75rem;">
          @csrf @method('PATCH')
          <div>
            <label class="adm-label">New Status</label>
            <select name="status" class="adm-input" required>
              @foreach($allowedStatuses as $s)
              <option value="{{ $s }}">{{ ucfirst(str_replace('_',' ',$s)) }}</option>
              @endforeach
            </select>
            <div style="font-size:.72rem;color:#94a3b8;margin-top:5px;line-height:1.5;">
              Currently <strong>{{ str_replace('_', ' ', $applicant->status) }}</strong>.
              Status can only move forward — it cannot be reverted.
            </div>
          </div>
          <div>
            <label class="adm-label">Remarks</label>
            <textarea name="remarks" class="adm-input" rows="3"
              placeholder="Notes or reason for this decision…">{{ old('remarks', $applicant->remarks) }}</textarea>
          </div>
          <button type="submit" class="enc-btn enc-btn--primary" style="width:100%;">Save Decision</button>
        </form>
        @endif
      </div>
    </div>
    @endif

    {{-- Create Student Account card removed: student accounts are created via
         CSV import / User Management, not per-applicant here. --}}

    @if($applicant->status === 'enrolled')
    <div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:.85rem 1rem;font-size:.85rem;color:#166534;font-weight:600;display:flex;align-items:center;gap:.5rem;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Student account created. Enrollment is complete.
    </div>
    @endif

  </div>
</div>

{{-- ── Create-account confirmation modal ─────────────────────────── --}}
<div id="create-account-modal"
     style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;"
     aria-modal="true" role="dialog" aria-labelledby="cam-title">

  {{-- Backdrop --}}
  <div onclick="closeCreateModal()"
       style="position:absolute;inset:0;background:rgba(2,8,23,.65);backdrop-filter:blur(5px);"></div>

  {{-- Card --}}
  <div style="position:relative;z-index:1;background:#fff;border-radius:20px;width:min(440px,92vw);
              box-shadow:0 32px 72px rgba(0,0,0,.28),0 4px 16px rgba(0,0,0,.12);
              overflow:hidden;animation:camIn .22s cubic-bezier(.22,1,.36,1);">

    {{-- Gradient top bar --}}
    <div style="height:4px;background:linear-gradient(90deg,#166534,#16a34a,#4ade80,#16a34a,#166534);
                background-size:200% 100%;animation:camBarFlow 3s linear infinite;"></div>

    {{-- Icon + headline --}}
    <div style="padding:2rem 2rem 1.25rem;text-align:center;">
      <div style="width:58px;height:58px;border-radius:16px;
                  background:linear-gradient(135deg,#dcfce7,#bbf7d0);
                  display:flex;align-items:center;justify-content:center;
                  margin:0 auto 1rem;box-shadow:0 4px 14px rgba(22,163,74,.18);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="#16a34a" stroke-width="1.7" style="width:28px;height:28px;">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
        </svg>
      </div>
      <div id="cam-title" style="font-size:1.05rem;font-weight:800;color:#0f172a;margin-bottom:.5rem;">
        Create Student Account
      </div>
      <span id="cam-name"
            style="display:inline-block;font-size:.84rem;font-weight:700;color:#166534;
                   background:#dcfce7;padding:.3rem .85rem;border-radius:99px;
                   border:1px solid #bbf7d0;"></span>
    </div>

    {{-- What will happen --}}
    <div style="margin:0 1.75rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;
                padding:.9rem 1rem;font-size:.8rem;color:#475569;line-height:1.7;">
      <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.35rem;font-weight:700;color:#0f172a;font-size:.82rem;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
        </svg>
        This will generate:
      </div>
      <ul style="margin:0;padding-left:1.1rem;display:flex;flex-direction:column;gap:.2rem;">
        <li>A unique <strong>username</strong> and <strong>LRN</strong></li>
        <li>A <strong>temporary password</strong> (must be changed on first login)</li>
        <li id="cam-email-line">Credentials emailed to the parent on file</li>
      </ul>
    </div>

    {{-- Warning --}}
    <div style="margin:1rem 1.75rem 0;background:#fffbeb;border:1px solid #fef3c7;border-left:3px solid #f59e0b;
                border-radius:10px;padding:.65rem .9rem;font-size:.75rem;color:#92400e;
                display:flex;align-items:flex-start;gap:.5rem;line-height:1.55;">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke="#d97706" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;margin-top:1px;">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
      </svg>
      This action <strong>cannot be undone.</strong> The applicant's status will permanently change to <strong>Enrolled</strong>.
    </div>

    {{-- Buttons --}}
    <div style="padding:1.5rem 1.75rem;display:flex;gap:.75rem;">
      <button onclick="closeCreateModal()"
              style="flex:1;padding:.7rem 1rem;border:1.5px solid #e2e8f0;border-radius:10px;
                     background:#fff;color:#475569;font-size:.875rem;font-weight:600;
                     cursor:pointer;font-family:inherit;transition:background .15s,border-color .15s;"
              onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'"
              onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0'">
        Cancel
      </button>
      <button id="cam-confirm-btn" onclick="confirmCreateAccount()"
              style="flex:2;padding:.7rem 1rem;border:none;border-radius:10px;
                     background:linear-gradient(135deg,#166534,#16a34a);color:#fff;
                     font-size:.875rem;font-weight:700;cursor:pointer;font-family:inherit;
                     box-shadow:0 4px 14px rgba(22,163,74,.3);transition:box-shadow .15s,opacity .15s;
                     display:flex;align-items:center;justify-content:center;gap:.45rem;"
              onmouseover="this.style.boxShadow='0 6px 20px rgba(22,163,74,.42)'"
              onmouseout="this.style.boxShadow='0 4px 14px rgba(22,163,74,.3)'">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2.2" style="width:15px;height:15px;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span id="cam-confirm-label">Confirm &amp; Create Account</span>
      </button>
    </div>
  </div>
</div>

@push('head')
<style>
@keyframes camIn {
  from { opacity:0; transform:scale(.93) translateY(12px); }
  to   { opacity:1; transform:scale(1)   translateY(0);    }
}
@keyframes camBarFlow {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
</style>
@endpush

@push('scripts')
<script>
function openCreateModal(name, email) {
  document.getElementById('cam-name').textContent = name;
  var emailLine = document.getElementById('cam-email-line');
  emailLine.innerHTML = email
    ? 'Credentials emailed to <strong>' + email + '</strong>'
    : '<em>No parent email on file — share credentials manually</em>';
  var modal = document.getElementById('create-account-modal');
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}
function closeCreateModal() {
  document.getElementById('create-account-modal').style.display = 'none';
  document.body.style.overflow = '';
}
function confirmCreateAccount() {
  var btn   = document.getElementById('cam-confirm-btn');
  var label = document.getElementById('cam-confirm-label');
  btn.disabled = true;
  btn.style.opacity = '.7';
  label.textContent = 'Creating account…';
  document.getElementById('create-account-form').submit();
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeCreateModal();
});
</script>
@endpush

@endsection
