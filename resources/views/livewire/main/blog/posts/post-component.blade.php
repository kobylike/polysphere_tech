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

                    {{-- ─── Search Results Header ─────────────────────────────── --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            @if($isSearching && $posts->count() > 0)
                                <h5 class="mb-0">
                                    <span class="text-primary">{{ $posts->count() }}</span> results found for
                                    "<strong>{{ $search }}</strong>"
                                </h5>
                            @elseif($isSearching && $posts->count() === 0)
                                <h5 class="mb-0 text-danger">
                                    No results found for "<strong>{{ $search }}</strong>"
                                </h5>
                            @elseif(!empty($category))
                                <h5 class="mb-0">
                                    Posts in category:
                                    <strong>{{ $categoriesData->firstWhere('slug', $category)?->name ?? $category }}</strong>
                                    <span class="text-muted">({{ $posts->count() }})</span>
                                </h5>
                            @else
                                {{-- <h5 class="mb-0">All Posts <span class="text-muted">({{ $posts->total() }})</span></h5>
                                --}}
                            @endif
                        </div>
                        @if($isSearching)
                            <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearSearch">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        @endif
                    </div>

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
                                    @if($post->categories->count())
                                        <span class="p-relative">
                                            <i class="fal fa-tag"></i>
                                            @foreach($post->categories as $cat)
                                                <a wire:navigate.hover href="{{ route('posts') }}?category={{ $cat->slug }}"
                                                    class="text-decoration-none">
                                                    {{ $cat->name }}
                                                </a>@if(!$loop->last), @endif
                                            @endforeach
                                        </span>
                                    @endif
                                </div>
                                <hr>
                                <h5 class="blog-title mb-30">
                                    <a wire:navigate.hover href="{{ route('blog.details', $post->slug) }}">
                                        {{ $post->title }}
                                    </a>
                                </h5>
                                <p class="mb-35">{{ Str::limit($post->excerpt ?? strip_tags($post->content), 150) }}</p>
                                @if($post->tags->count())
                                    <div class="mb-3">
                                        @foreach($post->tags as $tag)
                                            <span class="badge bg-light text-dark border me-1">
                                                #{{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="blog-link">
                                    <a class="primary-btn-5 btn-hover" wire:navigate.hover
                                        href="{{ route('blog.details', $post->slug) }}">
                                        Read More &nbsp; | <i class="icon-right-arrow"></i>
                                        <span style="top: 147.172px; left: 108.5px;"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            @if($isSearching)
                                <i class="fas fa-search fa-3x text-muted mb-3 d-block"></i>
                                <h4>No posts found</h4>
                                <p>Try adjusting your search terms or browse our categories.</p>
                                <button type="button" class="btn btn-primary mt-2" wire:click="clearSearch">
                                    Clear Search
                                </button>
                            @else
                                <i class="fas fa-newspaper fa-3x text-muted mb-3 d-block"></i>
                                <h4>No blog posts found</h4>
                                <p>Check back later for new content.</p>
                            @endif
                        </div>
                    @endforelse

                    <!-- Pagination -->
                    @if($posts->hasPages())
                        <div class="row">
                            <div class="col-xxl-12">
                                <div class="pagination__wrapper mt-50">
                                    <div class="bd-basic__pagination d-flex align-items-center justify-content-center">
                                        {{ $posts->onEachSide(1)->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
                                        <input type="text" wire:model.live.debounce.300ms="search"
                                            placeholder="Search posts, tags, categories...">
                                        <button type="submit"><i class="icon-search"></i></button>
                                    </form>
                                    @if(!empty($search))
                                        <div class="small text-muted mt-1">
                                            Searching in titles, content, categories &amp; tags
                                        </div>
                                    @endif
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
                                            <span>({{ $totalPosts }})</span>
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
                                            <a class="w-img blog-sidebar-thumb" wire:navigate.hover
                                                href="{{ route('blog.details', $recent->slug) }}">
                                                <img src="{{ $recent->featured_image ? asset('storage/' . $recent->featured_image) : asset('assets/main/imgs/blog/blog-sidebar-1.jpg') }}"
                                                    alt="{{ $recent->title }}">
                                            </a>
                                            <div class="content">
                                                <span><i class="fal fa-calendar-alt"></i>
                                                    {{ $recent->published_at->format('d M, Y') }}</span>
                                                <h6 class="blog-sidebar-post-title mt-10">
                                                    <a wire:navigate.hover
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
                                        <a href="#" wire:click.prevent="$set('search', '{{ $tag->name }}')">
                                            {{ $tag->name }}
                                            <span class="badge bg-light text-dark ms-1">{{ $tag->posts_count }}</span>
                                        </a>
                                    @empty
                                        <span class="text-muted">No tags yet.</span>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Contact Widget / CTA -->
                            @livewire('main.partials.help')

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

    .blog-categories-list li a {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        transition: all 0.2s;
        text-decoration: none;
        color: #333;
    }

    .blog-categories-list li a:hover {
        color: #3b82f6;
        padding-left: 5px;
    }

    .blog-categories-list li a.active {
        color: #3b82f6;
        font-weight: 600;
        padding-left: 10px;
        border-left: 3px solid #3b82f6;
    }

    .tagcloud a {
        display: inline-block;
        padding: 6px 16px;
        margin: 0 4px 8px 0;
        background: #f1f5f9;
        border-radius: 20px;
        font-size: 14px;
        color: #333;
        text-decoration: none;
        transition: all 0.2s;
    }

    .tagcloud a:hover {
        background: #3b82f6;
        color: #fff;
        transform: translateY(-2px);
    }

    .tagcloud a .badge {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 10px;
    }

    .blog-post-sidebar .content .blog-sidebar-post-title a {
        color: #1a1a1a;
        text-decoration: none;
        transition: color 0.2s;
        font-weight: 500;
    }

    .blog-post-sidebar .content .blog-sidebar-post-title a:hover {
        color: #3b82f6;
    }

    /* Loading spinner for search */
    .search-loading {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
    }
</style>