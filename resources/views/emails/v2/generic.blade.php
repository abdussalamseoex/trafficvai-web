<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject ?? 'Notification' }}</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Sora',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:20px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.10);">

@if($is_admin ?? false)
<!-- ADMIN RIBBON -->
<tr><td style="background:linear-gradient(90deg,#E8470A,#f97316);padding:8px 40px;text-align:center;font-size:11px;color:rgba(255,255,255,0.85);letter-spacing:1px;font-family:'JetBrains Mono',monospace;">⚙ ADMIN PANEL NOTIFICATION · TRAFFICVAI SYSTEM</td></tr>
@endif

<!-- HEADER -->
<tr><td style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 40%,#E8470A 100%);padding:36px 40px 28px;">
  <table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td style="vertical-align:middle;">
      <img src="{{ $logo_url ?? 'https://trafficvai.com/images/logo.png' }}" alt="TrafficVai" height="44" style="display:block;">
    </td>
  </tr>
  <tr><td style="padding-top:20px;">
    <span style="display:inline-flex;align-items:center;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);border-radius:100px;padding:6px 16px;">
      <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#4ade80;margin-right:8px;"></span>
      <span style="font-size:11px;color:rgba(255,255,255,0.9);font-family:'JetBrains Mono',monospace;letter-spacing:0.5px;">SYSTEM NOTIFICATION</span>
    </span>
  </td></tr>
  </table>
</td></tr>

<!-- BODY -->
<tr><td style="background:#fff;padding:36px 40px;">
  <div style="font-size:15px;color:#374151;line-height:1.7;">
    {!! $body !!}
  </div>
</td></tr>

<!-- FOOTER -->
<tr><td style="background:#1e1b4b;padding:28px 40px;text-align:center;">
  <img src="{{ $logo_url ?? 'https://trafficvai.com/images/logo.png' }}" alt="TrafficVai" height="34" style="display:block;margin:0 auto 16px;">
  <p style="margin:0 0 8px;font-size:16px;font-weight:700;color:#fff;">Traffic<span style="color:#E8470A;">Vai</span></p>
  <hr style="border:none;border-top:1px solid rgba(255,255,255,0.08);margin:12px 0;">
  <p style="margin:0 0 8px;">
    <a href="{{ url('/dashboard') }}" style="color:#8b92a5;text-decoration:none;font-size:12px;margin:0 10px;">Dashboard</a>
    <a href="{{ url('/contact') }}" style="color:#8b92a5;text-decoration:none;font-size:12px;margin:0 10px;">Contact</a>
    <a href="{{ url('/terms') }}" style="color:#8b92a5;text-decoration:none;font-size:12px;margin:0 10px;">Terms</a>
    <a href="{{ url('/privacy-policy') }}" style="color:#8b92a5;text-decoration:none;font-size:12px;margin:0 10px;">Privacy</a>
    <a href="{{ url('/refund-policy') }}" style="color:#8b92a5;text-decoration:none;font-size:12px;margin:0 10px;">Refund</a>
  </p>
  <p style="margin:0;font-size:11px;color:#4b5563;line-height:1.6;">© {{ date('Y') }} TrafficVai. All rights reserved.</p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>
