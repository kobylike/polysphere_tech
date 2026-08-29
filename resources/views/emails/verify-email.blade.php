<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .logo {
            text-align: center;
            margin-bottom: 24px;
        }

        .logo img {
            height: 40px;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            color: #475569;
            margin: 0 0 20px;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff !important;
            text-decoration: none;
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 50px;
            text-align: center;
            transition: all 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #94a3b8;
            margin-top: 32px;
            border-top: 1px solid #e2e8f0;
            padding-top: 24px;
        }

        .footer a {
            color: #6366f1;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('assets/users/img/logo/logo.png') }}" alt="Polysphere Tech">
        </div>
        <h1>Verify Your Email</h1>
        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        <p>Please click the button below to verify your email address and complete your account setup.</p>
        <p style="text-align: center; margin: 28px 0;">
            <a href="{{ $url }}" class="btn">Verify Email Address</a>
        </p>
        <p style="font-size: 14px; color: #94a3b8;">If you didn't create an account, you can safely ignore this email.
        </p>
        <p style="font-size: 14px; color: #94a3b8;">This link will expire in 60 minutes.</p>
        <div class="footer">
            &copy; {{ date('Y') }} <a href="{{ url('/') }}">Polysphere Tech</a> · All rights reserved.
        </div>
    </div>
</body>

</html>