<x-mail::message>
# Order Status Updated

Hi {{ $orderData['user_name'] }},

The status of your order **#{{ $orderData['id'] }}** ({{ $orderData['title'] }}) has been updated.

<x-mail::panel>
**Status Change:**
- **Previous Status:** {{ ucfirst($orderData['old_status']) }}
- **New Status:** **{{ ucfirst($orderData['new_status']) }}**
</x-mail::panel>

Log into your dashboard to view the latest progress or provide any additional information if requested.

<x-mail::button :url="route('client.orders.index')">
View Order Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
