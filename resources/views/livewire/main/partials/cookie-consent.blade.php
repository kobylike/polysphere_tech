<div>
    @if($showBanner)
        <div class="ps-cc-banner" role="region" aria-label="Cookie consent">
            <div class="ps-cc-banner__icon">
                <i class="fal fa-cookie-bite"></i>
            </div>

            <div class="ps-cc-banner__body">
                <h5>We value your privacy</h5>
                <p>
                    We use cookies to keep the site running, understand how it's used, and improve your experience.
                    You can accept everything, reject everything non-essential, or pick exactly what you're comfortable
                    with.
                    <a wire:navigate.hover href="{{ route('privacy') }}#cookies" class="ps-cc-link">Cookie Policy</a>

                </p>
            </div>

            <div class="ps-cc-banner__actions">
                <button type="button" class="ps-cc-btn ps-cc-btn--ghost" wire:click="openModal">
                    <i class="fal fa-sliders-h"></i> Customize
                </button>
                <button type="button" class="ps-cc-btn ps-cc-btn--outline" wire:click="rejectAll">
                    Reject All
                </button>
                <button type="button" class="ps-cc-btn ps-cc-btn--primary" wire:click="acceptAll">
                    Accept All
                </button>
            </div>
        </div>
    @endif

    @if($showModal)
        <div class="ps-cc-overlay" wire:click.self="closeModal">
            <div class="ps-cc-modal" role="dialog" aria-modal="true" aria-labelledby="ps-cc-modal-title">
                <div class="ps-cc-modal__handle" aria-hidden="true"></div>
                <div class="ps-cc-modal__header">
                    <div class="ps-cc-modal__title" id="ps-cc-modal-title">
                        <i class="fal fa-shield-check"></i>
                        <span>Cookie Preferences</span>
                    </div>
                    <button type="button" class="ps-cc-modal__close" wire:click="closeModal" aria-label="Close">
                        <i class="fal fa-times"></i>
                    </button>
                </div>

                <div class="ps-cc-modal__body">
                    <p class="ps-cc-modal__intro">
                        Choose which categories of cookies you're happy for us to use. Strictly necessary cookies
                        can't be switched off since the site won't work properly without them.
                    </p>

                    {{-- Necessary --}}
                    <div class="ps-cc-row">
                        <div class="ps-cc-row__text">
                            <h6>Strictly Necessary</h6>
                            <p>Required for core features like navigation, login, and security. Always active.</p>
                        </div>
                        <label class="ps-cc-switch ps-cc-switch--locked">
                            <input type="checkbox" checked disabled>
                            <span class="ps-cc-switch__track"><span class="ps-cc-switch__thumb"></span></span>
                        </label>
                    </div>

                    {{-- Functional --}}
                    <div class="ps-cc-row">
                        <div class="ps-cc-row__text">
                            <h6>Functional</h6>
                            <p>Remembers your preferences (language, region, saved settings) for a smoother visit.</p>
                        </div>
                        <label class="ps-cc-switch">
                            <input type="checkbox" @checked($categories['functional'])
                                wire:click="toggleCategory('functional')">
                            <span class="ps-cc-switch__track"><span class="ps-cc-switch__thumb"></span></span>
                        </label>
                    </div>

                    {{-- Analytics --}}
                    <div class="ps-cc-row">
                        <div class="ps-cc-row__text">
                            <h6>Analytics</h6>
                            <p>Helps us understand how the site is used so we can improve it. Data is aggregated.</p>
                        </div>
                        <label class="ps-cc-switch">
                            <input type="checkbox" @checked($categories['analytics'])
                                wire:click="toggleCategory('analytics')">
                            <span class="ps-cc-switch__track"><span class="ps-cc-switch__thumb"></span></span>
                        </label>
                    </div>

                    {{-- Marketing --}}
                    <div class="ps-cc-row">
                        <div class="ps-cc-row__text">
                            <h6>Marketing</h6>
                            <p>Used to show you relevant ads and measure campaign performance across sites.</p>
                        </div>
                        <label class="ps-cc-switch">
                            <input type="checkbox" @checked($categories['marketing'])
                                wire:click="toggleCategory('marketing')">
                            <span class="ps-cc-switch__track"><span class="ps-cc-switch__thumb"></span></span>
                        </label>
                    </div>
                </div>

                <div class="ps-cc-modal__footer">
                    <button type="button" class="ps-cc-btn ps-cc-btn--outline" wire:click="rejectAll">
                        Reject All
                    </button>
                    <div class="ps-cc-modal__footer-right">
                        <button type="button" class="ps-cc-btn ps-cc-btn--outline" wire:click="savePreferences">
                            Save Preferences
                        </button>
                        <button type="button" class="ps-cc-btn ps-cc-btn--primary" wire:click="acceptAll">
                            Accept All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Small persistent launcher so people can reopen their preferences any time --}}
    @if($hasConsented && !$showBanner && !$showModal)
        <button type="button" class="ps-cc-launcher" wire:click="openModal" aria-label="Manage cookie preferences">
            <i class="fal fa-cookie-bite"></i>
        </button>
    @endif

    <style>
        :root {
            --ps-cc-primary: #3b82f6;
            --ps-cc-primary-dark: #2563eb;
            --ps-cc-accent: #6366f1;
            --ps-cc-ink: #0a0a0a;
            --ps-cc-body: #334155;
            --ps-cc-muted: #94a3b8;
            --ps-cc-border: #e2e8f0;
            --ps-cc-surface: #ffffff;
            --ps-cc-safe-b: env(safe-area-inset-bottom, 0px);
            --ps-cc-safe-l: env(safe-area-inset-left, 0px);
            --ps-cc-safe-r: env(safe-area-inset-right, 0px);
        }

        body.ps-cc-lock-scroll {
            overflow: hidden;
        }

        /* ---------- Banner ---------- */
        .ps-cc-banner {
            position: fixed;
            left: max(16px, var(--ps-cc-safe-l));
            right: max(16px, var(--ps-cc-safe-r));
            bottom: calc(16px + var(--ps-cc-safe-b));
            z-index: 99999;
            display: flex;
            align-items: center;
            gap: clamp(12px, 3vw, 20px);
            max-width: 1100px;
            margin: 0 auto;
            padding: clamp(16px, 3vw, 22px) clamp(18px, 3.5vw, 26px);
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid var(--ps-cc-border);
            border-radius: 16px;
            box-shadow: 0 20px 45px rgba(10, 10, 10, 0.15);
            animation: ps-cc-rise 0.35s ease;
        }

        @keyframes ps-cc-rise {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .ps-cc-banner__icon {
            flex-shrink: 0;
            width: clamp(40px, 8vw, 52px);
            height: clamp(40px, 8vw, 52px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(17px, 3vw, 22px);
            color: #fff;
            background: linear-gradient(135deg, var(--ps-cc-primary), var(--ps-cc-accent));
        }

        .ps-cc-banner__body {
            flex: 1;
            min-width: 0;
        }

        .ps-cc-banner__body h5 {
            margin: 0 0 5px;
            font-size: clamp(15px, 2.5vw, 17px);
            font-weight: 700;
            color: var(--ps-cc-ink);
        }

        .ps-cc-banner__body p {
            margin: 0;
            font-size: clamp(12.5px, 2.2vw, 14px);
            line-height: 1.55;
            color: var(--ps-cc-body);
        }

        .ps-cc-link {
            color: var(--ps-cc-primary);
            text-decoration: underline;
            white-space: nowrap;
        }

        .ps-cc-link:hover {
            color: var(--ps-cc-primary-dark);
        }

        .ps-cc-banner__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            flex-shrink: 0;
        }

        .ps-cc-btn {
            border: none;
            border-radius: 8px;
            padding: 12px 18px;
            min-height: 44px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
            -webkit-tap-highlight-color: transparent;
        }

        .ps-cc-btn--primary {
            background: var(--ps-cc-primary);
            color: #fff;
        }

        .ps-cc-btn--primary:hover,
        .ps-cc-btn--primary:active {
            background: var(--ps-cc-primary-dark);
        }

        .ps-cc-btn--outline {
            background: #fff;
            color: var(--ps-cc-ink);
            border: 1px solid var(--ps-cc-border);
        }

        .ps-cc-btn--outline:hover,
        .ps-cc-btn--outline:active {
            border-color: var(--ps-cc-primary);
            color: var(--ps-cc-primary);
        }

        .ps-cc-btn--ghost {
            background: transparent;
            color: var(--ps-cc-body);
        }

        .ps-cc-btn--ghost:hover,
        .ps-cc-btn--ghost:active {
            color: var(--ps-cc-primary);
        }

        /* ---------- Modal ---------- */
        .ps-cc-overlay {
            position: fixed;
            inset: 0;
            z-index: 100000;
            background: rgba(10, 10, 10, 0.55);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: ps-cc-fade 0.2s ease;
        }

        @keyframes ps-cc-fade {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .ps-cc-modal {
            width: 100%;
            max-width: 560px;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            background: var(--ps-cc-surface);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(10, 10, 10, 0.35);
            animation: ps-cc-pop 0.25s ease;
        }

        @keyframes ps-cc-pop {
            from {
                opacity: 0;
                transform: scale(0.96);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .ps-cc-modal__handle {
            display: none;
        }

        .ps-cc-modal__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 22px;
            border-bottom: 1px solid var(--ps-cc-border);
            flex-shrink: 0;
        }

        .ps-cc-modal__title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: clamp(15.5px, 2.5vw, 17px);
            font-weight: 700;
            color: var(--ps-cc-ink);
        }

        .ps-cc-modal__title i {
            color: var(--ps-cc-primary);
        }

        .ps-cc-modal__close {
            border: none;
            background: transparent;
            font-size: 18px;
            color: var(--ps-cc-muted);
            cursor: pointer;
            line-height: 1;
            padding: 10px;
            margin: -10px;
        }

        .ps-cc-modal__close:hover {
            color: var(--ps-cc-ink);
        }

        .ps-cc-modal__body {
            padding: 18px 22px;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .ps-cc-modal__intro {
            font-size: 13.5px;
            color: var(--ps-cc-body);
            line-height: 1.6;
            margin: 0 0 16px;
        }

        .ps-cc-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid var(--ps-cc-border);
        }

        .ps-cc-row:last-child {
            border-bottom: none;
        }

        .ps-cc-row__text h6 {
            margin: 0 0 4px;
            font-size: 14.5px;
            font-weight: 700;
            color: var(--ps-cc-ink);
        }

        .ps-cc-row__text p {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            color: var(--ps-cc-muted);
        }

        .ps-cc-switch {
            position: relative;
            flex-shrink: 0;
            width: 46px;
            height: 26px;
            display: inline-block;
            touch-action: manipulation;
        }

        .ps-cc-switch input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            cursor: pointer;
            z-index: 1;
        }

        .ps-cc-switch__track {
            position: absolute;
            inset: 0;
            background: var(--ps-cc-border);
            border-radius: 999px;
            transition: background 0.2s ease;
        }

        .ps-cc-switch__thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            background: #fff;
            border-radius: 50%;
            transition: transform 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        }

        .ps-cc-switch input:checked+.ps-cc-switch__track {
            background: var(--ps-cc-primary);
        }

        .ps-cc-switch input:checked+.ps-cc-switch__track .ps-cc-switch__thumb {
            transform: translateX(20px);
        }

        .ps-cc-switch--locked .ps-cc-switch__track {
            cursor: not-allowed;
            opacity: 0.9;
        }

        .ps-cc-switch--locked input {
            cursor: not-allowed;
        }

        .ps-cc-modal__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding: 16px 22px;
            padding-bottom: calc(16px + var(--ps-cc-safe-b));
            border-top: 1px solid var(--ps-cc-border);
            background: #f8fafc;
            flex-shrink: 0;
        }

        .ps-cc-modal__footer-right {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ---------- Launcher ---------- */
        .ps-cc-launcher {
            position: fixed;
            left: max(16px, var(--ps-cc-safe-l));
            bottom: calc(16px + var(--ps-cc-safe-b));
            z-index: 99998;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: none;
            color: #fff;
            font-size: 18px;
            background: linear-gradient(135deg, var(--ps-cc-primary), var(--ps-cc-accent));
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }

        .ps-cc-launcher:hover {
            transform: translateY(-2px);
        }

        /* ---------- Tablet ---------- */
        @media (max-width: 900px) {
            .ps-cc-banner {
                max-width: 92%;
            }
        }

        /* ---------- Mobile: banner stacks, modal becomes a bottom sheet ---------- */
        @media (max-width: 640px) {
            .ps-cc-banner {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
                left: 12px;
                right: 12px;
                bottom: calc(12px + var(--ps-cc-safe-b));
                padding: 18px 16px;
                gap: 12px;
            }

            .ps-cc-banner__icon {
                margin: 0 auto;
            }

            .ps-cc-banner__actions {
                flex-direction: column;
            }

            .ps-cc-banner__actions .ps-cc-btn {
                width: 100%;
            }

            .ps-cc-overlay {
                align-items: flex-end;
                padding: 0;
            }

            .ps-cc-modal {
                max-width: 100%;
                width: 100%;
                max-height: 90vh;
                border-radius: 20px 20px 0 0;
                padding-bottom: var(--ps-cc-safe-b);
                animation: ps-cc-sheet-up 0.3s ease;
            }

            @keyframes ps-cc-sheet-up {
                from {
                    transform: translateY(100%);
                }

                to {
                    transform: translateY(0);
                }
            }

            .ps-cc-modal__handle {
                display: block;
                width: 40px;
                height: 4px;
                border-radius: 999px;
                background: var(--ps-cc-border);
                margin: 10px auto 2px;
                flex-shrink: 0;
            }

            .ps-cc-modal__footer {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .ps-cc-modal__footer .ps-cc-btn {
                width: 100%;
            }

            .ps-cc-modal__footer-right {
                flex-direction: column;
                width: 100%;
            }

            .ps-cc-launcher {
                width: 44px;
                height: 44px;
                font-size: 16px;
            }
        }

        /* ---------- Very small phones ---------- */
        @media (max-width: 360px) {
            .ps-cc-banner__body p {
                font-size: 12px;
            }
        }

        /* ---------- Respect reduced-motion preferences ---------- */
        @media (prefers-reduced-motion: reduce) {

            .ps-cc-banner,
            .ps-cc-overlay,
            .ps-cc-modal {
                animation: none !important;
            }
        }
    </style>

    @push('scripts')
        <script>
            (function () {
                const STORAGE_KEY = 'ps_cookie_consent';

                function persistLocally(payload) {
                    try {
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
                    } catch (e) { /* localStorage unavailable — the server cookie still works */ }

                    // Let any other script on the page (GTM, GA, ad pixels, etc.)
                    // react to the new consent state without touching Livewire.
                    window.dispatchEvent(new CustomEvent('ps:cookie-consent', { detail: payload }));
                }

                document.addEventListener('livewire:init', () => {
                    Livewire.on('cookie-consent-saved', (data) => persistLocally(data));
                    Livewire.on('cookie-consent-restored', (data) => persistLocally(data));
                    Livewire.on('cookie-consent-revoked', () => {
                        try { localStorage.removeItem(STORAGE_KEY); } catch (e) { }
                        window.dispatchEvent(new CustomEvent('ps:cookie-consent', { detail: null }));
                    });

                    // Prevent the page from scrolling behind the sheet/modal on
                    // mobile, and move focus into the dialog for accessibility.
                    Livewire.on('cookie-consent-modal-toggled', (data) => {
                        document.body.classList.toggle('ps-cc-lock-scroll', !!data.open);

                        if (data.open) {
                            setTimeout(() => {
                                document.querySelector('.ps-cc-modal__close')?.focus();
                            }, 50);
                        }
                    });
                });

                // Small public helper other scripts on the site can use:
                //   if (window.PSCookieConsent.has('analytics')) { ...load GA... }
                window.PSCookieConsent = {
                    get() {
                        try {
                            return JSON.parse(localStorage.getItem(STORAGE_KEY));
                        } catch (e) {
                            return null;
                        }
                    },
                    has(category) {
                        const data = this.get();
                        return !!(data && data.categories && data.categories[category]);
                    },
                    open() {
                        window.Livewire && window.Livewire.dispatch('open-cookie-settings');
                    },
                };
            })();
        </script>
    @endpush
</div>