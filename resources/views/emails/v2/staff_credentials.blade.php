<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Account Created</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Sora',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;border-radius:24px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,0.08);background:#ffffff;">

<!-- HEADER -->
<tr><td style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);padding:50px 50px;text-align:center;border-bottom:4px solid #E8470A;">
  <img src="{{ $logo_url ?? 'https://trafficvai.com/images/logo.png' }}" alt="TrafficVai" height="44" style="display:block;margin:0 auto 20px;">
  <h1 style="margin:0;font-size:26px;font-weight:700;color:#ffffff;letter-spacing:-0.5px;">Welcome to the Team!</h1>
</td></tr>

<!-- BODY -->
<tr><td style="padding:50px 50px 40px;">
  <h2 style="margin:0 0 24px;font-size:22px;font-weight:700;color:#111827;">Hi {{ $user_name ?? 'Team Member' }},</h2>
  
  <p style="margin:0 0 24px;font-size:16px;color:#4b5563;line-height:1.8;">
    An account has been created for you on the TrafficVai Admin Portal. You have been assigned the role of <strong>{{ $role ?? 'Staff' }}</strong>.
  </p>

  <div style="background:#f1f5f9;border-radius:16px;padding:32px;margin:10px 0 32px;">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td style="padding-bottom:12px;font-size:14px;color:#64748b;font-weight:600;text-transform:uppercase;">Login Credentials</td>
      </tr>
      <tr>
        <td style="padding:16px;background:#ffffff;border-radius:8px;border:1px solid #e2e8f0;">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td style="font-size:14px;color:#4b5563;padding-bottom:8px;"><strong>Email:</strong> {{ $recipient ?? $recipient_email }}</td>
            </tr>
            <tr>
              <td style="font-size:14px;color:#4b5563;"><strong>Temporary Password:</strong> <code style="font-family:'JetBrains Mono',monospace;color:#dc2626;background:#fee2e2;padding:2px 6px;border-radius:4px;">{{ $password ?? 'Check with Admin' }}</code></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>

  <p style="margin:0 0 32px;font-size:14px;color:#ef4444;font-style:italic;">
    * Important: Please change your password immediately after your first login for security purposes.
  </p>

  <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
  <tr><td style="background:#1e1b4b;border-radius:12px;">
    <a href="{{ $login_url ?? url('/login') }}" style="display:inline-block;padding:18px 48px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;">Access Portal</a>
  </td></tr>
  </table>
</td></tr>

<!-- FOOTER -->
<tr><td style="background:#0f172a;padding:40px 50px;text-align:center;">
  <p style="margin:0 0 16px;font-size:14px;color:rgba(255,255,255,0.4);">
    Internal Communication - Authorized Personnel Only
  </p>
  <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.3);">© {{ date('Y') }} TrafficVai Operations</p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>
