@php
    $tag = 'PAYMENT REFUNDED';
    $title = 'Payment Refunded';
    $button_text = 'View Order';
    $link = $order_url ?? url('/client/orders');
@endphp
@include('emails.v2.universal_v2', [
    'message' => 'Your payment for <strong>Order #{{ $order_id }}</strong> has been successfully refunded. The amount should appear in your account within a few business days.'
])
