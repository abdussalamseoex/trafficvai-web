<x-mail::message>
# Order Confirmation

Hi {{ $orderData['user_name'] }},

Thank you for your order! We've received it and it's currently **{{ $orderData['status'] }}**.

<x-mail::panel>
**Order Summary:**
- **Order ID:** #{{ $orderData['id'] }}
- **Service/Product:** {{ $orderData['title'] }}
- **Total Amount:** ${{ number_format($orderData['amount'], 2) }}
- **Date:** {{ $orderData['date'] }}
</x-mail::panel>

You can track the progress of your order or submit any required details by visiting the order management page.

<x-mail::button :url="route('client.orders.index')">
View My Orders
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
