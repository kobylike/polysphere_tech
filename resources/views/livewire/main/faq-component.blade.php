<div>
    <x-seo.faq-page :faqs="$faqs" />

    <!-- Breadcrumb area start -->
    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb"
            style="background-image: url('{{ asset('assets/main/imgs/resources/faq1.jpg') }}');"></div>
        <div class="breadcrumb__thumb_2"
            style="background-image: url('{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}');"></div>
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

                        {{-- ═══════════════════════════════════════════════ --}}
                        {{-- CHAT BOT HERO — primary CTA at top of FAQ --}}
                        {{-- ═══════════════════════════════════════════════ --}}
                        <div class="faq-chat-hero mb-35 wow fadeInUp" data-wow-delay=".3s">
                            <div class="faq-chat-hero__glow" aria-hidden="true"></div>
                            <div class="faq-chat-hero__content">
                                <div class="faq-chat-hero__avatar" aria-hidden="true">
                                    <i class="fal fa-sparkles"></i>
                                    <span class="faq-chat-hero__status" title="Online now"></span>
                                </div>
                                <div class="faq-chat-hero__body">
                                    <span class="faq-chat-hero__eyebrow">
                                        <i class="fal fa-bolt"></i> Instant answers, 24/7
                                    </span>
                                    <h3 class="faq-chat-hero__title">
                                        Can't find your answer? Ask Sphere.
                                    </h3>
                                    <p class="faq-chat-hero__text">
                                        Our AI assistant is right here on this page. Ask about services,
                                        pricing, timelines, open roles, or anything else — you'll get an
                                        answer in seconds.
                                    </p>
                                    <div class="faq-chat-hero__actions">
                                        <button type="button" onclick="Livewire.dispatch('open-chat-widget')"
                                            class="faq-chat-hero__btn"
                                            aria-label="Chat with Sphere, Polysphere Tech's AI assistant">
                                            <i class="fal fa-comment-dots"></i>
                                            <span>Chat with Sphere</span>
                                            <i class="fal fa-arrow-right faq-chat-hero__btn-arrow"></i>
                                        </button>
                                        <span class="faq-chat-hero__hint">
                                            Or look for the chat bubble in the bottom-right corner
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ─── Section title ─── --}}
                        <div class="title-box mb-25 wow fadeInLeft" data-wow-delay=".5s">
                            <span class="section-sub-title no-border">FAQ</span>
                            <h3 class="section-title mt-10">Frequently Asked Questions?</h3>
                        </div>

                        <!-- Search Bar -->
                        <div class="faq-search-wrap mb-30">
                            <div class="position-relative">
                                <i class="fas fa-search"
                                    style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    placeholder="Search for answers..." class="faq-search-input"
                                    style="width: 100%; padding: 14px 16px 14px 44px; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 15px; outline: none; transition: all 0.3s ease; background: #fff;">
                                @if($search)
                                    <span
                                        style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #94a3b8;">
                                        {{ count($filteredFaqs) }} result{{ count($filteredFaqs) !== 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Category Filters -->
                        <div class="faq-categories-wrap mb-35">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($categories as $cat)
                                    <button wire:click="$set('category', '{{ $cat }}')"
                                        class="faq-category-btn {{ $category === $cat ? 'active' : '' }}"
                                        style="padding: 6px 18px; border-radius: 50px; border: 1px solid #e2e8f0; background: #fff; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s ease;">
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
                                        <i class="fas fa-search"
                                            style="font-size: 48px; color: #e2e8f0; margin-bottom: 16px;"></i>
                                        <h4 style="font-weight: 600; color: #0a0a0a;">No results found</h4>
                                        <p style="color: #6c757d;">Try adjusting your search or filter.</p>
                                        <button wire:click="$set('search', ''); $set('category', 'all')"
                                            class="primary-btn-1 btn-hover"
                                            style="margin-top: 12px; display: inline-block;">
                                            Reset Filters &nbsp; | <i class="icon-right-arrow"></i>
                                            <span style="top: 147.172px; left: 108.5px;"></span>
                                        </button>
                                    </div>
                                @else
                                    <div class="bd-faq-group">
                                        @foreach($filteredFaqs as $faq)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading-{{ $faq['id'] }}">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#collapse-{{ $faq['id'] }}"
                                                        aria-expanded="false" aria-controls="collapse-{{ $faq['id'] }}"
                                                        style="font-weight: 600;">
                                                        {{ $faq['question'] }}
                                                    </button>
                                                </h2>
                                                <div id="collapse-{{ $faq['id'] }}" class="accordion-collapse collapse"
                                                    aria-labelledby="heading-{{ $faq['id'] }}"
                                                    data-bs-parent="#accordionExample-st-2">
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

                <!-- Right Column -->
                <div class="col-xxl-5 col-xl-5 col-lg-5">
                    <figure class="w-img pt-15">
                        <img src="{{ asset('assets/main/imgs/resources/faq-2.jpg') }}" alt="FAQ Illustration">
                    </figure>

                    {{-- ═══════════════════════════════════════════════ --}}
                    {{-- SIDEBAR CARD — Dual CTA: chat or contact --}}
                    {{-- ═══════════════════════════════════════════════ --}}
                    <div class="faq-sidebar-card mt-30 wow fadeInUp" data-wow-delay=".7s">
                        <div class="faq-sidebar-card__header">
                            <div class="faq-sidebar-card__icon" aria-hidden="true">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div>
                                <h4 class="faq-sidebar-card__title">Still have questions?</h4>
                                <p class="faq-sidebar-card__subtitle">Two ways to reach us — pick what's easiest.</p>
                            </div>
                        </div>

                        {{-- Option A: Chat bot --}}
                        <button type="button" onclick="Livewire.dispatch('open-chat-widget')"
                            class="faq-sidebar-card__option faq-sidebar-card__option--chat"
                            aria-label="Chat with Sphere, Polysphere Tech's AI assistant">
                            <div class="faq-sidebar-card__option-icon">
                                <i class="fal fa-comment-dots"></i>
                            </div>
                            <div class="faq-sidebar-card__option-body">
                                <span class="faq-sidebar-card__option-title">Chat with Sphere</span>
                                <span class="faq-sidebar-card__option-desc">AI assistant &middot; instant reply</span>
                            </div>
                            <i class="fal fa-arrow-right faq-sidebar-card__option-arrow"></i>
                        </button>

                        <div class="faq-sidebar-card__or">
                            <span>or</span>
                        </div>

                        {{-- Option B: Contact form --}}
                        <a wire:navigate.hover href="{{ route('contact') }}"
                            class="faq-sidebar-card__option faq-sidebar-card__option--contact"
                            aria-label="Contact the Polysphere Tech team">
                            <div class="faq-sidebar-card__option-icon">
                                <i class="fal fa-envelope"></i>
                            </div>
                            <div class="faq-sidebar-card__option-body">
                                <span class="faq-sidebar-card__option-title">Send us a message</span>
                                <span class="faq-sidebar-card__option-desc">Human follow-up &middot; within 24
                                    hours</span>
                            </div>
                            <i class="fal fa-arrow-right faq-sidebar-card__option-arrow"></i>
                        </a>

                        {{-- Reassurance footer --}}
                        <p class="faq-sidebar-card__footer">
                            <i class="fal fa-clock"></i>
                            Available Mon–Fri, 8:00 AM – 6:00 PM GMT
                        </p>
                    </div>

                    {{-- ═══════════════════════════════════════════════ --}}
                    {{-- QUICK CONTACT STRIP — phone + email --}}
                    {{-- ═══════════════════════════════════════════════ --}}
                    <div class="faq-quick-contact mt-20 wow fadeInUp" data-wow-delay=".9s">
                        <a href="tel:+233597563427" class="faq-quick-contact__item">
                            <i class="fal fa-phone-volume"></i>
                            <div>
                                <span class="faq-quick-contact__label">Call us</span>
                                <span class="faq-quick-contact__value">+233 (59) 756-3427</span>
                            </div>
                        </a>
                        <a href="mailto:contact@polyspheretech.com" class="faq-quick-contact__item">
                            <i class="fal fa-envelope"></i>
                            <div>
                                <span class="faq-quick-contact__label">Email us</span>
                                <span class="faq-quick-contact__value">contact@polyspheretech.com</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- ============================================ --}}
{{-- STYLES --}}
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

    /* ─── Chat bot hero card ─────────────────────────────────── */
    .faq-chat-hero {
        position: relative;
        padding: 28px 28px 26px;
        border-radius: 20px;
        background: linear-gradient(135deg, #0F172A 0%, #312E81 55%, #6366F1 100%);
        color: #fff;
        overflow: hidden;
        box-shadow: 0 20px 50px -18px rgba(49, 46, 129, 0.55),
            0 4px 12px rgba(15, 23, 42, 0.08);
        isolation: isolate;
    }

    .faq-chat-hero__glow {
        position: absolute;
        top: -60%;
        right: -30%;
        width: 480px;
        height: 480px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.18), transparent 65%);
        pointer-events: none;
        z-index: 0;
    }

    .faq-chat-hero__content {
        position: relative;
        z-index: 1;
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .faq-chat-hero__avatar {
        position: relative;
        flex-shrink: 0;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
        box-shadow: 0 10px 24px -8px rgba(139, 92, 246, 0.7),
            inset 0 1px 0 rgba(255, 255, 255, 0.25);
    }

    .faq-chat-hero__status {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #4ade80;
        border: 2.5px solid #1E1B4B;
        box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.25);
        animation: faq-status-pulse 2.4s ease-in-out infinite;
    }

    @keyframes faq-status-pulse {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.25);
        }

        50% {
            transform: scale(1.12);
            box-shadow: 0 0 0 6px rgba(74, 222, 128, 0);
        }
    }

    .faq-chat-hero__body {
        flex: 1;
        min-width: 0;
    }

    .faq-chat-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.09em;
        text-transform: uppercase;
        color: #FDE68A;
        margin-bottom: 8px;
    }

    .faq-chat-hero__eyebrow i {
        font-size: 11px;
    }

    .faq-chat-hero__title {
        font-size: 22px;
        font-weight: 700;
        line-height: 1.3;
        letter-spacing: -0.3px;
        color: #fff;
        margin: 0 0 8px;
    }

    .faq-chat-hero__text {
        font-size: 14.5px;
        line-height: 1.65;
        color: rgba(255, 255, 255, 0.78);
        margin: 0 0 20px;
        max-width: 460px;
    }

    .faq-chat-hero__actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
    }

    .faq-chat-hero__btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 22px;
        background: #ffffff;
        color: #1E1B4B;
        border: 0;
        border-radius: 12px;
        font-size: 14.5px;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.35);
        font-family: inherit;
        line-height: 1;
    }

    .faq-chat-hero__btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px -8px rgba(0, 0, 0, 0.45);
        background: #F8FAFC;
    }

    .faq-chat-hero__btn:focus-visible {
        outline: 3px solid rgba(255, 255, 255, 0.6);
        outline-offset: 3px;
    }

    .faq-chat-hero__btn i {
        font-size: 14px;
    }

    .faq-chat-hero__btn-arrow {
        transition: transform 0.2s ease;
    }

    .faq-chat-hero__btn:hover .faq-chat-hero__btn-arrow {
        transform: translateX(3px);
    }

    .faq-chat-hero__hint {
        font-size: 12.5px;
        color: rgba(255, 255, 255, 0.55);
        line-height: 1.4;
        max-width: 200px;
    }

    /* ─── Sidebar card ──────────────────────────────────────── */
    .faq-sidebar-card {
        background: #f8fafc;
        border-radius: 16px;
        padding: 28px;
        border: 1px solid #eef2f6;
    }

    .faq-sidebar-card__header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
    }

    .faq-sidebar-card__icon {
        flex-shrink: 0;
        width: 48px;
        height: 48px;
        background: rgba(37, 99, 235, 0.08);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #3b82f6;
    }

    .faq-sidebar-card__title {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #0a0a0a;
    }

    .faq-sidebar-card__subtitle {
        font-size: 13.5px;
        color: #6c757d;
        margin: 2px 0 0;
    }

    .faq-sidebar-card__option {
        display: flex;
        align-items: center;
        gap: 14px;
        width: 100%;
        padding: 14px 16px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        text-align: left;
        font-family: inherit;
        transition: all 0.2s ease;
    }

    .faq-sidebar-card__option:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px -14px rgba(37, 99, 235, 0.4);
        color: inherit;
    }

    .faq-sidebar-card__option--chat {
        border-color: rgba(99, 102, 241, 0.25);
        background: linear-gradient(135deg, #EEF2FF 0%, #FFFFFF 60%);
    }

    .faq-sidebar-card__option--chat:hover {
        border-color: #6366f1;
        box-shadow: 0 12px 24px -14px rgba(99, 102, 241, 0.5);
    }

    .faq-sidebar-card__option-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        box-shadow: 0 4px 12px -4px rgba(99, 102, 241, 0.55);
    }

    .faq-sidebar-card__option--contact .faq-sidebar-card__option-icon {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        box-shadow: 0 4px 12px -4px rgba(59, 130, 246, 0.55);
    }

    .faq-sidebar-card__option-body {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .faq-sidebar-card__option-title {
        font-size: 14.5px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .faq-sidebar-card__option-desc {
        font-size: 12.5px;
        color: #94a3b8;
        margin-top: 2px;
        line-height: 1.4;
    }

    .faq-sidebar-card__option-arrow {
        flex-shrink: 0;
        color: #94a3b8;
        font-size: 12px;
        transition: transform 0.2s ease, color 0.2s ease;
    }

    .faq-sidebar-card__option:hover .faq-sidebar-card__option-arrow {
        transform: translateX(3px);
        color: #3b82f6;
    }

    .faq-sidebar-card__or {
        position: relative;
        text-align: center;
        margin: 16px 0;
    }

    .faq-sidebar-card__or::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e2e8f0;
    }

    .faq-sidebar-card__or span {
        position: relative;
        display: inline-block;
        padding: 0 12px;
        background: #f8fafc;
        font-size: 11.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
    }

    .faq-sidebar-card__footer {
        margin: 20px 0 0;
        padding-top: 16px;
        border-top: 1px dashed #e2e8f0;
        font-size: 12.5px;
        color: #94a3b8;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .faq-sidebar-card__footer i {
        color: #3b82f6;
        font-size: 12px;
    }

    /* ─── Quick contact strip ───────────────────────────────── */
    .faq-quick-contact {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .faq-quick-contact__item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #ffffff;
        border: 1px solid #eef2f6;
        border-radius: 12px;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
    }

    .faq-quick-contact__item:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -12px rgba(37, 99, 235, 0.4);
        color: inherit;
    }

    .faq-quick-contact__item i {
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: rgba(37, 99, 235, 0.08);
        color: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    .faq-quick-contact__item>div {
        min-width: 0;
    }

    .faq-quick-contact__label {
        display: block;
        font-size: 11.5px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 600;
        line-height: 1.3;
    }

    .faq-quick-contact__value {
        display: block;
        font-size: 12.5px;
        color: #0f172a;
        font-weight: 600;
        margin-top: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ─── Accordion (preserved) ─────────────────────────────── */
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

    /* ─── Responsive ─────────────────────────────────────────── */
    @media (max-width: 991px) {
        .faq-chat-hero {
            padding: 24px 22px;
        }

        .faq-chat-hero__avatar {
            width: 48px;
            height: 48px;
            font-size: 18px;
        }

        .faq-chat-hero__title {
            font-size: 19px;
        }

        .faq-chat-hero__text {
            font-size: 14px;
        }
    }

    @media (max-width: 767px) {
        .faq-wrapper.pr-80 {
            padding-right: 0 !important;
        }

        .faq-chat-hero__content {
            flex-direction: column;
            gap: 16px;
        }

        .faq-chat-hero__title {
            font-size: 18px;
        }

        .faq-chat-hero__actions {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }

        .faq-chat-hero__btn {
            justify-content: center;
        }

        .faq-chat-hero__hint {
            max-width: none;
            text-align: center;
        }

        .faq-quick-contact {
            grid-template-columns: 1fr;
        }

        .accordion-button {
            font-size: 14px !important;
            padding: 14px 18px !important;
        }

        .accordion-body {
            font-size: 14px !important;
            padding: 0 18px 18px 18px !important;
        }

        .faq-sidebar-card {
            padding: 22px 20px;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .faq-chat-hero__status,
        .faq-chat-hero__btn,
        .faq-sidebar-card__option,
        .faq-sidebar-card__option-arrow {
            animation: none !important;
            transition: none !important;
        }
    }
</style>