<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vacancy?->title ?? 'Your application' }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #EEF1F8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            color: #1e1b2e;
        }

        table {
            border-spacing: 0;
            border-collapse: collapse;
        }

        td {
            padding: 0;
        }

        a {
            color: inherit;
        }
    </style>
</head>

<body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background:#EEF1F8; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width:600px;">

                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(120deg, #0F172A 0%, #1E293B 45%, #312E81 100%);
                                   border-radius: 16px 16px 0 0; padding: 32px 40px; text-align:center;">
                            <table role="presentation" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-right:12px; vertical-align:middle;">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <circle cx="15" cy="15" r="14" stroke="rgba(255,255,255,0.35)"
                                                stroke-width="1.4" />
                                            <circle cx="15" cy="15" r="9" stroke="rgba(255,255,255,0.6)"
                                                stroke-width="1.4" />
                                            <circle cx="15" cy="15" r="4" fill="#ffffff" />
                                        </svg>
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <span
                                            style="font-family:'Segoe UI',Arial,sans-serif; font-weight:700; font-size:21px; color:#fff; letter-spacing:-.4px;">
                                            Polysphere Tech
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 14px 0 0; color: rgba(255,255,255,0.65); font-size:13px;">
                                Re: {{ $vacancy?->title ?? 'your application' }}
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background:#ffffff; padding: 36px 40px;">
                            <p style="margin:0 0 20px; font-size:15px; color:#475467;">
                                Hi <strong style="color:#0f172a;">{{ $application->name }}</strong>,
                            </p>

                            <div style="font-size:15px; line-height:1.75; color:#334155;">
                                {!! $bodyHtml !!}
                            </div>

                            <hr style="border:none; border-top:1px solid #E2E8F0; margin: 32px 0 20px;">

                            <p style="margin:0; font-size:14px; line-height:1.65; color:#475569;">
                                Warm regards,<br>
                                <strong style="color:#0f172a;">{{ $senderName }}</strong><br>
                                <span style="color:#94a3b8;">Polysphere Tech · Hiring Team</span>
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#F8FAFC; border-top:1px solid #E2E8F0;
                                   border-radius:0 0 16px 16px; padding:20px 40px; text-align:center;">
                            <p style="margin:0; font-size:12px; color:#94A3B8;">
                                &copy; {{ date('Y') }} Polysphere Tech · Accra, Ghana
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>