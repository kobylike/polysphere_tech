<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">

    <title>Two-Factor Authentication Disabled</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #EEF1F8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #1e1b2e;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        td {
            padding: 0;
        }

        img {
            display: block;
            border: 0;
        }

        a {
            color: inherit;
        }
    </style>
</head>

<body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background:#EEF1F8; padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width:600px;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(120deg,#450A0A 0%,#991B1B 45%,#F59E0B 100%);
                                   border-radius:16px 16px 0 0;
                                   padding:32px 40px 28px;
                                   text-align:center;">
                            <table role="presentation" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-right:12px; vertical-align:middle;">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path
                                                d="M15 2.5L4.5 7.2v7.9c0 6.4 4.5 12.3 10.5 13.4 6-1.1 10.5-7 10.5-13.4V7.2L15 2.5z"
                                                stroke="rgba(255,255,255,0.45)" stroke-width="1.4" fill="none" />
                                            <path d="M15 9.5v6.5" stroke="#ffffff" stroke-width="2.2"
                                                stroke-linecap="round" fill="none" />
                                            <circle cx="15" cy="20.5" r="1.3" fill="#ffffff" />
                                        </svg>
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <span
                                            style="font-family:'Segoe UI',Arial,sans-serif; font-weight:700; font-size:20px; color:#fff; letter-spacing:-.4px;">
                                            {{ $companyName }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <p
                                style="margin:10px 0 0; color:rgba(255,255,255,0.8); font-size:13px; letter-spacing:.08em; text-transform:uppercase; font-weight:600;">
                                Security Alert
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background:#ffffff; padding:36px 40px;">
                            <h1
                                style="margin:0 0 8px; font-size:22px; font-weight:700; color:#0f172a; letter-spacing:-.3px;">
                                Two-Factor Authentication was turned OFF ⚠️
                            </h1>

                            <p style="margin:0 0 22px; font-size:15px; color:#475569; line-height:1.65;">
                                Hi <strong>{{ $user->name }}</strong>, two-factor authentication has
                                been <strong style="color:#B91C1C;">disabled</strong> on your
                                <strong>{{ $companyName }}</strong> account. Your account is now
                                protected only by your password.
                            </p>

                            {{-- Details card --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; margin-bottom:24px; overflow:hidden;">
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p
                                            style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">
                                            When</p>
                                        <p style="margin:0; font-size:15px; font-weight:600; color:#1e293b;">
                                            {{ $changedAt }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p
                                            style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">
                                            IP Address</p>
                                        <p
                                            style="margin:0; font-size:15px; font-weight:600; color:#1e293b; font-family:'SF Mono',Menlo,Consolas,monospace;">
                                            {{ $ipAddress }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p
                                            style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">
                                            Device</p>
                                        <p
                                            style="margin:0; font-size:13px; color:#475569; line-height:1.5; word-break:break-word;">
                                            {{ $userAgent }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Amber callout --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#FEF3C7; border-left:4px solid #F59E0B; border-radius:6px; margin-bottom:28px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0; font-size:14px; color:#78350F; line-height:1.6;">
                                            ⚠️ <strong>Didn't do this?</strong> Your account may have been
                                            compromised. Reset your password immediately and re-enable 2FA.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                <tr>
                                    <td style="border-radius:50px; overflow:hidden;">
                                        <a href="{{ $securityUrl }}" style="display:inline-block; padding:14px 40px;
                                                  background:linear-gradient(120deg,#991B1B,#F59E0B);
                                                  color:#ffffff; text-decoration:none; font-size:16px;
                                                  font-weight:700; border-radius:50px; letter-spacing:-.1px;">
                                            Re-enable Two-Factor Auth
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin:20px 0 0; font-size:13px; color:#94A3B8; text-align:center; line-height:1.6;">
                                Need help? <a href="mailto:support@{{ parse_url(config('app.url'), PHP_URL_HOST) }}"
                                    style="color:#B91C1C; text-decoration:none; font-weight:600;">Contact our security
                                    team</a> right away.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td
                            style="background:#F8FAFC; border-top:1px solid #E2E8F0; border-radius:0 0 16px 16px; padding:20px 40px; text-align:center;">
                            <p style="margin:0 0 6px; font-size:12px; color:#94A3B8;">
                                This is an automated security notification sent to
                                <strong style="color:#475569;">{{ $user->email }}</strong>
                            </p>
                            <p style="margin:0; font-size:12px; color:#CBD5E1;">
                                &copy; {{ date('Y') }} {{ $companyName }} · Accra, Ghana
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>