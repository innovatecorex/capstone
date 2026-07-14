<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Received</title>
  <style>
    body { margin:0; padding:0; background:#f0f4f8; font-family: 'Segoe UI', Arial, sans-serif; }
    .wrap { max-width:580px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(15,23,42,.10); }
    .header { background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 100%); padding:36px 40px 28px; text-align:center; }
    .header h1 { color:#fff; font-size:1.4rem; margin:0 0 6px; font-weight:800; }
    .header p  { color:rgba(255,255,255,.82); font-size:.88rem; margin:0; }
    .badge { display:inline-block; background:#dbeafe; color:#1e40af; font-size:.78rem; font-weight:700; padding:6px 18px; border-radius:999px; margin:20px 0 0; letter-spacing:.04em; text-transform:uppercase; }
    .body  { padding:32px 40px; }
    .body p { color:#374151; font-size:.93rem; line-height:1.7; margin:0 0 14px; }
    .body p strong { color:#111827; }

    /* Reference number — the whole point of this email. */
    .ref-box { background:#f8fafc; border:2px dashed #cbd5e1; border-radius:12px; padding:22px; margin:24px 0; text-align:center; }
    .ref-label { font-size:.72rem; font-weight:800; color:#64748b; letter-spacing:.12em; text-transform:uppercase; margin:0 0 8px; }
    .ref-number { font-family:'Courier New', Courier, monospace; font-size:1.6rem; font-weight:800; color:#0f172a; letter-spacing:.06em; margin:0; }
    .ref-hint { font-size:.76rem; color:#94a3b8; margin:10px 0 0; }

    .info-box { background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:18px 22px; margin:20px 0; }
    .info-box p { margin:0; font-size:.87rem; color:#1e40af; }
    .info-box p + p { margin-top:6px; }
    .steps { margin:22px 0; padding:0; list-style:none; }
    .steps li { display:flex; gap:12px; align-items:flex-start; margin-bottom:14px; font-size:.87rem; color:#374151; }
    .step-num { width:24px; height:24px; border-radius:50%; background:#1d4ed8; color:#fff; font-size:.72rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px; }
    .footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 40px; text-align:center; font-size:.78rem; color:#94a3b8; }
  </style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <h1>Application Received</h1>
    <p>Philippine Academy of Sakya — Admissions Office</p>
    <span class="badge">✓ Successfully Submitted</span>
  </div>

  <div class="body">
    <p>Dear <strong>{{ $applicant->parent_guardian_name }}</strong>,</p>

    <p>
      Thank you. We have received the application of
      <strong>{{ $applicant->first_name }} {{ $applicant->last_name }}</strong>
      for <strong>{{ $applicant->applying_for_grade }}</strong> at
      <strong>Philippine Academy of Sakya</strong>.
      This email is your proof of submission.
    </p>

    {{-- The reference number is the applicant's only handle for tracking the
         application, so it is the most prominent thing in this email. --}}
    <div class="ref-box">
      <p class="ref-label">Your Reference Number</p>
      <p class="ref-number">{{ $applicant->reference_number }}</p>
      <p class="ref-hint">Please keep this number. You will need it for any enquiry about your application.</p>
    </div>

    <div class="info-box">
      <p><strong>Applicant:</strong> {{ $applicant->first_name }} {{ $applicant->last_name }}</p>
      <p><strong>Grade Applying For:</strong> {{ $applicant->applying_for_grade }}</p>
      @if($applicant->applying_for_year)
      <p><strong>School Year:</strong> {{ $applicant->applying_for_year }}</p>
      @endif
      <p><strong>Date Submitted:</strong> {{ $applicant->created_at?->format('F d, Y \a\t g:i A') }}</p>
    </div>

    <p><strong>What happens next?</strong></p>
    <ul class="steps">
      <li>
        <span class="step-num">1</span>
        <span>Our registrar will review the application and the submitted documents.</span>
      </li>
      <li>
        <span class="step-num">2</span>
        <span>You will be notified by email once a decision has been made on the application.</span>
      </li>
      <li>
        <span class="step-num">3</span>
        <span>If accepted, you will receive further instructions for enrollment and the student portal.</span>
      </li>
    </ul>

    <p>
      No action is needed from you at this time. If you have any questions, please
      contact the school's registrar office and quote your reference number above.
    </p>
  </div>

  <div class="footer">
    <p>Philippine Academy of Sakya &nbsp;·&nbsp; Admissions Office</p>
    <p style="margin-top:4px;">This is an automated notification. Please do not reply to this email.</p>
    <p style="margin-top:4px; font-size:.72rem; color:#cbd5e1;">Protected under RA 10173 – Data Privacy Act of 2012</p>
  </div>

</div>
</body>
</html>
