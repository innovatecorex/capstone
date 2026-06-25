<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Accepted</title>
  <style>
    body { margin:0; padding:0; background:#f0f4f8; font-family: 'Segoe UI', Arial, sans-serif; }
    .wrap { max-width:580px; margin:32px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(15,23,42,.10); }
    .header { background:linear-gradient(135deg,#16a34a 0%,#15803d 100%); padding:36px 40px 28px; text-align:center; }
    .header img { width:120px; margin-bottom:14px; }
    .header h1 { color:#fff; font-size:1.4rem; margin:0 0 6px; font-weight:800; }
    .header p  { color:rgba(255,255,255,.82); font-size:.88rem; margin:0; }
    .badge { display:inline-block; background:#dcfce7; color:#166534; font-size:.78rem; font-weight:700; padding:6px 18px; border-radius:999px; margin:20px 0 0; letter-spacing:.04em; text-transform:uppercase; }
    .body  { padding:32px 40px; }
    .body p { color:#374151; font-size:.93rem; line-height:1.7; margin:0 0 14px; }
    .body p strong { color:#111827; }
    .info-box { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:18px 22px; margin:20px 0; }
    .info-box p { margin:0; font-size:.87rem; color:#166534; }
    .info-box p + p { margin-top:6px; }
    .steps { margin:22px 0; padding:0; list-style:none; }
    .steps li { display:flex; gap:12px; align-items:flex-start; margin-bottom:14px; font-size:.87rem; color:#374151; }
    .step-num { width:24px; height:24px; border-radius:50%; background:#16a34a; color:#fff; font-size:.72rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px; }
    .cta { text-align:center; margin:28px 0; }
    .cta a { display:inline-block; background:#16a34a; color:#fff; text-decoration:none; padding:12px 32px; border-radius:10px; font-weight:700; font-size:.92rem; }
    .footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 40px; text-align:center; font-size:.78rem; color:#94a3b8; }
    .footer a { color:#16a34a; text-decoration:none; }
  </style>
</head>
<body>
<div class="wrap">

  <div class="header">
    <h1>🎉 Congratulations!</h1>
    <p>Phil. Academy of Sakya — Admissions Office</p>
    <span class="badge">✓ Application Accepted</span>
  </div>

  <div class="body">
    <p>Dear <strong>{{ $applicant->parent_guardian_name }}</strong>,</p>

    <p>
      We are delighted to inform you that <strong>{{ $applicant->first_name }} {{ $applicant->last_name }}</strong>'s
      application for <strong>{{ $applicant->applying_for_grade }}</strong> at
      <strong>Phil. Academy of Sakya</strong> for S.Y. <strong>{{ $applicant->applying_for_year }}</strong>
      has been officially <strong>accepted</strong>.
    </p>

    <div class="info-box">
      <p><strong>Reference Number:</strong> {{ $applicant->reference_number }}</p>
      <p><strong>Applicant:</strong> {{ $applicant->full_name }}</p>
      <p><strong>Grade Applying For:</strong> {{ $applicant->applying_for_grade }}</p>
      <p><strong>School Year:</strong> {{ $applicant->applying_for_year }}</p>
    </div>

    <p><strong>What happens next?</strong></p>
    <ul class="steps">
      <li>
        <span class="step-num">1</span>
        <span>Our registrar will review the final enrollment requirements and prepare your child's student account.</span>
      </li>
      <li>
        <span class="step-num">2</span>
        <span>You will receive a second email containing your child's <strong>login credentials</strong> for the enrollment portal.</span>
      </li>
      <li>
        <span class="step-num">3</span>
        <span>Log in to the enrollment portal to complete the enrollment process and settle the required fees.</span>
      </li>
      <li>
        <span class="step-num">4</span>
        <span>Your child will be assigned to a section based on grade level and available capacity.</span>
      </li>
    </ul>

    <p>
      If you have any questions or concerns, please do not hesitate to contact us at the school's registrar office.
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
