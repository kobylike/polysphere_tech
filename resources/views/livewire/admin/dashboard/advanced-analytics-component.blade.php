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

        {{-- g-4 = 1.5rem (24px) gutters on all sides, including BETWEEN wrapped rows. --}}
        <div class="row g-4">

            <!-- ─── Period selector ─── -->
            <div class="col-12">
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
                                {{-- {{ Str::plural('visitor', $liveNow) }} on the site right now --}}
                                {{ Str::plural('visitor', $liveNow) }} in the last 5 minutes
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
                <div class="card chart-grd same-card kpi-card kpi-card--blue">
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
                        <div class="mt-2 kpi-foot">
                            <span class="kpi-chip">
                                <i class="fas fa-users"></i>
                                {{ number_format($overview['active_users']) }} users
                            </span>
                            <span class="kpi-chip kpi-chip--green">
                                <i class="fas fa-arrow-up"></i>
                                {{ number_format($overview['new_users']) }} new
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── Bounce Rate ─── -->
            <div class="col-xl-3 col-sm-6">
                <div class="card chart-grd same-card kpi-card kpi-card--rose">
                    <div class="card-body depostit-card p-0">
                        <div class="depostit-card-media d-flex justify-content-between pb-0">
                            <div>
                                <h6>Bounce Rate</h6>
                                <h3>{{ $overview['bounce_rate'] }}<span class="kpi-unit">%</span></h3>
                            </div>
                            <div class="icon-box bg-danger-light">
                                <i class="fas fa-sign-out-alt text-danger"></i>
                            </div>
                        </div>
                        <div class="mt-2 kpi-foot">
                            <span class="kpi-chip kpi-chip--soft">
                                <i class="fas fa-heart"></i>
                                {{ $overview['engagement_rate'] }}% engaged
                            </span>
                        </div>
                        <div class="kpi-meter mt-2">
                            <span style="width: {{ min(100, (float) $overview['engagement_rate']) }}%"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── Avg Session Duration / Conversions ─── -->
            <div class="col-xl-3 col-sm-6">
                <div class="card chart-grd same-card kpi-card kpi-card--violet">
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
                        <div class="mt-2 kpi-foot">
                            <span class="kpi-chip kpi-chip--violet">
                                <i class="fas fa-bullseye"></i>
                                {{ number_format($overview['conversions']) }} conversions
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── TRAFFIC OVER TIME ─── -->
            <div class="col-xl-8">
                <div class="card overflow-hidden chart-card">
                    <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="heading mb-0">Traffic Over Time</h4>
                            <p class="chart-sub">Daily sessions, users and pageviews</p>
                        </div>
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
                <div class="card chart-card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Traffic Sources</h4>
                        <p class="chart-sub">Sessions by channel</p>
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
                <div class="card chart-card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Devices</h4>
                        <p class="chart-sub">Sessions by device type</p>
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
                <div class="card chart-card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Browsers</h4>
                        <p class="chart-sub">Sessions by browser</p>
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
                <div class="card chart-card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Top Countries</h4>
                        <p class="chart-sub">By active users</p>
                    </div>
                    <div class="card-body p-0 dz-scroll" style="max-height: 320px;">
                        <ul class="list-group list-group-flush">
                            @forelse($topCountries as $country)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-baseline">
                                        <span class="country-name">{{ $country['country'] }}</span>
                                        <span class="country-value">{{ number_format($country['users']) }}
                                            <small class="text-muted">({{ $country['share'] }}%)</small></span>
                                    </div>
                                    <div class="progress mt-2" style="height:5px;">
                                        <div class="progress-bar bg-primary" style="width:{{ $country['share'] }}%"></div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-4">No data for this period</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ─── TOP PAGES ─── -->
            <div class="col-xl-8">
                <div class="card chart-card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Top Pages</h4>
                        <p class="chart-sub">Most viewed pages in the selected period</p>
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
                                            <div class="fw-semibold page-title-cell">{{ $page['title'] }}</div>
                                            <small class="text-muted page-path-cell">{{ $page['path'] }}</small>
                                        </td>
                                        <td class="text-end num-cell">{{ number_format($page['views']) }}</td>
                                        <td class="text-end num-cell">{{ $page['avg_duration'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No data for this period</td>
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
       Design system — scoped under .analytics-page so nothing leaks.
       ═══════════════════════════════════════════════════════════════ */
    .analytics-page {
        --ink: #0b1220;
        --ink-2: #1e293b;
        --muted: #64748b;
        --border: #e8eef5;
        --radius: 16px;
        --shadow-sm: 0 1px 2px rgba(15, 23, 42, .04), 0 8px 24px -14px rgba(15, 23, 42, .14);
        --shadow-lg: 0 4px 10px rgba(15, 23, 42, .06), 0 24px 44px -20px rgba(15, 23, 42, .22);
    }

    /* ─── Universal card surface ─────────────────────────────────── */
    .analytics-page .card {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        background: #fff;
        overflow: hidden;
    }

    .analytics-page .card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        border-color: #dbe5ee;
    }

    /* ═══════════════════════════════════════════════════════════════
       LIVE HERO — light with soft green tint
       ═══════════════════════════════════════════════════════════════ */
    .analytics-page .live-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        height: 100%;
        min-height: 260px;
        padding: 0 !important;
        border: 1px solid #d7f0e2 !important;
        background:
            radial-gradient(120% 100% at 0% 0%, #f2fbf6 0%, #eaf7f0 55%, #e4f5ec 100%) !important;
        box-shadow:
            0 1px 2px rgba(15, 23, 42, .04),
            0 12px 28px -18px rgba(20, 120, 70, .25) !important;
    }

    .analytics-page .live-hero:hover {
        transform: translateY(-2px);
        border-color: #c5ebd5 !important;
        box-shadow:
            0 4px 10px rgba(15, 23, 42, .05),
            0 20px 40px -20px rgba(20, 120, 70, .3) !important;
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
        background: radial-gradient(circle, rgba(58, 201, 119, .30) 0%, rgba(58, 201, 119, 0) 70%);
        animation: live-orb-drift 8s ease-in-out infinite alternate;
    }

    .analytics-page .live-hero-orb--2 {
        bottom: -60px;
        left: -50px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(13, 153, 255, .22) 0%, rgba(13, 153, 255, 0) 70%);
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
        color: var(--ink);
    }

    .analytics-page .live-hero-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        font-size: 11px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #64748b;
    }

    .analytics-page .live-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 4px 10px 4px 8px;
        border-radius: 999px;
        background: rgba(58, 201, 119, .15);
        border: 1px solid rgba(58, 201, 119, .45);
        color: #158f4c;
        font-weight: 700;
    }

    .analytics-page .live-hero-updated {
        text-transform: none;
        letter-spacing: 0;
        font-size: 11px;
        color: #64748b;
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
        border: 1px solid rgba(58, 201, 119, .45);
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
        color: #0a7a44;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 2px 12px rgba(58, 201, 119, .18);
    }

    .analytics-page .live-hero-label {
        margin-top: 6px;
        font-size: 12.5px;
        color: #475569;
    }

    .analytics-page .live-hero-pages {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid rgba(15, 23, 42, .07);
    }

    .analytics-page .live-hero-pages-head {
        display: flex;
        justify-content: space-between;
        font-size: 10.5px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #94a3b8;
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
        color: #1e293b;
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
        color: #0a7a44;
        flex: none;
    }

    .analytics-page .live-hero-page-bar {
        height: 4px;
        background: rgba(58, 201, 119, .15);
        border-radius: 999px;
        overflow: hidden;
    }

    .analytics-page .live-hero-page-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #3AC977, #7ee2a8);
        transition: width .4s ease;
    }

    .analytics-page .live-hero-empty {
        font-size: 12.5px;
        color: #64748b;
        padding: 6px 0;
    }

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
       KPI CARDS
       ═══════════════════════════════════════════════════════════════ */
    .analytics-page .kpi-card {
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .analytics-page .kpi-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        border-radius: var(--radius) var(--radius) 0 0;
        opacity: .9;
        z-index: 1;
    }

    .analytics-page .kpi-card--blue::before {
        background: linear-gradient(90deg, #0D99FF, #6ec2ff);
    }

    .analytics-page .kpi-card--rose::before {
        background: linear-gradient(90deg, #FF5E5E, #ffa1a1);
    }

    .analytics-page .kpi-card--violet::before {
        background: linear-gradient(90deg, #8A5CF6, #b89bff);
    }

    .analytics-page .kpi-card .card-body {
        padding: 22px 22px 20px !important;
    }

    .analytics-page .kpi-card .depostit-card-media h6 {
        color: var(--muted);
        font-weight: 600;
        letter-spacing: .02em;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .analytics-page .kpi-card .depostit-card-media h3 {
        font-variant-numeric: tabular-nums;
        letter-spacing: -.03em;
        font-weight: 800;
        color: var(--ink);
        font-size: 30px;
        line-height: 1;
        margin: 0;
    }

    .analytics-page .kpi-unit {
        font-size: 15px;
        font-weight: 600;
        color: var(--muted);
        margin-left: 3px;
        letter-spacing: 0;
    }

    .analytics-page .kpi-card .icon-box {
        width: 46px;
        height: 46px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        transition: transform .2s ease;
    }

    .analytics-page .kpi-card:hover .icon-box {
        transform: scale(1.06);
    }

    .analytics-page .kpi-foot {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .analytics-page .kpi-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 600;
        background: #eef2f7;
        color: #475569;
        line-height: 1;
        letter-spacing: .01em;
    }

    .analytics-page .kpi-chip i {
        font-size: 10px;
        opacity: .75;
    }

    .analytics-page .kpi-chip--green {
        background: rgba(58, 201, 119, .12);
        color: #1f8f4c;
    }

    .analytics-page .kpi-chip--violet {
        background: rgba(138, 92, 246, .12);
        color: #6d3fe0;
    }

    .analytics-page .kpi-chip--soft {
        background: #f1f5f9;
        color: #475569;
    }

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
        transition: width .5s ease;
    }

    /* ═══════════════════════════════════════════════════════════════
       CHART CARDS
       ═══════════════════════════════════════════════════════════════ */
    .analytics-page .chart-card .card-header {
        padding: 20px 22px 8px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .analytics-page .chart-card .heading {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.01em;
        color: var(--ink);
    }

    .analytics-page .chart-sub {
        font-size: 12px;
        color: var(--muted);
        margin: 2px 0 0;
    }

    .analytics-page .chart-card .card-body {
        padding: 8px 20px 20px;
    }

    /* ─── Chart legend chips ─────────────────────────────────────── */
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
        color: var(--muted);
        font-weight: 500;
    }

    .analytics-page .chart-legend i {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-radius: 3px;
    }

    /* ─── Traffic sources rows ───────────────────────────────────── */
    .analytics-page .project-date .project-media {
        padding: 7px 0;
        border-bottom: 1px dashed #eef2f7;
    }

    .analytics-page .project-date .project-media:last-child {
        border-bottom: 0;
    }

    .analytics-page .project-date .project-media p {
        color: #334155;
        font-size: 12.5px;
        font-weight: 500;
    }

    .analytics-page .project-date .project-media span {
        font-weight: 700;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }

    /* ─── Top countries ──────────────────────────────────────────── */
    .analytics-page .list-group-item {
        border-color: #f1f5f9;
        padding: 12px 22px;
        transition: background .12s ease;
    }

    .analytics-page .list-group-item:hover {
        background: #f8fbff;
    }

    .analytics-page .country-name {
        color: var(--ink-2);
        font-weight: 500;
        font-size: 13px;
    }

    .analytics-page .country-value {
        font-weight: 700;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
        font-size: 13px;
    }

    .analytics-page .country-value small {
        font-weight: 500;
        font-size: 11px;
    }

    .analytics-page .progress {
        background: #eef2f7;
        border-radius: 999px;
    }

    .analytics-page .progress-bar.bg-primary {
        background: linear-gradient(90deg, #0D99FF, #6ec2ff) !important;
        border-radius: 999px;
        transition: width .5s ease;
    }

    /* ═══════════════════════════════════════════════════════════════
       TABLE
       ═══════════════════════════════════════════════════════════════ */
    .analytics-page .table {
        font-size: 13.5px;
        margin-bottom: 0;
    }

    .analytics-page .table thead th {
        font-size: 11px;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
        background: #f8fafc;
        border-top: 1px solid #eef2f7;
        border-bottom: 1px solid #eef2f7;
        padding: 13px 22px;
        white-space: nowrap;
        font-weight: 700;
    }

    .analytics-page .table tbody td {
        padding: 15px 22px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .analytics-page .table tbody tr:last-child td {
        border-bottom: 0;
    }

    .analytics-page .table-hover tbody tr {
        transition: background .12s ease;
    }

    .analytics-page .table-hover tbody tr:hover {
        background: #f8fbff;
    }

    .analytics-page .page-title-cell {
        color: var(--ink);
        font-weight: 600;
        line-height: 1.3;
    }

    .analytics-page .page-path-cell {
        font-size: 11.5px;
        display: inline-block;
        margin-top: 3px;
        word-break: break-all;
    }

    .analytics-page .num-cell {
        font-variant-numeric: tabular-nums;
        color: var(--ink-2);
        font-weight: 500;
    }

    /* ═══════════════════════════════════════════════════════════════
       PERIOD PILLS
       ═══════════════════════════════════════════════════════════════ */
    .analytics-page ul.nav.nav-pills.mix-chart-tab {
        gap: 4px;
        padding: 4px;
        border-radius: 12px;
        background: #eef2f7;
        border: 1px solid #e3e9f0;
        display: inline-flex;
        flex-wrap: wrap;
    }

    .analytics-page ul.nav.nav-pills.mix-chart-tab .nav-item {
        margin: 0;
    }

    .analytics-page ul.nav.nav-pills.mix-chart-tab .nav-item .nav-link,
    .analytics-page ul.nav.nav-pills.mix-chart-tab .nav-item button.nav-link {
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: #475569 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        transition: background .15s ease, color .15s ease, box-shadow .15s ease;
        opacity: 1 !important;
    }

    .analytics-page ul.nav.nav-pills.mix-chart-tab .nav-item .nav-link:hover {
        color: #0b1220 !important;
        background: rgba(255, 255, 255, .6) !important;
    }

    .analytics-page ul.nav.nav-pills.mix-chart-tab .nav-item .nav-link.active,
    .analytics-page ul.nav.nav-pills.mix-chart-tab .nav-item button.nav-link.active {
        color: #0b1220 !important;
        background: #ffffff !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, .08), 0 4px 12px -6px rgba(15, 23, 42, .18) !important;
        font-weight: 700 !important;
    }
</style>

@push('scripts')
    <script>
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
                                { label: 'Sessions', data: data.sessions, borderColor: '#0D99FF', backgroundColor: 'rgba(13,153,255,0.10)', tension: 0.35, fill: true, borderWidth: 2, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#0D99FF', pointHoverBorderColor: '#ffffff', pointHoverBorderWidth: 2 },
                                { label: 'Users', data: data.users, borderColor: '#3AC977', backgroundColor: 'rgba(58,201,119,0.10)', tension: 0.35, fill: true, borderWidth: 2, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#3AC977', pointHoverBorderColor: '#ffffff', pointHoverBorderWidth: 2 },
                                { label: 'Pageviews', data: data.pageviews, borderColor: '#FF9F00', backgroundColor: 'rgba(255,159,0,0.08)', tension: 0.35, fill: true, borderWidth: 2, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#FF9F00', pointHoverBorderColor: '#ffffff', pointHoverBorderWidth: 2 },
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
                                    padding: 12,
                                    cornerRadius: 10,
                                    titleColor: '#cbd5e1',
                                    titleFont: { size: 12, weight: '600' },
                                    bodyColor: '#ffffff',
                                    bodyFont: { size: 12, weight: '500' },
                                    bodySpacing: 6,
                                    boxPadding: 6,
                                    usePointStyle: true,
                                },
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    border: { display: false },
                                    ticks: { color: '#94a3b8', font: { size: 11 }, maxRotation: 0, autoSkipPadding: 24 },
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#eef2f7', drawTicks: false },
                                    border: { display: false },
                                    ticks: { color: '#94a3b8', font: { size: 11 }, padding: 8 },
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
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.data,
                                backgroundColor: data.colors,
                                borderWidth: 0,
                                hoverOffset: 8,
                                spacing: 2,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '68%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0b1220',
                                    padding: 12,
                                    cornerRadius: 10,
                                    bodyColor: '#ffffff',
                                    bodyFont: { size: 12, weight: '500' },
                                    usePointStyle: true,
                                },
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
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.data,
                                backgroundColor: data.colors,
                                borderWidth: 0,
                                hoverOffset: 8,
                                spacing: 2,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        pointStyle: 'rectRounded',
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        padding: 14,
                                        color: '#64748b',
                                        font: { size: 12, weight: '500' },
                                    },
                                },
                                tooltip: {
                                    backgroundColor: '#0b1220',
                                    padding: 12,
                                    cornerRadius: 10,
                                    bodyColor: '#ffffff',
                                    bodyFont: { size: 12, weight: '500' },
                                    usePointStyle: true,
                                },
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
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Sessions',
                                data: data.data,
                                backgroundColor: data.colors,
                                borderRadius: 6,
                                borderSkipped: false,
                                barThickness: 14,
                            }],
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#0b1220',
                                    padding: 12,
                                    cornerRadius: 10,
                                    bodyColor: '#ffffff',
                                    bodyFont: { size: 12, weight: '500' },
                                    usePointStyle: true,
                                },
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    grid: { color: '#eef2f7', drawTicks: false },
                                    border: { display: false },
                                    ticks: { color: '#94a3b8', font: { size: 11 }, padding: 6 },
                                },
                                y: {
                                    grid: { display: false },
                                    border: { display: false },
                                    ticks: { color: '#475569', font: { size: 12 }, padding: 6 },
                                },
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