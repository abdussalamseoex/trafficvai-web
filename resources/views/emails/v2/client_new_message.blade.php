<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Message</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:'Sora',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:20px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.10);">

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
      <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#c084fc;margin-right:8px;"></span>
      <span style="font-size:11px;color:rgba(255,255,255,0.9);font-family:'JetBrains Mono',monospace;letter-spacing:0.5px;">NEW MESSAGE RECEIVED</span>
    </span>
  </td></tr>
  </table>
</td></tr>

<!-- BODY -->
<tr><td style="background:#fff;padding:36px 40px;">
  <p style="margin:0 0 4px;font-size:13px;color:#6b7280;">Hello, {{ $client_name }} 👋</p>
  <h1 style="margin:0 0 20px;font-size:26px;font-weight:700;color:#111827;letter-spacing:-0.5px;">New Message for Order <span style="color:#E8470A;">#{{ $order_id }}</span></h1>
  <hr style="border:none;border-top:1px solid #e5e7eb;margin:0 0 20px;">
  <p style="margin:0 0 20px;font-size:15px;color:#374151;line-height:1.7;">You have received a new message regarding your order. Please reply at your earliest convenience.</p>

  <!-- INFO CARD -->
  <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#fffbf0,#fff7ed);border:1px solid #ffedd5;border-left:4px solid #E8470A;border-radius:12px;margin-bottom:16px;">
  <tr><td style="padding:20px 24px;">
    <p style="margin:0 0 14px;font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:#E8470A;font-weight:600;font-family:'JetBrains Mono',monospace;">💬 Message Details</p>
    <table width="100%" cellpadding="0" cellspacing="0">
    <tr><td style="padding:8px 0;border-bottom:1px solid rgba(255,237,213,0.5);"><span style="font-size:13px;color:#6b7280;">Order ID</span></td><td align="right" style="padding:8px 0;border-bottom:1px solid rgba(255,237,213,0.5);"><span style="font-size:13px;color:#E8470A;font-weight:600;font-family:'JetBrains Mono',monospace;">#{{ $order_id }}</span></td></tr>
    <tr><td style="padding:8px 0;border-bottom:1px solid rgba(255,237,213,0.5);"><span style="font-size:13px;color:#6b7280;">From</span></td><td align="right" style="padding:8px 0;border-bottom:1px solid rgba(255,237,213,0.5);"><span style="font-size:13px;color:#111827;font-weight:600;">TrafficVai Support</span></td></tr>
    <tr><td style="padding:8px 0;"><span style="font-size:13px;color:#6b7280;">Received At</span></td><td align="right" style="padding:8px 0;"><span style="font-size:13px;color:#111827;font-weight:600;font-family:'JetBrains Mono',monospace;">{{ $message_date }}</span></td></tr>
    </table>
  </td></tr>
  </table>

  <!-- MESSAGE BUBBLE -->
  <p style="margin:0 0 6px;font-size:11px;text-transform:uppercase;letter-spacing:2px;color:#9ca3af;font-weight:600;">Message Preview</p>
  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
  <tr><td style="background:#f3f4f6;border-radius:12px;border-left:3px solid #E8470A;padding:16px 20px;font-size:14px;color:#374151;font-style:italic;">
    "{{ $message_preview }}"
  </td></tr>
  </table>

  <table cellpadding="0" cellspacing="0" style="margin:8px 0 20px;">
  <tr><td style="background:linear-gradient(135deg,#E8470A,#ea580c);border-radius:10px;">
    <a href="{{ $reply_url }}" style="display:inline-block;padding:14px 32px;font-size:14px;font-weight:600;color:#fff;text-decoration:none;">💬 Reply to Message →</a>
  </td></tr>
  </table>

  <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.7;">Please log in to your TrafficVai dashboard to view the full message and respond.</p>
</td></tr>

<!-- FOOTER -->
<tr><td style="background:#1e1b4b;padding:28px 40px;text-align:center;">
  <img src="{{ $logo_url ?? 'https://trafficvai.com/images/logo.png' }}" alt="TrafficVai" height="34" style="display:block;margin:0 auto 16px;">
  <p style="margin:0 0 8px;font-size:16px;font-weight:700;color:#fff;">Traffic<span style="color:#E8470A;">Vai</span></p>
  <hr style="border:none;border-top:1px solid rgba(255,255,255,0.08);margin:12px 0;">
  <p style="margin:0 0 8px;">
    <a href="{{ $order_url }}" style="color:#8b92a5;text-decoration:none;font-size:12px;margin:0 10px;">My Orders</a>
    <a href="{{ url('/messages') }}" style="color:#8b92a5;text-decoration:none;font-size:12px;margin:0 10px;">Messages</a>
    <a href="{{ url('/contact') }}" style="color:#8b92a5;text-decoration:none;font-size:12px;margin:0 10px;">Contact</a>
  </p>
  <p style="margin:0;font-size:11px;color:#4b5563;line-height:1.6;">Questions? Contact us at <span style="color:#E8470A;">support@trafficvai.com</span><br>© {{ date('Y') }} TrafficVai. All rights reserved.</p>
</td></tr>

</table>
</td></tr>
</table>
</body>
</html>
