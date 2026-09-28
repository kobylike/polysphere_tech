<!-- livewire/admin/dashboard/advanced-analytics-component.blade.php -->
<div x-data="analyticsCharts()" x-init="initCharts()" @update-analytics-charts.window="updateCharts($event.detail)"
    class="adv-wrap">
    <!-- ─── Page Header ────────────────────────────────────────────── -->
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Advanced Analytics</h5>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}" wire:navigate.hover>Dashboard</a>
            </li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Website Analytics</a></li>
        </ol>
        <div class="d-flex align-items-center gap-2">
            <button wire:click="forceRefresh" wire:loading.attr="disabled" wire:target="forceRefresh"
                class="adv-btn adv-btn--primary">
                <span wire:loading.remove wire:target="forceRefresh" class="adv-btn-inner">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12a9 9 0 1 1-2.64-6.36" />
                        <path d="M21 3v6h-6" />
                    </svg>
                    Refresh
                </span>
                <span wire:loading wire:target="forceRefresh" class="adv-btn-inner">
                    <svg class="adv-spin" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round">
                        <path d="M21 12a9 9 0 1 1-6.22-8.56" />
                    </svg>
                    Refreshing…
                </span>
            </button>
        </div>
    </div>

    <div class="container-fluid">

        {{-- ─── Connection / empty-state banners ──────────────────── --}}
        @unless($ga4Configured)
            <div class="adv-banner adv-banner--warn">
                <div class="adv-banner-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                </div>
                <div>
                    <strong>Google Analytics isn't connected yet.</strong>
                    <div class="adv-banner-sub">
                        Set <code>GA4_PROPERTY_ID</code> and <code>GA4_CREDENTIALS_PATH</code> in your
                        <code>.env</code>, drop the service-account JSON into
                        <code>storage/app/ga4/</code>, then run
                        <code>php artisan config:clear</code> on production.
                    </div>
                </div>
            </div>
        @endunless

        @if($ga4Configured && (int) ($overview['sessions'] ?? 0) === 0)
            <div class="adv-banner adv-banner--info">
                <div class="adv-banner-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="16" x2="12" y2="12" />
                        <line x1="12" y1="8" x2="12.01" y2="8" />
                    </svg>
                </div>
                <div>
                    <strong>Connection working.</strong>
                    Reports fill in about 24–48 hours after tracking starts.
                </div>
            </div>
        @endif

        {{-- ─── Period selector ───────────────────────────────────── --}}
        <div class="adv-period-bar">
            <div class="adv-period-pills">
                @foreach(['7d' => '7 days', '30d' => '30 days', '90d' => '90 days', '12m' => '12 months'] as $value => $label)
                    <button type="button" wire:click="$set('period', '{{ $value }}')"
                        class="adv-pill {{ $period === $value ? 'is-active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <div class="adv-period-note">
                Cache: 30 min · Realtime: 60 s
            </div>
        </div>

        <div class="row g-3">

            {{-- ═══════════════════════════════════════════════════
            ROW 1 — LIVE HERO + 3 KPIs
            ═══════════════════════════════════════════════════ --}}

            {{-- ─── LIVE RIGHT NOW ─── --}}
            <div class="col-xl-3 col-sm-6" wire:poll.15s="$refresh">
                @php
                    $liveNow = (int) $this->realtimeActiveUsers;
                    $livePages = array_slice($this->realtimeTopPages, 0, 4);
                    $liveMax = max(1, collect($livePages)->max('users') ?? 1);
                @endphp

                <div class="adv-live">
                    <div class="adv-live-orb adv-live-orb--1"></div>
                    <div class="adv-live-orb adv-live-orb--2"></div>

                    <div class="adv-live-head">
                        <div class="adv-live-badge">
                            <span class="adv-live-dot"></span>
                            <span>LIVE</span>
                        </div>
                        <div class="adv-live-updated">refreshes every 15s</div>
                    </div>

                    <div class="adv-live-main">
                        <div class="adv-live-rings" aria-hidden="true">
                            <span></span><span></span><span></span>
                        </div>
                        <div class="adv-live-count">{{ $liveNow }}</div>
                        <div class="adv-live-label">
                            {{ Str::plural('visitor', $liveNow) }} on the site right now
                        </div>
                    </div>

                    <div class="adv-live-pages">
                        <div class="adv-live-pages-head">
                            <span>Top active pages</span>
                            <span>{{ count($this->realtimeTopPages) }} tracked</span>
                        </div>

                        @forelse($livePages as $page)
                            @php
                                $clean = trim(Str::before($page['page'], ' | Polysphere Tech')) ?: $page['page'];
                                $pct = max(6, (int) round(($page['users'] / $liveMax) * 100));
                            @endphp
                            <div class="adv-live-page">
                                <div class="adv-live-page-row">
                                    <span class="adv-live-page-name" title="{{ $page['page'] }}">{{ $clean }}</span>
                                    <span class="adv-live-page-count">{{ $page['users'] }}</span>
                                </div>
                                <div class="adv-live-page-bar">
                                    <div class="adv-live-page-bar-fill" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="adv-live-empty">
                                Nobody on the site right now — check back in a moment.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ─── Sessions KPI ─── --}}
            <div class="col-xl-3 col-sm-6">
                <div class="adv-kpi">
                    <div class="adv-kpi-head">
                        <div class="adv-kpi-icon adv-kpi-icon--blue">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 17l6-6 4 4 8-8" />
                                <path d="M21 7v6h-6" />
                            </svg>
                        </div>
                        <div class="adv-kpi-label">Sessions</div>
                    </div>
                    <div class="adv-kpi-value">{{ number_format($overview['sessions']) }}</div>
                    <div class="adv-kpi-foot">
                        <span class="adv-chip">
                            <strong>{{ number_format($overview['active_users']) }}</strong> users
                        </span>
                        <span class="adv-chip adv-chip--green">
                            +{{ number_format($overview['new_users']) }} new
                        </span>
                    </div>
                </div>
            </div>

            {{-- ─── Bounce Rate KPI ─── --}}
            <div class="col-xl-3 col-sm-6">
                <div class="adv-kpi">
                    <div class="adv-kpi-head">
                        <div class="adv-kpi-icon adv-kpi-icon--rose">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 18l6-6-6-6" />
                                <path d="M15 6v12" />
                            </svg>
                        </div>
                        <div class="adv-kpi-label">Bounce rate</div>
                    </div>
                    <div class="adv-kpi-value">{{ $overview['bounce_rate'] }}<span class="adv-kpi-unit">%</span></div>
                    <div class="adv-kpi-foot">
                        <div class="adv-meter">
                            <div class="adv-meter-fill"
                                style="width: {{ min(100, (float) $overview['engagement_rate']) }}%"></div>
                        </div>
                        <span class="adv-chip adv-chip--soft">
                            Engagement {{ $overview['engagement_rate'] }}%
                        </span>
                    </div>
                </div>
            </div>

            {{-- ─── Avg Session + Conversions KPI ─── --}}
            <div class="col-xl-3 col-sm-6">
                <div class="adv-kpi">
                    <div class="adv-kpi-head">
                        <div class="adv-kpi-icon adv-kpi-icon--violet">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7v5l3 2" />
                            </svg>
                        </div>
                        <div class="adv-kpi-label">Avg. session</div>
                    </div>
                    <div class="adv-kpi-value">{{ $overview['avg_session_duration'] }}</div>
                    <div class="adv-kpi-foot">
                        <span class="adv-chip adv-chip--violet">
                            Conversions <strong>{{ number_format($overview['conversions']) }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
            ROW 2 — TRAFFIC OVER TIME + SOURCES
            ═══════════════════════════════════════════════════ --}}

            <div class="col-xl-8">
                <div class="adv-card">
                    <div class="adv-card-head">
                        <div>
                            <h4 class="heading mb-0">Traffic over time</h4>
                            <p class="adv-card-sub">Daily sessions, users and pageviews</p>
                        </div>
                        <div class="adv-legend">
                            <span class="adv-legend-item"><i style="background:#0D99FF"></i>Sessions</span>
                            <span class="adv-legend-item"><i style="background:#3AC977"></i>Users</span>
                            <span class="adv-legend-item"><i style="background:#FF9F00"></i>Pageviews</span>
                        </div>
                    </div>
                    <div class="adv-card-body">
                        <div wire:ignore class="adv-canvas" style="height: 280px;">
                            <canvas id="analyticsTrafficChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="adv-card">
                    <div class="adv-card-head">
                        <div>
                            <h4 class="heading mb-0">Traffic sources</h4>
                            <p class="adv-card-sub">Sessions by channel</p>
                        </div>
                    </div>
                    <div class="adv-card-body">
                        <div wire:ignore class="adv-canvas" style="height: 180px;">
                            <canvas id="analyticsSourcesChart"></canvas>
                        </div>

                        <ul class="adv-source-list">
                            @foreach($trafficSources['labels'] as $index => $label)
                                <li>
                                    <span class="adv-source-swatch"
                                        style="background: {{ $trafficSources['colors'][$index] }}"></span>
                                    <span class="adv-source-name">{{ $label }}</span>
                                    <span
                                        class="adv-source-value">{{ number_format($trafficSources['data'][$index]) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
            ROW 3 — DEVICES + BROWSERS + COUNTRIES
            ═══════════════════════════════════════════════════ --}}

            <div class="col-xl-4 col-md-6">
                <div class="adv-card">
                    <div class="adv-card-head">
                        <div>
                            <h4 class="heading mb-0">Devices</h4>
                            <p class="adv-card-sub">Sessions by device type</p>
                        </div>
                    </div>
                    <div class="adv-card-body">
                        <div wire:ignore class="adv-canvas" style="height: 200px;">
                            <canvas id="analyticsDeviceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="adv-card">
                    <div class="adv-card-head">
                        <div>
                            <h4 class="heading mb-0">Browsers</h4>
                            <p class="adv-card-sub">Sessions by browser</p>
                        </div>
                    </div>
                    <div class="adv-card-body">
                        <div wire:ignore class="adv-canvas" style="height: 200px;">
                            <canvas id="analyticsBrowserChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="adv-card">
                    <div class="adv-card-head">
                        <div>
                            <h4 class="heading mb-0">Top countries</h4>
                            <p class="adv-card-sub">By active users</p>
                        </div>
                    </div>
                    <div class="adv-card-body adv-card-body--flush">
                        <ul class="adv-country-list">
                            @forelse($topCountries as $country)
                                <li>
                                    <div class="adv-country-row">
                                        <span class="adv-country-name">{{ $country['country'] }}</span>
                                        <span class="adv-country-value">
                                            {{ number_format($country['users']) }}
                                            <em>{{ $country['share'] }}%</em>
                                        </span>
                                    </div>
                                    <div class="adv-country-bar">
                                        <div class="adv-country-bar-fill" style="width: {{ $country['share'] }}%"></div>
                                    </div>
                                </li>
                            @empty
                                <li class="adv-empty">No data for this period</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
            ROW 4 — TOP PAGES
            ═══════════════════════════════════════════════════ --}}

            <div class="col-xl-8">
                <div class="adv-card">
                    <div class="adv-card-head">
                        <div>
                            <h4 class="heading mb-0">Top pages</h4>
                            <p class="adv-card-sub">Most viewed pages in the selected period</p>
                        </div>
                    </div>
                    <div class="adv-card-body adv-card-body--flush">
                        <div class="adv-table-wrap">
                            <table class="adv-table">
                                <thead>
                                    <tr>
                                        <th>Page</th>
                                        <th class="adv-ta-right">Views</th>
                                        <th class="adv-ta-right">Avg. time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topPages as $page)
                                        <tr>
                                            <td>
                                                <div class="adv-page-title">{{ $page['title'] }}</div>
                                                <div class="adv-page-path">{{ $page['path'] }}</div>
                                            </td>
                                            <td class="adv-ta-right adv-mono">{{ number_format($page['views']) }}</td>
                                            <td class="adv-ta-right adv-mono">{{ $page['avg_duration'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="adv-empty">No data for this period</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
Styles — scoped under .adv-wrap so they don't leak into the theme.
═══════════════════════════════════════════════════════════════ --}}
<style>
    .adv-wrap {
        --adv-ink: #0b1220;
        --adv-ink-2: #1e293b;
        --adv-muted: #64748b;
        --adv-muted-2: #94a3b8;
        --adv-border: #e8eef5;
        --adv-card: #ffffff;
        --adv-primary: #0D99FF;
        --adv-success: #3AC977;
        --adv-warning: #FF9F00;
        --adv-danger: #FF5E5E;
        --adv-violet: #8A5CF6;
        --adv-radius: 16px;
        --adv-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 8px 24px -12px rgba(15, 23, 42, .12);
        --adv-shadow-hover: 0 4px 8px rgba(15, 23, 42, .06), 0 20px 40px -20px rgba(15, 23, 42, .20);

        color: var(--adv-ink);
        font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Inter, sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    /* ─── Refresh button ────────────────────────────────────────── */
    .adv-btn {
        appearance: none;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 9px 16px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: transform .12s ease, box-shadow .12s ease, background .12s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        line-height: 1;
    }

    .adv-btn--primary {
        background: linear-gradient(135deg, #0D99FF, #0670d4);
        color: #fff;
        box-shadow: 0 6px 16px -6px rgba(13, 153, 255, .6);
    }

    .adv-btn--primary:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px -8px rgba(13, 153, 255, .7);
    }

    .adv-btn--primary:disabled {
        opacity: .7;
        cursor: not-allowed;
    }

    .adv-btn-inner {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .adv-spin {
        animation: adv-spin 1s linear infinite;
    }

    @keyframes adv-spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ─── Banners ───────────────────────────────────────────────── */
    .adv-banner {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 14px 18px;
        border-radius: var(--adv-radius);
        margin-bottom: 18px;
        border: 1px solid var(--adv-border);
        background: var(--adv-card);
        box-shadow: var(--adv-shadow);
        font-size: 13.5px;
        line-height: 1.55;
    }

    .adv-banner strong {
        color: var(--adv-ink);
    }

    .adv-banner-sub {
        color: var(--adv-muted);
        margin-top: 2px;
    }

    .adv-banner code {
        background: #eef2f7;
        padding: 1px 6px;
        border-radius: 5px;
        font-size: 12px;
        color: #334155;
    }

    .adv-banner--warn {
        border-left: 4px solid var(--adv-warning);
    }

    .adv-banner--warn .adv-banner-icon {
        color: var(--adv-warning);
    }

    .adv-banner--info {
        border-left: 4px solid var(--adv-primary);
    }

    .adv-banner--info .adv-banner-icon {
        color: var(--adv-primary);
    }

    .adv-banner-icon {
        flex: none;
        padding-top: 1px;
    }

    /* ─── Period bar ────────────────────────────────────────────── */
    .adv-period-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .adv-period-pills {
        display: inline-flex;
        gap: 4px;
        padding: 4px;
        border-radius: 12px;
        background: #eef2f7;
        border: 1px solid #e3e9f0;
    }

    .adv-pill {
        appearance: none;
        border: 0;
        background: transparent;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--adv-muted);
        cursor: pointer;
        transition: background .15s ease, color .15s ease, box-shadow .15s ease;
    }

    .adv-pill:hover {
        color: var(--adv-ink-2);
    }

    .adv-pill.is-active {
        background: #fff;
        color: var(--adv-ink);
        box-shadow: 0 1px 2px rgba(15, 23, 42, .06), 0 4px 12px -6px rgba(15, 23, 42, .15);
    }

    .adv-period-note {
        font-size: 11.5px;
        color: var(--adv-muted-2);
        letter-spacing: .02em;
    }

    /* ─── Generic card ──────────────────────────────────────────── */
    .adv-card {
        background: var(--adv-card);
        border: 1px solid var(--adv-border);
        border-radius: var(--adv-radius);
        box-shadow: var(--adv-shadow);
        overflow: hidden;
        transition: transform .18s ease, box-shadow .18s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .adv-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--adv-shadow-hover);
    }

    .adv-card-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 18px 20px 6px;
    }

    .adv-card-head .heading {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.01em;
        color: var(--adv-ink);
    }

    .adv-card-sub {
        font-size: 12px;
        color: var(--adv-muted);
        margin: 3px 0 0;
    }

    .adv-card-body {
        padding: 10px 20px 20px;
        flex: 1;
    }

    .adv-card-body--flush {
        padding: 6px 0 0;
    }

    .adv-canvas {
        position: relative;
        width: 100%;
    }

    /* ─── Legend ────────────────────────────────────────────────── */
    .adv-legend {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
    }

    .adv-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--adv-muted);
        font-weight: 500;
    }

    .adv-legend-item i {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-radius: 3px;
    }

    /* ═══════════════════════════════════════════════════════════════
       LIVE HERO
       ═══════════════════════════════════════════════════════════════ */
    .adv-live {
        position: relative;
        isolation: isolate;
        border-radius: var(--adv-radius);
        padding: 22px 22px 20px;
        color: #e6edf7;
        background: radial-gradient(120% 100% at 0% 0%, #16233c 0%, #0c1424 55%, #0a0f1c 100%);
        border: 1px solid rgba(255, 255, 255, .06);
        box-shadow:
            0 1px 2px rgba(15, 23, 42, .15),
            0 24px 48px -24px rgba(9, 13, 26, .75),
            inset 0 1px 0 rgba(255, 255, 255, .05);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 260px;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .adv-live:hover {
        transform: translateY(-2px);
    }

    .adv-live-orb {
        position: absolute;
        pointer-events: none;
        border-radius: 999px;
        filter: blur(50px);
        opacity: .55;
        z-index: -1;
    }

    .adv-live-orb--1 {
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, #3AC977 0%, rgba(58, 201, 119, 0) 70%);
        top: -70px;
        right: -60px;
        animation: adv-orb-drift 8s ease-in-out infinite alternate;
    }

    .adv-live-orb--2 {
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, #0D99FF 0%, rgba(13, 153, 255, 0) 70%);
        bottom: -60px;
        left: -50px;
        opacity: .35;
        animation: adv-orb-drift 10s ease-in-out infinite alternate-reverse;
    }

    @keyframes adv-orb-drift {
        to {
            transform: translate3d(12px, 8px, 0) scale(1.08);
        }
    }

    .adv-live-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        font-size: 11px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgba(230, 237, 247, .55);
    }

    .adv-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 4px 10px 4px 8px;
        border-radius: 999px;
        background: rgba(58, 201, 119, .12);
        border: 1px solid rgba(58, 201, 119, .35);
        color: #8bf0b3;
        font-weight: 700;
    }

    .adv-live-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #3AC977;
        box-shadow: 0 0 0 0 rgba(58, 201, 119, .7);
        animation: adv-live-pulse 1.6s ease-out infinite;
    }

    @keyframes adv-live-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(58, 201, 119, .75);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(58, 201, 119, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(58, 201, 119, 0);
        }
    }

    .adv-live-updated {
        text-transform: none;
        letter-spacing: 0;
        font-size: 11px;
    }

    .adv-live-main {
        position: relative;
        text-align: left;
        margin-bottom: 20px;
    }

    .adv-live-rings {
        position: absolute;
        top: -6px;
        left: -4px;
        width: 88px;
        height: 88px;
        pointer-events: none;
    }

    .adv-live-rings span {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 1px solid rgba(58, 201, 119, .35);
        animation: adv-ring 3s ease-out infinite;
    }

    .adv-live-rings span:nth-child(2) {
        animation-delay: 1s;
    }

    .adv-live-rings span:nth-child(3) {
        animation-delay: 2s;
    }

    @keyframes adv-ring {
        0% {
            transform: scale(.6);
            opacity: .9;
        }

        100% {
            transform: scale(1.6);
            opacity: 0;
        }
    }

    .adv-live-count {
        font-size: 56px;
        font-weight: 800;
        letter-spacing: -.04em;
        line-height: 1;
        color: #ffffff;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 4px 24px rgba(58, 201, 119, .25);
    }

    .adv-live-label {
        margin-top: 6px;
        font-size: 12.5px;
        color: rgba(230, 237, 247, .7);
    }

    .adv-live-pages {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, .08);
    }

    .adv-live-pages-head {
        display: flex;
        justify-content: space-between;
        font-size: 10.5px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: rgba(230, 237, 247, .45);
        margin-bottom: 10px;
    }

    .adv-live-page {
        margin-bottom: 10px;
    }

    .adv-live-page:last-child {
        margin-bottom: 0;
    }

    .adv-live-page-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        font-size: 12.5px;
        color: rgba(230, 237, 247, .9);
        margin-bottom: 5px;
    }

    .adv-live-page-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-width: 0;
    }

    .adv-live-page-count {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        color: #ffffff;
        flex: none;
    }

    .adv-live-page-bar {
        height: 4px;
        background: rgba(255, 255, 255, .08);
        border-radius: 999px;
        overflow: hidden;
    }

    .adv-live-page-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #3AC977, #7ee2a8);
        box-shadow: 0 0 8px rgba(58, 201, 119, .6);
        transition: width .4s ease;
    }

    .adv-live-empty {
        font-size: 12.5px;
        color: rgba(230, 237, 247, .55);
        padding: 6px 0;
    }

    /* ═══════════════════════════════════════════════════════════════
       KPI CARD
       ═══════════════════════════════════════════════════════════════ */
    .adv-kpi {
        background: var(--adv-card);
        border: 1px solid var(--adv-border);
        border-radius: var(--adv-radius);
        box-shadow: var(--adv-shadow);
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: transform .18s ease, box-shadow .18s ease;
        height: 100%;
        min-height: 260px;
        position: relative;
        overflow: hidden;
    }

    .adv-kpi::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 60%, rgba(13, 153, 255, .03));
        pointer-events: none;
    }

    .adv-kpi:hover {
        transform: translateY(-2px);
        box-shadow: var(--adv-shadow-hover);
    }

    .adv-kpi-head {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .adv-kpi-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: none;
    }

    .adv-kpi-icon--blue {
        background: rgba(13, 153, 255, .10);
        color: #0D99FF;
    }

    .adv-kpi-icon--rose {
        background: rgba(255, 94, 94, .10);
        color: #FF5E5E;
    }

    .adv-kpi-icon--violet {
        background: rgba(138, 92, 246, .10);
        color: #8A5CF6;
    }

    .adv-kpi-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--adv-muted);
        letter-spacing: .01em;
    }

    .adv-kpi-value {
        font-size: 30px;
        font-weight: 700;
        letter-spacing: -.02em;
        color: var(--adv-ink);
        font-variant-numeric: tabular-nums;
        line-height: 1.05;
    }

    .adv-kpi-unit {
        font-size: 16px;
        font-weight: 600;
        color: var(--adv-muted);
        margin-left: 3px;
    }

    .adv-kpi-foot {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin-top: auto;
    }

    .adv-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 9px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 600;
        background: #eef2f7;
        color: #475569;
    }

    .adv-chip strong {
        color: var(--adv-ink);
        font-weight: 700;
    }

    .adv-chip--green {
        background: rgba(58, 201, 119, .10);
        color: #1f8f4c;
    }

    .adv-chip--violet {
        background: rgba(138, 92, 246, .10);
        color: #6d3fe0;
    }

    .adv-chip--soft {
        background: #f1f5f9;
        color: #475569;
        font-weight: 500;
    }

    .adv-meter {
        flex: 1;
        min-width: 60px;
        height: 6px;
        border-radius: 999px;
        background: #eef2f7;
        overflow: hidden;
    }

    .adv-meter-fill {
        height: 100%;
        background: linear-gradient(90deg, #3AC977, #7ee2a8);
        border-radius: 999px;
        transition: width .4s ease;
    }

    /* ═══════════════════════════════════════════════════════════════
       SOURCE LIST
       ═══════════════════════════════════════════════════════════════ */
    .adv-source-list {
        list-style: none;
        margin: 16px 0 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .adv-source-list li {
        display: grid;
        grid-template-columns: 12px 1fr auto;
        align-items: center;
        gap: 10px;
        padding: 7px 0;
        font-size: 12.5px;
        border-bottom: 1px dashed #eef2f7;
    }

    .adv-source-list li:last-child {
        border-bottom: 0;
    }

    .adv-source-swatch {
        width: 10px;
        height: 10px;
        border-radius: 3px;
        display: inline-block;
    }

    .adv-source-name {
        color: var(--adv-ink-2);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .adv-source-value {
        font-weight: 700;
        color: var(--adv-ink);
        font-variant-numeric: tabular-nums;
    }

    /* ═══════════════════════════════════════════════════════════════
       COUNTRY LIST
       ═══════════════════════════════════════════════════════════════ */
    .adv-country-list {
        list-style: none;
        margin: 0;
        padding: 4px 0;
    }

    .adv-country-list li {
        padding: 10px 20px;
        border-bottom: 1px solid #f1f5f9;
    }

    .adv-country-list li:last-child {
        border-bottom: 0;
    }

    .adv-country-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 10px;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .adv-country-name {
        color: var(--adv-ink-2);
        font-weight: 500;
    }

    .adv-country-value {
        font-weight: 700;
        color: var(--adv-ink);
        font-variant-numeric: tabular-nums;
    }

    .adv-country-value em {
        font-style: normal;
        font-weight: 500;
        color: var(--adv-muted);
        font-size: 11.5px;
        margin-left: 4px;
    }

    .adv-country-bar {
        height: 4px;
        background: #eef2f7;
        border-radius: 999px;
        overflow: hidden;
    }

    .adv-country-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #0D99FF, #6ec2ff);
        transition: width .5s ease;
    }

    /* ═══════════════════════════════════════════════════════════════
       TABLE
       ═══════════════════════════════════════════════════════════════ */
    .adv-table-wrap {
        overflow-x: auto;
    }

    .adv-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .adv-table thead th {
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--adv-muted);
        padding: 12px 20px;
        background: #f8fafc;
        border-bottom: 1px solid #eef2f7;
        white-space: nowrap;
    }

    .adv-table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .adv-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .adv-table tbody tr {
        transition: background .12s ease;
    }

    .adv-table tbody tr:hover {
        background: #f8fbff;
    }

    .adv-ta-right {
        text-align: right;
    }

    .adv-mono {
        font-variant-numeric: tabular-nums;
        color: var(--adv-ink-2);
        font-weight: 500;
    }

    .adv-page-title {
        font-weight: 600;
        color: var(--adv-ink);
        line-height: 1.3;
    }

    .adv-page-path {
        font-size: 11.5px;
        color: var(--adv-muted);
        margin-top: 2px;
        word-break: break-all;
    }

    .adv-empty {
        text-align: center;
        color: var(--adv-muted);
        font-size: 13px;
        padding: 24px 20px !important;
    }
</style>

@push('scripts')
    <script>
        // Mirrors the KPI dashboard's chart bootstrap pattern exactly
        // (dynamic ESM import of Chart.js v4 to dodge the theme's global
        // Chart.js v2 bundle, dual alpine:init/window.Alpine registration
        // so it survives wire:navigate regardless of load order, and
        // Chart.getChart()-based destroy-before-recreate so re-mounting
        // this component on the same canvas never hits Chart.js's
        // "canvas already in use" guard).
        function registerAnalyticsChartsComponent() {
            Alpine.data('analyticsCharts', () => ({
                trafficChart: null,
                sourcesChart: null,
                deviceChart: null,
                browserChart: null,
                ChartJS: null,

                waitForElement(id, maxTries = 20) {
                    return new Promise((resolve) => {
                        const tryFind = (attemptsLeft) => {
                            const el = document.getElementById(id);
                            if (el) return resolve(el);
                            if (attemptsLeft <= 0) return resolve(null);
                            requestAnimationFrame(() => tryFind(attemptsLeft - 1));
                        };
                        tryFind(maxTries);
                    });
                },

                async initCharts() {
                    if (!this.ChartJS) {
                        const mod = await import('https://cdn.jsdelivr.net/npm/chart.js@4/+esm');
                        mod.Chart.register(...mod.registerables);
                        this.ChartJS = mod.Chart;
                    }

                    const timeSeries = @json($timeSeries);
                    const trafficSources = @json($trafficSources);
                    const deviceBreakdown = @json($deviceBreakdown);
                    const browserBreakdown = @json($browserBreakdown);

                    await this.initTrafficChart(timeSeries);
                    await this.initSourcesChart(trafficSources);
                    await this.initDeviceChart(deviceBreakdown);
                    await this.initBrowserChart(browserBreakdown);
                },

                async initTrafficChart(data) {
                    const ctx = await this.waitForElement('analyticsTrafficChart');
                    if (!ctx || !this.ChartJS) return;
                    const existing = this.ChartJS.getChart(ctx);
                    if (existing) existing.destroy();
                    this.trafficChart = new this.ChartJS(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [
                                { label: 'Sessions', data: data.sessions, borderColor: '#0D99FF', backgroundColor: 'rgba(13,153,255,0.08)', tension: 0.35, fill: true, borderWidth: 2, pointRadius: 0, pointHoverRadius: 4 },
                                { label: 'Users', data: data.users, borderColor: '#3AC977', backgroundColor: 'rgba(58,201,119,0.08)', tension: 0.35, fill: true, borderWidth: 2, pointRadius: 0, pointHoverRadius: 4 },
                                { label: 'Pageviews', data: data.pageviews, borderColor: '#FF9F00', backgroundColor: 'rgba(255,159,0,0.06)', tension: 0.35, fill: true, borderWidth: 2, pointRadius: 0, pointHoverRadius: 4 },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0b1220',
                                    padding: 10,
                                    cornerRadius: 8,
                                    titleFont: { size: 12, weight: '600' },
                                    bodyFont: { size: 12 },
                                },
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#94a3b8', font: { size: 11 }, maxRotation: 0, autoSkipPadding: 24 },
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#eef2f7' },
                                    ticks: { color: '#94a3b8', font: { size: 11 } },
                                    border: { display: false },
                                },
                            },
                        },
                    });
                },

                async initSourcesChart(data) {
                    const ctx = await this.waitForElement('analyticsSourcesChart');
                    if (!ctx || !this.ChartJS) return;
                    const existing = this.ChartJS.getChart(ctx);
                    if (existing) existing.destroy();
                    this.sourcesChart = new this.ChartJS(ctx, {
                        type: 'doughnut',
                        data: { labels: data.labels, datasets: [{ data: data.data, backgroundColor: data.colors, borderWidth: 0, hoverOffset: 6 }] },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '68%',
                            plugins: {
                                legend: { display: false },
                                tooltip: { backgroundColor: '#0b1220', padding: 10, cornerRadius: 8 },
                            },
                        },
                    });
                },

                async initDeviceChart(data) {
                    const ctx = await this.waitForElement('analyticsDeviceChart');
                    if (!ctx || !this.ChartJS) return;
                    const existing = this.ChartJS.getChart(ctx);
                    if (existing) existing.destroy();
                    this.deviceChart = new this.ChartJS(ctx, {
                        type: 'pie',
                        data: { labels: data.labels, datasets: [{ data: data.data, backgroundColor: data.colors, borderWidth: 0, hoverOffset: 6 }] },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { boxWidth: 8, boxHeight: 8, padding: 12, font: { size: 11 }, color: '#475569', usePointStyle: true, pointStyle: 'rectRounded' },
                                },
                                tooltip: { backgroundColor: '#0b1220', padding: 10, cornerRadius: 8 },
                            },
                        },
                    });
                },

                async initBrowserChart(data) {
                    const ctx = await this.waitForElement('analyticsBrowserChart');
                    if (!ctx || !this.ChartJS) return;
                    const existing = this.ChartJS.getChart(ctx);
                    if (existing) existing.destroy();
                    this.browserChart = new this.ChartJS(ctx, {
                        type: 'bar',
                        data: { labels: data.labels, datasets: [{ label: 'Sessions', data: data.data, backgroundColor: data.colors, borderRadius: 6, borderSkipped: false }] },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: { backgroundColor: '#0b1220', padding: 10, cornerRadius: 8 },
                            },
                            scales: {
                                x: { beginAtZero: true, grid: { color: '#eef2f7' }, ticks: { color: '#94a3b8', font: { size: 11 } }, border: { display: false } },
                                y: { grid: { display: false }, ticks: { color: '#475569', font: { size: 11 } }, border: { display: false } },
                            },
                        },
                    });
                },

                updateCharts(payload) {
                    const data = payload || {};
                    if (data.timeSeries && this.trafficChart) {
                        this.trafficChart.data.labels = data.timeSeries.labels;
                        this.trafficChart.data.datasets[0].data = data.timeSeries.sessions;
                        this.trafficChart.data.datasets[1].data = data.timeSeries.users;
                        this.trafficChart.data.datasets[2].data = data.timeSeries.pageviews;
                        this.trafficChart.update();
                    }
                    if (data.trafficSources && this.sourcesChart) {
                        this.sourcesChart.data.labels = data.trafficSources.labels;
                        this.sourcesChart.data.datasets[0].data = data.trafficSources.data;
                        this.sourcesChart.data.datasets[0].backgroundColor = data.trafficSources.colors;
                        this.sourcesChart.update();
                    }
                    if (data.deviceBreakdown && this.deviceChart) {
                        this.deviceChart.data.labels = data.deviceBreakdown.labels;
                        this.deviceChart.data.datasets[0].data = data.deviceBreakdown.data;
                        this.deviceChart.data.datasets[0].backgroundColor = data.deviceBreakdown.colors;
                        this.deviceChart.update();
                    }
                    if (data.browserBreakdown && this.browserChart) {
                        this.browserChart.data.labels = data.browserBreakdown.labels;
                        this.browserChart.data.datasets[0].data = data.browserBreakdown.data;
                        this.browserChart.data.datasets[0].backgroundColor = data.browserBreakdown.colors;
                        this.browserChart.update();
                    }
                },
            }));
        }

        document.addEventListener('alpine:init', registerAnalyticsChartsComponent);
        if (window.Alpine) {
            registerAnalyticsChartsComponent();
        }
    </script>
@endpush