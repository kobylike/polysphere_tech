@if($url)
    @if($variant === 'topbar')
        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- TOPBAR VARIANT — plain text link --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <span class="wa-topbar p-relative">
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="Chat with Polysphere Tech on WhatsApp">
                <i class="fab fa-whatsapp" aria-hidden="true"></i>
                Chat on WhatsApp
            </a>
        </span>

    @elseif($variant === 'navbar')
        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- NAVBAR VARIANT — compact green pill --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="wa-navbar-link"
            aria-label="Chat with Polysphere Tech on WhatsApp">
            <span class="wa-navbar-icon" aria-hidden="true">
                <svg viewBox="0 0 32 32" width="16" height="16" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor"
                        d="M16.001 3C8.832 3 3 8.83 3 16c0 2.29.6 4.44 1.65 6.31L3 29l6.88-1.6A12.9 12.9 0 0 0 16 29c7.17 0 13-5.83 13-13S23.17 3 16.001 3Zm0 23.6c-2.04 0-3.95-.6-5.55-1.62l-.4-.24-4.08.95.97-3.98-.26-.41A10.55 10.55 0 0 1 5.4 16c0-5.85 4.76-10.6 10.6-10.6 5.85 0 10.6 4.75 10.6 10.6s-4.75 10.6-10.6 10.6Zm5.82-7.94c-.32-.16-1.9-.94-2.2-1.05-.29-.11-.5-.16-.72.16-.21.32-.83.94-1.02 1.14-.18.2-.37.21-.68.05-.32-.16-1.35-.5-2.56-1.6-.95-.85-1.6-1.9-1.78-2.22-.18-.32-.02-.49.14-.65.14-.14.32-.37.48-.55.16-.18.21-.32.32-.53.1-.21.05-.4-.03-.55-.08-.16-.72-1.73-.99-2.37-.26-.62-.52-.54-.72-.55h-.61c-.21 0-.55.08-.83.4-.29.32-1.09 1.06-1.09 2.6 0 1.53 1.11 3.02 1.27 3.23.16.21 2.18 3.33 5.28 4.55.74.32 1.31.51 1.76.65.74.24 1.41.2 1.94.12.59-.09 1.9-.78 2.17-1.53.27-.75.27-1.4.19-1.53-.08-.13-.29-.21-.6-.37Z" />
                </svg>
            </span>
            <span class="wa-navbar-label">WhatsApp</span>
        </a>

    @elseif($variant === 'floating')
        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- FLOATING VARIANT — opt-in circle button --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="wa-float"
            aria-label="Chat with Polysphere Tech on WhatsApp">
            <svg viewBox="0 0 32 32" width="26" height="26" xmlns="http://www.w3.org/2000/svg">
                <path fill="currentColor"
                    d="M16.001 3C8.832 3 3 8.83 3 16c0 2.29.6 4.44 1.65 6.31L3 29l6.88-1.6A12.9 12.9 0 0 0 16 29c7.17 0 13-5.83 13-13S23.17 3 16.001 3Zm0 23.6c-2.04 0-3.95-.6-5.55-1.62l-.4-.24-4.08.95.97-3.98-.26-.41A10.55 10.55 0 0 1 5.4 16c0-5.85 4.76-10.6 10.6-10.6 5.85 0 10.6 4.75 10.6 10.6s-4.75 10.6-10.6 10.6Zm5.82-7.94c-.32-.16-1.9-.94-2.2-1.05-.29-.11-.5-.16-.72.16-.21.32-.83.94-1.02 1.14-.18.2-.37.21-.68.05-.32-.16-1.35-.5-2.56-1.6-.95-.85-1.6-1.9-1.78-2.22-.18-.32-.02-.49.14-.65.14-.14.32-.37.48-.55.16-.18.21-.32.32-.53.1-.21.05-.4-.03-.55-.08-.16-.72-1.73-.99-2.37-.26-.62-.52-.54-.72-.55h-.61c-.21 0-.55.08-.83.4-.29.32-1.09 1.06-1.09 2.6 0 1.53 1.11 3.02 1.27 3.23.16.21 2.18 3.33 5.28 4.55.74.32 1.31.51 1.76.65.74.24 1.41.2 1.94.12.59-.09 1.9-.78 2.17-1.53.27-.75.27-1.4.19-1.53-.08-.13-.29-.21-.6-.37Z" />
            </svg>
        </a>

    @else
        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- FOOTER VARIANT — card block (default) --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="wa-footer-block">
            <div class="wa-footer-icon" aria-hidden="true">
                <svg viewBox="0 0 32 32" width="22" height="22" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor"
                        d="M16.001 3C8.832 3 3 8.83 3 16c0 2.29.6 4.44 1.65 6.31L3 29l6.88-1.6A12.9 12.9 0 0 0 16 29c7.17 0 13-5.83 13-13S23.17 3 16.001 3Zm0 23.6c-2.04 0-3.95-.6-5.55-1.62l-.4-.24-4.08.95.97-3.98-.26-.41A10.55 10.55 0 0 1 5.4 16c0-5.85 4.76-10.6 10.6-10.6 5.85 0 10.6 4.75 10.6 10.6s-4.75 10.6-10.6 10.6Zm5.82-7.94c-.32-.16-1.9-.94-2.2-1.05-.29-.11-.5-.16-.72.16-.21.32-.83.94-1.02 1.14-.18.2-.37.21-.68.05-.32-.16-1.35-.5-2.56-1.6-.95-.85-1.6-1.9-1.78-2.22-.18-.32-.02-.49.14-.65.14-.14.32-.37.48-.55.16-.18.21-.32.32-.53.1-.21.05-.4-.03-.55-.08-.16-.72-1.73-.99-2.37-.26-.62-.52-.54-.72-.55h-.61c-.21 0-.55.08-.83.4-.29.32-1.09 1.06-1.09 2.6 0 1.53 1.11 3.02 1.27 3.23.16.21 2.18 3.33 5.28 4.55.74.32 1.31.51 1.76.65.74.24 1.41.2 1.94.12.59-.09 1.9-.78 2.17-1.53.27-.75.27-1.4.19-1.53-.08-.13-.29-.21-.6-.37Z" />
                </svg>
            </div>

            <div class="wa-footer-content">
                <h6 class="wa-footer-title">Prefer WhatsApp?</h6>
                <p class="wa-footer-text">
                    Skip the form. Message us directly and we'll reply within business hours.
                </p>
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="wa-footer-cta">
                    <span>Start a chat</span>
                    <i class="fal fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- STYLES — inline so they render regardless of @push timing --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @once('wa-button-styles')
        <style>
            /* ─── Topbar variant ─── */
            .wa-topbar a {
                color: inherit;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                transition: color 0.2s;
                text-decoration: none;
            }

            .wa-topbar a:hover {
                color: #25D366;
            }

            .wa-topbar a i {
                color: #25D366;
                font-size: 15px;
            }

            @media (max-width: 991px) {
                .wa-topbar {
                    display: none !important;
                }
            }

            /* ─── Navbar pill variant ─── */
            .wa-navbar-link {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                background: #25D366;
                color: #ffffff !important;
                border-radius: 999px;
                font-size: 13px;
                font-weight: 600;
                text-decoration: none;
                line-height: 1;
                transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
                box-shadow: 0 2px 6px rgba(37, 211, 102, 0.25);
            }

            .wa-navbar-link:hover {
                background: #1ebe57;
                transform: translateY(-1px);
                box-shadow: 0 4px 10px rgba(37, 211, 102, 0.35);
                color: #fff !important;
            }

            /* ─── Floating variant ─── */
            .wa-float {
                position: fixed;
                bottom: 24px;
                left: 24px;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #25D366;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 8px 24px rgba(37, 211, 102, 0.4);
                z-index: 9998;
                transition: transform 0.2s, box-shadow 0.2s;
            }

            .wa-float:hover {
                transform: scale(1.08);
                box-shadow: 0 12px 28px rgba(37, 211, 102, 0.5);
            }

            @media (max-width: 576px) {
                .wa-float {
                    bottom: 20px;
                    left: 20px;
                    width: 50px;
                    height: 50px;
                }
            }

            /* ─── Footer card variant ─── */
            .wa-footer-block {
                display: flex !important;
                gap: 14px;
                padding: 18px;
                background: rgba(37, 211, 102, 0.08);
                border: 1px solid rgba(37, 211, 102, 0.25);
                border-radius: 14px;
                margin-top: 24px;
                max-width: 320px;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .wa-footer-icon {
                flex-shrink: 0;
                width: 42px;
                height: 42px;
                border-radius: 50%;
                background: #25D366;
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(37, 211, 102, 0.35);
            }

            .wa-footer-icon svg {
                display: block;
                width: 22px;
                height: 22px;
            }

            .wa-footer-content {
                flex: 1;
                min-width: 0;
            }

            .wa-footer-block .wa-footer-title {
                margin: 0 0 4px !important;
                font-size: 14px !important;
                font-weight: 700 !important;
                color: #ffffff !important;
                line-height: 1.3 !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .wa-footer-block .wa-footer-text {
                margin: 0 0 10px !important;
                font-size: 12.5px !important;
                line-height: 1.55 !important;
                color: rgba(255, 255, 255, 0.7) !important;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .wa-footer-block .wa-footer-cta {
                display: inline-flex !important;
                align-items: center;
                gap: 6px;
                font-size: 13px !important;
                font-weight: 600 !important;
                color: #25D366 !important;
                text-decoration: none !important;
                transition: gap 0.15s, color 0.15s;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .wa-footer-block .wa-footer-cta:hover {
                gap: 10px;
                color: #4ade80 !important;
            }

            .wa-footer-block .wa-footer-cta i {
                font-size: 11px;
            }

            /* Mobile centering */
            @media (max-width: 767.98px) {
                .wa-footer-block {
                    margin-left: auto !important;
                    margin-right: auto !important;
                    text-align: left;
                }
            }
        </style>
    @endonce
@endif