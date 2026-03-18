<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 13px; color: #1f2937; background: #fff; }
        .page { padding: 48px; max-width: 800px; margin: 0 auto; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 48px; }
        .company-name { font-size: 24px; font-weight: 900; color: #111827; }
        .invoice-badge { text-align: right; }
        .invoice-label { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; color: #9ca3af; margin-bottom: 4px; }
        .invoice-number { font-size: 28px; font-weight: 900; color: #f97316; }
        .status-pill { display: inline-block; margin-top: 8px; padding: 4px 14px; border-radius: 999px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-unpaid { background: #fef3c7; color: #92400e; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        .status-draft { background: #f3f4f6; color: #6b7280; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        /* Meta Boxes */
        .meta-row { display: flex; justify-content: space-between; margin-bottom: 40px; border-top: 2px solid #f3f4f6; padding-top: 24px; }
        .meta-box { }
        .meta-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: #9ca3af; font-weight: 700; margin-bottom: 6px; }
        .meta-value { font-size: 14px; font-weight: 600; color: #111827; }
        .meta-sub { font-size: 12px; color: #6b7280; margin-top: 2px; }

        /* Items Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #111827; }
        thead th { padding: 12px 16px; text-align: left; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        thead th:last-child, thead th:nth-child(2), thead th:nth-child(3) { text-align: right; }
        tbody tr { border-bottom: 1px solid #f3f4f6; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 12px 16px; color: #374151; }
        tbody td:nth-child(2), tbody td:nth-child(3), tbody td:last-child { text-align: right; }
        tbody td:last-child { font-weight: 700; color: #111827; }

        /* Totals */
        .totals { margin-left: auto; width: 280px; }
        .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; color: #6b7280; }
        .total-row.grand { border-top: 2px solid #111827; margin-top: 8px; padding-top: 12px; font-size: 18px; font-weight: 900; color: #111827; }
        .total-row .discount { color: #059669; }

        /* Notes */
        .notes-section { margin-top: 40px; border-top: 1px solid #f3f4f6; padding-top: 24px; display: flex; gap: 40px; }
        .notes-block { flex: 1; }
        .notes-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1.5px; color: #9ca3af; font-weight: 700; margin-bottom: 8px; }
        .notes-text { font-size: 12px; color: #6b7280; line-height: 1.6; }

        /* Footer */
        .footer { margin-top: 48px; border-top: 1px solid #f3f4f6; padding-top: 16px; text-align: center; font-size: 11px; color: #9ca3af; }

        .no-print {
            margin: 20px auto;
            max-width: 800px;
            display: flex;
            justify-content: flex-end;
            padding: 0 48px;
        }
        .print-btn {
            background: #f97316;
            color: #white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            color: white;
            font-family: inherit;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .page { padding: 0; margin: 0; max-width: none; }
        }
    </style>
</head>
<body onload="if(new URLSearchParams(window.location.search).get('print') === '1') window.print()">
    <div class="no-print">
        <button onclick="window.print()" class="print-btn">Print Invoice</button>
    </div>
<div class="page">
    {{-- Header --}}
    <div class="header">
        <div>
            @php $logo = \App\Models\Setting::get('site_logo'); @endphp
            @if($logo)
                <img src="{{ Storage::disk('public')->url(str_replace('storage/', '', $logo)) }}" alt="{{ config('app.name') }}" style="height: 48px; max-width: 200px; object-fit: contain; margin-bottom: 4px;">
            @else
                <div class="company-name">{{ config('app.name') }}</div>
            @endif
        </div>
        <div class="invoice-badge">
            <div class="invoice-label">Invoice</div>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            <div class="status-pill status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</div>
        </div>
    </div>

    {{-- Meta --}}
    <div class="meta-row">
        <div class="meta-box">
            <div class="meta-label">Billed To</div>
            <div class="meta-value">{{ $invoice->user->name }}</div>
            <div class="meta-sub">{{ $invoice->user->email }}</div>
        </div>
        <div class="meta-box">
            <div class="meta-label">Issue Date</div>
            <div class="meta-value">{{ $invoice->created_at->format('M d, Y') }}</div>
        </div>
        @if($invoice->due_date)
        <div class="meta-box">
            <div class="meta-label">Due Date</div>
            <div class="meta-value">{{ $invoice->due_date->format('M d, Y') }}</div>
        </div>
        @endif
        <div class="meta-box">
            <div class="meta-label">Currency</div>
            <div class="meta-value">{{ $invoice->currency }}</div>
        </div>
    </div>

    {{-- Items --}}
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                <td>{{ $invoice->currency }} {{ number_format($item->unit_price, 2) }}</td>
                <td>{{ $invoice->currency }} {{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals">
        <div class="total-row">
            <span>Subtotal</span>
            <span>{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</span>
        </div>
        @if($invoice->discount_value > 0)
        <div class="total-row">
            <span>Discount {{ $invoice->discount_type === 'percentage' ? '('.$invoice->discount_value.'%)' : '(Fixed)' }}</span>
            <span class="discount">- {{ $invoice->currency }} {{ number_format($invoice->subtotal - $invoice->total + $invoice->tax_amount, 2) }}</span>
        </div>
        @endif
        @if($invoice->tax_rate > 0)
        <div class="total-row">
            <span>Tax ({{ $invoice->tax_rate }}%)</span>
            <span>{{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</span>
        </div>
        @endif
        <div class="total-row grand">
            <span>Total Due</span>
            <span>{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</span>
        </div>
    </div>

    {{-- Notes & Terms --}}
    @if($invoice->notes || $invoice->terms)
    <div class="notes-section">
        @if($invoice->notes)
        <div class="notes-block">
            <div class="notes-label">Notes</div>
            <div class="notes-text">{{ $invoice->notes }}</div>
        </div>
        @endif
        @if($invoice->terms)
        <div class="notes-block">
            <div class="notes-label">Terms & Conditions</div>
            <div class="notes-text">{{ $invoice->terms }}</div>
        </div>
        @endif
    </div>
    @endif

    <div class="footer">
        Thank you for your business &mdash; {{ config('app.name') }} &bull; {{ config('app.url') }}
    </div>
</div>
</body>
</html>
