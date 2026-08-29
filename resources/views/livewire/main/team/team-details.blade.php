<div>
    <!-- ============================================================ -->
    <!-- BREADCRUMB                                                 -->
    <!-- ============================================================ -->
    <div class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb" data-background="{{ asset('assets/main/imgs/resources/team.jpg') }}"></div>
        <div class="breadcrumb__thumb_2" data-background="assets/imgs/resources/page-title-bg-2.png"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">{{ $member->name }}</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span><a wire:navigate.hover href="{{ route('team') }}">Team</a></span></li>
                                    <li><span>{{ $member->position }}</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TEAM SINGLE PROFILE                                         -->
    <!-- ============================================================ -->
    <section class="team-single pt-120 pb-120">
        <div class="container">
            <div class="row g-4 align-items-center">
                <!-- Avatar column -->
                <div class="col-lg-4 col-md-6">
                    <div class="team-single__image">
                        <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}"
                            style="width:100%; height:400px; object-fit:cover;">
                        <div class="team-info">
                            @if($member->social_links['facebook'] ?? false)
                                <a href="{{ $member->social_links['facebook'] }}" target="_blank"><i
                                        class="fa-brands fa-facebook-f"></i></a>
                            @endif
                            @if($member->social_links['linkedin'] ?? false)
                                <a href="{{ $member->social_links['linkedin'] }}" target="_blank" class="active"><i
                                        class="fa-brands fa-linkedin-in"></i></a>
                            @endif
                            @if($member->social_links['twitter'] ?? false)
                                <a href="{{ $member->social_links['twitter'] }}" target="_blank"><i
                                        class="fa-brands fa-twitter"></i></a>
                            @endif
                            @if($member->social_links['github'] ?? false)
                                <a href="{{ $member->social_links['github'] }}" target="_blank"><i
                                        class="fa-brands fa-github"></i></a>
                            @endif
                            @if($member->social_links['youtube'] ?? false)
                                <a href="{{ $member->social_links['youtube'] }}" target="_blank"><i
                                        class="fa-brands fa-youtube"></i></a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Profile content -->
                <div class="col-lg-8">
                    <div class="team-single__content">
                        <div class="title pb-20 mb-20 bor-bottom">
                            <h3>{{ $member->name }}</h3>
                            <span class="primary-color mt-1">{{ $member->position ?? 'Team Member' }}</span>
                        </div>
                        <div class="team-single__info">
                            <h4 class="pb-2">About Me</h4>
                            <p class="mb-20">{{ $member->about_me ?? 'No bio provided.' }}</p>

                            @if(!empty($member->skills))
                                <div class="skills mt-40">
                                    <div class="row g-4">
                                        @foreach($member->skills as $skill)
                                            <div class="col-md-6">
                                                <div class="experience-progress-wrapper">
                                                    <div class="experience-progress pb-4">
                                                        <div
                                                            class="experience-title-wrapper d-flex align-items-center justify-content-between">
                                                            <h5 class="experience-title pb-2">{{ $skill['name'] }}</h5>
                                                            <span class="exp">{{ $skill['level'] }}%</span>
                                                        </div>
                                                        <div class="progress">
                                                            <div class="progress-bar wow slideInLeft" data-wow-duration=".8s"
                                                                role="progressbar" style="width: {{ $skill['level'] }}%;"
                                                                aria-valuenow="{{ $skill['level'] }}" aria-valuemin="0"
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- EDUCATION                                                    -->
        <!-- ============================================================ -->
        @if(!empty($member->education))
            <div class="team-single-history mt-60">
                <div class="container">
                    <div class="title pb-30 mb-30 bor-bottom">
                        <h3>Education Background</h3>
                    </div>
                    @foreach($member->education as $edu)
                        <div class="edu-entry mb-4">
                            <h4>
                                <span class="primary-color text-capitalize">{{ $edu['degree'] }}</span>
                                @if($edu['start_year'])
                                    , {{ $edu['start_year'] }}
                                    @if($edu['end_year'] && !$edu['currently_studying'])
                                        – {{ $edu['end_year'] }}
                                    @elseif($edu['currently_studying'])
                                        – Present
                                    @endif
                                @endif
                            </h4>
                            <p><strong>{{ $edu['institution'] }}</strong></p>
                            @if(!empty($edu['description']))
                                <p class="mt-1">{{ $edu['description'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <!-- ============================================================ -->
    <!-- STYLES                                                       -->
    <!-- ============================================================ -->
    <style>
        /* ─── Team Single Page ────────────────────────────────────────────── */

        .team-single {
            background: #f8fafc;
        }

        /* ---- Avatar & overlay ---- */
        .team-single__image {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .team-single__image:hover {
            transform: translateY(-4px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.25);
        }

        .team-single__image img {
            width: 100%;
            height: 420px;
            object-fit: cover;
            display: block;
            background: #e2e8f0;
            transition: transform 0.5s ease;
        }

        .team-single__image:hover img {
            transform: scale(1.02);
        }

        /* Social overlay */
        .team-info {
            position: absolute;
            bottom: 24px;
            left: 24px;
            right: 24px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            padding: 14px 20px;
            border-radius: 60px;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .team-info a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 16px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .team-info a:hover {
            background: #fff;
            color: #0f172a;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .team-info a.active {
            background: #fff;
            color: #0f172a;
        }

        /* ---- Content ---- */
        .team-single__content {
            padding: 0 12px;
        }

        .title.bor-bottom {
            border-bottom: 2px solid rgba(0, 0, 0, 0.06);
            padding-bottom: 20px;
            margin-bottom: 24px;
        }

        .title h3 {
            font-size: 32px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .primary-color {
            color: #4338ca;
            font-weight: 600;
            font-size: 18px;
            letter-spacing: 0.02em;
        }

        .team-single__info h4 {
            font-size: 20px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .team-single__info p {
            color: #475569;
            line-height: 1.8;
            font-size: 16px;
        }

        /* ---- Skills progress ---- */
        .skills {
            padding-top: 8px;
        }

        .experience-progress-wrapper {
            background: #f1f5f9;
            padding: 16px 20px 8px;
            border-radius: 12px;
            transition: background 0.2s;
        }

        .experience-progress-wrapper:hover {
            background: #e9edf5;
        }

        .experience-title-wrapper {
            margin-bottom: 2px;
        }

        .experience-title {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        .experience-title-wrapper .exp {
            font-weight: 700;
            font-size: 14px;
            color: #4338ca;
        }

        .progress {
            height: 8px;
            background: #e2e8f0;
            border-radius: 40px;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .progress-bar {
            height: 100%;
            border-radius: 40px;
            background: linear-gradient(90deg, #4338ca, #6366f1);
            transition: width 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        /* ---- Education ---- */
        .team-single-history {
            background: #ffffff;
            border-radius: 16px;
            padding: 40px 0 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            margin-top: 60px;
        }

        .team-single-history .title h3 {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
        }

        .team-single-history h4 {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 28px;
            margin-bottom: 4px;
        }

        .team-single-history h4 .primary-color {
            font-size: 18px;
        }

        .team-single-history p {
            color: #475569;
            line-height: 1.8;
            font-size: 15px;
        }

        .team-single-history p strong {
            color: #1e293b;
            font-weight: 600;
        }

        /* ---- Responsive ---- */
        @media (max-width: 991px) {
            .team-single__image img {
                height: 300px;
            }

            .team-info {
                bottom: 16px;
                left: 16px;
                right: 16px;
                padding: 10px 16px;
                gap: 8px;
            }

            .team-info a {
                width: 34px;
                height: 34px;
                font-size: 14px;
            }

            .title h3 {
                font-size: 28px;
            }

            .team-single__content {
                padding: 0;
                margin-top: 24px;
            }
        }

        @media (max-width: 576px) {
            .team-single__image img {
                height: 260px;
            }

            .team-info {
                gap: 6px;
                padding: 8px 12px;
            }

            .team-info a {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            .title h3 {
                font-size: 24px;
            }

            .primary-color {
                font-size: 16px;
            }

            .experience-progress-wrapper {
                padding: 12px 14px 4px;
            }

            .team-single-history h4 {
                font-size: 16px;
            }
        }
    </style>
</div>