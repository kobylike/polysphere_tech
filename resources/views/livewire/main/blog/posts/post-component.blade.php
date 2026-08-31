<div>
    <!-- Breadcrumb area start -->
    <div class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb" data-background="{{ asset('assets/main/imgs/resources/blog.jpg') }}"></div>
        <div class="breadcrumb__thumb_2"
            data-background="{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Blog</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>Blog</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb area end -->

    <section class="blog-standard-page section-space">
        <div class="small-container">
            <div class="row">
                <!-- ─── MAIN CONTENT ─── -->
                <div class="col-xxl-8 col-xl-8 col-lg-8">

                    @forelse($posts as $post)
                        <div id="post-{{ $post->slug }}" class="blog-style-one mb-30">
                            <a class="blog-image w-img" wire:navigate.hover href="{{ route('blog.details', $post->slug) }}">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                                @else
                                    <img src="{{ asset('assets/main/imgs/blog/blog-8.jpg') }}" alt="{{ $post->title }}">
                                @endif
                            </a>
                            <div class="blog-content">
                                <div class="post-meta">
                                    <span class="p-relative">
                                        <a wire:navigate.hover href="{{ route('blog.details', $post->slug) }}">
                                            <i class="fal fa-user"></i> By {{ $post->author?->name ?? 'Admin' }}
                                        </a>
                                    </span>
                                    <span class="p-relative">
                                        <a wire:navigate.hover href="{{ route('blog.details', $post->slug) }}">
                                            <i class="fal fa-calendar-alt"></i> {{ $post->published_at->format('d M, Y') }}
                                        </a>
                                    </span>
                                </div>
                                <hr>
                                <h5 class="blog-title mb-30">
                                    <a wire:navigate.hover
                                        href="{{ route('blog.details', $post->slug) }}">{{ $post->title }}</a>
                                </h5>
                                <p class="mb-35">{{ Str::limit($post->excerpt ?? strip_tags($post->content), 150) }}</p>
                                <div class="blog-link">
                                    <a class="primary-btn-5 btn-hover" href="{{ route('blog.details', $post->slug) }}">
                                        Read More &nbsp; | <i class="icon-right-arrow"></i>
                                        <span style="top: 147.172px; left: 108.5px;"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <h4>No blog posts found.</h4>
                            <p>Check back later for new content.</p>
                        </div>
                    @endforelse

                    <!-- Pagination -->
                    <div class="row">
                        <div class="col-xxl-12">
                            <div class="pagination__wrapper mt-50">
                                <div class="bd-basic__pagination d-flex align-items-center justify-content-center">
                                    {{ $posts->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── SIDEBAR ─── -->
                <div class="col-xxl-4 col-xl-4 col-lg-4">
                    <div class="blog-sidebar">
                        <aside>

                            <!-- Search Widget -->
                            <div class="blog-widget-1 mb-30">
                                <h5 class="blog-widget-title p-relative mb-45">Search</h5>
                                <div class="search-form p-relative">
                                    <form wire:submit.prevent>
                                        <input type="text" wire:model.live.debounce.500ms="search"
                                            placeholder="Search here...">
                                        <button type="submit"><i class="icon-search"></i></button>
                                    </form>
                                </div>
                            </div>

                            <!-- Category Widget -->
                            <div class="blog-widget-2 mb-30">
                                <h5 class="blog-widget-title p-relative mb-45">Category</h5>
                                <ul class="blog-categories-list">
                                    <li>
                                        <a href="#" wire:click.prevent="$set('category', '')"
                                            class="{{ empty($category) ? 'active' : '' }}">
                                            <span>All Categories</span>
                                            <span>({{ $posts->total() }})</span>
                                        </a>
                                    </li>
                                    @foreach($categoriesData as $cat)
                                        <li>
                                            <a href="#" wire:click.prevent="$set('category', '{{ $cat->slug }}')"
                                                class="{{ $category === $cat->slug ? 'active' : '' }}">
                                                <span>{{ $cat->name }}</span>
                                                <span>({{ $cat->posts_count }})</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Recent Posts Widget -->
                            <div class="blog-widget-3 mb-30">
                                <h5 class="blog-widget-title p-relative mb-45">Recent Posts</h5>
                                <div class="blog-post-sidebar-area">
                                    @foreach($recentPosts as $recent)
                                        <div class="blog-post-sidebar">
                                            <a class="w-img blog-sidebar-thumb"
                                                href="{{ route('blog.details', $recent->slug) }}">
                                                <img src="{{ $recent->featured_image ? asset('storage/' . $recent->featured_image) : asset('assets/main/imgs/blog/blog-sidebar-1.jpg') }}"
                                                    alt="{{ $recent->title }}">
                                            </a>
                                            <div class="content">
                                                <span><i class="fal fa-calendar-alt"></i>
                                                    {{ $recent->published_at->format('d M, Y') }}</span>
                                                <h6 class="blog-sidebar-post-title mt-10">
                                                    <a
                                                        href="{{ route('blog.details', $recent->slug) }}">{{ Str::limit($recent->title, 40) }}</a>
                                                </h6>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Tags Widget -->
                            <div class="blog-widget-4 mb-30">
                                <h5 class="blog-widget-title p-relative mb-45">Tags</h5>
                                <div class="tagcloud">
                                    @forelse($popularTags as $tag)
                                        <a href="#">{{ $tag->name }}</a>
                                    @empty
                                        <span class="text-muted">No tags yet.</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Contact Widget / CTA -->
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

                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- ── Additional styles for category filter active state ── --}}
<style>
    .blog-categories-list li a.active {
        color: #3b82f6;
        font-weight: 600;
    }
</style>