<div>
    @php
        /** @var \App\Models\Vacancy $vacancy */
        $workplaceClass = match ($vacancy->workplace_type->value) {
            'remote' => 'vchip--remote',
            'onsite' => 'vchip--onsite',
            'hybrid' => 'vchip--hybrid',
            default => '',
        };
        $workplaceIcon = match ($vacancy->workplace_type->value) {
            'remote' => 'fa-solid fa-house-laptop',
            'onsite' => 'fa-solid fa-building',
            'hybrid' => 'fa-solid fa-shuffle',
            default => 'fa-solid fa-location-dot',
        };

        // Split multi-line textareas into clean bullet lists where appropriate.
        $asLines = fn(?string $text) => collect(preg_split("/\r\n|\n|\r/", (string) $text))
            ->map(fn($l) => trim($l))
            ->filter()
            ->values();

        $responsibilities = $asLines($vacancy->responsibilities);
        $requirements = $asLines($vacancy->requirements);
        $benefits = $asLines($vacancy->benefits);
    @endphp

    {{-- ─── Breadcrumb ─── --}}
    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb"
            style="background-image: url('{{ asset('assets/main/imgs/resources/vacancy.jpg') }}');"></div>
        <div class="breadcrumb__thumb_2"
            style="background-image: url('{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}');"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">{{ $vacancy->title }}</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span><a wire:navigate.hover href="{{ route('vacancies') }}">Careers</a></span>
                                    </li>
                                    <li><span>{{ \Illuminate\Support\Str::limit($vacancy->title, 40) }}</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Summary strip ─── --}}
    <section class="vd-summary-strip">
        <div class="small-container">
            <div class="vd-summary-grid">
                <span class="vchip"><i
                        class="fa-solid fa-building"></i>{{ $vacancy->department?->name ?? 'General' }}</span>
                <span class="vchip"><i class="fa-solid fa-clock"></i>{{ $vacancy->employment_type->label() }}</span>
                <span class="vchip {{ $workplaceClass }}"><i
                        class="{{ $workplaceIcon }}"></i>{{ $vacancy->workplace_type->label() }}</span>
                <span class="vchip"><i
                        class="fa-solid fa-gauge-high"></i>{{ $vacancy->experience_level->label() }}</span>
                @if($vacancy->location)
                    <span class="vchip"><i
                            class="fa-solid fa-location-dot"></i>{{ $vacancy->location }}@if($vacancy->country),
                            {{ $vacancy->country }}@endif</span>
                @endif
                @if($vacancy->is_salary_visible && $vacancy->salary_range)
                    <span class="vchip" style="background:rgba(16,185,129,.1); color:#047857;">
                        <i class="fa-solid fa-coins" style="color:#10b981;"></i>{{ $vacancy->salary_range }}
                    </span>
                @endif
                @if($vacancy->positions_available > 1)
                    <span class="vchip"><i class="fa-solid fa-user-group"></i>{{ $vacancy->positions_available }}
                        positions</span>
                @endif
                @if($vacancy->is_featured)
                    <span class="vchip" style="background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff;">
                        <i class="fa-solid fa-star" style="color:#fff;"></i>Featured
                    </span>
                @endif
            </div>
        </div>
    </section>

    {{-- ─── Body ─── --}}
    <section class="vd-body section-space">
        <div class="small-container">
            <div class="row g-4">
                {{-- Main column --}}
                <div class="col-xxl-8 col-xl-8 col-lg-7">
                    <div class="vd-panel">
                        @if($vacancy->summary)
                            <p class="vd-lede">{{ $vacancy->summary }}</p>
                        @endif

                        <div class="vd-section">
                            <h4 class="vd-h">About the role</h4>
                            <div class="vd-rich">{!! nl2br(e($vacancy->description)) !!}</div>
                        </div>

                        @if($responsibilities->isNotEmpty())
                            <div class="vd-section">
                                <h4 class="vd-h"><i class="fa-solid fa-list-check"></i> What you'll do</h4>
                                <ul class="vd-list">
                                    @foreach($responsibilities as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($requirements->isNotEmpty())
                            <div class="vd-section">
                                <h4 class="vd-h"><i class="fa-solid fa-user-check"></i> What we're looking for</h4>
                                <ul class="vd-list">
                                    @foreach($requirements as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($benefits->isNotEmpty())
                            <div class="vd-section">
                                <h4 class="vd-h"><i class="fa-solid fa-gift"></i> What we offer</h4>
                                <ul class="vd-list vd-list--check">
                                    @foreach($benefits as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    {{-- Apply block --}}
                    <div class="vd-apply">
                        <div>
                            <h3>Ready to apply?</h3>
                            <p>Send your CV and a short note about why this role excites you — we read every
                                application.</p>
                        </div>
                        <div class="vd-apply__ctas">
                            <a wire:navigate.hover href="{{ route('vacancies.apply', $vacancy->slug) }}"
                                class="vd-apply-btn">
                                <i class="fa-solid fa-paper-plane"></i> Apply for this role
                            </a>
                            <a href="mailto:careers@polyspheretech.com?subject={{ urlencode('Application: ' . $vacancy->title) }}"
                                class="vd-apply__fallback">
                                Or email us directly
                            </a>
                            @if($vacancy->closing_date)
                                <span class="vd-apply__deadline">
                                    <i class="fa-regular fa-clock"></i>
                                    Applications close {{ $vacancy->closing_date->format('F j, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <aside class="col-xxl-4 col-xl-4 col-lg-5">
                    <div class="vd-sidebar">
                        <div class="vd-sidebar__card">
                            <h5 class="vd-sidebar__title">Job snapshot</h5>
                            <dl class="vd-facts">
                                <div>
                                    <dt>Department</dt>
                                    <dd>{{ $vacancy->department?->name ?? 'General' }}</dd>
                                </div>
                                <div>
                                    <dt>Employment</dt>
                                    <dd>{{ $vacancy->employment_type->label() }}</dd>
                                </div>
                                <div>
                                    <dt>Workplace</dt>
                                    <dd>{{ $vacancy->workplace_type->label() }}</dd>
                                </div>
                                <div>
                                    <dt>Experience</dt>
                                    <dd>{{ $vacancy->experience_level->label() }}</dd>
                                </div>
                                @if($vacancy->location)
                                    <div>
                                        <dt>Location</dt>
                                        <dd>{{ $vacancy->location }}@if($vacancy->country), {{ $vacancy->country }}@endif
                                        </dd>
                                    </div>
                                @endif
                                <div>
                                    <dt>Salary</dt>
                                    <dd>{{ ($vacancy->is_salary_visible && $vacancy->salary_range) ? $vacancy->salary_range : 'Not disclosed' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt>Positions</dt>
                                    <dd>{{ $vacancy->positions_available }}</dd>
                                </div>
                                @if($vacancy->closing_date)
                                    <div>
                                        <dt>Closing date</dt>
                                        <dd>{{ $vacancy->closing_date->format('F j, Y') }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <div class="vd-sidebar__card vd-sidebar__card--accent">
                            <h5 class="vd-sidebar__title"><i class="fa-solid fa-lightbulb"></i> Not quite the right fit?
                            </h5>
                            <p>Email us anyway — we're always on the lookout for great people.</p>
                            <a href="mailto:careers@polyspheretech.com?subject=General%20Application"
                                class="vd-sidebar__cta">
                                Send a general application <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- ─── Related vacancies ─── --}}
    @if($related->count() > 0)
        <section class="vd-related section-space">
            <div class="small-container">
                <div class="vd-related__head">
                    <h3>More roles you might like</h3>
                    <a wire:navigate.hover href="{{ route('vacancies') }}" class="vd-related__link">
                        View all openings <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <div class="row g-4">
                    <div class="row g-4">
                        @foreach($related as $rv)
                            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                                @include('livewire.main.partials.vacancy-card', [
                                    'vacancy' => $rv,
                                    'featured' => false,
                                ])
                            </div>
                        @endforeach
                    </div>
                </div>
        </section>
    @endif
</div>

@push('styles')
    <style>
        .vd-summary-strip {
            background: #f6f7fb;
            padding: 0;
        }

        .vd-summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            background: #fff;
            border: 1px solid #e7e9f2;
            border-radius: 14px;
            padding: 16px 20px;
            box-shadow: 0 18px 40px rgba(13, 27, 46, .08);
            margin-top: -46px;
            position: relative;
            z-index: 3;
        }

        .vd-summary-grid .vchip {
            background: #f1f5f9;
        }

        .vd-summary-grid .vchip--remote {
            background: rgba(16, 185, 129, .1);
            color: #047857;
        }

        .vd-summary-grid .vchip--onsite {
            background: rgba(99, 102, 241, .1);
            color: #4338ca;
        }

        .vd-summary-grid .vchip--hybrid {
            background: rgba(245, 158, 11, .12);
            color: #b45309;
        }

        .vd-body {
            background: #f6f7fb;
        }

        .vd-panel {
            background: #fff;
            border: 1px solid #e7e9f2;
            border-radius: 16px;
            padding: 32px 34px;
        }

        .vd-lede {
            font-size: 17px;
            color: #344054;
            line-height: 1.65;
            margin: 0 0 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid #eef2f6;
        }

        .vd-section+.vd-section {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #eef2f6;
        }

        .vd-h {
            font-size: 18px;
            font-weight: 700;
            color: #0d1b2e;
            margin: 0 0 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -.2px;
        }

        .vd-h i {
            color: #2f6fed;
            font-size: 16px;
        }

        .vd-rich {
            color: #344054;
            line-height: 1.75;
            font-size: 15.5px;
        }

        .vd-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .vd-list li {
            position: relative;
            padding: 6px 0 6px 28px;
            color: #344054;
            line-height: 1.6;
            font-size: 15px;
        }

        .vd-list li::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 15px;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #2f6fed;
        }

        .vd-list--check li::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Pro', 'Font Awesome 6 Free';
            font-weight: 900;
            background: transparent;
            color: #10b981;
            font-size: 12px;
            left: 4px;
            top: 9px;
            width: auto;
            height: auto;
        }

        .vd-apply {
            margin-top: 24px;
            background: linear-gradient(120deg, #0F172A, #312E81);
            color: #fff;
            border-radius: 16px;
            padding: 32px 34px;
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            align-items: center;
            justify-content: space-between;
        }

        .vd-apply h3 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 6px;
            color: #fff;
            letter-spacing: -.3px;
        }

        .vd-apply p {
            color: rgba(255, 255, 255, .72);
            margin: 0;
            max-width: 480px;
            font-size: 14.5px;
        }

        .vd-apply__ctas {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        .vd-apply-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: #0F172A;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 14.5px;
            text-decoration: none;
            transition: transform .15s ease;
        }

        .vd-apply-btn:hover {
            transform: translateY(-2px);
            color: #0F172A;
        }

        .vd-apply-btn i {
            font-size: 13px;
            color: #2f6fed;
        }

        .vd-apply__deadline {
            color: rgba(255, 255, 255, .72);
            font-size: 13px;
        }

        .vd-sidebar {
            position: sticky;
            top: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .vd-sidebar__card {
            background: #fff;
            border: 1px solid #e7e9f2;
            border-radius: 16px;
            padding: 26px 26px 24px;
        }

        .vd-sidebar__card--accent {
            background: linear-gradient(160deg, #fffbeb, #fef3c7);
            border-color: #fde68a;
        }

        .vd-sidebar__title {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #0d1b2e;
            margin: 0 0 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .vd-sidebar__title i {
            color: #f59e0b;
        }

        .vd-facts {
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .vd-facts>div {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px dashed #eef2f6;
        }

        .vd-facts>div:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .vd-facts>div:first-child {
            padding-top: 0;
        }

        .vd-facts dt {
            color: #667085;
            font-size: 13.5px;
            font-weight: 500;
            margin: 0;
        }

        .vd-facts dd {
            color: #0d1b2e;
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            text-align: right;
        }

        .vd-sidebar__card--accent p {
            color: #78350f;
            font-size: 14px;
            line-height: 1.55;
            margin: 0 0 14px;
        }

        .vd-sidebar__cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #92400e;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
        }

        .vd-sidebar__cta:hover {
            color: #b45309;
        }

        .vd-sidebar__cta i {
            font-size: 12px;
        }

        .vd-related {
            background: #f6f7fb;
            padding-top: 0;
        }

        .vd-related__head {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 26px;
        }

        .vd-related__head h3 {
            font-size: 24px;
            font-weight: 700;
            color: #0d1b2e;
            margin: 0;
            letter-spacing: -.3px;
        }

        .vd-related__link {
            color: #2f6fed;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .vd-related__link i {
            font-size: 11px;
        }

        .vd-related__link:hover {
            color: #2559c7;
        }

        /* Reuse vcard styles from vacancy-component (they're global in the same layout) */

        @media (max-width: 991.98px) {
            .vd-sidebar {
                position: static;
            }

            .vd-panel {
                padding: 24px 22px;
            }

            .vd-apply {
                padding: 24px 22px;
            }
        }

        .vd-apply__fallback {
            color: rgba(255, 255, 255, .6);
            font-size: 13px;
            text-decoration: none;
        }

        .vd-apply__fallback:hover {
            color: #fff;
            text-decoration: underline;
        }
    </style>
@endpush