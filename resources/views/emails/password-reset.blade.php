<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reset Your Password</title>
<style>
  body { margin:0; padding:0; background:#060810; font-family:'Segoe UI',Arial,sans-serif; }
  .wrapper { max-width:520px; margin:40px auto; padding:0 16px; }
  .card { background:#0d1117; border:1px solid #1e2433; border-radius:16px; overflow:hidden; }
  .header { background:linear-gradient(135deg,#00d4ff22,#7c3aed22); border-bottom:1px solid #1e2433; padding:32px 40px 28px; text-align:center; }
  .logo { width:48px; height:48px; background:linear-gradient(135deg,#00d4ff,#7c3aed); border-radius:12px; margin:0 auto 16px; display:flex; align-items:center; justify-content:center; }
  .body { padding:36px 40px; }
  h1 { font-size:22px; font-weight:700; color:#f1f5f9; margin:0 0 6px; }
  p { font-size:15px; color:#8b9ab0; line-height:1.7; margin:0 0 20px; }
  .btn { display:block; text-align:center; background:linear-gradient(135deg,#00d4ff,#7c3aed); color:#fff; text-decoration:none; font-weight:600; font-size:15px; padding:14px 32px; border-radius:10px; margin:28px 0; }
  .note { background:#ffffff08; border:1px solid #1e2433; border-radius:8px; padding:14px 18px; font-size:13px; color:#8b9ab0; }
  .footer { text-align:center; padding:20px 40px 28px; font-size:13px; color:#4b5563; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="header">
      <div class="logo">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5">
          <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
        </svg>
      </div>
      <h1>{{ config('app.name') }}</h1>
    </div>
    <div class="body">
      <h1>Reset Your Password</h1>
      <p>Hi <strong style="color:#e2e8f0;">{{ $userName }}</strong>,</p>
      <p>We received a request to reset the password for your account. Click the button below to choose a new password.</p>

      <a href="{{ $resetUrl }}" class="btn">Reset Password</a>

      <div class="note">
        <strong style="color:#cbd5e1;">This link will expire in 60 minutes.</strong><br>
        If you did not request a password reset, you can safely ignore this email — your password will remain unchanged.
      </div>
    </div>
    <div class="footer">
      &copy; {{ date('Y') }} {{ config('app.name') }} &nbsp;·&nbsp; This is an automated email, please do not reply.
    </div>
  </div>
</div>
</body>
</html>
