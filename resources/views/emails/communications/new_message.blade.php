<x-mail::message>
# You Have a New Message

Hi {{ $messageData['recipient_name'] }},

**{{ $messageData['sender_name'] }}** just sent you a new message regarding your query or order.

<x-mail::panel>
"{{ Str::limit($messageData['message'], 150) }}"
</x-mail::panel>

Click the button below to view the full message thread and reply.

<x-mail::button :url="$messageData['link']">
View Message Thread
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
