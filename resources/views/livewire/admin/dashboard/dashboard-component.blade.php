<!-- livewire/admin/dashboard/dashboard-component.blade.php -->
<div x-data="dashboardCharts()" x-init="initCharts()" @update-charts.window="updateCharts($event.detail)">

    <!-- ─── Page Header ────────────────────────────────────────────── -->
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Dashboard</h5>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:void(0)" wire:navigate.hover>
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M2.125 6.375L8.5 1.41667L14.875 6.375V14.1667C14.875 14.5424 14.7257 14.9027 14.4601 15.1684C14.1944 15.4341 13.8341 15.5833 13.4583 15.5833H3.54167C3.16594 15.5833 2.80561 15.4341 2.53993 15.1684C2.27426 14.9027 2.125 14.5424 2.125 14.1667V6.375Z"
                            stroke="#2C2C2C" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M6.375 15.5833V8.5H10.625V15.5833" stroke="#2C2C2C" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Home
                </a>
            </li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)" wire:navigate.hover>Dashboard</a></li>
        </ol>
        <a class="text-primary fs-13" data-bs-toggle="offcanvas" href="#offcanvasExample1" role="button"
            aria-controls="offcanvasExample1" wire:navigate.hover>+ Add Task</a>
    </div>

    <div class="container-fluid">
        <div class="row">

            <!-- ─── EXECUTIVE SUMMARY KPI CARDS ─── -->
            <div class="col-xl-9 wid-100">
                <div class="row">
                    <!-- Total Users -->
                    <div class="col-xl-3 col-sm-6">
                        <div class="card chart-grd same-card">
                            <div class="card-body depostit-card p-0">
                                <div class="depostit-card-media d-flex justify-content-between pb-0">
                                    <div>
                                        <h6>Total Users</h6>
                                        <h3>{{ $stats['total_users'] }}</h3>
                                    </div>
                                    <div class="icon-box bg-primary-light">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M17.5 19.375V17.9167C17.5 13.675 14.075 10.2083 10 10.2083C5.925 10.2083 2.5 13.675 2.5 17.9167V19.375M10 10.2083C12.3467 10.2083 14.1667 8.38833 14.1667 6.04167C14.1667 3.695 12.3467 1.875 10 1.875C7.65333 1.875 5.83333 3.695 5.83333 6.04167C5.83333 8.38833 7.65333 10.2083 10 10.2083Z"
                                                stroke="var(--primary)" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Active: {{ $stats['active_users'] }}</small>
                                    <span class="badge bg-success ms-2">+{{ $stats['new_users_today'] }} today</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Projects -->
                    <div class="col-xl-3 col-sm-6">
                        <div class="card chart-grd same-card">
                            <div class="card-body depostit-card p-0">
                                <div class="depostit-card-media d-flex justify-content-between pb-0">
                                    <div>
                                        <h6>Total Projects</h6>
                                        <h3>{{ $stats['total_projects'] }}</h3>
                                    </div>
                                    <div class="icon-box bg-success-light">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M2.5 5.83333V15.8333C2.5 17.0833 3.33333 17.9167 4.58333 17.9167H15.4167C16.6667 17.9167 17.5 17.0833 17.5 15.8333V5.83333C17.5 4.58333 16.6667 3.75 15.4167 3.75H4.58333C3.33333 3.75 2.5 4.58333 2.5 5.83333Z"
                                                stroke="#3AC977" stroke-width="1.5" />
                                            <path d="M2.5 10H17.5" stroke="#3AC977" stroke-width="1.5"
                                                stroke-linecap="round" />
                                            <path d="M13.3333 14.1667H15" stroke="#3AC977" stroke-width="1.5"
                                                stroke-linecap="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-primary">Published: {{ $stats['published_projects'] }}</span>
                                    <span class="badge bg-warning ms-1">Draft: {{ $stats['draft_projects'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="col-xl-3 col-sm-6">
                        <div class="card chart-grd same-card">
                            <div class="card-body depostit-card p-0">
                                <div class="depostit-card-media d-flex justify-content-between pb-0">
                                    <div>
                                        <h6>Services</h6>
                                        <h3>{{ $stats['total_services'] }}</h3>
                                    </div>
                                    <div class="icon-box bg-info-light">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10 1.875L1.875 6.875L10 11.875L18.125 6.875L10 1.875Z"
                                                stroke="#0D99FF" stroke-width="1.5" stroke-linejoin="round" />
                                            <path d="M3.75 9.375V14.375L10 18.125L16.25 14.375V9.375" stroke="#0D99FF"
                                                stroke-width="1.5" stroke-linejoin="round" />
                                            <path d="M15 14.375L17.5 16.25" stroke="#0D99FF" stroke-width="1.5"
                                                stroke-linecap="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-success">Active: {{ $stats['active_services'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2FA Adoption -->
                    <div class="col-xl-3 col-sm-6 same-card">
                        <div class="card">
                            <div class="card-body depostit-card">
                                <div class="depostit-card-media d-flex justify-content-between style-1">
                                    <div>
                                        <h6>2FA Adoption</h6>
                                        <h3>{{ $stats['two_factor_adoption'] }}%</h3>
                                    </div>
                                    <div class="icon-box bg-secondary-light">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M4.16667 8.75V6.25C4.16667 3.35556 6.52222 1 9.41667 1C12.3111 1 14.6667 3.35556 14.6667 6.25V8.75M10 13.75V15.8333M4.16667 19H14.6667C16.6 19 18.1667 17.4333 18.1667 15.5V12.25C18.1667 10.3167 16.6 8.75 14.6667 8.75H4.16667C2.23333 8.75 0.666667 10.3167 0.666667 12.25V15.5C0.666667 17.4333 2.23333 19 4.16667 19Z"
                                                stroke="#6C757D" stroke-width="1.5" stroke-linecap="round" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="progress-box mt-0">
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-0">Enabled: {{ $stats['two_factor_adoption'] }}%</p>
                                        <p class="mb-0">{{ $stats['two_factor_adoption'] }}%</p>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary"
                                            style="width:{{ $stats['two_factor_adoption'] }}%; height:5px; border-radius:4px;"
                                            role="progressbar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── RIGHT SIDEBAR: Quick Stats & Spotlight ─── -->
            <div class="col-xl-3 t-earn">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="heading mb-0">Quick Stats</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>New Users (Week)</span>
                                <span class="badge bg-primary rounded-pill">{{ $stats['new_users_week'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Unread Notifications</span>
                                <span class="badge bg-danger rounded-pill">{{ $stats['unread_notifications'] }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Spotlight Team</span>
                                <span class="badge bg-success rounded-pill">{{ $stats['spotlight_count'] }} / 3</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>System Status</span>
                                <span
                                    class="badge bg-{{ $stats['system_status'] === 'healthy' ? 'success' : 'warning' }} rounded-pill">{{ ucfirst($stats['system_status']) }}</span>
                            </li>
                        </ul>

                        <!-- Quick Actions -->
                        <div class="mt-3">
                            <h6>Quick Actions</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ route('users') }}" wire:navigate.hover class="btn btn-primary btn-sm"><i
                                        class="fas fa-user-plus"></i> Manage Users</a>
                                <a href="{{ route('admin.projects.create') }}" wire:navigate.hover
                                    class="btn btn-success btn-sm"><i class="fas fa-folder-plus"></i> New Project</a>
                                <a href="{{ route('admin.services.create') }}" wire:navigate.hover
                                    class="btn btn-info btn-sm"><i class="fas fa-cog"></i> Add Service</a>
                                <button wire:click="$emit('openInviteModal')" class="btn btn-secondary btn-sm"><i
                                        class="fas fa-envelope"></i> Invite</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── PROJECT OVERVIEW CHART ─── -->
            <div class="col-xl-8">
                <div class="card overflow-hidden">
                    <div class="card-header border-0 pb-0 flex-wrap">
                        <h4 class="heading mb-0">Projects Overview</h4>
                        <ul class="nav nav-pills mix-chart-tab" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $projectChartPeriod === 'week' ? 'active' : '' }}"
                                    wire:click="$set('projectChartPeriod', 'week')" type="button">Week</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $projectChartPeriod === 'month' ? 'active' : '' }}"
                                    wire:click="$set('projectChartPeriod', 'month')" type="button">Month</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $projectChartPeriod === 'year' ? 'active' : '' }}"
                                    wire:click="$set('projectChartPeriod', 'year')" type="button">Year</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $projectChartPeriod === 'all' ? 'active' : '' }}"
                                    wire:click="$set('projectChartPeriod', 'all')" type="button">All</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <!-- Fixed height container with wire:ignore -->
                        <div wire:ignore style="height:200px;">
                            <canvas id="overiewChart" style="width:100%; height:100%;"></canvas>
                        </div>
                        <div class="ttl-project">
                            <div class="pr-data">
                                <h5>{{ $stats['total_projects'] }}</h5>
                                <span>Total Projects</span>
                            </div>
                            <div class="pr-data">
                                <h5 class="text-primary">{{ $stats['published_projects'] }}</h5>
                                <span>Published</span>
                            </div>
                            <div class="pr-data">
                                <h5 class="text-success">{{ $stats['active_users'] }}</h5>
                                <span>Active Users</span>
                            </div>
                            <div class="pr-data">
                                <h5 class="text-warning">{{ $stats['two_factor_adoption'] }}%</h5>
                                <span>2FA Adoption</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── PROJECT STATUS (DONUT) ─── -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Project Status</h4>
                        <div>
                            <a href="{{ route('admin.projects.index') }}" wire:navigate.hover
                                class="text-primary me-2">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div wire:ignore style="height:200px;">
                            <canvas id="projectChart" style="width:100%; height:100%;"></canvas>
                        </div>
                        <div class="project-date mt-3">
                            @foreach($projectStatusData['labels'] as $index => $label)
                                <div class="project-media">
                                    <p class="mb-0">
                                        <svg class="me-2" width="12" height="13" viewBox="0 0 12 13" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect y="0.5" width="12" height="12" rx="3"
                                                fill="{{ $projectStatusData['bgColors'][$index] }}" />
                                        </svg>
                                        {{ $label }}
                                    </p>
                                    <span>{{ $projectStatusData['data'][$index] }} Projects</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── ACTIVITY FEED ─── -->
            <div class="col-xl-6 active-p">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Recent Activity</h4>
                        <div>
                            <a href="{{ route('admin.logs') }}" wire:navigate.hover class="text-primary me-2">View
                                All</a>
                        </div>
                    </div>
                    <div class="card-body p-0 dz-scroll" style="max-height: 400px;">
                        <ul class="list-group list-group-flush">
                            @forelse($recentActivities as $activity)
                                <li class="list-group-item d-flex align-items-start">
                                    <div class="me-3">
                                        <i class="fas {{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>{{ $activity['causer_name'] }}</strong>
                                        <span>{{ $activity['description'] }}</span>
                                        @if($activity['entity_name'])
                                            <span class="badge bg-secondary">{{ $activity['entity_name'] }}</span>
                                        @endif
                                        <div class="text-muted small">{{ $activity['created_at'] }}</div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center">No recent activity</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ─── SPOTLIGHT TEAM ─── -->
            <div class="col-xl-3 col-md-6 up-shd">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Spotlight Team</h4>
                        <a href="{{ route('users') }}" wire:navigate.hover class="text-primary">Manage</a>
                    </div>
                    <div class="card-body">
                        @forelse($spotlightTeam as $member)
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $member['avatar'] }}" class="avatar rounded-circle me-2" width="40" height="40"
                                    alt="">
                                <div>
                                    <h6 class="mb-0">{{ $member['name'] }}</h6>
                                    <small class="text-muted">{{ $member['position'] }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No spotlight members.</p>
                        @endforelse
                        @if($stats['spotlight_count'] < 3)
                            <a href="{{ route('users') }}" wire:navigate.hover
                                class="btn btn-outline-primary btn-sm w-100">Add to Spotlight</a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ─── NOTIFICATIONS ─── -->
            <div class="col-xl-3 col-md-6 up-shd">
                <div class="card">
                    <div class="card-header pb-0 border-0">
                        <h4 class="heading mb-0">Notifications</h4>
                        <span class="badge bg-danger">{{ $stats['unread_notifications'] }}</span>
                    </div>
                    <div class="card-body dz-scroll" style="max-height: 250px;">
                        <h6>Unread</h6>
                        @forelse($notifications['unread'] as $notification)
                            <div class="d-flex align-items-start mb-2">
                                <i class="fas fa-circle text-primary mt-1 me-2" style="font-size: 8px;"></i>
                                <div>
                                    <p class="mb-0">{{ $notification->title }}</p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No unread notifications</p>
                        @endforelse
                        <hr>
                        <h6>Read</h6>
                        @forelse($notifications['read'] as $notification)
                            <div class="d-flex align-items-start mb-2">
                                <i class="fas fa-circle text-secondary mt-1 me-2" style="font-size: 8px;"></i>
                                <div>
                                    <p class="mb-0">{{ $notification->title }}</p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No read notifications</p>
                        @endforelse
                        <a href="{{ route('account', ['tab' => 'notifications']) }}" wire:navigate.hover
                            class="text-primary">View All</a>
                    </div>
                </div>
            </div>

            <!-- ─── SYSTEM HEALTH ─── -->
            <div class="col-xl-6 bst-seller">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">System Health</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6>Queued Jobs</h6>
                                    <h3>{{ $systemHealth['queued_jobs'] }}</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6>Storage</h6>
                                    <h5>{{ $systemHealth['storage_usage'] }}</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6>Cache</h6>
                                    <h5>{{ $systemHealth['cache_usage'] }}</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6>Last Backup</h6>
                                    <h5>{{ $systemHealth['last_backup'] }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── USER REGISTRATIONS (EARNING CHART) ─── -->
            <div class="col-xl-6 bst-seller">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">User Registrations</h4>
                        <ul class="nav nav-underline earning-tab" id="pills-tab1" role="tablist">
                            <li class="nav-item px-2">
                                <button
                                    class="nav-link py-2 px-0 border-3 m-0 {{ $earningChartPeriod === 'day' ? 'active' : '' }}"
                                    wire:click="$set('earningChartPeriod', 'day')">Day</button>
                            </li>
                            <li class="nav-item px-2">
                                <button
                                    class="nav-link py-2 px-0 border-3 m-0 {{ $earningChartPeriod === 'week' ? 'active' : '' }}"
                                    wire:click="$set('earningChartPeriod', 'week')">Week</button>
                            </li>
                            <li class="nav-item px-2">
                                <button
                                    class="nav-link py-2 px-0 border-3 m-0 {{ $earningChartPeriod === 'month' ? 'active' : '' }}"
                                    wire:click="$set('earningChartPeriod', 'month')">Month</button>
                            </li>
                            <li class="nav-item px-2">
                                <button
                                    class="nav-link py-2 px-0 border-3 m-0 {{ $earningChartPeriod === 'year' ? 'active' : '' }}"
                                    wire:click="$set('earningChartPeriod', 'year')">Year</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div wire:ignore style="height:150px;">
                            <canvas id="earningChart" style="width:100%; height:100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardCharts', () => ({
                projectChart: null,
                statusChart: null,
                registrationsChart: null,

                initCharts() {
                    const projectData = @json($initialProjectChartData);
                    const statusData = @json($initialProjectStatusData);
                    const registrationsData = @json($initialUserRegistrationsData);

                    this.initProjectChart(projectData);
                    this.initStatusChart(statusData);
                    this.initRegistrationsChart(registrationsData);
                },

                initProjectChart(data) {
                    const ctx = document.getElementById('overiewChart');
                    if (!ctx) return;
                    if (this.projectChart) this.projectChart.destroy();
                    this.projectChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Projects Created',
                                data: data.data,
                                borderColor: '#0D99FF',
                                backgroundColor: 'rgba(13, 153, 255, 0.1)',
                                tension: 0.2,
                                fill: true,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                },

                initStatusChart(data) {
                    const ctx = document.getElementById('projectChart');
                    if (!ctx) return;
                    if (this.statusChart) this.statusChart.destroy();
                    this.statusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.data,
                                backgroundColor: data.bgColors,
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                },

                initRegistrationsChart(data) {
                    const ctx = document.getElementById('earningChart');
                    if (!ctx) return;
                    if (this.registrationsChart) this.registrationsChart.destroy();
                    this.registrationsChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'New Users',
                                data: data.data,
                                backgroundColor: '#3AC977',
                                borderColor: '#2e9e5e',
                                borderWidth: 1,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                },

                updateCharts(payload) {
                    const data = payload || {};
                    if (data.projectChartData && this.projectChart) {
                        this.projectChart.data.labels = data.projectChartData.labels;
                        this.projectChart.data.datasets[0].data = data.projectChartData.data;
                        this.projectChart.update();
                    }
                    if (data.projectStatusData && this.statusChart) {
                        this.statusChart.data.labels = data.projectStatusData.labels;
                        this.statusChart.data.datasets[0].data = data.projectStatusData.data;
                        this.statusChart.data.datasets[0].backgroundColor = data.projectStatusData.bgColors;
                        this.statusChart.update();
                    }
                    if (data.userRegistrationsData && this.registrationsChart) {
                        this.registrationsChart.data.labels = data.userRegistrationsData.labels;
                        this.registrationsChart.data.datasets[0].data = data.userRegistrationsData.data;
                        this.registrationsChart.update();
                    }
                }
            }));
        });

        // ─── Fix charts after wire:navigate and on DOM reload ──────────
        document.addEventListener('livewire:navigated', function () {
            setTimeout(() => {
                if (window.Alpine) {
                    const chartsComponent = Alpine.$data(document.querySelector('[x-data="dashboardCharts()"]'));
                    if (chartsComponent && typeof chartsComponent.initCharts === 'function') {
                        chartsComponent.initCharts();
                    }
                }
            }, 50);
        });

        // Also run after hard refresh
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(() => {
                if (window.Alpine) {
                    const chartsComponent = Alpine.$data(document.querySelector('[x-data="dashboardCharts()"]'));
                    if (chartsComponent && typeof chartsComponent.initCharts === 'function') {
                        chartsComponent.initCharts();
                    }
                }
            }, 100);
        });
    </script>
@endpush