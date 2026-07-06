@extends('layouts.app')

@section('title', 'Application — ' . $applicant->reference_number)
@section('breadcrumb', 'Applicant Detail')

@section('content')
<div class="enc-page__header">
  <div class="enc-page__title-row">
    <div>
      <h1 class="enc-page__title">{{ $applicant->full_name }}</h1>
      <p class="enc-page__subtitle">Ref: {{ $applicant->reference_number }} · Submitted {{ $applicant->created_at->format('M d, Y') }}</p>
    </div>
    <a href="{{ route('admin.applicants.index') }}" class="app-btn app-btn--ghost">← Back to List</a>
  </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:.75rem 1rem;font-size:.87rem;color:#166534;margin-bottom:1rem;">
  {!! session('success') !!}
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.25rem;align-items:start;">

  {{-- ── Left: applicant details ──────────────────────────────────── --}}
  <div style="display:grid;gap:1.1rem;">

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Personal Information</div></div>
      <div class="enc-card__body">
        @include('admin.applicants._info-grid', ['rows' => [
          ['Date of Birth', $applicant->date_of_birth->format('F d, Y')],
          ['Sex', $applicant->sex],
          ['LRN', $applicant->lrn ?? '—'],
          ['Nationality', $applicant->nationality ?? '—'],
        ]])
      </div>
    </div>

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Address</div></div>
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
        @include('admin.applicants._info-grid', ['rows' => [
          ['School', $applicant->previous_school ?? '—'],
          ['Grade Completed', $applicant->previous_grade_level ?? '—'],
          ['School Year', $applicant->school_year_completed ?? '—'],
        ]])
      </div>
    </div>

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Parent / Guardian</div></div>
      <div class="enc-card__body">
        @include('admin.applicants._info-grid', ['rows' => [
          ['Name', $applicant->parent_guardian_name],
          ['Relationship', $applicant->relationship],
          ['Contact', $applicant->parent_contact],
          ['Email', $applicant->parent_email ?? '—'],
        ]])
      </div>
    </div>

    {{-- Guidance & Test Result --}}
    @php $etr = $applicant->entranceTestResult; @endphp
    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header">
        <div class="enc-card__title">Guidance &amp; Testing</div>
        <a href="{{ route('admin.guidance-testing.create', $applicant->id) }}"
           style="font-size:.78rem;font-weight:700;color:var(--primary);text-decoration:none;">
          {{ $etr ? 'Edit Record' : 'Record Test' }} →
        </a>
      </div>
      <div class="enc-card__body">
        @if($etr)
          <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;">
            <span style="display:inline-block;padding:.3rem .9rem;border-radius:999px;font-size:.78rem;font-weight:800;
              background:{{ $etr->passed ? '#dcfce7' : '#fee2e2' }};
              color:{{ $etr->passed ? '#166534' : '#991b1b' }};">
              {{ $etr->passed ? 'PASSED' : 'FAILED' }}
            </span>
            <span style="font-size:.85rem;color:var(--gray-500);">{{ $etr->test_date->format('M d, Y') }}</span>
          </div>
          @include('admin.applicants._info-grid', ['rows' => [
            ['Score',    number_format($etr->total_score, 0) . ' / ' . number_format($etr->max_score, 0) . ' (' . $etr->percentage . '%)'],
            ['Passing',  number_format($etr->passing_score, 0)],
            ['NV',    $etr->nv_score !== null ? number_format($etr->nv_score,0).($etr->nv_pct !== null ? ' ('.$etr->nv_pct.'%)' : '').($etr->nv_descriptive ? ' · '.$etr->nv_descriptive : '') : '—'],
            ['Verbal',$etr->v_score  !== null ? number_format($etr->v_score, 0).($etr->v_pct  !== null ? ' ('.$etr->v_pct.'%)'  : '').($etr->v_descriptive  ? ' · '.$etr->v_descriptive  : '') : '—'],
          ]])
          @if($etr->acad_filipino_score !== null || $etr->acad_english_score !== null)
          <div style="margin-top:.65rem;padding:.6rem .8rem;background:#f8fafc;border-radius:8px;font-size:.8rem;color:var(--gray-500);display:grid;grid-template-columns:1fr 1fr;gap:.35rem;">
            @foreach(['filipino'=>'Filipino','english'=>'English','math'=>'Math','science'=>'Science'] as $k=>$lbl)
            @php $pct = $etr->{'acad_'.$k.'_pct'}; $desc = $etr->{'acad_'.$k.'_desc'}; @endphp
            @if($pct !== null || $desc)
            <div><strong>{{ $lbl }}:</strong> {{ $pct !== null ? $pct.'%' : '' }}{{ $desc ? ' · '.$desc : '' }}</div>
            @endif
            @endforeach
          </div>
          @endif
          @if($etr->notes)
          <div style="margin-top:.55rem;font-size:.8rem;color:var(--gray-400);font-style:italic;">{{ $etr->notes }}</div>
          @endif
        @else
          <div style="color:var(--gray-400);font-size:.88rem;">No test record yet. <a href="{{ route('admin.guidance-testing.create', $applicant->id) }}" style="color:var(--primary);text-decoration:none;font-weight:600;">Record now →</a></div>
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

  </div>

  {{-- ── Right: status panel ───────────────────────────────────────── --}}
  <div style="display:grid;gap:1rem;">

    <div class="enc-card" style="padding:1.25rem;">
      <div class="enc-card__header"><div class="enc-card__title">Application Status</div></div>
      <div class="enc-card__body" style="display:grid;gap:.75rem;">

        <div style="display:flex;align-items:center;gap:.6rem;">
          <span class="status-chip status-{{ $applicant->status }}" style="font-size:.85rem;padding:.3rem .9rem;">
            {{ ucfirst(str_replace('_', ' ', $applicant->status)) }}
          </span>
        </div>

        @include('admin.applicants._info-grid', ['rows' => [
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
      <div class="enc-card__header"><div class="enc-card__title">Application Status</div></div>
      <div class="enc-card__body">
        {{-- Read-only: admission decisions are processed by the Registrar. --}}
        <div style="display:grid;gap:.5rem;">
          <div>
            <span class="app-label">Current Status</span>
            <div style="display:inline-block;margin-top:4px;padding:.35rem .9rem;border-radius:999px;font-weight:700;font-size:.85rem;
                        background:#eef2ff;color:#3730a3;text-transform:capitalize;">
              {{ str_replace('_',' ', $applicant->status) }}
            </div>
          </div>
          @if($applicant->remarks)
          <div>
            <span class="app-label">Remarks</span>
            <div style="font-size:.85rem;color:#475569;margin-top:2px;">{{ $applicant->remarks }}</div>
          </div>
          @endif
          <div style="font-size:.78rem;color:#94a3b8;margin-top:4px;display:flex;gap:6px;align-items:center;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
            Admission decisions are processed by the Registrar.
          </div>
        </div>
      </div>
    </div>
    @endif

    {{-- Create student account (only when accepted) --}}
    @if($applicant->status === 'accepted')
    <div class="enc-card" style="padding:1.25rem;border:2px solid #dcfce7;">
      <div class="enc-card__header">
        <div class="enc-card__title" style="color:#166534;">Create Student Account</div>
      </div>
      <div class="enc-card__body">
        <p style="font-size:.82rem;color:var(--gray-500);margin-bottom:.9rem;line-height:1.6;">
          Generates login credentials for this applicant.
          @if($applicant->parent_email)
            Credentials will be emailed to <strong>{{ $applicant->parent_email }}</strong>.
          @else
            No parent email on file — share credentials manually after creation.
          @endif
        </p>
        <form method="POST" action="{{ route('admin.applicants.create-account', $applicant->id) }}" id="create-account-form">
          @csrf
          <button type="button"
            style="width:100%;padding:.65rem;background:#16a34a;color:#fff;border:none;border-radius:8px;font-weight:700;cursor:pointer;font-size:.88rem;display:flex;align-items:center;justify-content:center;gap:.5rem;font-family:inherit;"
            onclick="openCreateModal(
              '{{ addslashes($applicant->full_name) }}',
              '{{ addslashes($applicant->parent_email ?? '') }}'
            )">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
            </svg>
            Create Student Account
          </button>
        </form>
      </div>
    </div>
    @endif

    @if($applicant->status === 'enrolled')
    <div style="background:#e0f2fe;border-radius:10px;padding:.85rem 1rem;font-size:.85rem;color:#0369a1;font-weight:600;">
      ✓ Student account has been created. This applicant is now enrolled.
    </div>
    @endif

  </div>
</div>

{{-- ── Create-account confirmation modal ─────────────────────────── --}}
<div id="create-account-modal"
     style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;"
     aria-modal="true" role="dialog" aria-labelledby="cam-title">
  <div onclick="closeCreateModal()"
       style="position:absolute;inset:0;background:rgba(2,8,23,.65);backdrop-filter:blur(5px);"></div>
  <div style="position:relative;z-index:1;background:#fff;border-radius:20px;width:min(440px,92vw);
              box-shadow:0 32px 72px rgba(0,0,0,.28),0 4px 16px rgba(0,0,0,.12);
              overflow:hidden;animation:camIn .22s cubic-bezier(.22,1,.36,1);">
    <div style="height:4px;background:linear-gradient(90deg,#166534,#16a34a,#4ade80,#16a34a,#166534);
                background-size:200% 100%;animation:camBarFlow 3s linear infinite;"></div>
    <div style="padding:2rem 2rem 1.25rem;text-align:center;">
      <div style="width:58px;height:58px;border-radius:16px;background:linear-gradient(135deg,#dcfce7,#bbf7d0);
                  display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;
                  box-shadow:0 4px 14px rgba(22,163,74,.18);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke="#16a34a" stroke-width="1.7" style="width:28px;height:28px;">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/>
        </svg>
      </div>
      <div id="cam-title" style="font-size:1.05rem;font-weight:800;color:#0f172a;margin-bottom:.5rem;">Create Student Account</div>
      <span id="cam-name" style="display:inline-block;font-size:.84rem;font-weight:700;color:#166534;
                                  background:#dcfce7;padding:.3rem .85rem;border-radius:99px;border:1px solid #bbf7d0;"></span>
    </div>
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
    <div style="padding:1.5rem 1.75rem;display:flex;gap:.75rem;">
      <button onclick="closeCreateModal()"
              style="flex:1;padding:.7rem 1rem;border:1.5px solid #e2e8f0;border-radius:10px;
                     background:#fff;color:#475569;font-size:.875rem;font-weight:600;
                     cursor:pointer;font-family:inherit;"
              onmouseover="this.style.background='#f8fafc';this.style.borderColor='#cbd5e1'"
              onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0'">
        Cancel
      </button>
      <button id="cam-confirm-btn" onclick="confirmCreateAccount()"
              style="flex:2;padding:.7rem 1rem;border:none;border-radius:10px;
                     background:linear-gradient(135deg,#166534,#16a34a);color:#fff;
                     font-size:.875rem;font-weight:700;cursor:pointer;font-family:inherit;
                     box-shadow:0 4px 14px rgba(22,163,74,.3);
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
.status-chip { display:inline-block; padding:.22rem .65rem; border-radius:999px; font-size:.73rem; font-weight:700; }
.status-pending      { background:#fef9c3; color:#854d0e; }
.status-under_review { background:#dbeafe; color:#1e40af; }
.status-waitlisted   { background:#fef3c7; color:#92400e; }
.status-accepted     { background:#dcfce7; color:#166534; }
.status-rejected     { background:#fee2e2; color:#991b1b; }
.status-enrolled                { background:#e0f2fe; color:#0369a1; }
.status-eligible_for_enrollment { background:#fffbeb; color:#92400e; }
.app-label { display:block; font-size:.76rem; font-weight:700; color:var(--gray-500); margin-bottom:.3rem; }
.app-input { width:100%; padding:.55rem .85rem; border:1px solid rgba(15,23,42,.14); border-radius:8px; font-size:.88rem; background:#fff; color:var(--navy); font-family:inherit; }
.app-input:focus { outline:none; border-color:var(--primary); }
textarea.app-input { resize:vertical; }
.app-btn { display:inline-flex; align-items:center; justify-content:center; padding:.55rem 1.1rem; border-radius:999px; font-weight:700; text-decoration:none; font-size:.87rem; }
.app-btn--ghost { background:rgba(15,23,42,.07); color:var(--navy); }
</style>
@endpush
