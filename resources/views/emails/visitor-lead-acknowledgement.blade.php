<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>We got your message — Polysphere Tech</title>
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
                                Thanks for reaching out
                            </p>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td style="background:#ffffff; padding: 40px 40px 32px;">

                            {{-- Success badge --}}
                            <div style="margin-bottom:24px;">
                                <span style="display:inline-block; padding:5px 14px; border-radius:50px;
                                             background:#ECFDF5; color:#047857;
                                             font-size:12px; font-weight:700; letter-spacing:.08em;
                                             text-transform:uppercase;">
                                    ✓ Message Received
                                </span>
                            </div>

                            {{-- Headline --}}
                            <h1 style="margin:0 0 20px; font-size:24px; font-weight:700;
                                       color:#0f172a; line-height:1.3; letter-spacing:-.3px;">
                                @if($firstName)
                                    Hi {{ $firstName }},
                                @else
                                    Hi there,
                                @endif
                            </h1>

                            {{-- Lead paragraph --}}
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.7; color:#334155;">
                                Thanks for getting in touch with Polysphere Tech. We've received your message and a
                                member of our team will get back to you within <strong>24 hours</strong>.
                            </p>

                            <p style="margin:0 0 28px; font-size:16px; line-height:1.7; color:#334155;">
                                In the meantime, feel free to reply to this email with anything else that might help us
                                prepare — additional context, timelines, or questions.
                            </p>

                            {{-- Next steps card --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:12px;
                                       margin-bottom:28px;">
                                <tr>
                                    <td style="padding:20px 22px;">
                                        <p style="margin:0 0 14px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                                  color:#94A3B8; font-weight:700;">
                                            What happens next
                                        </p>

                                        {{-- Step 1 --}}
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                            style="margin-bottom:14px;">
                                            <tr>
                                                <td style="width:34px; vertical-align:top;">
                                                    <div style="width:26px; height:26px; border-radius:50%;
                                                                background:linear-gradient(135deg,#6366f1,#8b5cf6);
                                                                color:#fff; font-size:12px; font-weight:700;
                                                                text-align:center; line-height:26px;">
                                                        1
                                                    </div>
                                                </td>
                                                <td style="vertical-align:top; padding-left:12px;">
                                                    <p
                                                        style="margin:0; font-size:14.5px; font-weight:600; color:#0f172a;">
                                                        We review your message
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td style="padding-left:12px; padding-top:2px;">
                                                    <p
                                                        style="margin:0; font-size:13.5px; line-height:1.6; color:#64748b;">
                                                        A member of our team looks at what you've shared and prepares a
                                                        thoughtful reply.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Step 2 --}}
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                            style="margin-bottom:14px;">
                                            <tr>
                                                <td style="width:34px; vertical-align:top;">
                                                    <div style="width:26px; height:26px; border-radius:50%;
                                                                background:linear-gradient(135deg,#6366f1,#8b5cf6);
                                                                color:#fff; font-size:12px; font-weight:700;
                                                                text-align:center; line-height:26px;">
                                                        2
                                                    </div>
                                                </td>
                                                <td style="vertical-align:top; padding-left:12px;">
                                                    <p
                                                        style="margin:0; font-size:14.5px; font-weight:600; color:#0f172a;">
                                                        We reach out personally
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td style="padding-left:12px; padding-top:2px;">
                                                    <p
                                                        style="margin:0; font-size:13.5px; line-height:1.6; color:#64748b;">
                                                        You'll hear from us within 24 hours — usually much sooner during
                                                        business hours.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Step 3 --}}
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width:34px; vertical-align:top;">
                                                    <div style="width:26px; height:26px; border-radius:50%;
                                                                background:linear-gradient(135deg,#6366f1,#8b5cf6);
                                                                color:#fff; font-size:12px; font-weight:700;
                                                                text-align:center; line-height:26px;">
                                                        3
                                                    </div>
                                                </td>
                                                <td style="vertical-align:top; padding-left:12px;">
                                                    <p
                                                        style="margin:0; font-size:14.5px; font-weight:600; color:#0f172a;">
                                                        We talk about your project
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td style="padding-left:12px; padding-top:2px;">
                                                    <p
                                                        style="margin:0; font-size:13.5px; line-height:1.6; color:#64748b;">
                                                        A short discovery call or email exchange to understand what you
                                                        need and how we can help.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- Urgent? Contact card --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#EEF2FF; border-left:4px solid #4338CA; border-radius:0 10px 10px 0;
                                       margin-bottom:28px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="margin:0 0 6px; font-size:14px; font-weight:700; color:#312E81;">
                                            Need to reach us sooner?
                                        </p>
                                        <p style="margin:0; font-size:13.5px; line-height:1.7; color:#3730A3;">
                                            Call us on
                                            <a href="tel:+233597563427"
                                                style="color:#4338CA; font-weight:600; text-decoration:none;">
                                                {{ $contactPhone }}
                                            </a>
                                            or reply directly to this email.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Sign-off --}}
                            <p style="margin:0 0 6px; font-size:15px; line-height:1.7; color:#334155;">
                                Talk soon,
                            </p>
                            <p style="margin:0; font-size:15px; font-weight:700; color:#0f172a;">
                                The Polysphere Tech Team
                            </p>

                        </td>
                    </tr>

                    {{-- ── Meta strip ── --}}
                    <tr>
                        <td style="background:#F8FAFC; border-top:1px solid #E2E8F0; padding:18px 40px;">
                            <p style="margin:0; font-size:12px; line-height:1.6; color:#94A3B8; text-align:center;">
                                Message received {{ $receivedAt }}
                                @if($lead->email)
                                    · From <span style="color:#64748B;">{{ $lead->email }}</span>
                                @endif
                            </p>
                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="background:#F8FAFC; border-top:1px solid #E2E8F0;
                                   border-radius:0 0 16px 16px; padding:22px 40px 26px; text-align:center;">
                            <p style="margin:0 0 8px; font-size:12px; color:#94A3B8;">
                                Polysphere Tech · Accra, Ghana
                            </p>
                            <p style="margin:0; font-size:12px; color:#CBD5E1;">
                                <a href="{{ $siteUrl }}" style="color:#94A3B8; text-decoration:none;">{{ $siteUrl }}</a>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>