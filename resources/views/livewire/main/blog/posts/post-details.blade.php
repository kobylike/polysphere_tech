@php
    $title = $post->seo_title ?? $post->title;
    $seo_description = $post->seo_description ?? strip_tags($post->excerpt ?? $post->content);
    $seo_keywords = $post->seo_keywords ?? '';
@endphp

<div>
    <!-- Breadcrumb area start -->
    <div class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb" data-background="{{ asset('assets/main/imgs/resources/page-title-bg-1.png') }}">
        </div>
        <div class="breadcrumb__thumb_2"
            data-background="{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Blog Details</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span><a wire:navigate.hover href="{{ route('posts') }}">Blog</a></span></li>
                                    <li><span>{{ $post->title }}</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb area end -->

    <section class="blog-details-page section-space">
        <div class="small-container">
            <div class="row">
                <!-- ─── MAIN CONTENT ─── -->
                <div class="col-xxl-8 col-xl-8 col-lg-8">

                    <!-- Featured Image -->
                    <figure class="blog-thumb w-img">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}">
                        @else
                            <img src="{{ asset('assets/main/imgs/blog/blog-details-1.jpg') }}" alt="{{ $post->title }}">
                        @endif
                    </figure>

                    <!-- Meta info -->
                    <ul class="blog-post-meta mb-20 mt-40">
                        <li>
                            <a href="#"><i class="fal fa-user"></i> By {{ $post->author?->name ?? 'Admin' }}</a>
                        </li>
                        <li>
                            <a href="#"><i class="fal fa-calendar-days"></i>
                                {{ $post->published_at->format('d M, Y') }}</a>
                        </li>
                        <li>
                            <a href="#"><i class="fal fa-tag"></i>
                                @foreach($post->categories as $cat)
                                    {{ $cat->name }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </a>
                        </li>
                    </ul>
                    <hr>

                    <!-- Title -->
                    <h3 class="blog-details-title mb-30 mt-20">{{ $post->title }}</h3>

                    <!-- ─── DYNAMIC CONTENT ─── -->
                    <div class="blog-content-body">
                        {!! $post->content !!}
                    </div>

                    <!-- Tags & Share -->
                    <div class="postbox__share-wrapper mb-60">
                        <div class="row g-4 align-items-center">
                            <div class="col-xl-7 col-lg-12">
                                <div class="tagcloud tagcloud-sm">
                                    <span>Tags:</span>
                                    @forelse($post->tags as $tag)
                                        <a href="#">{{ $tag->name }}</a>
                                    @empty
                                        <span class="text-muted">No tags</span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-12">
                                <div class="postbox__share text-xl-end">
                                    <span>Share:</span>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}"
                                        target="_blank"><i class="fab fa-linkedin-in"></i></a>
                                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}"
                                        target="_blank"><i class="fab fa-twitter"></i></a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                        target="_blank"><i class="fab fa-facebook-f"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ─── RELATED POSTS ─── -->
                    @if($relatedPosts->isNotEmpty())
                        <div class="related-posts mt-60">
                            <h4 class="mb-30">Related Posts</h4>
                            <div class="row g-4">
                                @foreach($relatedPosts as $related)
                                    <div class="col-md-4">
                                        <div class="blog-style-one h-100">
                                            <a class="blog-image w-img" href="{{ route('blog.details', $related->slug) }}">
                                                <img src="{{ $related->featured_image ? asset('storage/' . $related->featured_image) : asset('assets/main/imgs/blog/blog-sidebar-1.jpg') }}"
                                                    alt="{{ $related->title }}"
                                                    style="height: 180px; object-fit: cover; width: 100%;">
                                            </a>
                                            <div class="blog-content p-3">
                                                <h6 class="blog-title"><a wire:navigate.hover
                                                        href="{{ route('blog.details', $related->slug) }}">{{ Str::limit($related->title, 40) }}</a>
                                                </h6>
                                                <span
                                                    class="text-muted small">{{ $related->published_at->format('d M, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- ─── COMMENTS SECTION ───────────────────────────── -->
                    @if($post->allow_comments)
                        @livewire('main.blog.posts.comment-component', ['post' => $post], key($post->id))
                    @else
                        <div class="alert alert-info mt-60">
                            <i class="fa fa-info-circle me-2"></i> Comments are disabled for this post.
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
                                    <form action="{{ route('posts') }}" method="GET">
                                        <input type="text" name="search" placeholder="Search here...">
                                        <button type="submit"><i class="icon-search"></i></button>
                                    </form>
                                </div>
                            </div>

                            <!-- Categories Widget -->
                            <div class="blog-widget-2 mb-30">
                                <h5 class="blog-widget-title p-relative mb-45">Category</h5>
                                <ul class="blog-categories-list">
                                    @foreach($categoriesWithCount as $cat)
                                        <li>
                                            <a href="{{ route('posts') }}?category={{ $cat->slug }}">
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

                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- ── Styles ── --}}
<style>
    /* ─── BLOG CONTENT ─── */
    .blog-content-body {
        font-size: 16px;
        line-height: 1.8;
        color: #4a4a4a;
        overflow-wrap: break-word;
        word-wrap: break-word;
        hyphens: auto;
        max-width: 100%;
    }

    .blog-content-body img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 20px 0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    /* Side-by-side images inside a row */
    .blog-content-body .row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin: 20px 0;
    }

    .blog-content-body .row .col-lg-6 {
        flex: 0 0 calc(50% - 10px);
        max-width: calc(50% - 10px);
    }

    .blog-content-body .row .col-lg-6 img {
        margin: 0;
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .blog-content-body h2,
    .blog-content-body h3,
    .blog-content-body h4 {
        margin-top: 30px;
        margin-bottom: 15px;
        font-weight: 700;
    }

    /* ─── BLOCKQUOTE ─── */
    .blog-content-body blockquote {
        border-left: 4px solid #3b82f6;
        padding: 20px 30px;
        background: #f8fafc;
        margin: 30px 0;
        font-style: italic;
        border-radius: 0 8px 8px 0;
        color: #334155;
        position: relative;
        overflow: hidden;
    }

    .blog-content-body blockquote p {
        margin: 0;
    }

    .blog-content-body blockquote::before {
        content: "\f10e";
        font-family: "Font Awesome 6 Pro", "Font Awesome 5 Pro", "Font Awesome 5 Free", "FontAwesome", sans-serif;
        font-weight: 900;
        position: absolute;
        right: 20px;
        bottom: 20px;
        font-size: 2.2rem;
        color: #3b82f6;
        opacity: 0.25;
        pointer-events: none;
    }

    .blog-content-body ul,
    .blog-content-body ol {
        padding-left: 20px;
        margin-bottom: 20px;
    }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 767px) {
        .blog-content-body .row .col-lg-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .comment-replies {
            margin-left: 0;
            padding-left: 10px;
        }
    }

    /* ─── COMMENT STYLES ─── */
    .comment-item {
        border-bottom: 1px solid #eef2f6;
        padding: 20px 0;
    }

    .comment-item:first-child {
        padding-top: 0;
    }

    .comment-item:last-child {
        border-bottom: none;
    }

    .comment-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .comment-avatar-initials {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #6366f1;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        flex-shrink: 0;
        border: 2px solid #e2e8f0;
    }

    .comment-body {
        flex: 1;
        padding-left: 0;
    }

    .comment-author {
        font-weight: 600;
        color: #0a0a0a;
        margin-right: 10px;
    }

    .comment-date {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .comment-text {
        margin: 6px 0 10px;
        line-height: 1.7;
        color: #334155;
    }

    .comment-actions a {
        font-size: 0.85rem;
        color: #94a3b8;
        text-decoration: none;
        margin-right: 15px;
        transition: color 0.2s;
    }

    .comment-actions a:hover {
        color: #3b82f6;
    }

    .comment-replies {
        margin-left: 60px;
        margin-top: 15px;
        border-left: 2px solid #eef2f6;
        padding-left: 20px;
    }

    .reply-form-inline {
        margin-top: 10px;
        background: #f8fafc;
        padding: 15px;
        border-radius: 8px;
    }
</style>