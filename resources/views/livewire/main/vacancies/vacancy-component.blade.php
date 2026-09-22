<div>
    {{-- ─── Breadcrumb / hero ─── --}}
    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb"
            style="background-image: url('{{ asset('assets/main/imgs/resources/service.jpg') }}');"></div>
        <div class="breadcrumb__thumb_2"
            style="background-image: url('{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}');"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Careers at Polysphere</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>Careers</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Stats strip ─── --}}
    <section class="careers-stats-strip">
        <div class="small-container">
            <div class="careers-stats-grid">
                <div class="careers-stat">
                    <span class="careers-stat__num">{{ number_format($totalOpen) }}</span>
                    <span class="careers-stat__label">Open positions</span>
                </div>
                <div class="careers-stat">
                    <span class="careers-stat__num">{{ number_format($totalDepartments) }}</span>
                    <span class="careers-stat__label">Departments hiring</span>
                </div>
                <div class="careers-stat">
                    <span class="careers-stat__num">{{ number_format($totalRemote) }}</span>
                    <span class="careers-stat__label">Remote friendly</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── Featured vacancies ─── --}}
    @if($featuredVacancies->count() > 0)
        <section class="careers-featured section-space pb-0">
            <div class="small-container">
                <div class="careers-section-head">
                    <span class="careers-eyebrow"><i class="fa-solid fa-star"></i> Featured roles</span>
                    <h3 class="careers-section-title">Pinned opportunities</h3>
                    <p class="careers-section-sub">Hand-picked roles we're prioritising — clear impact, great teams.</p>
                </div>

                <div class="row g-4">
                    @foreach($featuredVacancies as $vacancy)
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                            @livewire('main.partials.vacancy-card', [
                                'vacancy' => $vacancy,
                                'featured' => true,
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ─── Filters + all vacancies ─── --}}
    <section class="careers-listing section-space">
        <div class="small-container">
            <div class="careers-section-head text-start">
                <span class="careers-eyebrow"><i class="fa-solid fa-briefcase"></i> All openings</span>
                <h3 class="careers-section-title">Find your next role</h3>
            </div>

            {{-- Filter bar --}}
            <div class="careers-filters">
                <div class="careers-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search title, location, or keyword…"
                        wire:model.live.debounce.300ms="search">
                    @if($search !== '')
                        <button type="button" class="careers-search-clear" wire:click="$set('search', '')"
                            aria-label="Clear search">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    @endif
                </div>

                <select class="careers-select" wire:model.live="departmentFilter">
                    <option value="">All departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>

                <select class="careers-select" wire:model.live="employmentTypeFilter">
                    <option value="">Any type</option>
                    @foreach($employmentTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>

                <select class="careers-select" wire:model.live="workplaceTypeFilter">
                    <option value="">Anywhere</option>
                    @foreach($workplaceTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>

                @if($search || $departmentFilter || $employmentTypeFilter || $workplaceTypeFilter)
                    <button type="button" class="careers-clear" wire:click="resetFilters">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </button>
                @endif
            </div>

            {{-- Loading bar --}}
            <div wire:loading
                wire:target="search, departmentFilter, employmentTypeFilter, workplaceTypeFilter, resetFilters"
                class="careers-loading-bar"></div>

            {{-- Results --}}
            <div wire:loading.remove
                wire:target="search, departmentFilter, employmentTypeFilter, workplaceTypeFilter, resetFilters">
                @if($vacancies->count() > 0)
                    <div class="row g-4">
                        @foreach($vacancies as $vacancy)
                            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                                @livewire('main.partials.vacancy-card', [
                                    'vacancy' => $vacancy,
                                    'featured' => false,
                                ])
                            </div>
                        @endforeach
                    </div>

                    <div class="row mt-50">
                        <div class="col-12">
                            <div class="bd-basic__pagination d-flex align-items-center justify-content-center">
                                {{ $vacancies->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="careers-empty">
                        <i class="fa-solid fa-compass"></i>
                        <h4>No openings match your filters</h4>
                        <p>Try clearing the filters, or check back soon — we're always hiring great people.</p>
                        <button class="careers-empty-btn" wire:click="resetFilters">
                            <i class="fa-solid fa-rotate-left"></i> Reset filters
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- ─── Why work with us (mirrors the services "Choose area") ─── --}}
    <section class="choose-section bg-color-1 section-space-top p-relative">
        <div class="bg-image" style="background-image: url('{{ asset('assets/main/imgs/bg/choose-bg.png') }}');"></div>
        <div class="shape-image" style="background-image: url('{{ asset('assets/main/imgs/shapes/shape-15.png') }}');">
        </div>
        <div class="small-container">
            <div class="row g-4">
                <div class="col-xxl-6 col-xl-6 col-lg-6 p-relative section-space-medium-bottom">
                    <div class="title-box mb-50 wow fadeInLeft" data-wow-delay=".5s">
                        <span class="section-sub-title">Why Polysphere</span>
                        <h3 class="section-title mt-10">A place to build things that matter</h3>
                    </div>

                    <div class="choose-area-icon-box mb-15 wow fadeInRight" data-wow-delay=".5s">
                        <div class="icon-box p-relative"><i class="icon-roof-4"></i></div>
                        <div class="content">
                            <h5><a href="javascript:void(0)">Real ownership, real impact</a></h5>
                            <p>Ship features that real customers use, and see the difference you make in weeks — not
                                quarters.</p>
                        </div>
                    </div>
                    <hr>

                    <div class="choose-area-icon-box mb-15 wow fadeInRight" data-wow-delay=".7s">
                        <div class="icon-box p-relative"><i class="icon-target"></i></div>
                        <div class="content">
                            <h5><a href="javascript:void(0)">Remote-first, flexible hours</a></h5>
                            <p>Work from wherever you're most productive. We care about outcomes, not clocked hours.</p>
                        </div>
                    </div>
                    <hr>

                    <div class="choose-area-icon-box mb-15 wow fadeInRight" data-wow-delay=".9s">
                        <div class="icon-box p-relative"><i class="icon-help"></i></div>
                        <div class="content">
                            <h5><a href="javascript:void(0)">Grow with us</a></h5>
                            <p>Learning budget, mentorship, and a culture of shipping — we invest in the people who
                                invest in us.</p>
                        </div>
                    </div>
                    <hr>
                </div>

                <div class="col-xxl-6 col-xl-6 col-lg-6 wow fadeInLeft" data-wow-delay="1.2s">
                    <figure class="image m-img">
                        <img src="{{ asset('assets/main/imgs/resources/choose-1.jpg') }}"
                            alt="Why work with Polysphere">
                    </figure>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── CTA ─── --}}
    <section class="careers-cta section-space">
        <div class="small-container">
            <div class="careers-cta-card">
                <div>
                    <span class="careers-eyebrow light"><i class="fa-solid fa-paper-plane"></i> Don't see a fit?</span>
                    <h3>Send us your CV anyway.</h3>
                    <p>We keep great people in mind for future roles — tell us what you're great at.</p>
                </div>
                <a href="mailto:careers@polyspheretech.com?subject=General%20Application" class="careers-cta-btn">
                    Email careers@polyspheretech.com <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>
</div>

@push('styles')
    <style>
        /* ─── Design tokens ─── */
        .careers-stats-strip,
        .careers-featured,
        .careers-listing,
        .careers-cta {
            --c-ink: #0d1b2e;
            --c-accent: #2f6fed;
            --c-surface: #fff;
            --c-bg: #f6f7fb;
            --c-border: #e7e9f2;
            --c-text: #101828;
            --c-muted: #667085;
        }

        /* ─── Stats strip ─── */
        .careers-stats-strip {
            background: var(--c-bg);
            padding: 0 0 0;
        }

        .careers-stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 16px;
            padding: 26px 30px;
            box-shadow: 0 20px 48px rgba(13, 27, 46, .08);
            margin-top: -60px;
            position: relative;
            z-index: 3;
        }

        .careers-stat {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .careers-stat+.careers-stat {
            border-left: 1px solid var(--c-border);
        }

        .careers-stat__num {
            font-size: 32px;
            font-weight: 800;
            color: var(--c-ink);
            line-height: 1;
            letter-spacing: -1px;
        }

        .careers-stat__label {
            font-size: 13.5px;
            color: var(--c-muted);
            margin-top: 6px;
        }

        /* ─── Section heads ─── */
        .careers-section-head {
            text-align: center;
            margin-bottom: 36px;
        }

        .careers-section-head.text-start {
            text-align: left;
            margin-bottom: 22px;
        }

        .careers-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--c-accent);
            background: rgba(47, 111, 237, .08);
            padding: 6px 12px;
            border-radius: 999px;
        }

        .careers-eyebrow.light {
            color: #fff;
            background: rgba(255, 255, 255, .15);
        }

        .careers-section-title {
            font-size: 30px;
            font-weight: 700;
            color: var(--c-ink);
            margin: 12px 0 8px;
            letter-spacing: -.5px;
        }

        .careers-section-sub {
            color: var(--c-muted);
            font-size: 15.5px;
            margin: 0;
        }

        /* ─── Vacancy card ─── */
        .vcard {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 16px;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .vcard:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 48px rgba(13, 27, 46, .10);
            border-color: #d5d9e8;
        }

        .vcard--featured {
            border-color: #fcd97a;
            box-shadow: 0 20px 40px rgba(217, 119, 6, .14);
        }

        .vcard__top {
            padding: 22px 22px 0;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .vcard__dept {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: linear-gradient(135deg, #eef2ff, #dbe4ff);
            color: #2f6fed;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: -.5px;
            flex-shrink: 0;
        }

        .vcard__head {
            flex: 1;
            min-width: 0;
        }

        .vcard__title {
            font-size: 17px;
            font-weight: 700;
            color: var(--c-ink);
            margin: 0 0 4px;
            line-height: 1.35;
            letter-spacing: -.2px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .vcard__title a {
            color: inherit;
            text-decoration: none;
        }

        .vcard__title a:hover {
            color: var(--c-accent);
        }

        .vcard__dept-name {
            font-size: 13px;
            color: var(--c-muted);
        }

        .vcard__featured-flag {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            padding: 4px 8px;
            border-radius: 999px;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .vcard__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 16px 22px 0;
        }

        .vchip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            color: #475467;
            background: #f1f5f9;
            padding: 5px 10px;
            border-radius: 999px;
            font-weight: 500;
        }

        .vchip i {
            font-size: 11px;
            color: #94a3b8;
        }

        .vchip--remote {
            background: rgba(16, 185, 129, .1);
            color: #047857;
        }

        .vchip--remote i {
            color: #10b981;
        }

        .vchip--onsite {
            background: rgba(99, 102, 241, .1);
            color: #4338ca;
        }

        .vchip--onsite i {
            color: #6366f1;
        }

        .vchip--hybrid {
            background: rgba(245, 158, 11, .12);
            color: #b45309;
        }

        .vchip--hybrid i {
            color: #f59e0b;
        }

        .vcard__summary {
            padding: 14px 22px 0;
            color: #475467;
            font-size: 14px;
            line-height: 1.55;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .vcard__foot {
            margin-top: auto;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-top: 1px solid #f1f5f9;
        }

        .vcard__foot .vcard__date {
            font-size: 12.5px;
            color: #94a3b8;
        }

        .vcard__apply {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--c-accent);
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 10px;
            background: rgba(47, 111, 237, .08);
            transition: background .15s ease, transform .15s ease;
        }

        .vcard__apply:hover {
            background: rgba(47, 111, 237, .15);
            transform: translateX(2px);
        }

        .vcard__apply i {
            font-size: 11px;
        }

        /* ─── Filters ─── */
        .careers-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 14px;
            padding: 12px;
            margin-bottom: 26px;
        }

        .careers-search {
            position: relative;
            flex: 1 1 260px;
            min-width: 220px;
            display: flex;
            align-items: center;
        }

        .careers-search i {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
        }

        .careers-search input {
            width: 100%;
            border: 1px solid var(--c-border);
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 38px 10px 38px;
            font-size: 14px;
            outline: none;
            transition: border-color .15s ease, background .15s ease;
        }

        .careers-search input:focus {
            border-color: var(--c-accent);
            background: #fff;
        }

        .careers-search-clear {
            position: absolute;
            right: 10px;
            border: none;
            background: transparent;
            color: #94a3b8;
            padding: 4px 6px;
            border-radius: 6px;
        }

        .careers-search-clear:hover {
            color: #ef4444;
            background: #fef2f2;
        }

        .careers-select {
            border: 1px solid var(--c-border);
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px 34px 10px 14px;
            font-size: 14px;
            color: #475467;
            outline: none;
            min-width: 150px;
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'><path d='M2 4l4 4 4-4' fill='none' stroke='%2394a3b8' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/></svg>");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 12px;
        }

        .careers-select:focus {
            border-color: var(--c-accent);
            background-color: #fff;
        }

        .careers-clear {
            border: 1px dashed #e2e8f0;
            background: transparent;
            color: #64748b;
            border-radius: 10px;
            padding: 9px 14px;
            font-size: 13.5px;
            font-weight: 500;
        }

        .careers-clear:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
        }

        .careers-loading-bar {
            height: 3px;
            border-radius: 3px;
            background: linear-gradient(90deg, transparent, var(--c-accent), transparent);
            background-size: 200% 100%;
            animation: careers-sweep 1.1s ease-in-out infinite;
            margin-bottom: 20px;
        }

        @keyframes careers-sweep {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* ─── Empty ─── */
        .careers-empty {
            text-align: center;
            padding: 70px 20px;
            background: #fff;
            border: 1px dashed var(--c-border);
            border-radius: 16px;
        }

        .careers-empty i {
            font-size: 36px;
            color: #cbd5e1;
            margin-bottom: 14px;
            display: block;
        }

        .careers-empty h4 {
            color: var(--c-ink);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .careers-empty p {
            color: var(--c-muted);
            margin-bottom: 18px;
        }

        .careers-empty-btn {
            border: none;
            background: var(--c-accent);
            color: #fff;
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 600;
            font-size: 14px;
        }

        .careers-empty-btn:hover {
            background: #2559c7;
        }

        /* ─── CTA ─── */
        .careers-cta-card {
            background: linear-gradient(120deg, #0F172A, #312E81);
            color: #fff;
            border-radius: 20px;
            padding: 40px 44px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 22px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .25);
        }

        .careers-cta-card h3 {
            color: #fff;
            font-size: 26px;
            font-weight: 700;
            margin: 10px 0 6px;
            letter-spacing: -.3px;
        }

        .careers-cta-card p {
            color: rgba(255, 255, 255, .72);
            margin: 0;
            font-size: 15px;
            max-width: 480px;
        }

        .careers-cta-btn {
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

        .careers-cta-btn:hover {
            transform: translateY(-2px);
            color: #0F172A;
        }

        .careers-cta-btn i {
            font-size: 12px;
        }

        @media (max-width: 767.98px) {
            .careers-stats-grid {
                grid-template-columns: 1fr;
                gap: 18px;
                padding: 20px;
                margin-top: -40px;
            }

            .careers-stat+.careers-stat {
                border-left: none;
                border-top: 1px solid var(--c-border);
                padding-top: 16px;
            }

            .careers-section-title {
                font-size: 24px;
            }

            .careers-cta-card {
                padding: 28px 24px;
            }

            .careers-cta-card h3 {
                font-size: 21px;
            }
        }
    </style>
@endpush