<div x-data="searchApp()" x-init="init()" class="gsearch">

    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb"
            style="background-image: url('{{ asset('assets/main/imgs/resources/search.jpg') }}');"></div>
        <div class="breadcrumb__thumb_2"
            style="background-image: url('{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}');"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Search Results</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>Search Results</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Search console — deliberately overlaps the hero/content boundary ─── --}}
    <section class="gsearch-console-wrap">
        <div class="gsearch-console">

            <div class="gsearch-input-row">
                <span class="gsearch-input-icon" wire:loading.class="gsearch-input-icon--spin"
                    wire:target="query, category">
                    <i class="fas fa-search" wire:loading.remove wire:target="query, category"></i>
                    <i class="fas fa-circle-notch" wire:loading wire:target="query, category"></i>
                </span>
                <input type="text" class="gsearch-input" placeholder="Search services, projects, articles, people…"
                    wire:model.live.debounce.300ms="query" x-ref="searchInput">
                <button type="button" class="gsearch-submit" wire:click="performSearch" aria-label="Search">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <div class="gsearch-filters">
                @foreach($categories as $cat)
                    <button type="button" class="gsearch-filter {{ $category === $cat ? 'is-active' : '' }}"
                        wire:click="$set('category', '{{ $cat }}')">
                        {{ ucfirst($cat) }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ─── Results ─── --}}
    <section class="gsearch-results-area">
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">

                    @if(!empty($query))
                        <p class="gsearch-status">
                            @if(!empty($results))
                                {{ count($results) }} {{ Str::plural('result', count($results)) }} for
                                <strong>&ldquo;{{ $query }}&rdquo;</strong>
                            @else
                                Nothing yet for <strong>&ldquo;{{ $query }}&rdquo;</strong>
                            @endif
                        </p>
                    @endif

                    <div wire:loading wire:target="query, category" class="gsearch-loading">
                        <div class="gsearch-loading-bar"></div>
                    </div>

                    <div wire:loading.remove wire:target="query, category">
                        @if(empty($results) && strlen($query) >= 2)
                            {{-- No matches --}}
                            <div class="gsearch-empty">
                                <i class="fas fa-compass"></i>
                                <h5>No matches for &ldquo;{{ $query }}&rdquo;</h5>
                                <p>Try a shorter term, check the spelling, or search a different category.</p>
                            </div>
                        @elseif(empty($results))
                            {{-- Nothing typed yet --}}
                            <div class="gsearch-empty">
                                <i class="fas fa-layer-group"></i>
                                <h5>Search across the whole site</h5>
                                <p>Find services, case studies, articles, and people in one place.</p>
                            </div>
                        @else
                            @php
                                $grouped = collect($results)->groupBy('category');
                                $labels = ['services' => 'Services', 'projects' => 'Projects', 'blog' => 'Blog', 'team' => 'Team'];
                            @endphp

                            @foreach($grouped as $groupKey => $items)
                                <div class="gsearch-group">
                                    @if($grouped->count() > 1)
                                        <h6 class="gsearch-group-label">{{ $labels[$groupKey] ?? ucfirst($groupKey) }}</h6>
                                    @endif

                                    <div class="gsearch-list">
                                        @foreach($items as $result)
                                            <a href="{{ $result['url'] }}" wire:navigate.hover
                                                class="gsearch-row gsearch-row--{{ $result['color'] }}">
                                                <span class="gsearch-row-icon">
                                                    <i class="fas {{ $result['icon'] }}"></i>
                                                </span>
                                                <span class="gsearch-row-body">
                                                    <span class="gsearch-row-title">{{ $result['title'] }}</span>
                                                    <span class="gsearch-row-subtitle">{{ $result['subtitle'] }}</span>
                                                </span>
                                                <i class="fas fa-chevron-right gsearch-row-chevron"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('searchApp', () => ({
                query: @entangle('query').live,
                category: @entangle('category').live,

                init() {
                    this.$nextTick(() => {
                        this.$refs.searchInput?.focus();
                    });
                }
            }));
        });
    </script>
@endpush

<style>
    /* ── Tokens ─────────────────────────────────────────────────────────── */
    .gsearch {
        --gs-ink: #0d1b2e;
        --gs-accent: #2f6fed;
        --gs-surface: #ffffff;
        --gs-bg: #f6f7fb;
        --gs-border: #e7e9f2;
        --gs-text: #101828;
        --gs-muted: #667085;
        --gs-services: #0ea5a4;
        --gs-projects: #d97706;
        --gs-blog: #7c5cff;
        --gs-team: #e0567a;
    }

    /* ── Console — pulled up so it straddles the dark hero / light section ── */
    .gsearch-console-wrap {
        background: var(--gs-bg);
        padding: 0 20px 10px;
    }

    .gsearch-console {
        max-width: 720px;
        margin: -58px auto 0;
        background: var(--gs-surface);
        border-radius: 16px;
        box-shadow: 0 20px 48px rgba(13, 27, 46, 0.16);
        padding: 10px 10px 16px;
        position: relative;
        z-index: 3;
    }

    .gsearch-input-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 10px 14px;
        border-bottom: 1px solid var(--gs-border);
    }

    .gsearch-input-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        color: var(--gs-muted);
        font-size: 16px;
    }

    .gsearch-input-icon--spin i {
        animation: gsearch-spin 0.8s linear infinite;
        color: var(--gs-accent);
    }

    @keyframes gsearch-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .gsearch-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 17px;
        color: var(--gs-text);
        background: transparent;
        padding: 6px 0;
    }

    .gsearch-input::placeholder {
        color: #a0a6b8;
    }

    .gsearch-submit {
        border: none;
        background: var(--gs-accent);
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: background 0.15s ease;
    }

    .gsearch-submit:hover {
        background: #2559c7;
    }

    .gsearch-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 12px 10px 2px;
    }

    .gsearch-filter {
        border: 1px solid var(--gs-border);
        background: transparent;
        color: var(--gs-muted);
        font-size: 13.5px;
        font-weight: 500;
        padding: 6px 14px;
        border-radius: 999px;
        transition: all 0.15s ease;
    }

    .gsearch-filter:hover {
        border-color: var(--gs-accent);
        color: var(--gs-accent);
    }

    .gsearch-filter.is-active {
        background: var(--gs-ink);
        border-color: var(--gs-ink);
        color: #fff;
    }

    /* ── Results area ──────────────────────────────────────────────────── */
    .gsearch-results-area {
        background: var(--gs-bg);
        padding: 40px 0 100px;
    }

    .gsearch-status {
        color: var(--gs-muted);
        font-size: 14.5px;
        margin: 0 4px 22px;
    }

    .gsearch-status strong {
        color: var(--gs-text);
    }

    .gsearch-loading-bar {
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--gs-accent), transparent);
        background-size: 200% 100%;
        animation: gsearch-loading-sweep 1.1s ease-in-out infinite;
        border-radius: 2px;
        margin-bottom: 22px;
    }

    @keyframes gsearch-loading-sweep {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    .gsearch-empty {
        text-align: center;
        padding: 70px 20px;
        color: var(--gs-muted);
    }

    .gsearch-empty i {
        font-size: 34px;
        color: #c7cbdb;
        margin-bottom: 14px;
        display: block;
    }

    .gsearch-empty h5 {
        color: var(--gs-text);
        font-weight: 600;
        margin-bottom: 6px;
    }

    .gsearch-empty p {
        margin: 0;
        font-size: 14.5px;
    }

    .gsearch-group {
        margin-bottom: 28px;
    }

    .gsearch-group-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--gs-muted);
        margin: 0 4px 10px;
    }

    .gsearch-list {
        background: var(--gs-surface);
        border: 1px solid var(--gs-border);
        border-radius: 14px;
        overflow: hidden;
    }

    .gsearch-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        text-decoration: none;
        border-bottom: 1px solid var(--gs-border);
        transition: background 0.15s ease;
    }

    .gsearch-list .gsearch-row:last-child {
        border-bottom: none;
    }

    .gsearch-row:hover {
        background: #fbfcfe;
    }

    .gsearch-row:hover .gsearch-row-chevron {
        transform: translateX(3px);
        color: var(--gs-accent);
    }

    .gsearch-row-icon {
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        color: #fff;
    }

    .gsearch-row--info .gsearch-row-icon,
    .gsearch-row--services .gsearch-row-icon {
        background: var(--gs-services);
    }

    .gsearch-row--success .gsearch-row-icon,
    .gsearch-row--projects .gsearch-row-icon {
        background: var(--gs-projects);
    }

    .gsearch-row--warning .gsearch-row-icon,
    .gsearch-row--blog .gsearch-row-icon {
        background: var(--gs-blog);
    }

    .gsearch-row--primary .gsearch-row-icon,
    .gsearch-row--team .gsearch-row-icon {
        background: var(--gs-team);
    }

    .gsearch-row-body {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .gsearch-row-title {
        font-weight: 600;
        color: var(--gs-text);
        font-size: 15px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .gsearch-row-subtitle {
        font-size: 13.5px;
        color: var(--gs-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .gsearch-row-chevron {
        color: #c7cbdb;
        font-size: 12px;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }

    @media (max-width: 576px) {
        .gsearch-console {
            margin-top: -40px;
            padding: 8px 8px 12px;
        }

        .gsearch-input {
            font-size: 15px;
        }

        .gsearch-row-subtitle {
            display: none;
        }
    }
</style>