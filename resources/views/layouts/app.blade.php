<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $title ?? 'Polysphere Tech - IT Solutions & Software Development' }}</title>

    <!-- Primary Meta Tags -->
    <meta name="description"
        content="Polysphere Tech delivers custom software development, SaaS platforms, and digital transformation solutions. We build future-ready technology for modern businesses.">
    <meta name="keywords"
        content="IT solutions, software development, SaaS platform, digital transformation, custom software, IT consulting, Polysphere Tech">

    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="Polysphere Tech - IT Solutions & Software Development">
    <meta property="og:description"
        content="Custom software development, SaaS platforms, and digital transformation solutions for modern businesses.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/main/imgs/favicon.svg') }}">

    <!-- CSS Stylesheets -->
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

    @livewireStyles
</head>

<body>



    <!-- Back to top start -->
    <div class="backtotop-wrap cursor-pointer">
        <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- Back to top end -->

    <!-- ============================================ -->
    <!-- SEARCH AREA - Polysphere Tech Branded         -->
    <!-- ============================================ -->
    <div class="df-search-area">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="df-search-form">
                        <div class="df-search-close text-center mb-20">
                            <button class="df-search-close-btn df-search-close-btn"></button>
                        </div>
                        <form action="#">
                            <div class="df-search-input mb-10">
                                <input type="text" placeholder="Search services, case studies, blog posts...">
                                <button type="submit"><i class="icon-search"></i></button>
                            </div>
                            <div class="df-search-category">
                                <span>Popular searches : </span>
                                <a href="service-details.html">Custom Software, </a>
                                <a href="service-details.html">SaaS Platform, </a>
                                <a href="service-details.html">Digital Transformation, </a>
                                <a href="service-details.html">IT Consulting</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="body-overlay"></div>
    <!-- ============================================ -->

    <!-- ============================================ -->
    <!-- OFFCANVAS MENU - Polysphere Tech Branded      -->
    <!-- ============================================ -->
    <div class="fix">
        <div class="offcanvas__info">
            <div class="offcanvas__wrapper">
                <div class="offcanvas__content">
                    <div class="offcanvas__top mb-40 d-flex justify-content-between align-items-center">
                        <div class="offcanvas__logo">
                            <a href="index.html">
                                <img src="{{ asset('assets/main/imgs/logo/logo-white.svg') }}"
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
                                        style="color: rgba(255,255,255,0.7); text-decoration: none;">123 Tech Hub,
                                        Innovation District, Silicon Valley, CA</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon mr-15">
                                    <i class="far fa-phone"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a href="tel:+1234567890"
                                        style="color: rgba(255,255,255,0.7); text-decoration: none;">+1 (234)
                                        567-8900</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div class="offcanvas__contact-icon mr-15">
                                    <i class="fal fa-envelope"></i>
                                </div>
                                <div class="offcanvas__contact-text">
                                    <a href="mailto:info@polyspheretech.com"
                                        style="color: rgba(255,255,255,0.7); text-decoration: none;">
                                        <span class="mailto:info@polyspheretech.com">info@polyspheretech.com</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="offcanvas__social">
                        <ul>
                            <li><a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a></li>
                            <li><a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas__overlay"></div>
    <div class="offcanvas__overlay-white"></div>
    <!-- ============================================ -->

    <!-- Navbar Component -->
    @livewire('main.partials.navbar')

    <!-- Main Content Area -->
    <main>
        @if(isset($slot))
            {{ $slot }}
        @endif
        @yield('content')
    </main>

    <!-- Footer Component -->
    @livewire('main.partials.footer')

    @livewireScripts

    <!-- ============================================ -->
    <!-- JAVASCRIPT                                  -->
    <!-- ============================================ -->
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