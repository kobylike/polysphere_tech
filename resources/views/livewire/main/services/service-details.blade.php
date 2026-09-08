<div>
    <!-- Breadcrumb area start -->
    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb" data-background="{{ asset('assets/main/imgs/resources/servicea.jpg') }}">
        </div>
        <div class="breadcrumb__thumb_2"
            data-background="{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Service Details</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span><a wire:navigate.hover href="{{ route('services') }}">Services</a></span>
                                    </li>
                                    <li><span>{{ $service->name }}</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb area end -->

    <section class="service-details-page section-space">
        <div class="small-container">
            <div class="row">
                <div class="col-xxl-8 col-xl-8 col-lg-8">
                    <div class="service-details-page-content">
                        <!-- Main Image -->
                        <figure class="w-img">
                            @if($service->featured_image)
                                <img src="{{ asset('storage/' . $service->featured_image) }}" alt="{{ $service->name }}">
                            @else
                                <img src="{{ asset('assets/main/imgs/service/service-details-1.jpg') }}"
                                    alt="{{ $service->name }}">
                            @endif
                        </figure>

                        <!-- Title -->
                        <h3 class="service-details-title mt-45 mb-25">{{ $service->name }}</h3>

                        <!-- Description (CKEditor) -->
                        <div class="service-content">
                            {!! $service->description !!}
                        </div>

                        <!-- Additional Images -->
                        @if($service->additional_images && count($service->additional_images) > 0)
                            <div class="row mt-35">
                                @foreach($service->additional_images as $img)
                                    <div class="col-lg-6">
                                        <figure class="w-img">
                                            <img src="{{ asset('storage/' . $img) }}" alt="{{ $service->name }} additional">
                                        </figure>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Feature List (static placeholder – you can make it dynamic later) -->
                        <h4 class="mt-35">Technology Solutions Built for Your Business</h4>
                        <p class="mt-25 mb-35"> At PolySphere Tech, we combine software engineering, cloud technology,
                            artificial intelligence, cybersecurity, and digital transformation to build solutions that
                            solve real business challenges. From understanding your requirements to designing,
                            developing, deploying, and supporting your technology, we work with you every step of the
                            way to create secure, scalable, and future-ready digital solutions. </p>
                        <div class="row">
                            <div class="col-lg-7">
                                <figure class="w-img">
                                    <img src="{{ asset('assets/main/imgs/service/service.jpg') }}"
                                        alt="Polysphere Tech">
                                </figure>
                            </div>
                            <div class="col-lg-5">
                                <ul class="service-details-page-list pt-20 pb-10">
                                    <li>Custom Software Development</li>
                                    <li>SaaS & Web Application Development</li>
                                    <li>AI & Business Automation</li>
                                    <li>Cloud & Infrastructure Solutions</li>
                                    <li>Cybersecurity & Application Security</li>
                                    <li>Digital Transformation & IT Consulting</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-xxl-4 col-xl-4 col-lg-4">
                    <div class="service-sidebar">
                        <aside>
                            <!-- Services List -->
                            <div class="service-widget-1 mb-30">
                                <h5>Main Services</h5>
                                <ul>
                                    @php
                                        $allServices = \App\Models\Service::where('status', 'active')
                                            ->orderBy('order', 'asc')
                                            ->orderBy('name', 'asc')
                                            ->get();
                                    @endphp
                                    @forelse($allServices as $svc)
                                        <li>
                                            <a wire:navigate.hover href="{{ route('service.details', $svc->slug) }}"
                                                class="{{ $svc->id === $service->id ? 'active' : '' }}">
                                                <span>{{ $svc->name }}</span>
                                                <span><i class="icon-arrow-right-double"></i></span>
                                            </a>
                                        </li>
                                    @empty
                                        <li><span>No services</span></li>
                                    @endforelse
                                </ul>
                            </div>

                            <!-- CTA Widget -->
                            @livewire('main.partials.help')

                            <!-- Company File -->
                            <div class="service-widget-3">
                                <div class="company-file">
                                    <h6>Company File</h6>
                                    <div class="file-size">
                                        <span>(1.5MB)</span>
                                        <span><a href="#"><i class="far fa-arrow-down-to-bracket"></i></a></span>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- Styles for dynamic content --}}
<style>
    .service-content {
        font-size: 16px;
        line-height: 1.8;
        color: #4a4a4a;
    }

    .service-content img {
        max-width: 100%;
        height: auto;
        margin: 20px 0;
        border-radius: 8px;
    }

    .service-content blockquote {
        border-left: 4px solid #3b82f6;
        padding: 20px 30px;
        background: #f8fafc;
        margin: 30px 0;
        font-style: italic;
        border-radius: 0 8px 8px 0;
    }

    /* Sidebar active link */
    .service-widget-1 ul li a.active {
        background: #3b82f6;
        color: #fff !important;
    }

    .service-widget-1 ul li a.active span:last-child {
        color: #fff !important;
    }
</style>