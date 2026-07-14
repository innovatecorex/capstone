<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application Received — Philippine Academy of Sakya</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --navy:  #0b1e3d;
    --green: #059669;
    --blue:  #1d4ed8;
  }

  body {
    font-family: 'Inter', sans-serif;
    background: #f1f5f9;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  /* ── Top bar ── */
  .top-bar {
    background: var(--navy);
    padding: .65rem 2rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-shrink: 0;
  }
  .top-bar img { height: 28px; width: 28px; border-radius: 50%; border: 1.5px solid rgba(255,255,255,.15); }
  .top-bar-name { font-size: .85rem; font-weight: 800; color: #fff; }
  .top-bar-sub  { font-size: .65rem; color: rgba(255,255,255,.4); }

  /* ── Main centered layout ── */
  .page {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1.5rem;
  }

  .wrap {
    width: 100%;
    max-width: 580px;
    animation: rise .4s cubic-bezier(.22,.68,0,1.2) both;
  }
  @keyframes rise {
    from { opacity:0; transform:translateY(16px) scale(.98); }
    to   { opacity:1; transform:translateY(0) scale(1); }
  }

  /* ── Success card ── */
  .success-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    box-shadow: 0 8px 40px rgba(15,23,42,.1), 0 2px 8px rgba(15,23,42,.06);
    margin-bottom: 1rem;
  }

  /* Green hero banner */
  .success-banner {
    background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #059669 100%);
    padding: 2.5rem 2rem 2.25rem;
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  .success-banner::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
    background-size: 20px 20px;
  }
  .success-banner::after {
    content: '';
    position: absolute;
    bottom: -2px; left: 0; right: 0;
    height: 32px;
    background: #fff;
    clip-path: ellipse(60% 100% at 50% 100%);
  }
  .banner-inner { position: relative; }

  .check-ring {
    width: 68px; height: 68px;
    border-radius: 50%;
    background: rgba(255,255,255,.12);
    border: 2px solid rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1rem;
  }
  .check-ring svg { width: 32px; height: 32px; color: #fff; }

  .banner-title {
    font-size: 1.35rem;
    font-weight: 900;
    color: #fff;
    letter-spacing: -.025em;
    margin-bottom: .4rem;
  }
  .banner-sub {
    font-size: .84rem;
    color: rgba(255,255,255,.7);
  }

  /* Card body */
  .card-body { padding: 1.75rem 1.75rem 1.5rem; }

  /* Reference number box */
  .ref-box {
    background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
    border: 1.5px solid #86efac;
    border-radius: 14px;
    padding: 1.1rem 1.25rem;
    text-align: center;
    margin-bottom: .75rem;
    position: relative;
    overflow: hidden;
  }
  .ref-box::before {
    content: '';
    position: absolute; top: -20px; right: -20px;
    width: 80px; height: 80px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(5,150,105,.08) 0%, transparent 70%);
  }
  .ref-label {
    font-size: .65rem;
    font-weight: 800;
    color: #059669;
    text-transform: uppercase;
    letter-spacing: .12em;
    margin-bottom: .5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
  }
  .ref-label svg { width: 11px; height: 11px; }
  .ref-number {
    font-size: 1.55rem;
    font-weight: 900;
    color: #065f46;
    letter-spacing: .06em;
    font-variant-numeric: tabular-nums;
  }

  .ref-note {
    font-size: .74rem;
    color: #64748b;
    text-align: center;
    margin-bottom: 1.4rem;
    line-height: 1.55;
    display: flex;
    align-items: flex-start;
    gap: 6px;
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 9px;
    padding: .65rem .85rem;
  }
  .ref-note svg { width: 14px; height: 14px; flex-shrink: 0; color: #d97706; margin-top: 1px; }

  /* Info grid */
  .info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
    margin-bottom: 1.4rem;
  }
  .info-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: .7rem .9rem;
  }
  .info-lbl {
    font-size: .63rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: .25rem;
  }
  .info-val {
    font-size: .875rem;
    font-weight: 700;
    color: #0f172a;
  }
  .status-pending {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #fef9c3;
    color: #854d0e;
    font-size: .75rem;
    font-weight: 700;
    padding: .2rem .6rem;
    border-radius: 99px;
  }
  .status-pending::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #d97706;
    display: block;
  }

  /* Next steps */
  .next-steps {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1.4rem;
  }
  .next-steps-head {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: .7rem 1rem;
    font-size: .75rem;
    font-weight: 800;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: .07em;
    display: flex;
    align-items: center;
    gap: 7px;
  }
  .next-steps-head svg { width: 14px; height: 14px; color: var(--blue); }
  .step-list { padding: .85rem 1rem; display: flex; flex-direction: column; gap: .65rem; }
  .step-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: .8rem;
    color: #475569;
    line-height: 1.55;
  }
  .step-num {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: var(--blue);
    color: #fff;
    font-size: .65rem;
    font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
  }

  /* Footer actions */
  .actions {
    display: flex;
    gap: .75rem;
    flex-wrap: wrap;
  }
  .btn-primary {
    display: inline-flex; align-items: center; gap: 7px;
    padding: .7rem 1.4rem;
    background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
    color: #fff; border-radius: 10px;
    font-size: .84rem; font-weight: 700;
    text-decoration: none;
    box-shadow: 0 3px 12px rgba(29,78,216,.35);
    transition: all .15s;
    flex: 1; justify-content: center;
  }
  .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(29,78,216,.45); }
  .btn-primary svg { width: 15px; height: 15px; }

  .btn-ghost {
    display: inline-flex; align-items: center; gap: 7px;
    padding: .7rem 1.4rem;
    background: #f8fafc; color: #374151;
    border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: .84rem; font-weight: 700;
    text-decoration: none;
    transition: all .15s;
    flex: 1; justify-content: center;
  }
  .btn-ghost:hover { background: #f1f5f9; border-color: #cbd5e1; }
  .btn-ghost svg { width: 15px; height: 15px; }

  /* Footer note */
  .footer-note {
    text-align: center;
    font-size: .7rem;
    color: #94a3b8;
    margin-top: .9rem;
    line-height: 1.6;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
  }
  .footer-note svg { width: 11px; height: 11px; flex-shrink: 0; }

  @media (max-width: 500px) {
    .info-grid { grid-template-columns: 1fr; }
    .card-body { padding: 1.25rem; }
    .actions { flex-direction: column; }
  }
</style>
</head>
<body>

<div class="top-bar">
  <img src="/images/logo.png" alt="PAS">
  <div>
    <div class="top-bar-name">Philippine Academy of Sakya</div>
    <div class="top-bar-sub">EncryptEd · Academic Management System</div>
  </div>
</div>

<div class="page">
  <div class="wrap">

    <div class="success-card">

      {{-- Banner --}}
      <div class="success-banner">
        <div class="banner-inner">
          <div class="check-ring">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
          </div>
          <div class="banner-title">Application Submitted!</div>
          <div class="banner-sub">Thank you, <strong style="color:#fff;">{{ $applicant->first_name }}</strong>. Your application has been received.</div>
        </div>
      </div>

      <div class="card-body">

        {{-- Reference number --}}
        <div class="ref-box">
          <div class="ref-label">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
            </svg>
            Your Reference Number
          </div>
          <div class="ref-number">{{ $applicant->reference_number }}</div>
        </div>

        <div class="ref-note">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
          <span>Save or screenshot this reference number. You will need it to follow up on your application status with the admissions office.</span>
        </div>

        {{-- Confirmation email is sent on submit (best-effort — the application
             is saved regardless), so tell them where to look for it. --}}
        @if($applicant->parent_email)
        <div class="ref-note" style="margin-top:10px;">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
          </svg>
          <span>
            A confirmation with your reference number has been sent to
            <strong>{{ $applicant->parent_email }}</strong>.
            If it isn&rsquo;t in your inbox, please check your spam folder.
          </span>
        </div>
        @endif

        {{-- Info summary --}}
        <div class="info-grid">
          <div class="info-item">
            <div class="info-lbl">Applicant Name</div>
            <div class="info-val">{{ $applicant->full_name }}</div>
          </div>
          <div class="info-item">
            <div class="info-lbl">Applying For</div>
            <div class="info-val">{{ $applicant->applying_for_grade }}</div>
          </div>
          <div class="info-item">
            <div class="info-lbl">Date Submitted</div>
            <div class="info-val">{{ $applicant->created_at->format('M d, Y') }}</div>
          </div>
          <div class="info-item">
            <div class="info-lbl">Current Status</div>
            <div class="info-val"><span class="status-pending">Pending Review</span></div>
          </div>
        </div>

        {{-- Next steps --}}
        <div class="next-steps">
          <div class="next-steps-head">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
            </svg>
            What Happens Next
          </div>
          <div class="step-list">
            <div class="step-item">
              <div class="step-num">1</div>
              <span>The admissions office will review your application and verify the information you provided.</span>
            </div>
            <div class="step-item">
              <div class="step-num">2</div>
              <span>You may be contacted for additional documents, a scheduled interview, or an entrance examination.</span>
            </div>
            <div class="step-item">
              <div class="step-num">3</div>
              <span>The final decision will be communicated via the contact number{{ $applicant->parent_email ? ' and email address' : '' }} you provided.</span>
            </div>
            @if($applicant->parent_email)
            <div class="step-item">
              <div class="step-num">4</div>
              <span>If accepted, login credentials for the student portal will be sent to <strong>{{ $applicant->parent_email }}</strong>.</span>
            </div>
            @endif
          </div>
        </div>

        {{-- Actions --}}
        <div class="actions">
          <a href="{{ route('login') }}" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
            </svg>
            Back to Login
          </a>
          <a href="{{ route('apply') }}" class="btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            New Application
          </a>
        </div>

        <div class="footer-note">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
          </svg>
          Your information is encrypted and protected under RA 10173 · Philippine Academy of Sakya
        </div>

      </div>
    </div>

  </div>
</div>

</body>
</html>
