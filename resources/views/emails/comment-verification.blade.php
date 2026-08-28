<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Verify Your Comment — Polysphere Tech</title>
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
                                   border-radius: 16px 16px 0 0; padding: 32px 40px 28px; text-align:center;">

                            {{-- Logo: concentric orbit mark --}}
                            <table role="presentation" align="center" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-right:12px; vertical-align:middle;">
                                        <svg width="28" height="28" viewBox="0 0 30 30" fill="none"
                                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <circle cx="15" cy="15" r="14" stroke="rgba(255,255,255,0.35)" stroke-width="1.4"/>
                                            <circle cx="15" cy="15" r="9" stroke="rgba(255,255,255,0.6)" stroke-width="1.4"/>
                                            <circle cx="15" cy="15" r="4" fill="#ffffff"/>
                                        </svg>
                                    </td>
                                    <td style="vertical-align:middle;">
                                        <span style="font-family:'Segoe UI',Arial,sans-serif; font-weight:700; font-size:20px; color:#fff; letter-spacing:-.4px;">
                                            Polysphere Tech
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 12px 0 0; color: rgba(255,255,255,0.6); font-size:13px; letter-spacing:.02em;">
                                Confirm your comment on <strong style="color:#fff;">{{ $comment->post->title }}</strong>
                            </p>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td style="background:#ffffff; padding: 36px 40px;">

                            {{-- Greeting --}}
                            <h1 style="margin:0 0 6px; font-size:22px; font-weight:700; color:#0f172a; letter-spacing:-.3px;">
                                Hi {{ $comment->guest_name }},
                            </h1>
                            <p style="margin:0 0 24px; font-size:15px; color:#475569; line-height:1.6;">
                                You've left a comment on our blog post. To make it visible to everyone, we just need you to confirm your email address.
                            </p>

                            {{-- Comment preview card --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px; margin-bottom:28px; overflow:hidden;">
                                <tr>
                                    <td style="padding:16px 20px; border-bottom:1px solid #E2E8F0;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                                  color:#94A3B8; font-weight:600;">Your comment</p>
                                        <p style="margin:0; font-size:15px; color:#1e293b; line-height:1.6; white-space:pre-wrap;">
                                            {{ $comment->body }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 20px;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                                  color:#94A3B8; font-weight:600;">On post</p>
                                        <p style="margin:0; font-size:14px; color:#334155; font-weight:500;">
                                            “{{ $comment->post->title }}”
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Verify button --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                <tr>
                                    <td style="border-radius:50px; overflow:hidden;">
                                        <a href="{{ $url }}"
                                           style="display:inline-block; padding:14px 40px;
                                                  background:linear-gradient(120deg,#312E81,#4338CA);
                                                  color:#ffffff; text-decoration:none; font-size:16px;
                                                  font-weight:700; border-radius:50px; letter-spacing:-.1px;">
                                            Verify Comment
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:20px 0 0; font-size:13px; color:#94A3B8; text-align:center; line-height:1.6;">
                                If you didn't leave this comment, you can safely ignore this email.<br>
                                The link will expire in 24 hours.
                            </p>

                        </td>
                    </tr>

                    {{-- ── Footer ── --}}
                    <tr>
                        <td style="background:#F8FAFC; border-top:1px solid #E2E8F0;
                                   border-radius:0 0 16px 16px; padding:20px 40px; text-align:center;">
                            <p style="margin:0 0 6px; font-size:12px; color:#94A3B8;">
                                This verification was requested at
                                <a href="{{ config('app.url') }}{{ route('blog.details', $comment->post->slug) }}"
                                   style="color:#4338CA; text-decoration:none;">
                                    {{ config('app.url') }}{{ route('blog.details', $comment->post->slug) }}
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