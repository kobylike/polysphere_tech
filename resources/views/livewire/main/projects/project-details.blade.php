<div>
    <!-- Breadcrumb area start -->
    <div class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb" data-background="{{ asset('assets/main/imgs/resources/project.jpg') }}"></div>
        <div class="breadcrumb__thumb_2"
            data-background="{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Project Details</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span><a wire:navigate.hover href="{{ route('projects') }}">Projects</a></span>
                                    </li>
                                    <li><span>{{ $project->title }}</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb area end -->

    <section class="Project-details-page section-space">
        <div class="small-container">
            <!-- Main Image (Featured) -->
            <figure class="w-img">
                @if($project->featured_image)
                    <img src="{{ asset('storage/' . $project->featured_image) }}" alt="{{ $project->title }}">
                @else
                    <img src="{{ asset('assets/main/imgs/project/project-details-1.jpg') }}" alt="{{ $project->title }}">
                @endif
            </figure>

            <div class="row mt-50">
                <div class="col-xxl-8 col-xl-8 col-lg-8">
                    <div class="project-details-page-content">
                        <!-- Project Info -->
                        <div class="project-info mb-50">
                            @if($project->start_year || $project->end_year)
                                <h6><span>Year:</span>
                                    {{ $project->start_year }}{{ $project->end_year ? ' - ' . $project->end_year : '' }}
                                </h6>
                            @endif
                            @if($project->client)
                                <h6><span>Client:</span> {{ $project->client }}</h6>
                            @endif
                            @if($project->service)
                                <h6><span>Service:</span> {{ $project->service->name }}</h6>
                            @endif
                            @if($project->company)
                                <h6><span>Company:</span> {{ $project->company }}</h6>
                            @endif
                        </div>

                        <!-- Title -->
                        <h5 class="project-details-page-title">{{ $project->title }}</h5>

                        <!-- Content (CKEditor) -->
                        <div class="project-content mt-30 mb-30">
                            {!! $project->content !!}
                        </div>

                        <!-- Additional Images (if any) -->
                        @if($project->additional_images && count($project->additional_images) > 0)
                            <div class="row mb-40">
                                @foreach($project->additional_images as $img)
                                    <div class="col-lg-6">
                                        <figure class="w-img">
                                            <img src="{{ asset('storage/' . $img) }}" alt="{{ $project->title }} additional">
                                        </figure>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Video Section -->
                        @if($project->video || $project->video_url)
                            <div class="project-details-video p-relative">
                                <figure class="image w-img">
                                    @if($project->thumbnail_image)
                                        <img src="{{ asset('storage/' . $project->thumbnail_image) }}" alt="Video thumbnail">
                                    @else
                                        <img src="{{ asset('assets/main/imgs/project/project-details-3.jpg') }}"
                                            alt="Video thumbnail">
                                    @endif
                                </figure>
                                <div class="play-btn">
                                    <div class="video_player_btn">
                                        <a href="{{ $project->video ? $project->video : $project->video_url }}"
                                            class="popup-video">
                                            <i class="icon-play"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Related Projects -->
                        @if($relatedProjects->isNotEmpty())
                            <div class="related-projects mt-60">
                                <h4 class="mb-30">Related Projects</h4>
                                <div class="row g-4">
                                    @foreach($relatedProjects as $related)
                                        <div class="col-md-4">
                                            <div class="project-slider-area p-relative">
                                                <figure class="image m-img">
                                                    @if($related->featured_image)
                                                        <img src="{{ asset('storage/' . $related->featured_image) }}"
                                                            alt="{{ $related->title }}"
                                                            style="height: 180px; object-fit: cover; width: 100%;">
                                                    @else
                                                        <img src="{{ asset('assets/main/imgs/project/project-1.jpg') }}"
                                                            alt="{{ $related->title }}"
                                                            style="height: 180px; object-fit: cover; width: 100%;">
                                                    @endif
                                                </figure>
                                                <div class="content-area">
                                                    <div class="title-area">
                                                        <h6 class="mb-5">{{ $related->service?->name ?? 'General' }}</h6>
                                                        <h5><a wire:navigate.hover
                                                                href="{{ route('project.details', $related->slug) }}">{{ Str::limit($related->title, 25) }}</a>
                                                        </h5>
                                                    </div>
                                                    <div class="icon-area">
                                                        <a wire:navigate.hover
                                                            href="{{ route('project.details', $related->slug) }}">
                                                            <i class="icon-arrow-up"></i>
                                                        </a>
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

                <!-- Sidebar -->
                <div class="col-xxl-4 col-xl-4 col-lg-4">
                    <div class="service-sidebar">
                        <aside>
                            <!-- Service Widget -->
                            <div class="service-widget-1 mb-30">
                                <h5>Our Services</h5>
                                <ul>
                                    @php
                                        $services = \App\Models\Service::where('status', 'active')->orderBy('name')->get();
                                    @endphp
                                    @forelse($services as $svc)
                                        <li>
                                            <a href="{{ route('projects') }}?service={{ $svc->slug }}">
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
                            <div class="service-widget-2 mb-30">
                                <figure class="w-img">
                                    <img src="{{ asset('assets/main/imgs/service/service-widget-1.jpg') }}"
                                        alt="Need Help?">
                                </figure>
                                <div class="content bg-color-1 text-center">
                                    <div class="icon-box p-relative">
                                        <i class="fal fa-phone-volume"></i>
                                    </div>
                                    <h5>Need Help? Call Here</h5>
                                    <a class="pt-25 pb-25 phone" href="tel:+1234567890">+1 (234) 567-8900</a>
                                    <div class="btn-box">
                                        <a class="primary-btn-1 btn-hover" href="{{ route('contact') }}">
                                            GET A QUOTE &nbsp; | <i class="icon-right-arrow"></i>
                                            <span style="top: 147.172px; left: 108.5px;"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>

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
    .project-content {
        font-size: 16px;
        line-height: 1.8;
        color: #4a4a4a;
    }

    .project-content img {
        max-width: 100%;
        height: auto;
        margin: 20px 0;
        border-radius: 8px;
    }

    .project-content blockquote {
        border-left: 4px solid #3b82f6;
        padding: 20px 30px;
        background: #f8fafc;
        margin: 30px 0;
        font-style: italic;
        border-radius: 0 8px 8px 0;
    }
</style>