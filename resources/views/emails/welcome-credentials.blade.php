{{-- resources/views/emails/welcome-credentials.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EncryptEd — Your Login Credentials</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f1f5f9;
      color: #334155;
      padding: 40px 20px;
    }
    .wrapper { max-width: 520px; margin: 0 auto; }
    .card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 24px rgba(0,0,0,.08);
    }
    .card-top {
      height: 5px;
      background: linear-gradient(90deg, #0f1e3c 0%, #2563eb 60%, #3b82f6 100%);
    }
    .card-header {
      background: #0f1e3c;
      padding: 28px 36px;
      text-align: center;
    }
    .card-header-title {
      color: white;
      font-size: 22px;
      font-weight: 700;
      letter-spacing: -0.02em;
    }
    .card-body { padding: 32px 36px; }
    .greeting { font-size: 16px; margin-bottom: 16px; }
    .lead { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.6; }
    .creds {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 20px 24px;
      margin-bottom: 24px;
    }
    .cred-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
    .cred-label { color: #64748b; }
    .cred-value { font-weight: 700; color: #0f1e3c; font-family: 'Consolas', monospace; }
    .note {
      font-size: 13px;
      color: #92400e;
      background: #fffbeb;
      border: 1px solid #fde68a;
      border-radius: 8px;
      padding: 12px 16px;
      line-height: 1.5;
    }
    .footer {
      text-align: center;
      font-size: 12px;
      color: #94a3b8;
      margin-top: 24px;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="card">
      <div class="card-top"></div>
      <div class="card-header">
        <div class="card-header-title">EncryptEd</div>
      </div>
      <div class="card-body">
        <p class="greeting">Hello {{ $firstName }},</p>
        <p class="lead">
          An account has been created for you on the EncryptEd Academic Management
          Portal of the Philippine Academy of Sakya. Use the credentials below to log in.
        </p>
        <div class="creds">
          <div class="cred-row">
            <span class="cred-label">Username</span>
            <span class="cred-value">{{ $username }}</span>
          </div>
          <div class="cred-row">
            <span class="cred-label">Temporary Password</span>
            <span class="cred-value">{{ $tempPassword }}</span>
          </div>
        </div>
        <p class="note">
          For your security, you will be required to change this temporary password
          the first time you log in. Do not share these credentials with anyone.
        </p>
      </div>
    </div>
    <p class="footer">© {{ date('Y') }} EncryptEd · Philippine Academy of Sakya</p>
  </div>
</body>
</html>
