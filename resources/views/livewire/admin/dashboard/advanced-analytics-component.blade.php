<!-- livewire/admin/dashboard/advanced-analytics-component.blade.php -->
<div x-data="analyticsCharts()" x-init="initCharts()" @update-analytics-charts.window="updateCharts($event.detail)">

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
                <div class="card chart-grd same-card border-start border-4 border-success">
                    <div class="card-body depostit-card p-0">
                        <div class="depostit-card-media d-flex justify-content-between pb-0">
                            <div>
                                <h6 class="d-flex align-items-center gap-1">
                                    <span class="live-dot"></span> Live Right Now
                                </h6>
                                <h3>{{ $this->realtimeActiveUsers }}</h3>
                            </div>
                            <div class="icon-box bg-success-light">
                                <i class="fas fa-bolt text-success"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            @forelse($this->realtimeTopPages as $page)
                                <div class="d-flex justify-content-between small text-muted">
                                    <span class="text-truncate" style="max-width: 160px;"
                                        title="{{ $page['page'] }}">{{ $page['page'] }}</span>
                                    <span>{{ $page['users'] }}</span>
                                </div>
                            @empty
                                <small class="text-muted">No active visitors</small>
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
                    <div class="card-header border-0 pb-0">
                        <h4 class="heading mb-0">Traffic Over Time</h4>
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
    .live-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #3AC977;
        animation: live-pulse 1.6s infinite;
    }

    @keyframes live-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(58, 201, 119, 0.6);
        }

        70% {
            box-shadow: 0 0 0 8px rgba(58, 201, 119, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(58, 201, 119, 0);
        }
    }
</style>

@push('scripts')
    <script>
        // Same bootstrap pattern as before — only the per-chart options
        // below are upgraded (rounded bars, softer grid, styled tooltips,
        // smoother lines, thinner doughnut ring, cleaner legend swatches).
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
                                { label: 'Sessions',  data: data.sessions,  borderColor: '#0D99FF', backgroundColor: 'rgba(13,153,255,0.10)', tension: 0.35, fill: true, borderWidth: 2, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#0D99FF', pointHoverBorderColor: '#ffffff', pointHoverBorderWidth: 2 },
                                { label: 'Users',     data: data.users,     borderColor: '#3AC977', backgroundColor: 'rgba(58,201,119,0.10)', tension: 0.35, fill: true, borderWidth: 2, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#3AC977', pointHoverBorderColor: '#ffffff', pointHoverBorderWidth: 2 },
                                { label: 'Pageviews', data: data.pageviews, borderColor: '#FF9F00', backgroundColor: 'rgba(255,159,0,0.08)',  tension: 0.35, fill: true, borderWidth: 2, pointRadius: 0, pointHoverRadius: 5, pointHoverBackgroundColor: '#FF9F00', pointHoverBorderColor: '#ffffff', pointHoverBorderWidth: 2 },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        pointStyle: 'rectRounded',
                                        boxWidth: 8,
                                        boxHeight: 8,
                                        padding: 16,
                                        color: '#64748b',
                                        font: { size: 12, weight: '500' },
                                    },
                                },
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
                                    ticks: {
                                        color: '#94a3b8',
                                        font: { size: 11 },
                                        maxRotation: 0,
                                        autoSkipPadding: 24,
                                    },
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#eef2f7', drawTicks: false },
                                    border: { display: false },
                                    ticks: {
                                        color: '#94a3b8',
                                        font: { size: 11 },
                                        padding: 8,
                                    },
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