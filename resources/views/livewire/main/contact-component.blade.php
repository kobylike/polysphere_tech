<div>

    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb"
            data-background="{{ asset('assets/main/imgs/resources/page-title-bg-1.png.jpg') }}">
        </div>
        <div class="breadcrumb__thumb_2"
            data-background="{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Contact Us</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>Contact Us</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--
        No Alpine anywhere in this component anymore. The toast and the
        submit-button loading state are both driven purely by Livewire's
        own server state / wire:loading, so there's no separate client-side
        JS state that can ever drift out of sync with what the server
        actually knows.
    --}}
    <script>
        document.addEventListener('livewire:navigated', () => {
            // Fires on every navigation Livewire handles, including a
            // browser back/forward restore of a cached page snapshot.
            // Broadcasting this makes sure the component never shows a
            // stale "success" state left over from a previous visit.
            window.Livewire.dispatch('contact-form-reset');
        });
    </script>

    <section class="contact-page-section section-space">
        <div class="small-container">
            <div class="row g-4">

                {{-- LEFT: Contact Info --}}
                <div class="col-xxl-4 col-xl-4 col-lg-4">
                    <div class="contact-p-info-area">

                        {{-- Success Toast: plain server-driven markup, no client-side state --}}
                        @if ($success)
                            <div
                                wire:key="contact-success-toast"
                                wire:poll.6000ms="dismissSuccess"
                                class="alert alert-success d-flex align-items-center gap-3 rounded-3 mb-30 ps-toast-in"
                                role="alert"
                            >
                                <i class="fas fa-check-circle fa-lg" style="color: #16a34a;"></i>
                                <div>
                                    <strong>Message sent!</strong> We'll get back to you within 24 hours.
                                </div>
                                <button
                                    type="button"
                                    class="btn-close ms-auto"
                                    wire:click="dismissSuccess"
                                    aria-label="Close"
                                ></button>
                            </div>
                        @endif

                        <!-- Location -->
                        <div class="contact-box mb-30 ps-contact-card">
                            <div class="icon-1 ps-contact-card__icon">
                                <i class="fat fa-location-dot"></i>
                            </div>
                            <div class="info">
                                <span class="ps-contact-card__label">Our Location</span>
                                <h4 class="ps-contact-card__value">Accra, Ghana</h4>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="contact-box mb-30 ps-contact-card">
                            <div class="icon-1 ps-contact-card__icon">
                                <i class="fat fa-phone-volume"></i>
                            </div>
                            <div class="info">
                                <span class="ps-contact-card__label">Call Us 24/7</span>233597563427
                                <h4 class="ps-contact-card__value"><a href="tel:+233597563427">+233 (59) 756-3427</a></h4>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="contact-box ps-contact-card">
                            <div class="icon-1 ps-contact-card__icon">
                                <i class="fat fa-envelope"></i>
                            </div>
                            <div class="info">
                                <span class="ps-contact-card__label">Email Us</span>
                                <h4 class="ps-contact-card__value"><a href="mailto:contact@polyspheretech.com">contact@polyspheretech.com</a></h4>
                            </div>
                        </div>

                        <!-- Trust Badge -->
                        <div class="ps-trust-badge mt-30">
                            <div class="d-flex align-items-center gap-3">
                                <span class="ps-pulse-dot-small" aria-hidden="true"></span>
                                <span style="font-size: 14px; color: #6c757d;">We respond within <strong style="color: #0a0a0a;">24 hours</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Contact Form --}}
                <div class="col-xxl-8 col-xl-8 col-lg-8">
                    <div class="contact-page-form-area ps-form-card">

                        <div class="title-box mb-40 wow fadeInLeft" data-wow-delay=".5s">
                            <span class="section-sub-title">LET'S TALK</span>
                            <h3 class="section-title mt-10">Let's Get in Touch</h3>
                            <p class="mt-15" style="font-size: 15px; color: #6c757d; line-height: 1.7;">
                                Have a project in mind? Need a custom software solution? We'd love to hear from you.
                                Fill out the form below and our team will get back to you within 24 hours.
                            </p>
                        </div>

                        <div class="contact-page-form">
                            <form
                                wire:submit.prevent="submit"
                                novalidate
                            >
                                {{-- Honeypot -- hidden from real users via CSS, removed
                                     from tab order, marked aria-hidden. Name/id/label
                                     deliberately avoid the word "website" since Chrome's
                                     autofill heuristics match that field name against
                                     saved address/business profiles and will silently
                                     fill it in even with autocomplete="off". --}}
                                <div style="position:absolute; left:-9999px; width:1px; height:1px; overflow:hidden;" aria-hidden="true">
                                    <label for="ps-hp-field-home">Leave this field blank</label>
                                    <input
                                        type="text"
                                        id="ps-hp-field-home"
                                        name="ps_hp_field_home_x92"
                                        wire:model="website"
                                        tabindex="-1"
                                        autocomplete="off"
                                    >
                                </div>

                                <div class="row g-3">

                                    <!-- Name -->
                                    <div class="col-lg-6">
                                        <div class="ps-form-group">
                                            <label class="ps-form-label">Your Name <span class="ps-required">*</span></label>
                                            <div class="ps-form-field @error('name') ps-form-field--error @enderror">
                                                <span class="ps-form-icon" aria-hidden="true">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                                <input
                                                    type="text"
                                                    wire:model.live.debounce.400ms="name"
                                                    placeholder="Enter your full name"
                                                    class="ps-form-input"
                                                    autocomplete="name"
                                                    required
                                                >
                                            </div>
                                            @error('name')
                                                <p class="ps-form-error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-lg-6">
                                        <div class="ps-form-group">
                                            <label class="ps-form-label">Your Email <span class="ps-required">*</span></label>
                                            <div class="ps-form-field @error('email') ps-form-field--error @enderror">
                                                <span class="ps-form-icon" aria-hidden="true">
                                                    <i class="fas fa-envelope"></i>
                                                </span>
                                                <input
                                                    type="email"
                                                    wire:model.live.debounce.400ms="email"
                                                    placeholder="Enter your email address"
                                                    class="ps-form-input"
                                                    autocomplete="email"
                                                    required
                                                >
                                            </div>
                                            @error('email')
                                                <p class="ps-form-error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Subject -->
                                    <div class="col-lg-6">
                                        <div class="ps-form-group">
                                            <label class="ps-form-label">Subject <span class="ps-required">*</span></label>
                                            <div class="ps-form-field @error('subject') ps-form-field--error @enderror">
                                                <span class="ps-form-icon" aria-hidden="true">
                                                    <i class="fas fa-tag"></i>
                                                </span>
                                                <input
                                                    type="text"
                                                    wire:model.live.debounce.400ms="subject"
                                                    placeholder="What is this regarding?"
                                                    class="ps-form-input"
                                                    autocomplete="off"
                                                    required
                                                >
                                            </div>
                                            @error('subject')
                                                <p class="ps-form-error">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Category Pills -->
                                    <div class="col-lg-6">
                                        <div class="ps-form-group">
                                            <label class="ps-form-label">Category</label>
                                            <div class="ps-category-pills">
                                                @foreach(['General', 'Billing', 'Technical', 'Partnership'] as $cat)
                                                <button
                                                    type="button"
                                                    wire:click="setCategory('{{ $cat }}')"
                                                    class="ps-category-pill {{ $category === $cat ? 'ps-category-pill--active' : '' }}"
                                                >
                                                    {{ $cat }}
                                                </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Message -->
                                    <div class="col-lg-12">
                                        <div class="ps-form-group">
                                            <label class="ps-form-label">Your Message <span class="ps-required">*</span></label>
                                            <div class="ps-form-field ps-form-field--textarea @error('message') ps-form-field--error @enderror">
                                                <span class="ps-form-icon ps-form-icon--textarea" aria-hidden="true">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </span>
                                                <textarea
                                                    wire:model.live.debounce.400ms="message"
                                                    rows="5"
                                                    placeholder="Tell us about your project, requirements, or any questions you have..."
                                                    class="ps-form-input ps-form-input--textarea"
                                                    maxlength="5000"
                                                    required
                                                ></textarea>
                                            </div>
                                            <div class="ps-form-footer">
                                                @error('message')
                                                    <p class="ps-form-error">{{ $message }}</p>
                                                @else
                                                    <span></span>
                                                @enderror
                                                <span class="ps-char-count">{{ strlen($this->message) }} / 5000</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Privacy Note -->
                                    <div class="col-lg-12">
                                        <p class="ps-privacy-note">
                                            <i class="fas fa-lock" aria-hidden="true"></i>
                                            Your information is safe with us. We'll never share your data.
                                            <a href="#" class="ps-privacy-link">Privacy Policy</a>
                                        </p>
                                    </div>

                                 <!-- Submit Button -->
<div class="col-lg-12">
    <button
        type="submit"
        class="ps-submit-btn primary-btn-1 w-100"
        wire:loading.attr="disabled"
        wire:loading.class="ps-submit-btn--loading"
        wire:target="submit"
    >
        <span class="ps-btn-inner" wire:loading.remove wire:target="submit">
            Send Message &nbsp; <i class="icon-right-arrow"></i>
        </span>
        <span
            class="ps-btn-inner"
            wire:loading
            wire:target="submit"
            style="display:none;"
        >
            <i class="fas fa-spinner fa-spin"></i> Sending…
        </span>
    </button>
</div>

                                </div>
                            </form>
                        </div>

                        <script>
                            // Sets a per-render timestamp used server-side to reject
                            // bot submissions that fire faster than a human possibly
                            // could. Plain Livewire JS API, no Alpine required.
                            (function () {
                                function psSetRenderedAt() {
                                    @this.set('renderedAt', Date.now().toString());
                                }
                                document.addEventListener('livewire:initialized', psSetRenderedAt);
                                document.addEventListener('livewire:navigated', psSetRenderedAt);
                            })();
                        </script>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <style>
        .ps-contact-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            padding: 20px 24px;
            background: var(--surface, #ffffff);
            border: 1px solid rgba(0,0,0,0.06);
        }
        .ps-contact-card:hover {
            border-color: rgba(37, 99, 235, 0.2);
            box-shadow: 0 8px 30px -12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .ps-contact-card__icon {
            width: 48px;
            height: 48px;
            background: rgba(37, 99, 235, 0.08);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 20px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        .ps-contact-card:hover .ps-contact-card__icon {
            background: rgba(37, 99, 235, 0.15);
            transform: scale(1.05);
        }
        .ps-contact-card__label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6c757d;
            font-weight: 600;
            display: block;
            margin-bottom: 2px;
        }
        .ps-contact-card__value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary, #0a0a0a);
            margin: 0;
            line-height: 1.4;
        }
        .ps-contact-card__value a {
            color: var(--text-primary, #0a0a0a);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .ps-contact-card__value a:hover {
            color: #3b82f6;
        }

        .ps-trust-badge {
            padding: 12px 20px;
            background: rgba(34, 197, 94, 0.06);
            border-radius: 12px;
            border: 1px solid rgba(34, 197, 94, 0.1);
        }
        .ps-pulse-dot-small {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: ps-pulse 1.8s ease-in-out infinite;
            box-shadow: 0 0 12px rgba(34, 197, 94, 0.3);
        }
        @keyframes ps-pulse {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.7); }
            100% { opacity: 1; transform: scale(1); }
        }

        .ps-form-card {
            padding: 0 10px;
        }

        .ps-form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .ps-form-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary, #0a0a0a);
        }
        .ps-required {
            color: #f43f5e;
        }

        .ps-form-field {
            position: relative;
            display: flex;
            align-items: center;
            background: var(--surface, #ffffff);
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .ps-form-field:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .ps-form-field--error {
            border-color: #f43f5e;
        }
        .ps-form-field--error:focus-within {
            border-color: #f43f5e;
            box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.1);
        }
        .ps-form-field--textarea {
            align-items: flex-start;
        }

        .ps-form-icon {
            flex: 0 0 42px;
            width: 42px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 14px;
            line-height: 1;
            transition: color 0.3s ease;
        }
        .ps-form-field:focus-within .ps-form-icon {
            color: #3b82f6;
        }
        .ps-form-icon--textarea {
            height: 46px;
            padding-top: 2px;
        }

        .ps-form-input {
            flex: 1 1 auto;
            min-width: 0;
            padding: 12px 14px 12px 0;
            border: none;
            background: transparent;
            font-size: 15px;
            color: var(--text-primary, #0a0a0a);
            outline: none;
            border-radius: 10px;
            font-family: inherit;
        }
        .ps-form-input::placeholder {
            color: #94a3b8;
        }
        .ps-form-input--textarea {
            padding: 14px 14px 14px 0;
            resize: vertical;
            min-height: 120px;
        }

        .ps-form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 24px;
            margin-top: 4px;
        }
        .ps-char-count {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .ps-char-count--warn {
            color: #f59e0b;
            font-weight: 600;
        }

        .ps-form-error {
            font-size: 13px;
            color: #f43f5e;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .ps-category-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .ps-category-pill {
            padding: 6px 16px;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            background: var(--surface, #ffffff);
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .ps-category-pill:hover {
            border-color: #3b82f6;
            color: #0a0a0a;
        }
        .ps-category-pill--active {
            border-color: #3b82f6;
            background: rgba(37, 99, 235, 0.08);
            color: #3b82f6;
        }

        .ps-privacy-note {
            font-size: 13px;
            color: #6c757d;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .ps-privacy-note i {
            color: #3b82f6;
        }
        .ps-privacy-link {
            color: #3b82f6;
            text-decoration: underline;
            text-underline-offset: 2px;
            transition: color 0.3s ease;
        }
        .ps-privacy-link:hover {
            color: #1d4ed8;
        }

        .ps-submit-btn {
            display: block;
            position: relative;
            width: 100%;
            box-sizing: border-box;
            height: 56px;
            padding: 0 32px;
            font-size: 16px;
            font-weight: 600;
            line-height: 1.2;
            color: #ffffff !important;
            background: #2563eb;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
        }
        .ps-submit-btn .ps-btn-inner {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            height: 100%;
        }
        .ps-submit-btn:hover {
            background: #000000;
            transform: translateY(-2px);
            box-shadow: 0 8px 30px -8px rgba(0, 0, 0, 0.4);
        }
        .ps-submit-btn:disabled,
        .ps-submit-btn--loading {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Plain CSS entrance animation, replayed automatically by the browser
           every time this element is freshly inserted into the DOM — no JS
           transition library needed. */
        @keyframes ps-toast-in {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .ps-toast-in {
            animation: ps-toast-in 0.3s ease;
        }

        @media (max-width: 767px) {
            .ps-contact-card { padding: 16px 20px; }
            .ps-contact-card__icon { width: 40px; height: 40px; font-size: 16px; }
            .ps-form-card { padding: 0; }
            .ps-form-input { font-size: 14px; }
        }
    </style>

    <div class="container-fluid g-0 fix">
        <div class="row">
            <div class="col-xxl-12">
                <div class="contact-map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d4216.433331900906!2d90.36996032419312!3d23.83718617432321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sbd!4v1693682874850!5m2!1sen!2sbd"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        style="width: 100%; height: 450px; border: 0;"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>

</div>