<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $announcement->subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="color: #4f46e5;">{{ config('app.name') }}</h2>
    </div>

    <div style="background-color: #f9fafb; padding: 20px; border-radius: 8px;">
        {!! nl2br(e($content)) !!}
    </div>

    <div style="margin-top: 30px; font-size: 12px; color: #9ca3af; text-align: center;">
        <p>You are receiving this email because you are a registered client at {{ config('app.name') }}.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>

</body>
</html>
