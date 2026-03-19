@php
    $tag = 'NEW INVOICE';
    $title = 'New Invoice Generated';
    $button_text = 'View Invoice';
@endphp
@include('emails.v2.universal_v2', [
    'message' => 'A new invoice has been generated for your recent order or renewal. You can view it and complete the payment through your dashboard.'
])
