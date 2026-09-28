<!-- livewire/admin/dashboard/advanced-analytics-component.blade.php -->
<div class="analytics-page" x-data="analyticsCharts()" x-init="initCharts()"
    @update-analytics-charts.window="updateCharts($event.detail)">

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
            <button wire:click="forceRefresh" wire:loading.attr="disabled" class="btn btn-outline-primary btn-sm">
                <span wire:loading.remove wire:target="forceRefresh"><i class="fas fa-sync-alt"></i> Refresh</span>
                <span wire:loading wire:target="forceRefresh"><i class="fas fa-spinner fa-spin"></i> Refreshing…</span>
            </button>
        </div>
    </div>

    <div class="container-fluid">

        @unless($ga4Configured)
            <div class="alert alert-warning d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Google Analytics isn't connected yet.</strong>
                    Set <code>GA4_PROPERTY_ID</code> and <code>GA4_CREDENTIALS_PATH</code> in your <code>.env</code>
                    and drop your service-account JSON in place to start seeing live data here.
                </div>
            </div>
        @endunless

        @if($ga4Configured && (int) ($overview['sessions'] ?? 0) === 0)
            <div class="alert alert-info d-flex align-items-center gap-2">
                <i class="fas fa-info-circle"></i>
                <div>Live data is working. Reports and charts fill in about 24–48 hours after tracking starts.</div>
            </div>
        @endif

        <div class="row">

            <!-- ─── Period selector ─── -->
            <div class="col-12 mb-2">
                <ul class="nav nav-pills mix-chart-tab">
                    @foreach(['7d' => '7 Days', '30d' => '30 Days', '90d' => '90 Days', '12m' => '12 Months'] as $value => $label)
                        <li class="nav-item">
                            <button class="nav-link {{ $period === $value ? 'active' : '' }}"
                                wire:click="$set('period', '{{ $value }}')" type="button">{{ $label }}</button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- ─── LIVE RIGHT NOW ─── -->
            <div class="col-xl-3 col-sm-6" wire:poll.15s="$refresh">
                @php
                    $liveNow = (int) $this->realtimeActiveUsers;
                    $livePages = array_slice($this->realtimeTopPages, 0, 4);
                    $liveMax = max(1, collect($livePages)->max('users') ?? 1);
                @endphp

                <div class="card chart-grd same-card live-hero">
                    <div class="live-hero-orb live-hero-orb--1"></div>
                    <div class="live-hero-orb live-hero-orb--2"></div>

                    <div class="live-hero-body">
                        <div class="live-hero-head">
                            <div class="live-hero-badge">
                                <span class="live-dot"></span>
                                <span>LIVE</span>
                            </div>
                            <span class="live-hero-updated">refreshes every 15s</span>
                        </div>

                        <div class="live-hero-main">
                            <div class="live-hero-rings" aria-hidden="true">
                                <span></span><span></span><span></span>
                            </div>
                            <div class="live-hero-count">{{ $liveNow }}</div>
                            <div class="live-hero-label">
                                {{ Str::plural('visitor', $liveNow) }} on the site right now
                            </div>
                        </div>

                        <div class="live-hero-pages">
                            <div class="live-hero-pages-head">
                                <span>Top active pages</span>
                                <span>{{ count($this->realtimeTopPages) }} tracked</span>
                            </div>

                            @forelse($livePages as $page)
                                @php
                                    $clean = trim(Str::before($page['page'], ' | Polysphere Tech')) ?: $page['page'];
                                    $pct = max(6, (int) round(($page['users'] / $liveMax) * 100));
                                @endphp
                                <div class="live-hero-page">
                                    <div class="live-hero-page-row">
                                        <span class="live-hero-page-name" title="{{ $page['page'] }}">{{ $clean }}</span>
                                        <span class="live-hero-page-count">{{ $page['users'] }}</span>
                                    </div>
                                    <div class="live-hero-page-bar">
                                        <div class="live-hero-page-bar-fill" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="live-hero-empty">Nobody on the site right now</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── Sessions ─── -->
            <div class="col-xl-3 col-sm-6">
                <div class="card chart-grd same-card">
                    <div class="card-body depostit-card p-0">
                        <div class="depostit-card-media d-flex justify-content-between pb-0">
                            <div>
                                <h6>Sessions</h6>
                                <h3>{{ number_format($overview['sessions']) }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <i class="fas fa-chart-line text-primary"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Users: {{ number_format($overview['active_users']) }}</small>
                            <span class="badge bg-success ms-2">+{{ number_format($overview['new_users']) }} new</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── Bounce Rate ─── -->
            <div class="col-xl-3 col-sm-6">
                <div class="card chart-grd same-card">
                    <div class="card-body depostit-card p-0">
                        <div class="depostit-card-media d-flex justify-content-between pb-0">
                            <div>
                                <h6>Bounce Rate</h6>
                                <h3>{{ $overview['bounce_rate'] }}%</h3>
                            </div>
                            <div class="icon-box bg-danger-light">
                                <i class="fas fa-sign-out-alt text-danger"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">Engagement rate: {{ $overview['engagement_rate'] }}%</small>
                            <div class="kpi-meter mt-2">
                                <span style="width: {{ min(100, (float) $overview['engagement_rate']) }}%"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── Avg Session Duration / Conversions ─── -->
            <div class="col-xl-3 col-sm-6">
                <div class="card chart-grd same-card">
                    <div class="card-body depostit-card p-0">
                        <div class="depostit-card-media d-flex justify-content-between pb-0">
                            <div>
                                <h6>Avg. Session</h6>
                                <h3>{{ $overview['avg_session_duration'] }}</h3>
                            </div>
                            <div class="icon-box bg-info-light">
                                <i class="fas fa-clock text-info"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-primary">Conversions:
                                {{ number_format($overview['conversions']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── TRAFFIC OVER TIME ─── -->
            <div class="col-xl-8">
                <div class="card overflow-hidden">
                    <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-start">
                        <h4 class="heading mb-0">Traffic Over Time</h4>
                        <div class="chart-legend">
                            <span><i style="background:#0D99FF"></i> Sessions</span>
                            <span><i style="background:#3AC977"></i> Users</span>
                            <span><i style="background:#FF9F00"></i> Pageviews</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div wire:ignore style="height:260px;">
                            <canvas id="analyticsTrafficChart" style="width:100%; height:100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── TRAFFIC SOURCES ─── -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Traffic Sources</h4>
                    </div>
                    <div class="card-body">
                        <div wire:ignore style="height:200px;">
                            <canvas id="analyticsSourcesChart" style="width:100%; height:100%;"></canvas>
                        </div>
                        <div class="project-date mt-3">
                            @foreach($trafficSources['labels'] as $index => $label)
                                <div class="project-media">
                                    <p class="mb-0">
                                        <svg class="me-2" width="12" height="13" viewBox="0 0 12 13" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect y="0.5" width="12" height="12" rx="3"
                                                fill="{{ $trafficSources['colors'][$index] }}" />
                                        </svg>
                                        {{ $label }}
                                    </p>
                                    <span>{{ number_format($trafficSources['data'][$index]) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── DEVICE BREAKDOWN ─── -->
            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Devices</h4>
                    </div>
                    <div class="card-body">
                        <div wire:ignore style="height:180px;">
                            <canvas id="analyticsDeviceChart" style="width:100%; height:100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── BROWSER BREAKDOWN ─── -->
            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Browsers</h4>
                    </div>
                    <div class="card-body">
                        <div wire:ignore style="height:180px;">
                            <canvas id="analyticsBrowserChart" style="width:100%; height:100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── TOP COUNTRIES ─── -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Top Countries</h4>
                    </div>
                    <div class="card-body p-0 dz-scroll" style="max-height: 320px;">
                        <ul class="list-group list-group-flush">
                            @forelse($topCountries as $country)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $country['country'] }}</span>
                                        <span>{{ number_format($country['users']) }} <small
                                                class="text-muted">({{ $country['share'] }}%)</small></span>
                                    </div>
                                    <div class="progress mt-1" style="height:4px;">
                                        <div class="progress-bar bg-primary" style="width:{{ $country['share'] }}%"></div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted">No data for this period</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ─── TOP PAGES ─── -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Top Pages</h4>
                    </div>
                    <div class="card-body p-0 dz-scroll" style="max-height: 320px;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Page</th>
                                    <th class="text-end">Views</th>
                                    <th class="text-end">Avg. Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPages as $page)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $page['title'] }}</div>
                                            <small class="text-muted">{{ $page['path'] }}</small>
                                        </td>
                                        <td class="text-end">{{ number_format($page['views']) }}</td>
                                        <td class="text-end">{{ $page['avg_duration'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No data for this period</td>
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

<style>
    /* ═══════════════════════════════════════════════════════════════
       Polish layer — scoped under .analytics-page so nothing leaks
       into the rest of the theme. All rules target the theme's own
       classes so layout is unchanged — only the surface gets upgraded.
       ═══════════════════════════════════════════════════════════════ */
    .analytics-page .card {
        border: 1px solid #e8eef5;
        border-radius: 14px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .04), 0 8px 24px -14px rgba(15, 23, 42, .14);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .analytics-page .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(15, 23, 42, .06), 0 20px 40px -20px rgba(15, 23, 42, .22);
    }

    /* ─── KPI number weight + tabular numerics ───────────────────── */
    .analytics-page .depostit-card-media h3 {
        font-variant-numeric: tabular-nums;
        letter-spacing: -.02em;
    }

    .analytics-page .depostit-card-media h6 {
        color: #64748b;
        font-weight: 600;
        letter-spacing: .01em;
    }

    .analytics-page .icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* ─── Bounce-rate meter ──────────────────────────────────────── */
    .analytics-page .kpi-meter {
        height: 5px;
        border-radius: 999px;
        background: #eef2f7;
        overflow: hidden;
    }

    .analytics-page .kpi-meter>span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #3AC977, #7ee2a8);
        transition: width .4s ease;
    }

    /* ═══════════════════════════════════════════════════════════════
       LIVE HERO — the star of the row
       ═══════════════════════════════════════════════════════════════ */
    .analytics-page .live-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        height: 100%;
        min-height: 260px;
        padding: 0 !important;
        border: 1px solid rgba(255, 255, 255, .06) !important;
        background:
            radial-gradient(120% 100% at 0% 0%, #16233c 0%, #0c1424 55%, #0a0f1c 100%) !important;
        box-shadow:
            0 1px 2px rgba(15, 23, 42, .15),
            0 24px 48px -24px rgba(9, 13, 26, .75),
            inset 0 1px 0 rgba(255, 255, 255, .05) !important;
    }

    .analytics-page .live-hero:hover {
        transform: translateY(-2px);
        box-shadow:
            0 4px 8px rgba(15, 23, 42, .2),
            0 28px 56px -24px rgba(9, 13, 26, .85),
            inset 0 1px 0 rgba(255, 255, 255, .05) !important;
    }

    .analytics-page .live-hero-orb {
        position: absolute;
        pointer-events: none;
        border-radius: 999px;
        filter: blur(50px);
        z-index: 0;
    }

    .analytics-page .live-hero-orb--1 {
        top: -70px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, #3AC977 0%, rgba(58, 201, 119, 0) 70%);
        opacity: .55;
        animation: live-orb-drift 8s ease-in-out infinite alternate;
    }

    .analytics-page .live-hero-orb--2 {
        bottom: -60px;
        left: -50px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, #0D99FF 0%, rgba(13, 153, 255, 0) 70%);
        opacity: .35;
        animation: live-orb-drift 10s ease-in-out infinite alternate-reverse;
    }

    @keyframes live-orb-drift {
        to {
            transform: translate3d(12px, 8px, 0) scale(1.08);
        }
    }

    .analytics-page .live-hero-body {
        position: relative;
        z-index: 1;
        padding: 22px;
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 260px;
        color: #e6edf7;
    }

    .analytics-page .live-hero-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        font-size: 11px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgba(230, 237, 247, .55);
    }

    .analytics-page .live-hero-badge {
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

    .analytics-page .live-hero-updated {
        text-transform: none;
        letter-spacing: 0;
        font-size: 11px;
    }

    .analytics-page .live-hero-main {
        position: relative;
        margin-bottom: 20px;
    }

    .analytics-page .live-hero-rings {
        position: absolute;
        top: -6px;
        left: -4px;
        width: 88px;
        height: 88px;
        pointer-events: none;
    }

    .analytics-page .live-hero-rings span {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 1px solid rgba(58, 201, 119, .35);
        animation: live-ring 3s ease-out infinite;
    }

    .analytics-page .live-hero-rings span:nth-child(2) {
        animation-delay: 1s;
    }

    .analytics-page .live-hero-rings span:nth-child(3) {
        animation-delay: 2s;
    }

    @keyframes live-ring {
        0% {
            transform: scale(.6);
            opacity: .9;
        }

        100% {
            transform: scale(1.6);
            opacity: 0;
        }
    }

    .analytics-page .live-hero-count {
        font-size: 56px;
        font-weight: 800;
        letter-spacing: -.04em;
        line-height: 1;
        color: #ffffff;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 4px 24px rgba(58, 201, 119, .25);
    }

    .analytics-page .live-hero-label {
        margin-top: 6px;
        font-size: 12.5px;
        color: rgba(230, 237, 247, .7);
    }

    .analytics-page .live-hero-pages {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, .08);
    }

    .analytics-page .live-hero-pages-head {
        display: flex;
        justify-content: space-between;
        font-size: 10.5px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: rgba(230, 237, 247, .45);
        margin-bottom: 10px;
    }

    .analytics-page .live-hero-page {
        margin-bottom: 10px;
    }

    .analytics-page .live-hero-page:last-child {
        margin-bottom: 0;
    }

    .analytics-page .live-hero-page-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        font-size: 12.5px;
        color: rgba(230, 237, 247, .9);
        margin-bottom: 5px;
    }

    .analytics-page .live-hero-page-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-width: 0;
    }

    .analytics-page .live-hero-page-count {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        color: #ffffff;
        flex: none;
    }

    .analytics-page .live-hero-page-bar {
        height: 4px;
        background: rgba(255, 255, 255, .08);
        border-radius: 999px;
        overflow: hidden;
    }

    .analytics-page .live-hero-page-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #3AC977, #7ee2a8);
        box-shadow: 0 0 8px rgba(58, 201, 119, .6);
        transition: width .4s ease;
    }

    .analytics-page .live-hero-empty {
        font-size: 12.5px;
        color: rgba(230, 237, 247, .55);
        padding: 6px 0;
    }

    /* ─── live dot pulse (used inside the hero badge) ────────────── */
    .analytics-page .live-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #3AC977;
        box-shadow: 0 0 0 0 rgba(58, 201, 119, .7);
        animation: live-pulse 1.6s ease-out infinite;
    }

    @keyframes live-pulse {
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

    /* ═══════════════════════════════════════════════════════════════
       Period pills — segmented-control restyle
       ═══════════════════════════════════════════════════════════════ */
    .analytics-page .nav-pills.mix-chart-tab {
        gap: 4px;
        padding: 4px;
        border-radius: 12px;
        background: #eef2f7;
        border: 1px solid #e3e9f0;
        display: inline-flex;
        flex-wrap: wrap;
    }

    .analytics-page .nav-pills.mix-chart-tab .nav-item {
        margin: 0;
    }

    .analytics-page .nav-pills.mix-chart-tab .nav-link {
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: 0;
        transition: background .15s ease, color .15s ease, box-shadow .15s ease;
    }

    .analytics-page .nav-pills.mix-chart-tab .nav-link:hover {
        color: #1e293b;
    }

    .analytics-page .nav-pills.mix-chart-tab .nav-link.active {
        background: #ffffff;
        color: #0b1220;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .06), 0 4px 12px -6px rgba(15, 23, 42, .15);
    }

    /* ─── Chart legend chips next to card titles ─────────────────── */
    .analytics-page .chart-legend {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
    }

    .analytics-page .chart-legend span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .analytics-page .chart-legend i {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-radius: 3px;
    }

    /* ─── Traffic sources legend rows ────────────────────────────── */
    .analytics-page .project-date .project-media {
        padding: 6px 0;
        border-bottom: 1px dashed #eef2f7;
    }

    .analytics-page .project-date .project-media:last-child {
        border-bottom: 0;
    }

    .analytics-page .project-date .project-media p {
        color: #334155;
        font-size: 12.5px;
    }

    .analytics-page .project-date .project-media span {
        font-weight: 700;
        color: #0b1220;
        font-variant-numeric: tabular-nums;
    }

    /* ─── Top countries list polish ──────────────────────────────── */
    .analytics-page .list-group-item {
        border-color: #f1f5f9;
        padding: 12px 20px;
        transition: background .12s ease;
    }

    .analytics-page .list-group-item:hover {
        background: #f8fbff;
    }

    .analytics-page .list-group-item>div>span:last-child {
        font-weight: 700;
        color: #0b1220;
        font-variant-numeric: tabular-nums;
    }

    /* ─── Tables ─────────────────────────────────────────────────── */
    .analytics-page .table {
        font-size: 13px;
        margin-bottom: 0;
    }

    .analytics-page .table thead th {
        font-size: 11px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #eef2f7;
        padding: 12px 20px;
        white-space: nowrap;
        font-weight: 700;
    }

    .analytics-page .table tbody td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .analytics-page .table tbody tr:last-child td {
        border-bottom: 0;
    }

    .analytics-page .table-hover tbody tr:hover {
        background: #f8fbff;
    }

    .analytics-page .table tbody td.text-end,
    .analytics-page .table thead th.text-end {
        font-variant-numeric: tabular-nums;
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
        // "canvas already in use" guard). See dashboard-component.blade.php
        // for the full rationale in comments.
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
                                    backgroundColor: '#0b1220', padding: 10, cornerRadius: 8,
                                    titleFont: { size: 12, weight: '600' }, bodyFont: { size: 12 },
                                },
                            },
                            scales: {
                                x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 }, maxRotation: 0, autoSkipPadding: 24 } },
                                y: { beginAtZero: true, grid: { color: '#eef2f7' }, ticks: { color: '#94a3b8', font: { size: 11 } }, border: { display: false } },
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
                            responsive: true, maintainAspectRatio: false, cutout: '68%',
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
                            responsive: true, maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 8, boxHeight: 8, padding: 12, font: { size: 11 }, color: '#475569', usePointStyle: true, pointStyle: 'rectRounded' } },
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
                            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
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