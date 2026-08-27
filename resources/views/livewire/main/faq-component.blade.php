<div>

    <!-- Breadcrumb area start -->
    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb" data-background="{{ asset('assets/main/imgs/resources/faq1.jpg') }}"></div>
        <div class="breadcrumb__thumb_2" data-background="{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">FAQ</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>FAQ</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb area end -->

    <section class="faq-page-section section-space">
        <div class="small-container">
            <div class="row">
                <div class="col-xxl-7 col-xl-7 col-lg-7">
                    <div class="faq-wrapper pr-80">
                        <div class="title-box mb-25 wow fadeInLeft" data-wow-delay=".5s">
                            <span class="section-sub-title no-border">FAQ</span>
                            <h3 class="section-title mt-10">Frequently Asked Questions?</h3>
                        </div>

                        <!-- Search Bar (NEW) -->
                        <div class="faq-search-wrap mb-30">
                            <div class="position-relative">
                                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="Search for answers..."
                                    class="faq-search-input"
                                    style="width: 100%; padding: 14px 16px 14px 44px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; transition: all 0.3s ease; background: #fff;"
                                >
                                @if($search)
                                    <span style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #94a3b8;">
                                        {{ count($filteredFaqs) }} result{{ count($filteredFaqs) !== 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Category Filters (NEW) -->
                        <div class="faq-categories-wrap mb-35">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($categories as $cat)
                                    <button
                                        wire:click="$set('category', '{{ $cat }}')"
                                        class="faq-category-btn {{ $category === $cat ? 'active' : '' }}"
                                        style="padding: 6px 18px; border-radius: 50px; border: 1px solid #e2e8f0; background: #fff; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s ease;"
                                    >
                                        {{ $cat === 'all' ? 'All' : $cat }}
                                        <span style="font-weight: normal; color: #94a3b8;">
                                            ({{ $cat === 'all' ? count($faqs) : collect($faqs)->where('category', $cat)->count() }})
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Accordion -->
                        <div class="bd-faq">
                            <div class="accordion" id="accordionExample-st-2">

                                @if(count($filteredFaqs) === 0)
                                    <div class="text-center py-5">
                                        <i class="fas fa-search" style="font-size: 48px; color: #e2e8f0; margin-bottom: 16px;"></i>
                                        <h4 style="font-weight: 600; color: #0a0a0a;">No results found</h4>
                                        <p style="color: #6c757d;">Try adjusting your search or filter.</p>
                                        <button wire:click="$set('search', ''); $set('category', 'all')" class="primary-btn-1 btn-hover" style="margin-top: 12px; display: inline-block;">
                                            Reset Filters &nbsp; | <i class="icon-right-arrow"></i>
                                            <span style="top: 147.172px; left: 108.5px;"></span>
                                        </button>
                                    </div>
                                @else
                                    <div class="bd-faq-group">
                                        @foreach($filteredFaqs as $faq)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading-{{ $faq['id'] }}">
                                                    <button
                                                        class="accordion-button collapsed"
                                                        type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapse-{{ $faq['id'] }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapse-{{ $faq['id'] }}"
                                                        style="font-weight: 600;"
                                                    >
                                                        {{ $faq['question'] }}
                                                    </button>
                                                </h2>
                                                <div
                                                    id="collapse-{{ $faq['id'] }}"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="heading-{{ $faq['id'] }}"
                                                    data-bs-parent="#accordionExample-st-2"
                                                >
                                                    <div class="accordion-body">
                                                        {{ $faq['answer'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Image (kept as is) -->
                <div class="col-xxl-5 col-xl-5 col-lg-5">
                    <figure class="w-img pt-15">
                        <img src="{{ asset('assets/main/imgs/resources/faq-2.jpg') }}" alt="FAQ Illustration">
                    </figure>

                    <!-- Contact Card (NEW) -->
                    <div class="faq-sidebar-card mt-30" style="background: #f8fafc; border-radius: 16px; padding: 32px; border: 1px solid #eef2f6;">
                        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                            <div style="width: 48px; height: 48px; background: rgba(37,99,235,0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #3b82f6;">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 18px; font-weight: 700; margin: 0; color: #0a0a0a;">Still have questions?</h4>
                                <p style="font-size: 14px; color: #6c757d; margin: 0;">We're here to help</p>
                            </div>
                        </div>
                        <p style="font-size: 15px; color: #64748b; line-height: 1.7; margin-bottom: 20px;">
                            Can't find what you're looking for? Reach out to us directly — we'll get back to you within 24 hours.
                        </p>
                        <a wire:navigate.hover href="{{ route('contact') }}" class="primary-btn-1 btn-hover" style="display: inline-block; width: 100%; text-align: center;">
                            Contact Us &nbsp; | <i class="icon-right-arrow"></i>
                            <span style="top: 147.172px; left: 108.5px;"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

{{-- ============================================ --}}
{{-- STYLES                                       --}}
{{-- ============================================ --}}
<style>
    .faq-category-btn.active {
        border-color: #3b82f6 !important;
        background: rgba(37, 99, 235, 0.08) !important;
        color: #3b82f6 !important;
    }
    .faq-category-btn:hover {
        border-color: #3b82f6;
        color: #0a0a0a;
    }
    .faq-search-input:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    /* Keep your existing accordion styles intact */
    .accordion-item {
        border: 1px solid #eef2f6;
        border-radius: 12px !important;
        margin-bottom: 12px;
        overflow: hidden;
    }
    .accordion-button {
        padding: 18px 24px;
        font-weight: 600;
        font-size: 16px;
        color: #0a0a0a;
        background: #ffffff;
        border: none;
        border-radius: 12px !important;
        box-shadow: none !important;
    }
    .accordion-button:not(.collapsed) {
        color: #3b82f6;
        background: #f8fafc;
    }
    .accordion-button:focus {
        box-shadow: none !important;
        border-color: transparent !important;
    }
    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%233b82f6'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E") !important;
    }
    .accordion-body {
        padding: 0 24px 24px 24px;
        font-size: 15px;
        line-height: 1.8;
        color: #64748b;
    }
    @media (max-width: 767px) {
        .faq-wrapper.pr-80 {
            padding-right: 0 !important;
        }
        .accordion-button {
            font-size: 14px !important;
            padding: 14px 18px !important;
        }
        .accordion-body {
            font-size: 14px !important;
            padding: 0 18px 18px 18px !important;
        }
    }
</style>