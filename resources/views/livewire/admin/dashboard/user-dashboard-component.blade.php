<!-- livewire/admin/dashboard/user-dashboard-component.blade.php -->
<div x-data="userDashboardCharts()" x-init="initCharts()" @update-user-charts.window="updateCharts($event.detail)">

    <!-- ─── Page Header ────────────────────────────────────────────── -->
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">My Dashboard</h5>
            </li>
            <li class="breadcrumb-item"><a href="javascript:void(0)" wire:navigate.hover>Home</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)" wire:navigate.hover>Dashboard</a></li>
        </ol>
        <a href="{{ route('account') }}" wire:navigate.hover class="text-primary fs-13"><i
                class="fas fa-user-edit me-1"></i> Edit Profile</a>
    </div>

    <div class="container-fluid">
        <div class="row">

            <!-- ─── WELCOME CARD ─── -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="me-3">
                                <img src="{{ $user->avatar_url }}" class="avatar avatar-xl rounded-circle"
                                    alt="{{ $user->name }}">
                            </div>
                            <div>
                                <h2 class="mb-1">Welcome back, {{ $user->name }}!</h2>
                                <p class="mb-0 text-muted">
                                    <span class="badge bg-primary me-2">{{ $stats['role'] }}</span>
                                    Member since {{ $stats['member_since'] }}
                                </p>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-success fs-14">Profile Completion:
                                    {{ $profileCompletion }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── KPI CARDS ─── -->
            <div class="col-xl-3 col-sm-6">
                <div class="card chart-grd same-card">
                    <div class="card-body depostit-card p-0">
                        <div class="depostit-card-media d-flex justify-content-between pb-0">
                            <div>
                                <h6>Total Projects</h6>
                                <h3>{{ $stats['total_projects'] }}</h3>
                            </div>
                            <div class="icon-box bg-primary-light">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.5 5.83333V15.8333C2.5 17.0833 3.33333 17.9167 4.58333 17.9167H15.4167C16.6667 17.9167 17.5 17.0833 17.5 15.8333V5.83333C17.5 4.58333 16.6667 3.75 15.4167 3.75H4.58333C3.33333 3.75 2.5 4.58333 2.5 5.83333Z"
                                        stroke="var(--primary)" stroke-width="1.5" />
                                    <path d="M2.5 10H17.5" stroke="var(--primary)" stroke-width="1.5"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card chart-grd same-card">
                    <div class="card-body depostit-card p-0">
                        <div class="depostit-card-media d-flex justify-content-between pb-0">
                            <div>
                                <h6>Total Activities</h6>
                                <h3>{{ $stats['total_activities'] }}</h3>
                            </div>
                            <div class="icon-box bg-success-light">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 1.875L1.875 6.875L10 11.875L18.125 6.875L10 1.875Z" stroke="#3AC977"
                                        stroke-width="1.5" stroke-linejoin="round" />
                                    <path d="M3.75 9.375V14.375L10 18.125L16.25 14.375V9.375" stroke="#3AC977"
                                        stroke-width="1.5" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card chart-grd same-card">
                    <div class="card-body depostit-card p-0">
                        <div class="depostit-card-media d-flex justify-content-between pb-0">
                            <div>
                                <h6>Unread Notifications</h6>
                                <h3>{{ $unreadCount }}</h3>
                            </div>
                            <div class="icon-box bg-danger-light">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M4.16667 8.75V6.25C4.16667 3.35556 6.52222 1 9.41667 1C12.3111 1 14.6667 3.35556 14.6667 6.25V8.75M10 13.75V15.8333M4.16667 19H14.6667C16.6 19 18.1667 17.4333 18.1667 15.5V12.25C18.1667 10.3167 16.6 8.75 14.6667 8.75H4.16667C2.23333 8.75 0.666667 10.3167 0.666667 12.25V15.5C0.666667 17.4333 2.23333 19 4.16667 19Z"
                                        stroke="#FF5E5E" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card chart-grd same-card">
                    <div class="card-body depostit-card p-0">
                        <div class="depostit-card-media d-flex justify-content-between pb-0">
                            <div>
                                <h6>Profile Completion</h6>
                                <h3>{{ $profileCompletion }}%</h3>
                            </div>
                            <div class="icon-box bg-info-light">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17.5 19.375V17.9167C17.5 13.675 14.075 10.2083 10 10.2083C5.925 10.2083 2.5 13.675 2.5 17.9167V19.375M10 10.2083C12.3467 10.2083 14.1667 8.38833 14.1667 6.04167C14.1667 3.695 12.3467 1.875 10 1.875C7.65333 1.875 5.83333 3.695 5.83333 6.04167C5.83333 8.38833 7.65333 10.2083 10 10.2083Z"
                                        stroke="#0D99FF" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── ACTIVITY CHART ─── -->
            <div class="col-xl-8">
                <div class="card overflow-hidden">
                    <div class="card-header border-0 pb-0 flex-wrap">
                        <h4 class="heading mb-0">My Activity</h4>
                        <ul class="nav nav-pills mix-chart-tab">
                            <li class="nav-item">
                                <button class="nav-link {{ $activityChartPeriod === 'week' ? 'active' : '' }}"
                                    wire:click="$set('activityChartPeriod', 'week')">Week</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link {{ $activityChartPeriod === 'month' ? 'active' : '' }}"
                                    wire:click="$set('activityChartPeriod', 'month')">Month</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link {{ $activityChartPeriod === 'year' ? 'active' : '' }}"
                                    wire:click="$set('activityChartPeriod', 'year')">Year</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div wire:ignore style="height:200px;">
                            <canvas id="userActivityChart" style="width:100%; height:100%;"></canvas>
                        </div>
                        <div class="ttl-project">
                            <div class="pr-data">
                                <h5>{{ $stats['total_activities'] }}</h5>
                                <span>Total Actions</span>
                            </div>
                            <div class="pr-data">
                                <h5 class="text-primary">{{ $profileCompletion }}%</h5>
                                <span>Profile Complete</span>
                            </div>
                            <div class="pr-data">
                                <h5 class="text-success">{{ $stats['total_projects'] }}</h5>
                                <span>Projects</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── NOTIFICATIONS ─── -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Recent Notifications</h4>
                        <a href="{{ route('account', ['tab' => 'notifications']) }}" wire:navigate.hover
                            class="text-primary">View All</a>
                    </div>
                    <div class="card-body dz-scroll" style="max-height: 250px;">
                        @forelse($notifications as $notification)
                            <div class="d-flex align-items-start mb-2">
                                @if($notification->read_at)
                                    <i class="fas fa-circle text-secondary mt-1 me-2" style="font-size: 8px;"></i>
                                @else
                                    <i class="fas fa-circle text-primary mt-1 me-2" style="font-size: 8px;"></i>
                                @endif
                                <div>
                                    <p class="mb-0">{{ $notification->title }}</p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No notifications.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- ─── RECENT ACTIVITIES ─── -->
            <div class="col-xl-6 active-p">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">My Recent Activity</h4>
                    </div>
                    <div class="card-body p-0 dz-scroll" style="max-height: 300px;">
                        <ul class="list-group list-group-flush">
                            @forelse($recentActivities as $activity)
                                <li class="list-group-item d-flex align-items-start">
                                    <div class="me-3">
                                        <i class="fas fa-circle text-primary" style="font-size: 8px;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span>{{ $activity['description'] }}</span>
                                        <div class="text-muted small">{{ $activity['created_at'] }}</div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center">No recent activity.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ─── MY PROJECTS ─── -->
            <div class="col-xl-6 active-p">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">My Projects</h4>
                        <a href="{{ route('admin.projects.index') }}" wire:navigate.hover class="text-primary">View
                            All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($projects as $project)
                                        <tr>
                                            <td>{{ $project->title }}</td>
                                            <td><span
                                                    class="badge bg-{{ $project->status === 'published' ? 'success' : 'warning' }}">{{ ucfirst($project->status) }}</span>
                                            </td>
                                            <td>{{ $project->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">You have no projects yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── QUICK ACTIONS ─── -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <h4 class="heading mb-0">Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('account') }}" wire:navigate.hover
                                    class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-user-edit fa-2x d-block mb-2"></i>
                                    Edit Profile
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('account', ['tab' => 'notifications']) }}" wire:navigate.hover
                                    class="btn btn-outline-success w-100 py-3">
                                    <i class="fas fa-bell fa-2x d-block mb-2"></i>
                                    Notifications
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ route('admin.projects.create') }}" wire:navigate.hover
                                    class="btn btn-outline-info w-100 py-3">
                                    <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                    New Project
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <button wire:click="logout" class="btn btn-outline-danger w-100 py-3">
                                    <i class="fas fa-sign-out-alt fa-2x d-block mb-2"></i>
                                    Logout
                                </button>
                            </div>
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
            Alpine.data('userDashboardCharts', () => ({
                activityChart: null,

                initCharts() {
                    const data = @json($activityChartData);
                    this.initActivityChart(data);
                },

                initActivityChart(data) {
                    const ctx = document.getElementById('userActivityChart');
                    if (!ctx) return;
                    if (this.activityChart) this.activityChart.destroy();
                    this.activityChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Actions',
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
                    const data = payload?.activityChartData;
                    if (data && this.activityChart) {
                        this.activityChart.data.labels = data.labels;
                        this.activityChart.data.datasets[0].data = data.data;
                        this.activityChart.update();
                    }
                }
            }));
        });

        // Re-init after Livewire navigation
        document.addEventListener('livewire:navigated', function () {
            setTimeout(() => {
                if (window.Alpine) {
                    const chartsComponent = Alpine.$data(document.querySelector('[x-data="userDashboardCharts()"]'));
                    if (chartsComponent && typeof chartsComponent.initCharts === 'function') {
                        chartsComponent.initCharts();
                    }
                }
            }, 50);
        });
    </script>
@endpush