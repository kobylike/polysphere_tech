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
                        <h2 class="breadcrumb__title">Projects</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>Projects</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb area end -->

    <!-- Filter / Services -->
    <section class="project-filter-section pt-40 pb-20">
        <div class="small-container">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0">Filter by Service:</h5>
                </div>
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#" wire:click.prevent="$set('service', '')"
                            class="btn btn-sm {{ empty($service) ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill">
                            All Projects
                        </a>
                        @foreach($services as $svc)
                            <a href="#" wire:click.prevent="$set('service', '{{ $svc->slug }}')"
                                class="btn btn-sm {{ $service === $svc->slug ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill">
                                {{ $svc->name }}
                                <span class="badge bg-light text-dark ms-1">{{ $svc->projects_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Project Grid -->
    <section class="project-page-section section-space p-relative fix">
        <div class="small-container">
            @if($projects->count() > 0)
                <div class="row g-4">
                    @foreach($projects as $project)
                        <div class="col-xxl-6 col-xl-6 col-lg-6 mb-15">
                            <div class="project-slider-area p-relative">
                                <figure class="image m-img">
                                    @if($project->featured_image)
                                        <img src="{{ asset('storage/' . $project->featured_image) }}" alt="{{ $project->title }}">
                                    @else
                                        <img src="{{ asset('assets/main/imgs/project/project-1.jpg') }}"
                                            alt="{{ $project->title }}">
                                    @endif
                                </figure>
                                <div class="content-area">
                                    <div class="title-area">
                                        <h6 class="mb-5">{{ $project->service?->name ?? 'General' }}</h6>
                                        <h5><a wire:navigate.hover
                                                href="{{ route('project.details', $project->slug) }}">{{ $project->title }}</a>
                                        </h5>
                                    </div>
                                    <div class="icon-area">
                                        <a wire:navigate.hover href="{{ route('project.details', $project->slug) }}">
                                            <i class="icon-arrow-up"></i>
                                        </a>
                                    </div>
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
                                {{ $projects->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <h4>No projects found</h4>
                    <p>Check back later for our latest work.</p>
                </div>
            @endif
        </div>
    </section>
</div>