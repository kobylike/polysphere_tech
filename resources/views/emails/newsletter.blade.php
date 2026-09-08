<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $subject ?? 'Newsletter' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }

        .header {
            background: linear-gradient(120deg, #0F172A, #312E81);
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }

        .header h1 {
            color: #fff;
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
            background: #f8fafc;
            border-radius: 0 0 8px 8px;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }

        .unsubscribe {
            color: #4338CA;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Polysphere Tech</h1>
    </div>
    <div class="content">
        {!! $body !!}
    </div>
    <div class="footer">
        <p>&copy; {{ date('Y') }} Polysphere Tech. All rights reserved.</p>
        <p>You received this email because you subscribed to our newsletter.</p>
        <p>To unsubscribe, <a
                href="{{ route('newsletter.unsubscribe', ['email' => $subscriber->email, 'token' => $subscriber->verification_token]) }}"
                class="unsubscribe">click here</a>.</p>
    </div>
</body>

</html>