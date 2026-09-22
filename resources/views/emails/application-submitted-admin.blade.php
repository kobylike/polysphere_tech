<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>New application — {{ $vacancy->title }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; background: #EEF1F8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #1e1b2e; }
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
                                            <circle cx="15" cy="15" r="14" stroke="rgba(255,255,255,0.35)" stroke-width="1.4" />
                                            <circle cx="15" cy="15" r="9" stroke="rgba(255,255,255,0.6)" stroke-width="1.4" />
                                            <circle cx="15" cy="15" r="4" fill="#ffffff" />
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
                                New application received
                            </p>
                        </td>
                    </tr>

                    {{-- ── Body ── --}}
                    <tr>
                        <td style="background:#ffffff; padding: 36px 40px;">

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
                                    New
                                </span>
                            </div>

                            {{-- Title --}}
                            <h1 style="margin:0 0 6px; font-size:22px; font-weight:700;
                                       color:#0f172a; line-height:1.3; letter-spacing:-.3px;">
                                {{ $application->name }}
                            </h1>
                            <p style="margin:0 0 26px; font-size:15px; color:#475569;">
                                applied for <strong style="color:#0f172a;">{{ $vacancy->title }}</strong>
                            </p>

                            {{-- Candidate meta table --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:10px;
                                       margin-bottom:26px; overflow:hidden;">
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">Email</p>
                                        <p style="margin:0; font-size:15px; font-weight:600; color:#1e293b;">
                                            <a href="mailto:{{ $application->email }}" style="color:#4338CA; text-decoration:none;">
                                                {{ $application->email }}
                                            </a>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">Phone</p>
                                        <p style="margin:0; font-size:14px; color:#475569;">{{ $application->phone ?: '—' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">Location</p>
                                        <p style="margin:0; font-size:14px; color:#475569;">
                                            {{ trim(($application->location ?? '') . ($application->country ? ', ' . $application->country : '')) ?: '—' }}
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">Experience</p>
                                        <p style="margin:0; font-size:14px; color:#475569;">
                                            {{ $application->years_experience_label }}
                                            @if($application->current_role)
                                                · {{ $application->current_role }}@if($application->current_company), {{ $application->current_company }}@endif
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                                @if($application->availability)
                                    <tr>
                                        <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                            <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">Availability</p>
                                            <p style="margin:0; font-size:14px; color:#475569;">{{ $application->availability }}</p>
                                        </td>
                                    </tr>
                                @endif
                                @if($application->salary_expectation)
                                    <tr>
                                        <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                            <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">Salary expectation</p>
                                            <p style="margin:0; font-size:14px; color:#475569;">{{ $application->salary_expectation }}</p>
                                        </td>
                                    </tr>
                                @endif
                                @if($application->timezone)
                                    <tr>
                                        <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                            <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">Timezone</p>
                                            <p style="margin:0; font-size:14px; color:#475569;">{{ $application->timezone }}</p>
                                        </td>
                                    </tr>
                                @endif
                                @if($application->work_authorization)
                                    <tr>
                                        <td style="padding:14px 18px; border-bottom:1px solid #E2E8F0;">
                                            <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">Work authorization</p>
                                            <p style="margin:0; font-size:14px; color:#475569;">{{ $application->work_authorization }}</p>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:14px 18px; {{ $application->source ? 'border-bottom:1px solid #E2E8F0;' : '' }}">
                                        <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">Applied</p>
                                        <p style="margin:0; font-size:14px; color:#475569;">{{ $sentAt }}</p>
                                    </td>
                                </tr>
                                @if($application->source)
                                    <tr>
                                        <td style="padding:14px 18px;">
                                            <p style="margin:0 0 3px; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:#94A3B8; font-weight:600;">Heard about us via</p>
                                            <p style="margin:0; font-size:14px; color:#475569;">
                                                {{ $application->source }}@if($application->referrer_name) — {{ $application->referrer_name }}@endif
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            {{-- Links --}}
                            @php
                                $links = array_filter([
                                    'LinkedIn'  => $application->linkedin_url,
                                    'Portfolio' => $application->portfolio_url,
                                    'GitHub'    => $application->github_url,
                                    'Site'      => $application->personal_site_url,
                                    'Behance'   => $application->behance_url,
                                    'Dribbble'  => $application->dribbble_url,
                                    'Writing'   => $application->writing_samples_url,
                                ]);
                            @endphp

                            @if(! empty($links))
                                <p style="margin:0 0 10px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                          color:#94A3B8; font-weight:600;">Links</p>
                                <table role="presentation" cellpadding="0" cellspacing="0" style="margin-bottom:26px;">
                                    <tr>
                                        @foreach($links as $label => $url)
                                            <td style="padding: 0 8px 8px 0;">
                                                <a href="{{ $url }}" target="_blank" rel="noopener"
                                                    style="display:inline-block; padding:6px 14px; border-radius:50px;
                                                          background:#EEF2FF; color:#4338CA;
                                                          font-size:13px; font-weight:600; text-decoration:none;">
                                                    {{ $label }} &rarr;
                                                </a>
                                            </td>
                                        @endforeach
                                    </tr>
                                </table>
                            @endif

                            {{-- Cover note --}}
                            @if($application->cover_letter)
                                <p style="margin:0 0 12px; font-size:11px; text-transform:uppercase; letter-spacing:.1em;
                                          color:#94A3B8; font-weight:600;">Cover note</p>

                                <div style="background:#F8FAFC; border-left:4px solid #4338CA; border-radius:0 8px 8px 0;
                                            padding:18px 22px; margin-bottom:26px;">
                                    <p style="margin:0; font-size:15px; line-height:1.7; color:#334155; white-space:pre-wrap;">{{ $application->cover_letter }}</p>
                                </div>
                            @endif

                            {{-- CV notice --}}
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:10px;
                                       padding: 14px 18px; margin-bottom:26px;">
                                <tr>
                                    <td style="font-size:13.5px; color:#78350F; line-height:1.55;">
                                        <strong>📎 CV attached:</strong> {{ $application->cv_original_name }}
                                    </td>
                                </tr>
                            </table>

                            {{-- CTAs --}}
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto 8px;">
                                <tr>
                                    <td style="padding-right: 8px;">
                                        <a href="{{ $reviewUrl }}"
                                            style="display:inline-block; padding:13px 26px;
                                                  background:linear-gradient(120deg,#312E81,#4338CA);
                                                  color:#ffffff; text-decoration:none; font-size:14px;
                                                  font-weight:700; border-radius:50px; letter-spacing:-.1px;">
                                            Review in admin
                                        </a>
                                    </td>
                                    <td style="padding-left: 8px;">
                                        <a href="mailto:{{ $application->email }}?subject=Re%3A {{ urlencode($vacancy->title . ' application') }}"
                                            style="display:inline-block; padding:13px 26px;
                                                  background:#ffffff; color:#312E81;
                                                  border:1px solid #E2E8F0;
                                                  text-decoration:none; font-size:14px;
                                                  font-weight:700; border-radius:50px; letter-spacing:-.1px;">
                                            Reply to {{ Str::limit($application->name, 20) }}
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
                                Sent to the hiring inbox because a new application was submitted for
                                <strong style="color:#475569;">{{ $vacancy->title }}</strong>
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