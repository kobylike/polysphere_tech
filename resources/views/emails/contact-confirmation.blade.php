<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>We've received your message — Polysphere Tech</title>
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
        a { color: inherit; }
    </style>
</head>

<body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background:#EEF1F8; padding: 40px 16px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" style="max-width:560px;">

                    {{-- ── Header ── --}}
                    <tr>
                        <td style="background: linear-gradient(120deg, #0F172A 0%, #1E293B 45%, #312E81 100%);
                                   border-radius: 16px 16px 0 0; padding: 32px 40px; text-align:center;">
                            <table role="presentation" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-right:12px; vertical-align:middle;">
                                        <svg width="26" height="26" viewBox="0 0 30 30" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <circle cx="15" cy="15" r="14" stroke="rgba(255,255,255,0.35)" stroke-width="1.4"/>
                                            <circle cx="15" cy="15" r="9" stroke="rgba(255,255,255,0.6)" stroke-width="1.4"/>
                                            <circle cx="15" cy="15" r="4" fill="#ffffff"/>
                                        </svg>
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <span style="font-family:'Segoe UI',Arial,sans-serif; font-weight:700; font-size:19px; color:#fff; letter-spacing:-.4px;">
                                            Polysphere Tech
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td style="background:#ffffff; padding: 44px 40px 36px; text-align:center;">

                            {{-- Check mark icon --}}
                            <div style="width:56px; height:56px; margin:0 auto 22px; border-radius:50%;
                                        background:#ECFDF5; display:inline-flex; align-items:center; justify-content:center;">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 12.5L9.5 18L20 6" stroke="#047857" stroke-width="2.4"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>

                            <h1 style="margin:0 0 12px; font-size:21px; font-weight:700; color:#0f172a; letter-spacing:-.3px;">
                                Thanks for reaching out, {{ $recipientName }}!
                            </h1>

                            <p style="margin:0 0 24px; font-size:15px; line-height:1.7; color:#475569; max-width:420px; margin-left:auto; margin-right:auto;">
                                We've received your message about
                                <strong style="color:#1e293b;">&ldquo;{{ $originalSubject }}&rdquo;</strong>
                                and a member of our team will get back to you within 24 hours.
                            </p>

                            {{-- Divider --}}
                            <div style="height:1px; background:#E2E8F0; margin:28px 0;"></div>

                            <p style="margin:0; font-size:13px; line-height:1.7; color:#94A3B8;">
                                Need this sooner? Call us directly at
                                <a href="tel:+1234567890" style="color:#4338CA; font-weight:600; text-decoration:none;">
                                    +1 (234) 567-8900
                                </a>
                            </p>

                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="background:#F8FAFC; border-top:1px solid #E2E8F0;
                                   border-radius:0 0 16px 16px; padding:22px 40px; text-align:center;">
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