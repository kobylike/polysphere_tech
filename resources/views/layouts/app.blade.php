<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    @php
        $metaTitle = $title ?? 'Polysphere Tech - IT Solutions & Software Development';
        $metaDescription = $description ?? 'Polysphere Tech delivers custom software development, SaaS platforms, and digital transformation solutions. We build future-ready technology for modern businesses.';
        $metaKeywords = $keywords ?? 'IT solutions, software development, SaaS platform, digital transformation, custom software, IT consulting, Polysphere Tech';
        $canonicalUrl = $canonical ?? url()->current();
        $ogImageUrl = $ogImage ?? asset('assets/main/imgs/logo/logo-white.png');
        $ogType = $ogType ?? 'website';
        $isNoindex = $noindex ?? false;
        $siteName = 'Polysphere Tech';
        $twitterHandle = '@polyspheretech';
    @endphp

    <title>{{ $metaTitle }}</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ $metaTitle }}">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="author" content="{{ $siteName }}">

    <!-- Robots -->
    <meta name="robots"
        content="{{ $isNoindex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' }}">
    <meta name="googlebot" content="{{ $isNoindex ? 'noindex, nofollow' : 'index, follow' }}">

    <!-- Canonical -->
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <!-- Open Graph -->
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:alt" content="{{ $metaTitle }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">

    @if($ogType === 'article')
        {{--
        Only meaningful for article-type pages (blog posts, projects).
        $publishedTime / $modifiedTime are passed via layoutData() from
        PostDetails / ProjectDetails — see their render() methods below.
        Falls back silently (just omits the tag) if a page sets
        ogType => 'article' without providing these, so nothing breaks.
        --}}
        @isset($publishedTime)
            <meta property="article:published_time" content="{{ $publishedTime }}">
        @endisset
        @isset($modifiedTime)
            <meta property="article:modified_time" content="{{ $modifiedTime }}">
        @endisset
        <meta property="article:author" content="{{ $articleAuthor ?? $siteName }}">
    @endif

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ $twitterHandle }}">
    <meta name="twitter:creator" content="{{ $twitterHandle }}">
    <meta name="twitter:url" content="{{ $canonicalUrl }}">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">
    <meta name="twitter:image:alt" content="{{ $metaTitle }}">

    <!-- Theme color -->
    <meta name="theme-color" content="#3b82f6">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/main/imgs/favicon-96x96.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/main/imgs/favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('assets/main/imgs/favicon-96x96.png') }}">
    <link rel="apple-touch-icon" sizes="96x96" href="{{ asset('assets/main/imgs/favicon-96x96.png') }}">

    <!-- Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/main/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/main/css/meanmenu.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/main/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/main/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/main/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/main/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/main/css/fontawesome-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/main/css/icomoon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/main/css/spacing.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/main/css/main.css') }}">

    {{-- Structured Data: Organization --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "Polysphere Tech",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('assets/main/imgs/logo/logo-white.png') }}",
        "description": "Polysphere Tech delivers custom software development, SaaS platforms, and digital transformation solutions.",
        "sameAs": [
            "https://web.facebook.com/polyspheretech",
            "https://x.com/polyspheretech",
            "https://www.youtube.com/@polyspheretech",
            "https://www.linkedin.com/company/polysphere-tech/",
            "https://www.instagram.com/polyspheretech"
        ],
        "contactPoint": {
            "@@type": "ContactPoint",
            "telephone": "+233-59-756-3427",
            "contactType": "customer service",
            "email": "contact@polyspheretech.com",
            "areaServed": "GH"
        },
        "address": {
            "@@type": "PostalAddress",
            "addressLocality": "Accra",
            "addressCountry": "GH"
        }
    }
    </script>

    {{-- Structured Data: WebSite --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "url": "{{ url('/') }}",
        "name": "Polysphere Tech",
        "potentialAction": {
            "@@type": "SearchAction",
            "target": "{{ url('/search') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    @stack('schema')

    @production
        <script async data-navigate-once src="https://www.googletagmanager.com/gtag/js?id=G-355G42DGFP"></script>
        <script data-navigate-once>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', 'G-355G42DGFP');
        </script>
    @endproduction

    @livewireStyles
    @stack('styles')

    <style>
        .comment-item {
            border-bottom: 1px solid #eef2f6;
            padding: 20px 0;
            margin-left: 0;
        }

        .comment-item:first-child {
            padding-top: 0;
        }

        .comment-item:last-child {
            border-bottom: none;
        }

        .comment-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .comment-avatar-initials {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #6366f1;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            flex-shrink: 0;
            border: 2px solid #e2e8f0;
        }

        .comment-body {
            flex: 1;
            padding-left: 0;
        }

        .comment-author {
            font-weight: 600;
            color: #0a0a0a;
            margin-right: 10px;
        }

        .comment-date {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .comment-text {
            margin: 6px 0 10px;
            line-height: 1.7;
            color: #334155;
        }

        .comment-actions a {
            font-size: 0.85rem;
            color: #94a3b8;
            text-decoration: none;
            margin-right: 15px;
            transition: color 0.2s;
        }

        .comment-actions a:hover {
            color: #3b82f6;
        }

        .reply-form-inline {
            margin-top: 10px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }

        .comment-item.is-reply {
            margin-left: 90px;
        }

        .bd-basic__pagination .pagination {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            list-style: none;
            padding: 0;
        }

        .bd-basic__pagination .page-item .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            padding: 0;
            margin: 0;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-weight: 600;
            font-size: 0.95rem;
            background: #fff;
            line-height: 1;
            transition: all 0.2s ease;
        }

        .bd-basic__pagination .page-item .page-link:hover {
            background: #3b82f6;
            border-color: #3b82f6;
            color: #fff;
        }

        .bd-basic__pagination .page-item.active .page-link {
            background: #3b82f6;
            border-color: #3b82f6;
            color: #fff;
            box-shadow: none;
        }

        .bd-basic__pagination .page-item.disabled .page-link {
            opacity: 0.4;
            cursor: not-allowed;
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #94a3b8;
        }

        .bd-basic__pagination .page-item .page-link:focus {
            box-shadow: none;
        }

        .swiper:not(.swiper-initialized) {
            opacity: 0;
        }

        .swiper.swiper-initialized {
            opacity: 1;
            transition: opacity 0.25s ease;
        }

        /* Accessibility fix: original background (rgb(60,114,252), ~4.2:1
           against white text) was just under WCAG AA's 4.5:1 minimum for
           normal-size text. This darkens it slightly to ~4.75:1 while
           staying visually close to the original brand blue. */
        .primary-btn-1 {
            background-color: #386AEA !important;
        }
    </style>
</head>

<body>

    <!-- Back to top start -->
    <div class="backtotop-wrap cursor-pointer">
        <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- Back to top end -->

    <!-- SEARCH AREA -->
    @livewire('main.partials.main-search')
    <div class="body-overlay"></div>

    <!-- OFFCANVAS MENU -->
    <div class="fix">
        <div class="offcanvas__info">
            <div class="offcanvas__wrapper">
                <div class="offcanvas__content">
                    <div class="offcanvas__top mb-40 d-flex justify-content-between align-items-center">
                        <div class="offcanvas__logo">
                            <a href="{{ url('/') }}">
                                <img src="{{ asset('assets/main/imgs/logo/logo-white.png') }}"
                                    alt="Polysphere Tech Logo">
                            </a>
                        </div>
                        <div class="offcanvas__close">
                            <button>
                                <i class="fal fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="offcanvas__search mb-25">
                        <p class="text-white" style="font-size: 15px; line-height: 1.7; opacity: 0.85;">
                            Polysphere Tech delivers cutting-edge software development, SaaS engineering, and digital
                            transformation strategies that empower businesses to innovate and scale.
                        </p>
                    </div>
                    <div class="mobile-menu fix mb-40"></div>
                    <div class="offcanvas__contact mt-30 mb-20">
                        <h4 style="color: #fff; font-size: 18px; margin-bottom: 20px;">Get in Touch</h4>
                        <ul>
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon mr-15">
                                    <i class="fal fa-map-marker-alt"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a target="_blank" href="#"
                                        style="color: rgba(255,255,255,0.7); text-decoration: none;">Accra, Ghana</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon mr-15">
                                    <i class="far fa-phone"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a href="tel:+233597563427"
                                        style="color: rgba(255,255,255,0.7); text-decoration: none;">+233 (59)
                                        756-3427</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon mr-15">
                                    <i class="fal fa-envelope"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a href="mailto:contact@@polyspheretech.com"
                                        style="color: rgba(255,255,255,0.7); text-decoration: none;">
                                        <span>contact@@polyspheretech.com</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="offcanvas__social">
                        <ul>
                            <li><a href="https://web.facebook.com/polyspheretech" target="_blank"
                                    rel="noopener noreferrer" aria-label="Facebook"><i
                                        class="fab fa-facebook-f"></i></a></li>
                            <li><a href="https://x.com/polyspheretech" target="_blank" rel="noopener noreferrer"
                                    aria-label="Twitter"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="https://www.youtube.com/@polyspheretech" target="_blank"
                                    rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                            </li>
                            <li><a href="https://www.linkedin.com/company/polysphere-tech/" target="_blank"
                                    rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                            </li>
                            <li><a href="https://www.instagram.com/polyspheretech" target="_blank"
                                    rel="noopener noreferrer" aria-label="Instagram"><i
                                        class="fab fa-instagram"></i></a></li>

                            <li><a href=" https://www.tiktok.com/@polyspheretech" target="_blank"
                                    rel="noopener noreferrer" aria-label="tiktok"><i class="fab fa-tiktok"></i></a></li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas__overlay"></div>
    <div class="offcanvas__overlay-white"></div>

    <!-- Navbar Component -->
    @livewire('main.partials.navbar')

    <!-- Main Content Area -->
    <main>
        @if(isset($slot))
            {{ $slot }}
        @endif
        @yield('content')
    </main>

    <!-- AI Chat Widget — persisted across wire:navigate so the
         conversation survives page swaps -->
    @persist('chat-widget')
    @livewire('main.chat-widget')
    @endpersist

    <!-- Footer Component -->
    @livewire('main.partials.footer')

    @livewire('main.partials.cookie-consent')

    @livewireScripts
    @vite('resources/js/app.js')
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', scrollToHashIfPresent);
        document.addEventListener('livewire:navigated', scrollToHashIfPresent);

        function scrollToHashIfPresent() {
            const hash = window.location.hash?.slice(1);
            if (!hash) return;

            const el = document.getElementById(hash);
            if (!el) return;

            requestAnimationFrame(() => {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    </script>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/main/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/main/js/waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/main/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/main/js/meanmenu.min.js') }}"></script>
    <script src="{{ asset('assets/main/js/swiper.min.js') }}"></script>
    <script src="{{ asset('assets/main/js/slick.min.js') }}"></script>
    <script src="{{ asset('assets/main/js/magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/main/js/counterup.js') }}"></script>
    <script src="{{ asset('assets/main/js/wow.js') }}"></script>
    <script src="{{ asset('assets/main/js/main.js') }}"></script>
</body>

</html>