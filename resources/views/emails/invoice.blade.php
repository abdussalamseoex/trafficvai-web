<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 40px 20px; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }
        .hero { background: #111827; padding: 40px; color: #fff; }
        .hero h1 { font-size: 22px; font-weight: 900; margin: 0 0 4px; }
        .hero p { color: #9ca3af; font-size: 14px; margin: 0; }
        .badge { display: inline-block; background: #f97316; color: #fff; font-weight: 700; font-size: 11px; padding: 4px 12px; border-radius: 999px; letter-spacing: 1px; text-transform: uppercase; margin-top: 12px; }
        .content { padding: 32px 40px; }
        .content p { color: #374151; font-size: 14px; line-height: 1.7; margin-bottom: 16px; }
        .summary { background: #f9fafb; border-radius: 12px; padding: 20px 24px; margin: 24px 0; }
        .summary-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; color: #6b7280; }
        .summary-row.total { font-size: 16px; font-weight: 900; color: #111827; border-top: 1px solid #e5e7eb; padding-top: 12px; margin-top: 8px; }
        .btn { display: inline-block; background: #f97316; color: #fff; font-weight: 700; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-size: 14px; margin-top: 8px; }
        .footer { padding: 24px 40px; border-top: 1px solid #f3f4f6; color: #9ca3af; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="hero">
        @php $logo = \App\Models\Setting::get('site_logo'); @endphp
        @if($logo)
            <img src="{{ Storage::disk('public')->url(str_replace('storage/', '', $logo)) }}" alt="{{ config('app.name') }}" style="height: 40px; max-width: 160px; object-fit: contain; margin-bottom: 16px; filter: brightness(2);">
        @endif
        <h1>New Invoice from {{ config('app.name') }}</h1>
        <p>Invoice {{ $invoice->invoice_number }} has been issued to you.</p>
        <span class="badge">{{ ucfirst($invoice->status) }}</span>
    </div>
    <div class="content">
        <p>Hello {{ $invoice->user->name }},</p>
        <p>Please find attached your invoice. Here is a summary:</p>
        <div class="summary">
            <div class="summary-row"><span>Invoice #</span><span>{{ $invoice->invoice_number }}</span></div>
            <div class="summary-row"><span>Issue Date</span><span>{{ $invoice->created_at->format('M d, Y') }}</span></div>
            @if($invoice->due_date)
            <div class="summary-row"><span>Due Date</span><span>{{ $invoice->due_date->format('M d, Y') }}</span></div>
            @endif
            <div class="summary-row total"><span>Total Due</span><span>{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</span></div>
        </div>
        @if($invoice->notes)
        <p>{{ $invoice->notes }}</p>
        @endif
        <p>If you have any questions, please don't hesitate to reach out to us.</p>
        <div style="text-align: center; margin-top: 24px;">
            <a href="{{ route('client.invoices.show', $invoice) }}" class="btn">View Invoice Online</a>
        </div>
        <p>Thank you for your business!</p>
    </div>
    <div class="footer">
        {{ config('app.name') }} &bull; {{ config('app.url') }}<br>
        This email was sent automatically. You can view your invoice details in your dashboard.
    </div>
</div>
</body>
</html>
