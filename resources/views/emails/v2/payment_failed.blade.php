@php
    $tag = 'PAYMENT FAILED';
    $title = 'Payment Failed';
    $button_text = 'Retry Payment';
    $link = $order_url ?? url('/client/orders');
@endphp
@include('emails.v2.universal_v2', [
    'message' => 'Unfortunately, your payment for <strong>Order #{{ $order_id }}</strong> could not be processed. Please try again or contact support if the issue persists.'
])
