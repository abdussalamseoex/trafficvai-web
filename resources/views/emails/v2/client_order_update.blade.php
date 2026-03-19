<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Update</title>
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
    <td>
      <img src="{{ $logo_url ?? 'https://trafficvai.com/images/logo.png' }}" alt="TrafficVai" height="44" style="display:block;">
    </td>
  </tr>
  <tr><td style="padding-top:20px;">
    <span style="display:inline-flex;align-items:center;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);border-radius:100px;padding:6px 16px;">
      <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#60a5fa;margin-right:8px;"></span>
      <span style="font-size:11px;color:rgba(255,255,255,0.9);font-family:'JetBrains Mono',monospace;letter-spacing:0.5px;">ORDER STATUS UPDATE</span>
    </span>
  </td></tr>
  </table>
</td></tr>

<!-- BODY -->
<tr><td style="background:#fff;padding:36px 40px;">
  <p style="margin:0 0 4px;font-size:13px;color:#6b7280;">Hello, {{ $client_name ?? ($user_name ?? 'Client') }} 👋</p>
  <h1 style="margin:0 0 20px;font-size:26px;font-weight:700;color:#111827;letter-spacing:-0.5px;">Order <span style="color:#E8470A;">#{{ $order_id }}</span> Updated</h1>
  <hr style="border:none;border-top:1px solid #e5e7eb;margin:0 0 20px;">
  <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.7;">Your order status has been updated. Here is the latest information on your order:</p>

  <!-- INFO CARD -->
  <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#fffbf0,#fff7ed);border:1px solid #ffedd5;border-left:4px solid #E8470A;border-radius:12px;margin-bottom:24px;">
  <tr><td style="padding:20px 24px;">
    <p style="margin:0 0 14px;font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:#E8470A;font-weight:600;font-family:'JetBrains Mono',monospace;">Order Status Details</p>
    <table width="100%" cellpadding="0" cellspacing="0">
    <tr><td style="padding:8px 0;border-bottom:1px solid rgba(255,237,213,0.5);"><span style="font-size:13px;color:#6b7280;">Order ID</span></td><td align="right" style="padding:8px 0;border-bottom:1px solid rgba(255,237,213,0.5);"><span style="font-size:13px;color:#E8470A;font-weight:600;font-family:'JetBrains Mono',monospace;">#{{ $order_id }}</span></td></tr>
    @if($previous_status ?? false)
    <tr><td style="padding:8px 0;border-bottom:1px solid rgba(255,237,213,0.5);"><span style="font-size:13px;color:#6b7280;">Previous Status</span></td><td align="right" style="padding:8px 0;border-bottom:1px solid rgba(255,237,213,0.5);"><span style="font-size:13px;color:#111827;font-weight:600;">{{ $previous_status }}</span></td></tr>
    @endif
    <tr><td style="padding:8px 0;border-bottom:1px solid rgba(255,237,213,0.5);"><span style="font-size:13px;color:#6b7280;">Current Status</span></td><td align="right" style="padding:8px 0;border-bottom:1px solid rgba(255,237,213,0.5);"><span style="font-size:12px;font-weight:600;background:#dbeafe;color:#1e3a8a;padding:4px 12px;border-radius:100px;">{{ $order_status ?? ($status ?? 'processing') }}</span></td></tr>
    <tr><td style="padding:8px 0;"><span style="font-size:13px;color:#6b7280;">Updated At</span></td><td align="right" style="padding:8px 0;"><span style="font-size:13px;color:#111827;font-weight:600;font-family:'JetBrains Mono',monospace;">{{ $update_date ?? ($date ?? now()->format('M d, Y')) }}</span></td></tr>
    </table>
  </td></tr>
  </table>

  <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.7;">Our team is actively working on your order. We will notify you when the status changes again.</p>

  <table cellpadding="0" cellspacing="0" style="margin:8px 0 20px;">
  <tr><td style="background:linear-gradient(135deg,#E8470A,#ea580c);border-radius:10px;">
    <a href="{{ $order_url ?? ($link ?? url('/client/orders')) }}" style="display:inline-block;padding:14px 32px;font-size:14px;font-weight:600;color:#fff;text-decoration:none;">Track Your Order</a>
  </td></tr>
  </table>
</td></tr>

<!-- FOOTER -->
<tr><td style="background:#1e1b4b;padding:28px 40px;text-align:center;">
  <img src="{{ $logo_url ?? 'https://trafficvai.com/images/logo.png' }}" alt="TrafficVai" height="34" style="display:block;margin:0 auto 16px;">
  <p style="margin:0 0 8px;font-size:16px;font-weight:700;color:#fff;">Traffic<span style="color:#E8470A;">Vai</span></p>
  <hr style="border:none;border-top:1px solid rgba(255,255,255,0.08);margin:12px 0;">
  <p style="margin:0 0 8px;">
    <a href="{{ $dashboard_portal_url ?? url('/dashboard') }}" style="color:#8b92a5;text-decoration:none;font-size:12px;margin:0 10px;">Dashboard</a>
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
