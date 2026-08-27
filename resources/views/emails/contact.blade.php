<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>{{ $messageSubject }} — Polysphere Tech Contact</title>
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

        /* Category badge colours */
        .badge-general     { background: #EEF2FF; color: #4338CA; }
        .badge-billing      { background: #FFF7ED; color: #C2410C; }
        .badge-technical    { background: #ECFDF5; color: #047857; }
        .badge-partnership  { background: #FDF4FF; color: #A21CAF; }
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

                            {{-- Logo: concentric orbit mark, distinct from PolyTopUp's bar-icon --}}
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
                                New submission from your website contact form
                            </p>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td style="background:#ffffff; padding: 36px 40px;">

                            {{-- Category badge --}}
                            @php
                                $badgeColors = [
                                    'General'     => ['bg' => '#EEF2FF', 'color' => '#4338CA'],
                                    'Billing'     => ['bg' => '#FFF7ED', 'color' => '#C2410C'],
                                    'Technical'   => ['bg' => '#ECFDF5', 'color' => '#047857'],
                                    'Partnership' => ['bg' => '#FDF4FF', 'color' => '#A21CAF'],
                                ];
                                $bc = $badgeColors[$category] ?? $badgeColors['General'];
                            @endphp
                            <div style="margin-bottom:22px;">
                                <span style="display:inline-block; padding:4px 12px; border-radius:50px;
                                             background:{{ $bc['bg'] }}; color:{{ $bc['color'] }};
                                             font-size:12px; font-weight:700; letter-spacing:.08em;
                                             text-transform:uppercase;">
                                    {{ $category }}
                                </span>
                            </div>

                            {{-- Subject line --}}
                            <h1 style="margin:0 0 24px; font-size:22px; font-weight:700;
                                       color:#0f172a; line-height:1.3; letter-spacing:-.3px;">
                                {{ $messageSubject }}
                            </h1>

                            {{-- Sender meta table --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;
                                       margin-bottom:28px; overflow:hidden;">
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                                  color:#94A3B8; font-weight:600;">From</p>
                                        <p style="margin:0; font-size:15px; font-weight:600; color:#1e293b;">
                                            {{ $senderName }}
                                            <a href="mailto:{{ $senderEmail }}"
                                               style="font-size:13px; font-weight:400; color:#4338CA;
                                                      text-decoration:none; margin-left:6px;">
                                                &lt;{{ $senderEmail }}&gt;
                                            </a>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                                  color:#94A3B8; font-weight:600;">Received</p>
                                        <p style="margin:0; font-size:14px; color:#475569;">{{ $sentAt }}</p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Divider label --}}
                            <p style="margin:0 0 12px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                      color:#94A3B8; font-weight:600;">Message</p>

                            {{-- Message body --}}
                            <div style="background:#F8FAFC; border-left:4px solid #4338CA; border-radius:0 8px 8px 0;
                                        padding:18px 22px; margin-bottom:28px;">
                                <p style="margin:0; font-size:15px; line-height:1.7; color:#334155; white-space:pre-wrap;">
                                    {{ $messageBody }}
                                </p>
                            </div>

                            {{-- CTA reply button --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                <tr>
                                    <td style="border-radius:50px; overflow:hidden;">
                                        <a href="mailto:{{ $senderEmail }}?subject=Re%3A {{ urlencode('Contact: ' . $messageSubject) }}"
                                           style="display:inline-block; padding:13px 32px;
                                                  background:linear-gradient(120deg,#312E81,#4338CA);
                                                  color:#ffffff; text-decoration:none; font-size:15px;
                                                  font-weight:700; border-radius:50px; letter-spacing:-.1px;">
                                            Reply to {{ $senderName }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="background:#F8FAFC; border-top:1px solid #E2E8F0;
                                   border-radius:0 0 16px 16px; padding:24px 40px; text-align:center;">
                            <p style="margin:0 0 8px; font-size:12px; color:#94A3B8;">
                                This message was submitted via the contact form at
                                <a href="{{ config('app.url') }}/contact" style="color:#4338CA; text-decoration:none;">
                                    {{ config('app.url') }}/contact
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