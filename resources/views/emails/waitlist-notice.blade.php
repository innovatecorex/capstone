<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Waitlisted</title>
  <style>
    body { margin:0; padding:0; background:#f0f4f8; font-family: 'Segoe UI', Arial, sans-serif; }
    .wrap { max-width:580px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(15,23,42,.10); }
    .header { background:linear-gradient(135deg,#d97706 0%,#b45309 100%); padding:36px 40px 28px; text-align:center; }
    .header h1 { color:#fff; font-size:1.4rem; margin:0 0 6px; font-weight:800; }
    .header p  { color:rgba(255,255,255,.82); font-size:.88rem; margin:0; }
    .badge { display:inline-block; background:#fef9c3; color:#92400e; font-size:.78rem; font-weight:700; padding:6px 18px; border-radius:999px; margin:20px 0 0; letter-spacing:.04em; text-transform:uppercase; }
    .body  { padding:32px 40px; }
    .body p { color:#374151; font-size:.93rem; line-height:1.7; margin:0 0 14px; }
    .body p strong { color:#111827; }
    .info-box { background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:18px 22px; margin:20px 0; }
    .info-box p { margin:0; font-size:.87rem; color:#92400e; }
    .info-box p + p { margin-top:6px; }
    .steps { margin:22px 0; padding:0; list-style:none; }
    .steps li { display:flex; gap:12px; align-items:flex-start; margin-bottom:14px; font-size:.87rem; color:#374151; }
    .step-num { width:24px; height:24px; border-radius:50%; background:#d97706; color:#fff; font-size:.72rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px; }
    .footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 40px; text-align:center; font-size:.78rem; color:#94a3b8; }
  </style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <h1>Application Update</h1>
    <p>Phil. Academy of Sakya — Admissions Office</p>
    <span class="badge">⏳ Waitlisted</span>
  </div>

  <div class="body">
    <p>Dear <strong>{{ $applicant->parent_guardian_name }}</strong>,</p>

    <p>
      Thank you for your interest in enrolling <strong>{{ $applicant->first_name }} {{ $applicant->last_name }}</strong>
      at <strong>Phil. Academy of Sakya</strong> for S.Y. <strong>{{ $applicant->applying_for_year }}</strong>.
    </p>

    <p>
      After careful review, we would like to inform you that your child's application for
      <strong>{{ $applicant->applying_for_grade }}</strong> has been placed on our
      <strong>waitlist</strong>. This means that while we cannot offer a slot at this time,
      your application remains active and will be considered as slots become available.
    </p>

    <div class="info-box">
      <p><strong>Reference Number:</strong> {{ $applicant->reference_number }}</p>
      <p><strong>Applicant:</strong> {{ $applicant->full_name }}</p>
      <p><strong>Grade Applying For:</strong> {{ $applicant->applying_for_grade }}</p>
      <p><strong>Current Status:</strong> Waitlisted</p>
    </div>

    <p><strong>What this means for you:</strong></p>
    <ul class="steps">
      <li>
        <span class="step-num">1</span>
        <span>Your application is <strong>still active</strong>. No action is required from your end at this time.</span>
      </li>
      <li>
        <span class="step-num">2</span>
        <span>If a slot opens up in <strong>{{ $applicant->applying_for_grade }}</strong>, you will be notified immediately via email.</span>
      </li>
      <li>
        <span class="step-num">3</span>
        <span>You may contact our registrar office to check on your child's waitlist position or to ask any questions.</span>
      </li>
    </ul>

    <p>
      We appreciate your patience and understanding. We hope to welcome your child to our school community soon.
    </p>
  </div>

  <div class="footer">
    <p>Phil. Academy of Sakya &nbsp;·&nbsp; Admissions Office</p>
    <p style="margin-top:4px;">This is an automated notification. Please do not reply to this email.</p>
    <p style="margin-top:4px; font-size:.72rem; color:#cbd5e1;">Protected under RA 10173 – Data Privacy Act of 2012</p>
  </div>

</div>
</body>
</html>
