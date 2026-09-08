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
                        <h2 class="breadcrumb__title">{{ $project->title }}</h2>
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

            <!-- Featured Image -->
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
                            @if($this->yearRange)
                                <h6><span>Year:</span> {{ $this->yearRange }}</h6>
                            @endif
                            @if($project->client)
                                <h6><span>Client:</span> {{ $project->client }}</h6>
                            @endif
                            @if($project->service)
                                <h6><span>Category:</span> {{ $project->service->name }}</h6>
                            @endif
                            @if($project->location)
                                <h6><span>Location:</span> {{ $project->location }}</h6>
                            @endif
                            {{-- @if($project->company)
                            <h6><span>Company:</span> {{ $project->company }}</h6>
                            @endif --}}
                        </div>

                        <!-- Title -->
                        <h5 class="project-details-page-title">{{ $project->title }}</h5>

                        <!-- Intro Content (CKEditor) -->
                        @if($project->content)
                            <div class="project-content mt-30 mb-30">
                                {!! $project->content !!}
                            </div>
                        @endif

                        <!-- Additional Images Gallery -->
                        @if($project->additional_images && count($project->additional_images) > 0)
                            <div class="row g-3 mb-40">
                                @foreach($project->additional_images as $img)
                                    <div class="col-lg-6">
                                        <figure class="w-img">
                                            <img src="{{ asset('storage/' . $img) }}" alt="{{ $project->title }} gallery image">
                                        </figure>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- The Challenge Of Project -->
                        @if($project->challenge_content || $project->challenge_image || count($project->challenge_features ?? []))
                            <h4 class="mt-35 mb-25">The Challenge Of Project</h4>

                            @if($project->challenge_content)
                                <p>{{ $project->challenge_content }}</p>
                            @endif

                            @if($project->challenge_image || count($project->challenge_features ?? []))
                                <div class="row">
                                    @if($project->challenge_image)
                                        <div class="col-lg-7">
                                            <figure class="w-img">
                                                <img src="{{ asset('storage/' . $project->challenge_image) }}"
                                                    alt="Project challenge">
                                            </figure>
                                        </div>
                                    @endif

                                    @if(count($project->challenge_features ?? []))
                                        <div class="{{ $project->challenge_image ? 'col-lg-5' : 'col-lg-12' }}">
                                            <ul class="service-details-page-list pt-20 pb-10">
                                                @foreach($project->challenge_features as $feature)
                                                    <li>{{ $feature }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif

                        <!-- The Final View Of Project -->
                        @if($project->final_view_content || $this->videoSrc)
                            <h4 class="mt-35 mb-25">The Final View Of Project</h4>

                            @if($project->final_view_content)
                                <p class="mb-30">{{ $project->final_view_content }}</p>
                            @endif

                            @if($this->videoSrc)
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
                                            <a href="{{ $this->videoSrc }}" class="popup-video">
                                                <i class="icon-play"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
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

                            {{-- ─── Recent Projects Widget ─────────────────────────── --}}
                            <div class="service-widget-1 mb-30">
                                <h5>Recent Projects</h5>
                                <ul>
                                    @forelse($recentProjects as $recent)
                                        <li>
                                            <a wire:navigate.hover href="{{ route('project.details', $recent->slug) }}">
                                                <span>{{ $recent->title }}</span>
                                                <span><i class="icon-arrow-right-double"></i></span>
                                            </a>
                                        </li>
                                    @empty
                                        <li><span>No other projects</span></li>
                                    @endforelse
                                </ul>
                            </div>

                            {{-- ─── CTA Widget ─────────────────────────────────────── --}}
                            @livewire('main.partials.help')

                            {{-- ─── Company File ───────────────────────────────────── --}}
                            @if($project->attachment)
                                <div class="service-widget-3">
                                    <div class="company-file">
                                        <h6>{{ $project->attachment_original_name ?? 'Company File' }}</h6>
                                        <div class="file-size">
                                            @if($this->attachmentSizeFormatted)
                                                <span>({{ $this->attachmentSizeFormatted }})</span>
                                            @endif
                                            <span>
                                                <a href="{{ asset('storage/' . $project->attachment) }}" download>
                                                    <i class="far fa-arrow-down-to-bracket"></i>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif

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