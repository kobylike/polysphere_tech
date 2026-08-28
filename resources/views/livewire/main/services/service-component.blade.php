<div>
    <!-- Breadcrumb area start -->
    <div class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb" data-background="{{ asset('assets/main/imgs/resources/service.jpg') }}"></div>
        <div class="breadcrumb__thumb_2" data-background="{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Services</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>Services</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb area end -->

    <!-- Service Grid -->
    <section class="service-page-section section-space">
        <div class="small-container">
            @if($services->count() > 0)
                <div class="row g-4">
                    @foreach($services as $service)
                        <div class="col-xxl-4 col-xl-4 col-lg-4 mb-15">
                            <div class="service-slider-area p-relative">
                                <figure class="image w-img">
                                    @if($service->featured_image)
                                        <img src="{{ asset('storage/' . $service->featured_image) }}" alt="{{ $service->name }}">
                                    @else
                                        <img src="{{ asset('assets/main/imgs/service/service-1.jpg') }}" alt="{{ $service->name }}">
                                    @endif
                                </figure>
                                <div class="content">
                                    <div class="icon-box">
                                        @if($service->icon)
                                            <i class="{{ $service->icon }}" style="font-size: 2rem; color: #3b82f6;"></i>
                                        @else
                                            <img src="{{ asset('assets/main/imgs/icon/icon-2.png') }}" alt="icon">
                                        @endif
                                    </div>
                                    <h4 class="mb-15"><a wire:navigate.hover href="{{ route('service.details', $service->slug) }}">{{ $service->name }}</a></h4>
                                    <p class="mb-25">{{ Str::limit($service->description ?? 'Lorem ipsum dolor sit amet, is consectetur adipisci elit. Integer feugiat tortor non there are many other nullam.', 120) }}</p>
                                    <a wire:navigate.hover href="{{ route('service.details', $service->slug) }}" class="service-btn">
                                        Read More <i class="icon-arrow-right-double"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="row mt-50">
                    <div class="col-xxl-12">
                        <div class="pagination__wrapper">
                            <div class="bd-basic__pagination d-flex align-items-center justify-content-center">
                                {{ $services->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <h4>No services available</h4>
                    <p>Check back later for our offerings.</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Why Choose Us (static) – kept as is -->
    <section class="choose-section bg-color-1 section-space-top p-relative">
        <div class="bg-image" data-background="{{ asset('assets/main/imgs/bg/choose-bg.png') }}"></div>
        <div class="shape-image" data-background="{{ asset('assets/main/imgs/shapes/shape-15.png') }}"></div>
        <div class="small-container">
            <div class="row g-4">
                <div class="col-xxl-6 col-xl-6 col-lg-6 p-relative section-space-medium-bottom">
                    <div class="title-box mb-50 wow fadeInLeft" data-wow-delay=".5s">
                        <span class="section-sub-title">why choose us</span>
                        <h3 class="section-title mt-10">What's Make Us Different</h3>
                    </div>
                    <!-- block -->
                    <div class="choose-area-icon-box mb-15 wow fadeInRight" data-wow-delay=".5s">
                        <div class="icon-box p-relative">
                            <i class="icon-roof-4"></i>
                        </div>
                        <div class="content">
                            <h5><a href="{{ route('services') }}">Commercial Service</a></h5>
                            <p>Embarrassing hidden in the middle All the Lorem Ipsum generators on the Internet repeat predefined chunks</p>
                        </div>
                    </div>
                    <hr>
                    <div class="choose-area-icon-box mb-15 wow fadeInRight" data-wow-delay=".7s">
                        <div class="icon-box p-relative">
                            <i class="icon-target"></i>
                        </div>
                        <div class="content">
                            <h5><a href="{{ route('services') }}">Mission Statement</a></h5>
                            <p>Embarrassing hidden in the middle All the Lorem Ipsum generators on the Internet repeat predefined chunks</p>
                        </div>
                    </div>
                    <hr>
                    <div class="choose-area-icon-box mb-15 wow fadeInRight" data-wow-delay=".9s">
                        <div class="icon-box p-relative">
                            <i class="icon-help"></i>
                        </div>
                        <div class="content">
                            <h5><a href="{{ route('services') }}">Safety And Reliability</a></h5>
                            <p>Embarrassing hidden in the middle All the Lorem Ipsum generators on the Internet repeat predefined chunks</p>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6 wow fadeInLeft" data-wow-delay="1.2s">
                    <figure class="image m-img">
                        <img src="{{ asset('assets/main/imgs/resources/choose-1.png') }}" alt="">
                    </figure>
                </div>
            </div>
        </div>
    </section>

    <!-- Counter stats (static) – kept as is -->
    <section class="icon-box-counter-section section-space">
        <div class="small-container">
            <div class="row g-4">
                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                    <div class="icon-box-counter-area">
                        <div class="icon-box">
                            <img src="{{ asset('assets/main/imgs/icon/icon-7.png') }}" alt="img">
                        </div>
                        <div class="content">
                            <h3><span class="counter">300</span>+</h3>
                            <span class="text-1">Successfully Projects</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                    <div class="icon-box-counter-area">
                        <div class="icon-box">
                            <img src="{{ asset('assets/main/imgs/icon/icon-4.png') }}" alt="img">
                        </div>
                        <div class="content">
                            <h3><span class="counter">450</span>+</h3>
                            <span class="text-1">Company Staffs</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                    <div class="icon-box-counter-area">
                        <div class="icon-box">
                            <img src="{{ asset('assets/main/imgs/icon/icon-5.png') }}" alt="img">
                        </div>
                        <div class="content">
                            <h3><span class="counter">3,150</span></h3>
                            <span class="text-1">Tons of Products</span>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
                    <div class="icon-box-counter-area">
                        <div class="icon-box">
                            <img src="{{ asset('assets/main/imgs/icon/icon-6.png') }}" alt="img">
                        </div>
                        <div class="content">
                            <h3><span class="counter">6,561</span></h3>
                            <span class="text-1">Satisfied Clients</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>