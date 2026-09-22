<div>
    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb"
            style="background-image: url('{{ asset('assets/main/imgs/resources/service.jpg') }}');"></div>
        <div class="breadcrumb__thumb_2"
            style="background-image: url('{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}');"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Your application</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>Application status</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="as-shell section-space">
        <div class="small-container">
            <div class="as-grid">
                <div class="as-card">
                    <div class="as-card__head">
                        <span class="as-avatar">{{ strtoupper($application->initials) }}</span>
                        <div>
                            <h3>{{ $application->name }}</h3>
                            <p>
                                Applied for <strong>{{ $application->vacancy->title }}</strong>
                                · {{ $application->vacancy->department?->name ?? 'General' }}
                            </p>
                        </div>
                        <span class="as-status as-status--{{ $application->statusEnum()->color() }}">
                            {{ $application->statusEnum()->label() }}
                        </span>
                    </div>

                    <div class="as-timeline">
                        @php
                            $stages = ['new', 'reviewing', 'shortlisted', 'interviewing', 'offer', 'hired'];
                            $currentIndex = array_search($application->status, $stages, true);
                            $isRejected = in_array($application->status, ['rejected', 'withdrawn'], true);
                        @endphp

                        @foreach($stages as $i => $stage)
                            @php
                                $done = $currentIndex !== false && $i < $currentIndex;
                                $active = $currentIndex !== false && $i === $currentIndex && !$isRejected;
                                $label = match ($stage) {
                                    'new' => 'Received',
                                    'reviewing' => 'Under review',
                                    'shortlisted' => 'Shortlisted',
                                    'interviewing' => 'Interviewing',
                                    'offer' => 'Offer',
                                    'hired' => 'Hired',
                                    default => ucfirst($stage),
                                };
                            @endphp
                            <div class="as-stage {{ $done ? 'is-done' : '' }} {{ $active ? 'is-active' : '' }}">
                                <div class="as-stage__dot"></div>
                                <div class="as-stage__label">{{ $label }}</div>
                            </div>
                        @endforeach

                        @if($isRejected)
                            <div class="as-stage as-stage--rejected is-active">
                                <div class="as-stage__dot"></div>
                                <div class="as-stage__label">
                                    {{ $application->statusEnum()->label() }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="as-facts">
                        <div>
                            <dt>Applied</dt>
                            <dd>{{ $application->created_at->format('F j, Y') }}</dd>
                        </div>
                        <div>
                            <dt>Reference</dt>
                            <dd><code>{{ \Illuminate\Support\Str::limit($application->tracking_token, 20) }}…</code>
                            </dd>
                        </div>
                        @if($application->reviewed_at)
                            <div>
                                <dt>Last reviewed</dt>
                                <dd>{{ $application->reviewed_at->diffForHumans() }}</dd>
                            </div>
                        @endif
                    </div>

                    @if($application->status === 'interviewing')
                        <div class="as-note as-note--info">
                            <i class="fa-solid fa-calendar-check"></i>
                            <div>
                                <strong>We'll be in touch shortly</strong>
                                <p>Our team will email you to schedule the next conversation. Check your inbox (and spam
                                    folder just in case).</p>
                            </div>
                        </div>
                    @elseif($application->status === 'offer')
                        <div class="as-note as-note--success">
                            <i class="fa-solid fa-party-horn"></i>
                            <div>
                                <strong>We've sent you an offer 🎉</strong>
                                <p>Check your inbox for the full details. Reply with any questions — we're excited.</p>
                            </div>
                        </div>
                    @elseif(in_array($application->status, ['rejected', 'withdrawn'], true))
                        <div class="as-note as-note--muted">
                            <i class="fa-solid fa-heart"></i>
                            <div>
                                <strong>Thank you for applying</strong>
                                <p>We really appreciated your interest. We'll keep your details on file for future roles
                                    that might be a stronger match.</p>
                            </div>
                        </div>
                    @else
                        <div class="as-note as-note--soft">
                            <i class="fa-solid fa-hourglass-half"></i>
                            <div>
                                <strong>We're on it</strong>
                                <p>A real person reads every application. Expect an update within 3–5 working days.</p>
                            </div>
                        </div>
                    @endif

                    <div class="as-actions">
                        <a wire:navigate.hover href="{{ route('vacancy.details', $application->vacancy->slug) }}"
                            class="as-btn as-btn--ghost">
                            <i class="fa-solid fa-arrow-left"></i> Back to role
                        </a>
                        <a href="mailto:careers@polyspheretech.com?subject={{ urlencode('Question about my application — ' . $application->vacancy->title) }}"
                            class="as-btn as-btn--primary">
                            <i class="fa-solid fa-envelope"></i> Email the team
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('styles')
    <style>
        .as-shell {
            background: #f6f7fb;
        }

        .as-grid {
            display: flex;
            justify-content: center;
        }

        .as-card {
            background: #fff;
            border: 1px solid #e7e9f2;
            border-radius: 20px;
            padding: 34px 36px;
            max-width: 820px;
            width: 100%;
            box-shadow: 0 20px 48px rgba(13, 27, 46, .06);
        }

        .as-card__head {
            display: flex;
            align-items: center;
            gap: 18px;
            padding-bottom: 26px;
            border-bottom: 1px solid #eef2f6;
            flex-wrap: wrap;
        }

        .as-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2f6fed, #6366f1);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 22px;
            flex-shrink: 0;
        }

        .as-card__head h3 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 4px;
            color: #0d1b2e;
            letter-spacing: -.3px;
        }

        .as-card__head p {
            color: #667085;
            font-size: 14px;
            margin: 0;
        }

        .as-card__head p strong {
            color: #0d1b2e;
        }

        .as-status {
            margin-left: auto;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .as-status--primary {
            background: rgba(47, 111, 237, .12);
            color: #1d4ed8;
        }

        .as-status--info {
            background: rgba(56, 189, 248, .14);
            color: #0284c7;
        }

        .as-status--warning {
            background: rgba(245, 158, 11, .14);
            color: #b45309;
        }

        .as-status--success {
            background: rgba(16, 185, 129, .14);
            color: #047857;
        }

        .as-status--danger {
            background: rgba(239, 68, 68, .12);
            color: #b91c1c;
        }

        .as-status--secondary {
            background: #f1f5f9;
            color: #64748b;
        }

        /* Timeline */
        .as-timeline {
            display: flex;
            align-items: center;
            gap: 0;
            margin: 32px 0 26px;
            position: relative;
        }

        .as-stage {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .as-stage::before {
            content: '';
            position: absolute;
            top: 9px;
            left: 0;
            right: 0;
            height: 2px;
            background: #eef2f6;
        }

        .as-stage:first-child::before {
            left: 50%;
        }

        .as-stage:last-child::before {
            right: 50%;
        }

        .as-stage__dot {
            position: relative;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #e2e8f0;
            border: 3px solid #fff;
            box-shadow: 0 0 0 1px #e2e8f0;
            margin: 0 auto 10px;
            z-index: 1;
            transition: all .2s ease;
        }

        .as-stage__label {
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .as-stage.is-done .as-stage__dot {
            background: #10b981;
            box-shadow: 0 0 0 1px #10b981;
        }

        .as-stage.is-done .as-stage__label {
            color: #047857;
        }

        .as-stage.is-active .as-stage__dot {
            background: #2f6fed;
            box-shadow: 0 0 0 4px rgba(47, 111, 237, .18);
            animation: as-pulse 1.8s ease-in-out infinite;
        }

        .as-stage.is-active .as-stage__label {
            color: #0d1b2e;
            font-weight: 700;
        }

        .as-stage--rejected .as-stage__dot {
            background: #ef4444;
            box-shadow: 0 0 0 1px #ef4444;
        }

        .as-stage--rejected .as-stage__label {
            color: #b91c1c;
        }

        @keyframes as-pulse {

            0%,
            100% {
                box-shadow: 0 0 0 4px rgba(47, 111, 237, .18);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(47, 111, 237, .08);
            }
        }

        .as-facts {
            display: flex;
            flex-wrap: wrap;
            gap: 28px;
            padding: 20px 0 24px;
            border-bottom: 1px solid #eef2f6;
        }

        .as-facts>div {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .as-facts dt {
            font-size: 11.5px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 700;
            margin: 0;
        }

        .as-facts dd {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: #0d1b2e;
        }

        .as-facts code {
            background: #f1f5f9;
            padding: 2px 6px;
            border-radius: 5px;
            font-size: 12.5px;
        }

        .as-note {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 18px 20px;
            border-radius: 14px;
            margin-top: 22px;
        }

        .as-note i {
            font-size: 20px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .as-note strong {
            display: block;
            font-size: 14.5px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .as-note p {
            margin: 0;
            font-size: 13.5px;
            line-height: 1.55;
        }

        .as-note--info {
            background: rgba(56, 189, 248, .08);
            color: #075985;
        }

        .as-note--info i {
            color: #0ea5e9;
        }

        .as-note--success {
            background: rgba(16, 185, 129, .08);
            color: #065f46;
        }

        .as-note--success i {
            color: #10b981;
        }

        .as-note--muted {
            background: #f8fafc;
            color: #475467;
        }

        .as-note--muted i {
            color: #94a3b8;
        }

        .as-note--soft {
            background: #fffbeb;
            color: #78350f;
        }

        .as-note--soft i {
            color: #f59e0b;
        }

        .as-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 28px;
        }

        .as-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: transform .15s ease;
        }

        .as-btn--ghost {
            color: #475467;
        }

        .as-btn--ghost:hover {
            color: #0d1b2e;
            background: #f8fafc;
        }

        .as-btn--primary {
            background: #0d1b2e;
            color: #fff;
            margin-left: auto;
        }

        .as-btn--primary:hover {
            transform: translateY(-1px);
            color: #fff;
        }

        @media (max-width: 767.98px) {
            .as-card {
                padding: 24px 22px;
            }

            .as-timeline {
                flex-wrap: wrap;
                gap: 14px;
            }

            .as-stage {
                flex: 0 0 calc(50% - 7px);
            }

            .as-stage::before {
                display: none;
            }

            .as-status {
                margin-left: 0;
            }
        }
    </style>
@endpush