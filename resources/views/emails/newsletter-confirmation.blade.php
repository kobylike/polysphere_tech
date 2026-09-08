<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Confirm Your Subscription — Polysphere Tech</title>
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

        .btn-primary {
            display: inline-block;
            padding: 14px 36px;
            background: linear-gradient(120deg, #312E81, #4338CA);
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 700;
            border-radius: 50px;
            letter-spacing: -0.2px;
            transition: transform 0.2s ease;
        }

        .btn-primary:hover {
            transform: scale(1.02);
        }

        .footer-link {
            color: #4338CA;
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
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

                            {{-- Logo: concentric orbit mark ── --}}
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
                                Confirm your newsletter subscription
                            </p>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td style="background:#ffffff; padding: 40px 40px;">

                            {{-- Greeting --}}
                            <h1
                                style="margin:0 0 8px; font-size:24px; font-weight:700; color:#0f172a; letter-spacing:-.4px;">
                                Welcome to Polysphere Tech! 🚀
                            </h1>

                            <p style="margin:0 0 24px; font-size:15px; color:#475569; line-height:1.6;">
                                Thanks for subscribing to our newsletter. You'll receive exclusive insights, industry
                                trends, and special offers.
                            </p>

                            {{-- Email confirmation box --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;
                                       margin-bottom:28px; overflow:hidden;">
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                                  color:#94A3B8; font-weight:600;">Email Address</p>
                                        <p style="margin:0; font-size:15px; font-weight:600; color:#1e293b;">
                                            {{ $email }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                                  color:#94A3B8; font-weight:600;">Status</p>
                                        <p style="margin:0; font-size:14px; color:#475569;">
                                            <span
                                                style="display:inline-block; padding:2px 12px; border-radius:50px;
                                                         background:#FEF3C7; color:#D97706; font-size:12px; font-weight:600;">
                                                Pending Confirmation
                                            </span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Verification CTA --}}
                            <p style="margin:0 0 16px; font-size:15px; color:#475569; line-height:1.6;">
                                Please confirm your email address by clicking the button below:
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto 28px;">
                                <tr>
                                    <td style="border-radius:50px; overflow:hidden;">
                                        <a href="{{ $verificationUrl }}" class="btn-primary">
                                            Confirm Subscription
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Expiry note --}}
                            <div
                                style="background:#FEF2F2; border:1px solid #FECACA; border-radius:8px; padding:12px 16px; margin-bottom:28px;">
                                <p style="margin:0; font-size:13px; color:#991B1B;">
                                    <span style="font-weight:600;">⚠️ This link expires in 24 hours.</span>
                                    If you didn't sign up, you can safely ignore this email.
                                </p>
                            </div>

                            {{-- Additional info --}}
                            <p style="margin:0; font-size:13px; color:#94A3B8; line-height:1.6;">
                                <span style="display:block; margin-bottom:4px;">What you'll receive:</span>
                                <span style="display:block; margin-bottom:2px;">• 📰 Monthly industry insights &amp;
                                    trends</span>
                                <span style="display:block; margin-bottom:2px;">• 🚀 Exclusive offers &amp; early
                                    access</span>
                                <span style="display:block;">• 💡 Expert tips &amp; case studies</span>
                            </p>

                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="background:#F8FAFC; border-top:1px solid #E2E8F0;
                                   border-radius:0 0 16px 16px; padding:24px 40px; text-align:center;">
                            <p style="margin:0 0 8px; font-size:12px; color:#94A3B8;">
                                You received this email because you subscribed to the
                                <a href="{{ config('app.url') }}" class="footer-link">Polysphere Tech</a> newsletter.
                            </p>
                            <p style="margin:0; font-size:12px; color:#CBD5E1;">
                                &copy; {{ date('Y') }} Polysphere Tech · 123 Tech Hub, Innovation District, Silicon
                                Valley, CA
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>