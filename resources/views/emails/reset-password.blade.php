<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Reset your password — Polysphere Tech</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 0;
            background: #EEF1F8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            color: #1e1b2e;
        }

        table { border-spacing: 0; border-collapse: collapse; }
        td { padding: 0; }
        img { display: block; border: 0; }
        a { color: inherit; }
    </style>
</head>

<body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background:#EEF1F8; padding: 40px 16px;">
        <tr>
            <td align="center">

                {{-- ── Card wrapper ── --}}
                <table role="presentation" width="100%" style="max-width:600px;">

                    {{-- ── Header bar ── --}}
                    <tr>
                        <td style="background: linear-gradient(120deg, #0F172A 0%, #1E293B 45%, #312E81 100%);
                                   border-radius: 16px 16px 0 0; padding: 36px 40px; text-align:center;">

                            <table role="presentation" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-right:12px; vertical-align:middle;">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <circle cx="15" cy="15" r="14" stroke="rgba(255,255,255,0.35)" stroke-width="1.4"/>
                                            <circle cx="15" cy="15" r="9" stroke="rgba(255,255,255,0.6)" stroke-width="1.4"/>
                                            <circle cx="15" cy="15" r="4" fill="#ffffff"/>
                                        </svg>
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <span style="font-family:'Segoe UI',Arial,sans-serif; font-weight:700; font-size:21px; color:#fff; letter-spacing:-.4px;">
                                            Polysphere Tech
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 14px 0 0; color: rgba(255,255,255,0.65); font-size:13px; letter-spacing:.02em;">
                                Password reset request
                            </p>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td style="background:#ffffff; padding: 36px 40px;">

                            <div style="margin-bottom:22px;">
                                <span style="display:inline-block; padding:4px 12px; border-radius:50px;
                                             background:#EEF2FF; color:#4338CA;
                                             font-size:12px; font-weight:700; letter-spacing:.08em;
                                             text-transform:uppercase;">
                                    Security
                                </span>
                            </div>

                            <h1 style="margin:0 0 12px; font-size:22px; font-weight:700;
                                       color:#0f172a; line-height:1.3; letter-spacing:-.3px;">
                                Reset your password
                            </h1>

                            <p style="margin:0 0 28px; font-size:15px; line-height:1.7; color:#475569;">
                                Hi {{ $recipientName }}, we received a request to reset the password for your
                                Polysphere Tech account (<strong>{{ $recipientEmail }}</strong>). Click the
                                button below to choose a new password.
                            </p>

                            {{-- CTA button --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 28px;">
                                <tr>
                                    <td style="border-radius:50px; overflow:hidden;">
                                        <a href="{{ $resetUrl }}"
                                           style="display:inline-block; padding:13px 32px;
                                                  background:linear-gradient(120deg,#312E81,#4338CA);
                                                  color:#ffffff; text-decoration:none; font-size:15px;
                                                  font-weight:700; border-radius:50px; letter-spacing:-.1px;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Meta table --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;
                                       margin-bottom:20px; overflow:hidden;">
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                                  color:#94A3B8; font-weight:600;">Requested</p>
                                        <p style="margin:0; font-size:14px; color:#475569;">{{ $sentAt }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                                  color:#94A3B8; font-weight:600;">Link expires</p>
                                        <p style="margin:0; font-size:14px; color:#475569;">In {{ $expiresInMinutes }} minutes</p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0; font-size:13px; line-height:1.6; color:#94A3B8;">
                                If the button doesn't work, copy and paste this URL into your browser:<br>
                                <a href="{{ $resetUrl }}" style="color:#4338CA; word-break:break-all;">{{ $resetUrl }}</a>
                            </p>

                            <p style="margin:20px 0 0; font-size:13px; line-height:1.6; color:#94A3B8;">
                                Didn't request this? You can safely ignore this email — your password won't change.
                            </p>

                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="background:#F8FAFC; border-top:1px solid #E2E8F0;
                                   border-radius:0 0 16px 16px; padding:24px 40px; text-align:center;">
                            <p style="margin:0 0 8px; font-size:12px; color:#94A3B8;">
                                This is an automated message from
                                <a href="{{ config('app.url') }}" style="color:#4338CA; text-decoration:none;">
                                    {{ config('app.url') }}
                                </a>
                            </p>
                            <p style="margin:0; font-size:12px; color:#CBD5E1;">
                                &copy; {{ date('Y') }} Polysphere Tech · 123 Tech Hub, Innovation District, Silicon Valley, CA
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>