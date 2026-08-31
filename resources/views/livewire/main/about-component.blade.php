<div>

    <!-- Breadcrumb area start -->
    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb" data-background="{{ asset('assets/main/imgs/resources/about.jpg') }}">
        </div>
        <div class="breadcrumb__thumb_2"
            data-background="{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">About Us</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>About Us</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb area end -->

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
                    <!-- image area start -->
                    <div class="about-us-image-area p-relative wow fadeInRight" data-wow-delay=".5s">
                        <div class="border-shape" data-background="{{ asset('assets/main/imgs/shapes/shape-6.png') }}">
                        </div>
                        <figure class="image-1">
                            <img src="{{ asset('assets/main/imgs/about/about-1.jpg') }}"
                                alt="Polysphere Tech team collaborating">
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
                    <!-- image area end -->
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6">
                    <!-- .content start -->
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
                                <p>From discovery and architecture to deployment and maintenance — we're with you at
                                    every stage of your journey.</p>
                            </div>
                        </div>
                        <div class="about-btn-box wow fadeInLeft" data-wow-delay="1s">
                            <a class="primary-btn-1 btn-hover" href="{{ route('contact') }}">
                                Get in Touch &nbsp; | <i class="icon-right-arrow"></i>
                                <span style="top: 147.172px; left: 108.5px;"></span>
                            </a>
                        </div>
                    </div>
                    <!-- .content end -->
                </div>
            </div>
        </div>
    </section>
    <!-- About us area end -->

    <!-- Stats / Trust Bar (NEW) -->
    <section class="stats-section"
        style="background: #0a0a0a; padding: 60px 0; border-top: 1px solid rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.05);">
        <div class="small-container">
            <div class="row g-4 text-center">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <h2
                            style="font-size: 48px; font-weight: 800; color: #fff; margin: 0; background: linear-gradient(135deg, #3b82f6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            <span class="counter">200</span>+
                        </h2>
                        <p style="color: rgba(255,255,255,0.6); margin: 0; font-size: 16px;">Projects Delivered</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <h2
                            style="font-size: 48px; font-weight: 800; color: #fff; margin: 0; background: linear-gradient(135deg, #3b82f6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            <span class="counter">97</span>%
                        </h2>
                        <p style="color: rgba(255,255,255,0.6); margin: 0; font-size: 16px;">Client Satisfaction</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <h2
                            style="font-size: 48px; font-weight: 800; color: #fff; margin: 0; background: linear-gradient(135deg, #3b82f6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            <span class="counter">50</span>+
                        </h2>
                        <p style="color: rgba(255,255,255,0.6); margin: 0; font-size: 16px;">Enterprise Clients</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <h2
                            style="font-size: 48px; font-weight: 800; color: #fff; margin: 0; background: linear-gradient(135deg, #3b82f6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                            <span class="counter">8</span>+
                        </h2>
                        <p style="color: rgba(255,255,255,0.6); margin: 0; font-size: 16px;">Years of Excellence</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Choose area start (Why Choose Us) -->
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
                            <p>We work in short, iterative cycles with regular demos and clear communication — so you're
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
                            <p>We take full responsibility — from ideation and design to deployment, support, and
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
                            <p>We build with scalability, security, and maintainability in mind — so your technology
                                evolves with your business.</p>
                        </div>
                    </div>
                    <hr>
                    <!-- block -->
                    <div class="choose-area-icon-box mb-15 wow fadeInRight" data-wow-delay="1.1s">
                        <div class="icon-box p-relative">
                            <i class="icon-help"></i>
                        </div>
                        <div class="content">
                            <h5><a href="services.html">Cutting‑Edge Technology</a></h5>
                            <p>We leverage the latest frameworks, cloud platforms, and AI tools to deliver solutions
                                that are both innovative and reliable.</p>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6 wow fadeInLeft" data-wow-delay="1.2s">
                    <figure class="image m-img">
                        <img src="{{ asset('assets/main/imgs/resources/choose-1.png') }}"
                            alt="Why choose Polysphere Tech">
                    </figure>
                </div>
            </div>
        </div>
    </section>
    <!-- Choose area end -->

    <!-- Work process area start -->
    <section class="work-process-section section-space p-relative"
        data-background="{{ asset('assets/main/imgs/bg/process-bg.png') }}">
        <div class="shape-1" data-background="{{ asset('assets/main/imgs/bg/line.png') }}"></div>
        <div class="small-container">
            <div class="title-box text-center mb-60 wow fadeInLeft" data-wow-delay=".5s">
                <span class="section-sub-title">Our Process</span>
                <h3 class="section-title mt-10">How We Deliver Excellence</h3>
            </div>
            <div class="row g-4">
                <div class="col-xxl-4 col-xl-4 col-lg-4">
                    <div class="work-process-box text-center">
                        <div class="icon-box p-relative">
                            <img src="{{ asset('assets/main/imgs/icon/icon-4.png') }}" alt="Discovery icon">
                            <span>1</span>
                        </div>
                        <div class="content">
                            <h4 class="pt-25 pb-25">Discovery & Strategy</h4>
                            <p>We dive deep into your business goals, user needs, and technical requirements to create a
                                tailored roadmap for success.</p>
                        </div>
                    </div>
                </div>
                <!-- block -->
                <div class="col-xxl-4 col-xl-4 col-lg-4">
                    <div class="work-process-box text-center">
                        <div class="icon-box p-relative">
                            <img src="{{ asset('assets/main/imgs/icon/icon-5.png') }}" alt="Design icon">
                            <span>2</span>
                        </div>
                        <div class="content">
                            <h4 class="pt-25 pb-25">Design & Prototyping</h4>
                            <p>We craft intuitive user experiences and interactive prototypes, iterating quickly to
                                validate ideas before development.</p>
                        </div>
                    </div>
                </div>
                <!-- block -->
                <div class="col-xxl-4 col-xl-4 col-lg-4">
                    <div class="work-process-box text-center">
                        <div class="icon-box p-relative">
                            <img src="{{ asset('assets/main/imgs/icon/icon-6.png') }}" alt="Delivery icon">
                            <span>3</span>
                        </div>
                        <div class="content">
                            <h4 class="pt-25 pb-25">Build & Launch</h4>
                            <p>We develop, test, and deploy your solution with precision, ensuring a smooth launch and
                                ongoing support for continuous growth.</p>
                        </div>
                    </div>
                </div>
                <!-- block -->
            </div>
        </div>
    </section>
    <!-- Work process area end -->

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
                @forelse($teamMembers as $member)
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 mb-15">
                        <div class="team-area-box p-relative mb-60 wow fadeInLeft" data-wow-delay=".7s">

                            <figure class="image w-img p-relative">
                                {{-- Image container with fixed aspect ratio 370x451 --}}
                                <div class="team-image-wrapper">
                                    <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="team-member-img">
                                </div>
                            </figure>
                            <div class="content">
                                <div class="author-info">
                                    <h5 class="mb-5">
                                        <a wire:navigate href="{{ route('team.details', ['slug' => $member->username]) }}">
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
    </section>
    <!-- Team area end -->

    <!-- CTA / Testimonial Strip (NEW) -->
    <section style="background: #0a0a0a; padding: 80px 0; border-top: 1px solid rgba(255,255,255,0.05);">
        <div class="small-container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 style="font-size: 32px; font-weight: 700; color: #fff; margin: 0;">
                        Ready to Build Something <span
                            style="background: linear-gradient(135deg, #3b82f6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Amazing</span>?
                    </h3>
                    <p style="color: rgba(255,255,255,0.6); margin-top: 10px; font-size: 18px;">
                        Let's create technology that drives your business forward.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="contact.html" class="primary-btn-1 btn-hover">
                        Start a Project &nbsp; | <i class="icon-right-arrow"></i>
                        <span style="top: 147.172px; left: 108.5px;"></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Map area (unchanged) -->
    <div class="container-fluid g-0 fix">
        <div class="row">
            <div class="col-xxl-12">
                <div class="contact-map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d4216.433331900906!2d90.36996032419312!3d23.83718617432321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sbd!4v1693682874850!5m2!1sen!2sbd"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        style="width: 100%; height: 450px; border: 0;"></iframe>
                </div>
            </div>
        </div>
    </div>

</div>