<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Your Password</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Sora',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;border-radius:24px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,0.08);background:#ffffff;">

<!-- HEADER -->
<tr><td style="background:#1e1b4b;padding:40px 50px;text-align:center;">
  <img src="{{ $logo_url ?? 'https://trafficvai.com/images/logo.png' }}" alt="TrafficVai" height="40" style="display:block;margin:0 auto;">
</td></tr>

<!-- BODY -->
<tr><td style="padding:50px 50px 40px;">
  <h1 style="margin:0 0 16px;font-size:28px;font-weight:700;color:#111827;letter-spacing:-1px;">Password Reset Request</h1>
  <p style="margin:0 0 32px;font-size:16px;color:#4b5563;line-height:1.8;">
    You are receiving this email because we received a password reset request for your account. If you did not request a password reset, no further action is required.
  </p>

  <table cellpadding="0" cellspacing="0" style="margin:0 auto 32px;">
  <tr><td style="background:linear-gradient(135deg,#E8470A,#ea580c);border-radius:12px;">
    <a href="{{ $action_url ?? url('/') }}" style="display:inline-block;padding:18px 48px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;">Reset Password</a>
  </td></tr>
  </table>

  <p style="margin:0 0 20px;font-size:14px;color:#6b7280;line-height:1.6;">
    This password reset link will expire in 60 minutes.
  </p>

  <div style="border-top:1px solid #e5e7eb;padding-top:24px;margin-top:24px;">
    <p style="margin:0;font-size:12px;color:#9ca3af;line-height:1.6;">
      If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:
      <br>
      <a href="{{ $action_url ?? url('/') }}" style="color:#312e81;word-break:break-all;">{{ $action_url ?? url('/') }}</a>
    </p>
  </div>
</td></tr>

<!-- FOOTER -->
<tr><td style="background:#f8fafc;padding:32px 50px;text-align:center;border-radius:0 0 24px 24px;">
  <p style="margin:0 0 12px;font-size:13px;color:#6b7280;">Securely sent by TrafficVai Protection System</p>
  <p style="margin:0;font-size:11px;color:#9ca3af;">© {{ date('Y') }} TrafficVai. All rights reserved.</p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>
