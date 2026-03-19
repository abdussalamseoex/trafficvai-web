<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to TrafficVai</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Sora',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;border-radius:24px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,0.08);background:#ffffff;">

<!-- HEADER -->
<tr><td style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 100%);padding:60px 50px 40px;text-align:center;">
  <img src="{{ $logo_url ?? 'https://trafficvai.com/images/logo.png' }}" alt="TrafficVai" height="50" style="display:block;margin:0 auto 24px;">
  <h1 style="margin:0;font-size:32px;font-weight:700;color:#ffffff;letter-spacing:-1px;">Welcome to the Family!</h1>
  <p style="margin:12px 0 0;font-size:16px;color:rgba(255,255,255,0.8);line-height:1.6;">We're thrilled to have you on board. Let's start building your presence.</p>
</td></tr>

<!-- BODY -->
<tr><td style="padding:50px 50px 40px;">
  <p style="margin:0 0 8px;font-size:14px;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:1px;">Salutation</p>
  <h2 style="margin:0 0 24px;font-size:24px;font-weight:700;color:#111827;">Hi {{ $user_name ?? 'Client' }},</h2>
  
  <p style="margin:0 0 24px;font-size:16px;color:#4b5563;line-height:1.8;">
    Thank you for choosing TrafficVai. Your account is now active and you have full access to our premium SEO services, high-quality guest posting sites, and expert marketing tools.
  </p>

  <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:32px;margin:32px 0;">
    <h3 style="margin:0 0 16px;font-size:18px;font-weight:700;color:#1e1b4b;">Next Steps for You:</h3>
    <ul style="margin:0;padding:0 0 0 20px;color:#4b5563;font-size:15px;line-height:2;">
      <li>Browse our <a href="{{ url('/services') }}" style="color:#E8470A;text-decoration:none;font-weight:600;">Marketing Services</a></li>
      <li>Explore high DA <a href="{{ url('/guest-posts') }}" style="color:#E8470A;text-decoration:none;font-weight:600;">Guest Post Sites</a></li>
      <li>Set up your profile in the <a href="{{ url('/dashboard') }}" style="color:#E8470A;text-decoration:none;font-weight:600;">Client Dashboard</a></li>
    </ul>
  </div>

  <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
  <tr><td style="background:linear-gradient(135deg,#E8470A,#ea580c);border-radius:12px;box-shadow:0 10px 20px rgba(232,71,10,0.2);">
    <a href="{{ $login_url ?? url('/login') }}" style="display:inline-block;padding:18px 48px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;letter-spacing:0.5px;">Go to Dashboard</a>
  </td></tr>
  </table>
</td></tr>

<!-- FOOTER -->
<tr><td style="background:#1e1b4b;padding:40px 50px;text-align:center;border-radius:0 0 24px 24px;">
  <p style="margin:0 0 20px;font-size:14px;color:rgba(255,255,255,0.6);line-height:1.6;">
    If you have any questions, simply reply to this email or contact our support team at any time.
  </p>
  <div style="margin-bottom:24px;">
    <a href="{{ url('/contact') }}" style="color:#ffffff;text-decoration:none;font-size:13px;font-weight:600;margin:0 15px;">Support</a>
    <a href="{{ url('/privacy-policy') }}" style="color:#ffffff;text-decoration:none;font-size:13px;font-weight:600;margin:0 15px;">Privacy</a>
    <a href="{{ url('/terms') }}" style="color:#ffffff;text-decoration:none;font-size:13px;font-weight:600;margin:0 15px;">Terms</a>
  </div>
  <p style="margin:0;font-size:12px;color:rgba(255,255,255,0.4);">© {{ date('Y') }} TrafficVai. All rights reserved.</p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>
