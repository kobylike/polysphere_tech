{{-- resources/views/index.blade.php --}}
<div>
    <!-- Banner area start -->
    <section class="banner-section p-relative fix">
        <div class="swiper banner-active">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div wire:ignore class="banner-main"
                        data-background="{{ asset('assets/main/imgs/banner/banner-1.jpg') }}">
                        <div class="large-container">
                            <div class="banner-area p-relative z-3 wow img-custom-anim-left animated"
                                data-wow-delay="1500ms">
                                <span class="p-relative banner-sub-title">SAAS & SOFTWARE SOLUTIONS</span>
                                <h1 class="banner-title">Build Smarter, Scale Faster</h1>
                                <p class="banner-text">We craft custom software, cloud‑native platforms, and digital
                                    strategies that turn your vision into a competitive advantage.</p>
                                <div class="banner-btn-area">
                                    <a class="primary-btn-1 btn-hover" href="services.html">
                                        Explore Our Work &nbsp; | <i class="icon-right-arrow"></i>
                                        <span style="top: 147.172px; left: 108.5px;"></span>
                                    </a>
                                    <a href="https://www.youtube.com/watch?v=eEzD-Y97ges" class="play-btn popup-video">
                                        <div class="icon-1">
                                            <i class="icon-play"></i>
                                        </div>
                                        <span>How We Deliver</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="banner-shape-area">
                        <div class="shape-1" data-background="{{ asset('assets/main/imgs/shapes/shape-1.png') }}"></div>
                        <div class="shape-2" data-background="{{ asset('assets/main/imgs/shapes/shape-2.png') }}"></div>
                        <div class="shape-3" data-background="{{ asset('assets/main/imgs/shapes/shape-3.png') }}"></div>
                        <div class="shape-4" data-background="{{ asset('assets/main/imgs/shapes/shape-4.png') }}"></div>
                        <div class="shape-5" data-background="{{ asset('assets/main/imgs/shapes/shape-5.png') }}"></div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div wire:ignore class="banner-main"
                        data-background="{{ asset('assets/main/imgs/banner/banner-2.jpg') }}">
                        <div class="large-container">
                            <div class="banner-area p-relative z-3 wow img-custom-anim-left animated"
                                data-wow-delay="2500ms">
                                <span class="p-relative banner-sub-title">DIGITAL TRANSFORMATION</span>
                                <h1 class="banner-title">Modernise Your Business with Confidence</h1>
                                <p class="banner-text">From legacy migration to AI‑driven automation – we guide you
                                    through every step of your digital evolution.</p>
                                <div class="banner-btn-area">
                                    <a class="primary-btn-1 btn-hover" href="services.html">
                                        Start Your Journey &nbsp; | <i class="icon-right-arrow"></i>
                                        <span style="top: 147.172px; left: 108.5px;"></span>
                                    </a>
                                    <a href="https://www.youtube.com/watch?v=eEzD-Y97ges" class="play-btn popup-video">
                                        <div class="icon-1">
                                            <i class="icon-play"></i>
                                        </div>
                                        <span>See Our Process</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="banner-shape-area">
                        <div class="shape-1" data-background="{{ asset('assets/main/imgs/shapes/shape-1.png') }}"></div>
                        <div class="shape-2" data-background="{{ asset('assets/main/imgs/shapes/shape-2.png') }}"></div>
                        <div class="shape-3" data-background="{{ asset('assets/main/imgs/shapes/shape-3.png') }}"></div>
                        <div class="shape-4" data-background="{{ asset('assets/main/imgs/shapes/shape-4.png') }}"></div>
                        <div class="shape-5" data-background="{{ asset('assets/main/imgs/shapes/shape-5.png') }}"></div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div wire:ignore class="banner-main"
                        data-background="{{ asset('assets/main/imgs/banner/banner-3.jpg') }}">
                        <div class="large-container">
                            <div class="banner-area p-relative z-3 wow img-custom-anim-left animated"
                                data-wow-delay="3000ms">
                                <span class="p-relative banner-sub-title">FUTURE‑READY INFRASTRUCTURE</span>
                                <h1 class="banner-title">Cloud & Cyber – When You’re Ready</h1>
                                <p class="banner-text">We’re building the foundation for tomorrow’s security and
                                    scalability – so you can grow without limits.</p>
                                <div class="banner-btn-area">
                                    <a class="primary-btn-1 btn-hover" href="contact.html">
                                        Talk to an Expert &nbsp; | <i class="icon-right-arrow"></i>
                                        <span style="top: 147.172px; left: 108.5px;"></span>
                                    </a>
                                    <a href="https://www.youtube.com/watch?v=eEzD-Y97ges" class="play-btn popup-video">
                                        <div class="icon-1">
                                            <i class="icon-play"></i>
                                        </div>
                                        <span>Explore Our Vision</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="banner-shape-area">
                        <div class="shape-1" data-background="{{ asset('assets/main/imgs/shapes/shape-1.png') }}"></div>
                        <div class="shape-2" data-background="{{ asset('assets/main/imgs/shapes/shape-2.png') }}"></div>
                        <div class="shape-3" data-background="{{ asset('assets/main/imgs/shapes/shape-3.png') }}"></div>
                        <div class="shape-4" data-background="{{ asset('assets/main/imgs/shapes/shape-4.png') }}"></div>
                        <div class="shape-5" data-background="{{ asset('assets/main/imgs/shapes/shape-5.png') }}"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="banner-dot-inner">
            <div class="banner-dot"></div>
        </div>
    </section>
    <!-- Banner area end -->

    <!-- About us area start -->
    <section class="about-us-section section-space p-relative">
        <div class="shape-area">
            <div class="shape-1" data-background="{{ asset('assets/main/imgs/bg/bg-shape-1.png') }}"></div>
            <div class="shape-2 quote-animation" data-background="{{ asset('assets/main/imgs/shapes/shape-7.png') }}">
            </div>
            <div class="shape-3 quote-animation" data-background="{{ asset('assets/main/imgs/shapes/shape-8.png') }}">
            </div>
        </div>
        <div class="small-container">
            <div class="row g-4">
                <div class="col-xxl-6 col-xl-6 col-lg-6">
                    <div class="about-us-image-area p-relative wow fadeInRight" data-wow-delay=".5s">
                        <div class="border-shape" data-background="{{ asset('assets/main/imgs/shapes/shape-6.png') }}">
                        </div>
                        <figure class="image-1">
                            <img src="{{ asset('assets/main/imgs/about/about-1.jpg') }}" alt="Polysphere Tech team">
                        </figure>
                        <div class="image-2-area">
                            <div class="image-2 p-relative">
                                <img src="{{ asset('assets/main/imgs/about/about-2.jpg') }}"
                                    alt="Polysphere Tech workspace">
                                <div class="play-btn">
                                    <div class="video_player_btn">
                                        <a href="https://www.youtube.com/watch?v=eEzD-Y97ges" class="popup-video"><i
                                                class="icon-play"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="working-area float-bob-y">
                            <div class="inner p-relative">
                                <div class="icon-box">
                                    <i class="icon-prize"></i>
                                    <h4><span class="counter">8</span>+ Years</h4>
                                    <p>Delivering Excellence</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6">
                    <div class="about-us-content-area p-relative z-1 pl-30">
                        <div class="title-box mb-35 wow fadeInLeft" data-wow-delay=".5s">
                            <span class="section-sub-title">About Polysphere Tech</span>
                            <h3 class="section-title mt-10">Your Partner in Digital Innovation</h3>
                        </div>
                        <p class="mb-35 wow fadeInLeft" data-wow-delay=".5s">
                            We are a team of passionate engineers, designers, and strategists who build high‑performance
                            software, modernise legacy systems, and guide businesses through digital transformation. Our
                            approach combines deep technical expertise with a relentless focus on your business goals.
                        </p>
                        <div class="icon-box mb-20 wow fadeInLeft" data-wow-delay=".8s">
                            <div class="icon">
                                <img src="{{ asset('assets/main/imgs/about/about-three-icon1.png') }}"
                                    alt="Growth icon">
                            </div>
                            <div class="content">
                                <h5><a href="about.html">Business‑Driven Development</a></h5>
                                <p>We align every line of code with your strategic objectives, ensuring measurable ROI
                                    and faster time‑to‑market.</p>
                            </div>
                        </div>
                        <div class="icon-box mb-20 wow fadeInLeft" data-wow-delay=".9s">
                            <div class="icon">
                                <img src="{{ asset('assets/main/imgs/about/about-three-icon2.png') }}"
                                    alt="Consultancy icon">
                            </div>
                            <div class="content">
                                <h5><a href="about.html">End‑to‑End Consultancy</a></h5>
                                <p>From discovery and architecture to deployment and maintenance – we’re with you at
                                    every stage of your journey.</p>
                            </div>
                        </div>
                        <div class="about-btn-box wow fadeInLeft" data-wow-delay="1s">
                            <a class="primary-btn-1 btn-hover" href="about.html">
                                Learn More &nbsp; | <i class="icon-right-arrow"></i>
                                <span style="top: 147.172px; left: 108.5px;"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About us area end -->

    <!-- Service Slider area start -->
    <section class="service-slider-section section-space bg-color-1 p-relative">
        <div class="shape-1 float-bob-y" data-background="{{ asset('assets/main/imgs/shapes/shape-10.png') }}"></div>
        <div class="shape-2 float-bob-y" data-background="{{ asset('assets/main/imgs/shapes/shape-9.png') }}"></div>
        <div class="shape-3" data-background="{{ asset('assets/main/imgs/shapes/shape-11.png') }}"></div>
        <div class="small-container">
            <div class="row">
                <div class="col-xxl-6">
                    <div class="title-box mb-50 wow fadeInLeft" data-wow-delay=".5s">
                        <span class="section-sub-title">What We Deliver</span>
                        <h3 class="section-title mt-10">Core Services</h3>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="service_1_navigation__wrapprer position-relative z-1 text-end mt-30">
                        <div class="common-slider-navigation">
                            <button class="service-1-button-prev"><i class="icon-arrow-left-angle"></i></button>
                            <button class="service-1-button-next"><i class="icon-arrow-right-angle"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper service-active-1">
                <div class="swiper-wrapper">
                    <!-- Service 1: Custom Software Development -->
                    <div class="swiper-slide">
                        <div class="service-slider-area p-relative">
                            <figure class="image w-img">
                                <img src="{{ asset('assets/main/imgs/service/service-1.jpg') }}" alt="Custom Software">
                            </figure>
                            <div class="content">
                                <div class="icon-box">
                                    <img src="{{ asset('assets/main/imgs/icon/icon.png') }}" alt="Software icon">
                                </div>
                                <h4 class="mb-15"><a href="service-details.html">Custom Software Development</a></h4>
                                <p class="mb-25">We design and build tailor‑made web, mobile, and desktop applications
                                    that solve your unique business challenges and scale with your growth.</p>
                                <a href="service-details.html" class="service-btn">Read More <i
                                        class="icon-arrow-right-double"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Service 2: Digital Transformation -->
                    <div class="swiper-slide">
                        <div class="service-slider-area p-relative">
                            <figure class="image w-img">
                                <img src="{{ asset('assets/main/imgs/service/service-2.jpg') }}"
                                    alt="Digital Transformation">
                            </figure>
                            <div class="content">
                                <div class="icon-box">
                                    <img src="{{ asset('assets/main/imgs/icon/icon-2.png') }}"
                                        alt="Transformation icon">
                                </div>
                                <h4 class="mb-15"><a href="service-details.html">Digital Transformation</a></h4>
                                <p class="mb-25">We help you re‑imagine operations with modern architectures,
                                    automation, and data‑driven insights – turning complexity into competitive
                                    advantage.</p>
                                <a href="service-details.html" class="service-btn">Read More <i
                                        class="icon-arrow-right-double"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- Service 3: SaaS Platform Engineering -->
                    <div class="swiper-slide">
                        <div class="service-slider-area p-relative">
                            <figure class="image w-img">
                                <img src="{{ asset('assets/main/imgs/service/service-3.jpg') }}" alt="SaaS Platform">
                            </figure>
                            <div class="content">
                                <div class="icon-box">
                                    <img src="{{ asset('assets/main/imgs/icon/icon-3.png') }}" alt="SaaS icon">
                                </div>
                                <h4 class="mb-15"><a href="service-details.html">SaaS Platform Engineering</a></h4>
                                <p class="mb-25">From MVP to enterprise‑grade multi‑tenant systems – we build resilient,
                                    subscription‑ready platforms with secure APIs and seamless integrations.</p>
                                <a href="service-details.html" class="service-btn">Read More <i
                                        class="icon-arrow-right-double"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Service Slider area end -->

    <!-- Cta-1 area start -->
    <section class="cta-1-section bg-color-1 p-relative wow fadeInDown" data-wow-delay=".5s">
        <div class="small-container">
            <div class="row g-0 box-shadow-1 fix">
                <div class="col-xxl-6 col-lg-6 bg-white">
                    <figure class="image w-img">
                        <img src="{{ asset('assets/main/imgs/resources/cta-1.jpg') }}" alt="Get in touch">
                    </figure>
                </div>
                <div class="col-xxl-6 col-lg-6">
                    <div class="content p-relative">
                        <div class="shape-1" data-background="{{ asset('assets/main/imgs/shapes/shape-12.png') }}">
                        </div>
                        <div class="icon-box">
                            <i class="fal fa-phone-volume"></i>
                        </div>
                        <h3 class="mb-15">Need a Custom Solution?</h3>
                        <h5><a href="tel:+233597563427">+233 (59) 756‑3427</a></h5>
                        <p class="mt-3" style="font-size:14px; color:#666;">Call us or <a href="contact.html"
                                style="color:#0056b3;">schedule a free consultation</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Cta-1 area end -->

    <!-- Project slider area start -->
    <section class="project-slider-section section-space fix">
        <div class="small-container">
            <div class="row">
                <div class="col-xxl-6">
                    <div class="title-box mb-50 wow fadeInLeft" data-wow-delay=".5s">
                        <span class="section-sub-title">Case Studies</span>
                        <h3 class="section-title mt-10">Recent Success Stories</h3>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="project_1_navigation__wrapprer position-relative z-1 text-end mt-30">
                        <div class="common-slider-navigation">
                            <button class="project-1-button-prev"><i class="icon-arrow-left-angle"></i></button>
                            <button class="project-1-button-next"><i class="icon-arrow-right-angle"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper project-active-1">
            <div class="swiper-wrapper">
                <!-- Project 1 -->
                <div class="swiper-slide">
                    <div class="project-slider-area p-relative">
                        <figure class="image m-img">
                            <img src="{{ asset('assets/main/imgs/project/project-1.jpg') }}" alt="FinTech Platform">
                        </figure>
                        <div class="content-area">
                            <div class="title-area">
                                <h6 class="mb-5">FinTech</h6>
                                <h5><a href="project-details.html">Digital Banking Platform</a></h5>
                            </div>
                            <div class="icon-area">
                                <a href="project-details.html"><i class="icon-arrow-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Project 2 -->
                <div class="swiper-slide">
                    <div class="project-slider-area p-relative">
                        <figure class="image m-img">
                            <img src="{{ asset('assets/main/imgs/project/project-2.jpg') }}" alt="Healthcare SaaS">
                        </figure>
                        <div class="content-area">
                            <div class="title-area">
                                <h6 class="mb-5">Healthcare</h6>
                                <h5><a href="project-details.html">Telemedicine SaaS</a></h5>
                            </div>
                            <div class="icon-area">
                                <a href="project-details.html"><i class="icon-arrow-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Project 3 -->
                <div class="swiper-slide">
                    <div class="project-slider-area p-relative">
                        <figure class="image m-img">
                            <img src="{{ asset('assets/main/imgs/project/project-3.jpg') }}" alt="Logistics Solution">
                        </figure>
                        <div class="content-area">
                            <div class="title-area">
                                <h6 class="mb-5">Logistics</h6>
                                <h5><a href="project-details.html">Supply Chain Optimisation</a></h5>
                            </div>
                            <div class="icon-area">
                                <a href="project-details.html"><i class="icon-arrow-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Project 4 -->
                <div class="swiper-slide">
                    <div class="project-slider-area p-relative">
                        <figure class="image m-img">
                            <img src="{{ asset('assets/main/imgs/project/project-4.jpg') }}" alt="E-commerce">
                        </figure>
                        <div class="content-area">
                            <div class="title-area">
                                <h6 class="mb-5">Retail</h6>
                                <h5><a href="project-details.html">Headless E‑commerce Engine</a></h5>
                            </div>
                            <div class="icon-area">
                                <a href="project-details.html"><i class="icon-arrow-up"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Project area end -->

    <!-- Choose area start -->
    <section class="choose-section bg-color-1 section-space-top p-relative">
        <div class="bg-image" data-background="{{ asset('assets/main/imgs/bg/choose-bg.png') }}"></div>
        <div class="shape-image" data-background="{{ asset('assets/main/imgs/shapes/shape-15.png') }}"></div>
        <div class="small-container">
            <div class="row g-4">
                <div class="col-xxl-6 col-xl-6 col-lg-6 p-relative section-space-medium-bottom">
                    <div class="title-box mb-50 wow fadeInLeft" data-wow-delay=".5s">
                        <span class="section-sub-title">Why Polysphere Tech</span>
                        <h3 class="section-title mt-10">What Sets Us Apart</h3>
                    </div>
                    <!-- block -->
                    <div class="choose-area-icon-box mb-15 wow fadeInRight" data-wow-delay=".5s">
                        <div class="icon-box p-relative">
                            <i class="icon-roof-4"></i>
                        </div>
                        <div class="content">
                            <h5><a href="services.html">Agile & Transparent</a></h5>
                            <p>We work in short, iterative cycles with regular demos and clear communication – so you’re
                                never in the dark.</p>
                        </div>
                    </div>
                    <hr>
                    <!-- block -->
                    <div class="choose-area-icon-box mb-15 wow fadeInRight" data-wow-delay=".7s">
                        <div class="icon-box p-relative">
                            <i class="icon-target"></i>
                        </div>
                        <div class="content">
                            <h5><a href="services.html">End‑to‑End Ownership</a></h5>
                            <p>We take full responsibility – from ideation and design to deployment, support, and
                                continuous improvement.</p>
                        </div>
                    </div>
                    <hr>
                    <!-- block -->
                    <div class="choose-area-icon-box mb-15 wow fadeInRight" data-wow-delay=".9s">
                        <div class="icon-box p-relative">
                            <i class="icon-help"></i>
                        </div>
                        <div class="content">
                            <h5><a href="services.html">Future‑Proof Engineering</a></h5>
                            <p>We build with scalability, security, and maintainability in mind – so your technology
                                evolves with your business.</p>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6 wow fadeInLeft" data-wow-delay="1.2s">
                    <figure class="image m-img">
                        <img src="{{ asset('assets/main/imgs/resources/choose-1.png') }}" alt="Why choose us">
                    </figure>
                </div>
            </div>
        </div>
    </section>
    <!-- Choose area end -->

    <!-- Team area start -->
    <section class="team-section p-relative section-space">
        <div class="shape-1" data-background="{{ asset('assets/main/imgs/shapes/shape-13.png') }}"></div>
        <div class="shape-2 float-bob-y" data-background="{{ asset('assets/main/imgs/shapes/shape-14.png') }}"></div>
        <div class="small-container">
            <div class="title-box text-center mb-50 wow fadeInLeft" data-wow-delay=".5s">
                <span class="section-sub-title">Our Leadership</span>
                <h3 class="section-title mt-10">Meet the Team</h3>
            </div>
            <div class="row g-4">

                <!-- Team 1 -->
                {{-- <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                    <div class="team-area-box p-relative mb-60 wow fadeInLeft" data-wow-delay=".7s">
                        <figure class="image w-img p-relative">
                            <img src="{{ asset('assets/main/imgs/team/team-1.jpg') }}" alt="CEO">
                        </figure>
                        <div class="content">
                            <div class="author-info">
                                <h5 class="mb-5"><a href="team-details.html">Alexander Reed</a></h5>
                                <span>CEO & Co‑Founder</span>
                            </div>
                            <div class="social-links p-relative">
                                <span><i class="icon-share"></i></span>
                                <ul>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="row g-4">
                    @forelse($teamMembers as $member)
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 mb-15">
                            <div class="team-area-box p-relative mb-60 wow fadeInLeft" data-wow-delay=".7s">

                                <figure class="image w-img p-relative">
                                    {{-- Image container with fixed aspect ratio 370x451 --}}
                                    <div class="team-image-wrapper">
                                        <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}"
                                            class="team-member-img">
                                    </div>
                                </figure>
                                <div class="content">
                                    <div class="author-info">
                                        <h5 class="mb-5">
                                            <a wire:navigate
                                                href="{{ route('team.details', ['slug' => $member->username]) }}">
                                                {{ $member->name }}
                                            </a>
                                        </h5>
                                        <span>{{ $member->position ?? 'Team Member' }}</span>
                                    </div>
                                    <div class="social-links p-relative">
                                        <span><i class="icon-share"></i></span>
                                        <ul>
                                            @if($member->social_links['linkedin'] ?? false)
                                                <li><a href="{{ $member->social_links['linkedin'] }}" target="_blank"><i
                                                            class="fab fa-linkedin-in"></i></a></li>
                                            @endif
                                            @if($member->social_links['github'] ?? false)
                                                <li><a href="{{ $member->social_links['github'] }}" target="_blank"><i
                                                            class="fab fa-github"></i></a></li>
                                            @endif
                                            @if($member->social_links['twitter'] ?? false)
                                                <li><a href="{{ $member->social_links['twitter'] }}" target="_blank"><i
                                                            class="fab fa-twitter"></i></a></li>
                                            @endif
                                            @if($member->social_links['youtube'] ?? false)
                                                <li><a href="{{ $member->social_links['youtube'] }}" target="_blank"><i
                                                            class="fab fa-youtube"></i></a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <h4>No team members found.</h4>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </section>
    <!-- Team area end -->

    <!-- testimonials two area start -->
    <section class="testimonials-two-section fix">
        <div class="container-fluid g-0">
            <div class="row g-0">
                <div class="col-xxl-4 col-xl-4 col-lg-12">
                    <div class="testimonials-video-area p-relative">
                        <figure class="image w-img">
                            <img src="{{ asset('assets/main/imgs/resources/video-1.jpg') }}" alt="Client stories">
                        </figure>
                        <div class="play-btn">
                            <div class="video_player_btn">
                                <a href="https://www.youtube.com/watch?v=eEzD-Y97ges" class="popup-video"><i
                                        class="icon-play"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-8 col-xl-8 col-lg-12">
                    <div class="testimonials-two-area section-space-medium bg-color-1 p-relative">
                        <div class="shape-1" data-background="{{ asset('assets/main/imgs/shapes/shape-36.png') }}">
                        </div>
                        <div class="title-box wow fadeInLeft mb-60" data-wow-delay=".5s">
                            <span class="section-sub-title">Testimonials</span>
                            <h3 class="section-title mt-10">What Our Clients Say</h3>
                        </div>
                        <div class="swiper testimonial-active-2">
                            <div class="swiper-wrapper">
                                <!-- Testimonial 1 -->
                                <div class="swiper-slide">
                                    <div class="testimonials-two-box">
                                        <div class="author-image">
                                            <img src="{{ asset('assets/main/imgs/resources/testimonials-1.png') }}"
                                                alt="Client">
                                        </div>
                                        <div class="icon-1">
                                            <i class="icon-comma-double"></i>
                                        </div>
                                        <h4>Sarah Mitchell</h4>
                                        <span>CTO, FinVault</span>
                                        <ul class="ratings">
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                        </ul>
                                        <p>“Polysphere Tech delivered our core banking platform on time and under
                                            budget. Their architecture is rock‑solid, and their team is a true extension
                                            of ours.”</p>
                                    </div>
                                </div>
                                <!-- Testimonial 2 -->
                                <div class="swiper-slide">
                                    <div class="testimonials-two-box">
                                        <div class="author-image">
                                            <img src="{{ asset('assets/main/imgs/resources/testimonials-2.png') }}"
                                                alt="Client">
                                        </div>
                                        <div class="icon-1">
                                            <i class="icon-comma-double"></i>
                                        </div>
                                        <h4>James Okafor</h4>
                                        <span>Director, HealthBridge</span>
                                        <ul class="ratings">
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                        </ul>
                                        <p>“They took our legacy system and transformed it into a modern,
                                            HIPAA‑compliant SaaS. Our users love the new experience.”</p>
                                    </div>
                                </div>
                                <!-- Testimonial 3 -->
                                <div class="swiper-slide">
                                    <div class="testimonials-two-box">
                                        <div class="author-image">
                                            <img src="{{ asset('assets/main/imgs/resources/testimonials-1.png') }}"
                                                alt="Client">
                                        </div>
                                        <div class="icon-1">
                                            <i class="icon-comma-double"></i>
                                        </div>
                                        <h4>Elena Rodriguez</h4>
                                        <span>VP of Product, LogiMove</span>
                                        <ul class="ratings">
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                            <li><i class="fa fa-star"></i></li>
                                        </ul>
                                        <p>“The custom logistics platform they built reduced our dispatch time by 40%.
                                            Their Agile approach kept us aligned throughout.”</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="testimonial-dot-inner text-center mt-60">
                            <div class="testimonial-swiper-dot"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- testimonials two area end -->

    <!-- Blog area start -->
    <section class="blog-section-one section-space">
        <div class="small-container">
            <div class="title-box mb-40 wow fadeInLeft" data-wow-delay=".5s">
                <span class="section-sub-title">Insights</span>
                <h3 class="section-title mt-10">Latest from Our Blog</h3>
            </div>
            <div class="row g-4">
                <!-- Blog 1 -->


                @forelse($posts as $index => $post)


                    <div class="col-xxl-4 col-xl-4 col-lg-6">
                        <div class="blog-style-one">
                            <a class="blog-image w-img" href="news-details.html">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                                @else
                                    <img src="{{ asset('assets/main/imgs/blog/blog-1.jpg') }}" alt="{{ $post->title }}">
                                @endif


                            </a>
                            <div class="blog-content">
                                <div class="post-meta">

                                    <span class="p-relative">
                                        <a wire:navigate.hover href="{{ route('blog.details', $post->slug) }}">
                                            <i class="fal fa-user"></i> By {{ $post->author?->name ?? 'Admin' }}
                                        </a></span>

                                    <span class="p-relative">
                                        <a wire:navigate.hover href="{{ route('blog.details', $post->slug) }}">
                                            <i class="fal fa-calendar-alt"></i> {{ $post->published_at->format('d M, Y') }}
                                        </a></span>
                                </div>
                                <hr>
                                <h5 class="blog-title mb-30">
                                    <a wire:navigate.hover
                                        href="{{ route('blog.details', $post->slug) }}">{{ $post->title }}</a>
                                </h5>
                                <div class="blog-link">
                                    <a class="primary-btn-5 btn-hover" href="{{ route('blog.details', $post->slug) }}">
                                        Read More &nbsp; | <i class="icon-right-arrow"></i>
                                        <span style="top: 147.172px; left: 108.5px;"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="text-center py-5">
                        <h4>No blog posts found.</h4>
                        <p>Check back later for new content.</p>
                    </div>
                @endforelse


            </div>
        </div>
    </section>
    <!-- Blog area end -->

    <!-- Brand area start -->
    <div class="brand-section section-space-bottom">
        <div class="small-container">
            <div class="swiper brand-active">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <span><a href="#"><img src="{{ asset('assets/main/imgs/resources/brand-1.png') }}"
                                    alt="Brand"></a></span>
                    </div>
                    <div class="swiper-slide">
                        <span><a href="#"><img src="{{ asset('assets/main/imgs/resources/brand-2.png') }}"
                                    alt="Brand"></a></span>
                    </div>
                    <div class="swiper-slide">
                        <span><a href="#"><img src="{{ asset('assets/main/imgs/resources/brand-3.png') }}"
                                    alt="Brand"></a></span>
                    </div>
                    <div class="swiper-slide">
                        <span><a href="#"><img src="{{ asset('assets/main/imgs/resources/brand-4.png') }}"
                                    alt="Brand"></a></span>
                    </div>
                    <div class="swiper-slide">
                        <span><a href="#"><img src="{{ asset('assets/main/imgs/resources/brand-1.png') }}"
                                    alt="Brand"></a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Brand area end -->
</div>