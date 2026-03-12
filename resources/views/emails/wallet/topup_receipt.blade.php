<x-mail::message>
# Wallet Top-Up Successful

Hi {{ $topupData['user_name'] }},

We've successfully processed your wallet top-up request. The funds have been added to your account balance and are ready to be used for your next purchase!

<x-mail::panel>
**Transaction Details:**
- **Amount:** ${{ number_format($topupData['amount'], 2) }}
- **Method:** {{ ucfirst($topupData['payment_method']) }}
- **Transaction ID:** {{ $topupData['transaction_id'] ?? 'N/A' }}
- **Date:** {{ $topupData['date'] }}
</x-mail::panel>

<x-mail::button :url="route('client.dashboard')">
View My Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
