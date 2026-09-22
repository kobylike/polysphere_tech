<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Application received — {{ $vacancy->title }}</title>
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
        style="background:#EEF1F8; padding: 40px 16px;">
        <tr>
            <td align="center">

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

                            <p
                                style="margin: 14px 0 0; color: rgba(255,255,255,0.65); font-size:13px; letter-spacing:.02em;">
                                We've received your application
                            </p>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td style="background:#ffffff; padding: 40px;">

                            <h1 style="margin:0 0 10px; font-size:23px; font-weight:700;
                                       color:#0f172a; line-height:1.3; letter-spacing:-.3px;">
                                Thanks for applying, {{ $application->name }}.
                            </h1>
                            <p style="margin:0 0 26px; font-size:15px; line-height:1.65; color:#475569;">
                                We've received your application for the
                                <strong style="color:#0f172a;">{{ $vacancy->title }}</strong> role.
                                A real person on our team will read it within
                                <strong style="color:#0f172a;">3 working days</strong>.
                            </p>

                            {{-- Department badge --}}
                            <div style="margin-bottom:22px;">
                                <span style="display:inline-block; padding:4px 12px; border-radius:50px;
                                             background:#EEF2FF; color:#4338CA;
                                             font-size:12px; font-weight:700; letter-spacing:.08em;
                                             text-transform:uppercase;">
                                    {{ $department }}
                                </span>
                                <span style="display:inline-block; margin-left:8px; padding:4px 12px; border-radius:50px;
                                             background:#ECFDF5; color:#047857;
                                             font-size:12px; font-weight:700; letter-spacing:.08em;
                                             text-transform:uppercase;">
                                    Received
                                </span>
                            </div>

                            {{-- Role meta table --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;
                                       margin-bottom:28px; overflow:hidden;">
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p
                                            style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">
                                            Role</p>
                                        <p style="margin:0; font-size:15px; font-weight:600; color:#1e293b;">
                                            {{ $vacancy->title }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p
                                            style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">
                                            Applied</p>
                                        <p style="margin:0; font-size:14px; color:#475569;">{{ $sentAt }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p
                                            style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">
                                            Reference</p>
                                        <p
                                            style="margin:0; font-size:14px; color:#475569; font-family:'SF Mono', Menlo, Consolas, monospace;">
                                            {{ $reference }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- What happens next --}}
                            <p style="margin:0 0 12px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                      color:#94A3B8; font-weight:600;">What happens next</p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="margin-bottom:28px;">
                                <tr>
                                    <td style="padding:10px 0; vertical-align:top; width:34px;">
                                        <span style="display:inline-block; width:24px; height:24px; border-radius:50%;
                                                     background:#EEF2FF; color:#4338CA;
                                                     font-size:12px; font-weight:700; text-align:center;
                                                     line-height:24px;">1</span>
                                    </td>
                                    <td
                                        style="padding:10px 0; vertical-align:top; font-size:14px; color:#334155; line-height:1.6;">
                                        <strong style="color:#0f172a;">We read it.</strong>
                                        A member of our hiring team reviews every application personally.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0; vertical-align:top;">
                                        <span style="display:inline-block; width:24px; height:24px; border-radius:50%;
                                                     background:#EEF2FF; color:#4338CA;
                                                     font-size:12px; font-weight:700; text-align:center;
                                                     line-height:24px;">2</span>
                                    </td>
                                    <td
                                        style="padding:10px 0; vertical-align:top; font-size:14px; color:#334155; line-height:1.6;">
                                        <strong style="color:#0f172a;">We get back to you.</strong>
                                        If there's a fit, we'll reach out to schedule a chat — usually within 3–5 days.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 0; vertical-align:top;">
                                        <span style="display:inline-block; width:24px; height:24px; border-radius:50%;
                                                     background:#EEF2FF; color:#4338CA;
                                                     font-size:12px; font-weight:700; text-align:center;
                                                     line-height:24px;">3</span>
                                    </td>
                                    <td
                                        style="padding:10px 0; vertical-align:top; font-size:14px; color:#334155; line-height:1.6;">
                                        <strong style="color:#0f172a;">You stay in the loop.</strong>
                                        You can check your status anytime using the private link below — no login
                                        needed.
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 20px;">
                                <tr>
                                    <td style="border-radius:50px; overflow:hidden;">
                                        <a href="{{ $statusUrl }}" style="display:inline-block; padding:13px 32px;
                                                  background:linear-gradient(120deg,#312E81,#4338CA);
                                                  color:#ffffff; text-decoration:none; font-size:15px;
                                                  font-weight:700; border-radius:50px; letter-spacing:-.1px;">
                                            Track your application
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin:0 0 24px; font-size:12.5px; color:#94A3B8; text-align:center; line-height:1.55;">
                                Bookmark the link above — it's private to you.<br>
                                Need to change something? Just reply to this email.
                            </p>

                            <hr style="border:none; border-top:1px solid #E2E8F0; margin: 24px 0;">

                            <p style="margin:0; font-size:14px; line-height:1.65; color:#475569;">
                                Best of luck,<br>
                                <strong style="color:#0f172a;">The Polysphere Tech hiring team</strong>
                            </p>

                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="background:#F8FAFC; border-top:1px solid #E2E8F0;
                                   border-radius:0 0 16px 16px; padding:24px 40px; text-align:center;">
                            <p style="margin:0 0 8px; font-size:12px; color:#94A3B8;">
                                This message was sent because you applied for a role at
                                <a href="{{ config('app.url') }}" style="color:#4338CA; text-decoration:none;">
                                    Polysphere Tech
                                </a>
                            </p>
                            <p style="margin:0; font-size:12px; color:#CBD5E1;">
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