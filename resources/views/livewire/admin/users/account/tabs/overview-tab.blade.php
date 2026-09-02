<div>
    {{-- ─── STATS ROW ─────────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Total Posts --}}
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-primary">
                <div class="stats-card-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Total Posts</span>
                    <h3 class="stats-card-value">{{ number_format($stats['total_posts']) }}</h3>
                    <span class="stats-card-trend">
                        <i class="fas fa-arrow-up"></i> 12%
                    </span>
                </div>
            </div>
        </div>

        {{-- Published --}}
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-success">
                <div class="stats-card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Published</span>
                    <h3 class="stats-card-value">{{ number_format($stats['published_posts']) }}</h3>
                    <span class="stats-card-trend">
                        <i class="fas fa-arrow-up"></i> 8%
                    </span>
                </div>
            </div>
        </div>

        {{-- Drafts --}}
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-warning">
                <div class="stats-card-icon">
                    <i class="fas fa-pen"></i>
                </div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Drafts</span>
                    <h3 class="stats-card-value">{{ number_format($stats['draft_posts']) }}</h3>
                    <span class="stats-card-trend">
                        <i class="fas fa-arrow-down"></i> 3%
                    </span>
                </div>
            </div>
        </div>

        {{-- Account Age --}}
        <div class="col-6 col-md-3">
            <div class="stats-card stats-card-info">
                <div class="stats-card-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stats-card-content">
                    <span class="stats-card-label">Account Age</span>
                    <h3 class="stats-card-value">{{ $stats['account_age_display'] }}</h3>
                    <span class="stats-card-trend">
                        <i class="fas fa-calendar-check"></i> Active
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── MAIN CONTENT ─────────────────────────────────────────────────── --}}
    <div class="row g-4">
        {{-- Left Column: Profile Details --}}
        <div class="col-xl-5 col-lg-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title fw-bold"><i class="fas fa-user-circle text-primary me-2"></i> Profile Details
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Avatar & Name --}}
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-4 me-3"
                            style="width: 56px; height: 56px; object-fit: cover; border: 2px solid #e2e8f0;">
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $user->name }}</h5>
                            <span class="text-muted small">{{ $profile->position ?? 'Team Member' }}</span>
                            @if($profile && $profile->is_employee)
                                <span class="badge bg-primary light border-0 ms-2">
                                    <i class="fas fa-briefcase me-1"></i> Employee
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Basic Info --}}
                        <div class="col-sm-6 col-12">
                            <span class="text-muted small text-uppercase fw-bold">Email</span>
                            <p class="mb-0 text-break">{{ $user->email }}</p>
                        </div>
                        <div class="col-sm-6 col-12">
                            <span class="text-muted small text-uppercase fw-bold">Phone</span>
                            <p class="mb-0">{{ $user->phone ?? '—' }}</p>
                        </div>
                        <div class="col-sm-6 col-12">
                            <span class="text-muted small text-uppercase fw-bold">Role</span>
                            <p class="mb-0">
                                {{ $user->roles->pluck('name')->map(fn($r) => ucfirst($r))->implode(', ') ?: 'No role' }}
                            </p>
                        </div>
                        <div class="col-sm-6 col-12">
                            <span class="text-muted small text-uppercase fw-bold">Status</span>
                            <p class="mb-0">
                                <span
                                    class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }} light border-0">
                                    {{ ucfirst($user->status ?? 'active') }}
                                </span>
                            </p>
                        </div>
                        <div class="col-sm-6 col-12">
                            <span class="text-muted small text-uppercase fw-bold">Joined</span>
                            <p class="mb-0">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-sm-6 col-12">
                            <span class="text-muted small text-uppercase fw-bold">Verified</span>
                            <p class="mb-0">
                                @if($user->hasVerifiedEmail())
                                    <span class="badge bg-success light border-0"><i class="fas fa-check-circle me-1"></i>
                                        Verified</span>
                                @else
                                    <span class="badge bg-secondary light border-0">Unverified</span>
                                @endif
                            </p>
                        </div>

                        {{-- Employee Details – only if employee --}}
                        @if($profile && $profile->is_employee)
                            <div class="col-12">
                                <hr>
                                <h6 class="fw-bold text-primary mb-2"><i class="fas fa-briefcase me-2"></i>Employment
                                    Details</h6>
                            </div>

                            <div class="col-sm-6 col-12">
                                <span class="text-muted small text-uppercase fw-bold">Employee ID</span>
                                <p class="mb-0">{{ $profile->employee_id ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6 col-12">
                                <span class="text-muted small text-uppercase fw-bold">Department</span>
                                <p class="mb-0">{{ $profile->department ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6 col-12">
                                <span class="text-muted small text-uppercase fw-bold">Employment Type</span>
                                <p class="mb-0">
                                    {{ $profile->employment_type ? ucfirst(str_replace('-', ' ', $profile->employment_type)) : '—' }}
                                </p>
                            </div>
                            <div class="col-sm-6 col-12">
                                <span class="text-muted small text-uppercase fw-bold">Hire Date</span>
                                <p class="mb-0">{{ $profile->hire_date ? $profile->hire_date->format('M d, Y') : '—' }}</p>
                            </div>

                            {{-- Personal Details --}}
                            <div class="col-12">
                                <hr>
                                <h6 class="fw-bold text-primary mb-2"><i class="fas fa-user me-2"></i>Personal Details</h6>
                            </div>

                            <div class="col-sm-6 col-12">
                                <span class="text-muted small text-uppercase fw-bold">Gender</span>
                                <p class="mb-0">{{ $profile->gender ? ucfirst($profile->gender) : '—' }}</p>
                            </div>
                            <div class="col-sm-6 col-12">
                                <span class="text-muted small text-uppercase fw-bold">Date of Birth</span>
                                <p class="mb-0">
                                    {{ $profile->date_of_birth ? $profile->date_of_birth->format('M d, Y') : '—' }}</p>
                            </div>
                            <div class="col-sm-6 col-12">
                                <span class="text-muted small text-uppercase fw-bold">Country</span>
                                <p class="mb-0">{{ $profile->country_name ?? $profile->country_code ?? '—' }}</p>
                            </div>
                            <div class="col-sm-6 col-12">
                                <span class="text-muted small text-uppercase fw-bold">City</span>
                                <p class="mb-0">{{ $profile->city ?? '—' }}</p>
                            </div>

                            @if($profile->emergency_contact_name || $profile->emergency_contact_phone)
                                <div class="col-12">
                                    <hr>
                                    <h6 class="fw-bold text-primary mb-2"><i class="fas fa-phone-alt me-2"></i>Emergency Contact
                                    </h6>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <span class="text-muted small text-uppercase fw-bold">Name</span>
                                    <p class="mb-0">{{ $profile->emergency_contact_name ?? '—' }}</p>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <span class="text-muted small text-uppercase fw-bold">Phone</span>
                                    <p class="mb-0">{{ $profile->emergency_contact_phone ?? '—' }}</p>
                                </div>
                            @endif
                        @endif

                        {{-- About Me (always shown) --}}
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold text-primary mb-2"><i class="fas fa-edit me-2"></i>About Me</h6>
                            <p class="mb-0 text-muted">{{ $profile->about_me ?? 'No bio provided yet.' }}</p>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('account', ['tab' => 'profile']) }}" wire:navigate
                            class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit Profile
                        </a>
                        <a href="{{ route('account', ['tab' => 'security']) }}" wire:navigate
                            class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-shield-alt me-1"></i> Security
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Recent Activity & Quick Actions --}}
        <div class="col-xl-7 col-lg-12">
            <div class="row g-4">
                {{-- Recent Activity --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div
                            class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h6 class="card-title fw-bold mb-0"><i class="fas fa-clock text-primary me-2"></i> Recent
                                Activity</h6>
                            <a href="{{ route('account', ['tab' => 'activity']) }}" wire:navigate
                                class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            @if($recentActivities->count() > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach($recentActivities as $activity)
                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                            <div>
                                                <span
                                                    class="badge bg-{{ $activity->action === 'login' ? 'success' : ($activity->action === 'logout' ? 'danger' : 'primary') }} light border-0 me-2">
                                                    {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                                </span>
                                                <span class="text-muted small">{{ $activity->description ?? '' }}</span>
                                            </div>
                                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox text-muted fs-2 d-block mb-2"></i>
                                    <p class="text-muted">No recent activity found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick Actions & Account Stats --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="card-title fw-bold"><i class="fas fa-bolt text-warning me-2"></i> Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('account', ['tab' => 'profile']) }}" wire:navigate
                                    class="btn btn-outline-primary text-start">
                                    <i class="fas fa-user-edit me-2"></i> Edit Profile
                                </a>
                                <a href="{{ route('account', ['tab' => 'security']) }}" wire:navigate
                                    class="btn btn-outline-primary text-start">
                                    <i class="fas fa-key me-2"></i> Change Password
                                </a>
                                <a href="{{ route('account', ['tab' => 'activity']) }}" wire:navigate
                                    class="btn btn-outline-primary text-start">
                                    <i class="fas fa-history me-2"></i> View Activity Log
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Account Stats --}}
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-transparent border-0">
                            <h6 class="card-title fw-bold"><i class="fas fa-chart-simple text-info me-2"></i> Account
                                Stats</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Posts</span>
                                <strong>{{ $stats['total_posts'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Published</span>
                                <strong>{{ $stats['published_posts'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Drafts</span>
                                <strong>{{ $stats['draft_posts'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Account Age</span>
                                <strong>{{ $stats['account_age_display'] }}</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Last Login</span>
                                <strong>{{ $stats['last_login'] ? $stats['last_login']->diffForHumans() : 'Never' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── STYLES ─────────────────────────────────────────────────────────── --}}
    <style>
        /* ── Stats Cards ──────────────────────────────────────────────────── */
        .stats-card {
            position: relative;
            padding: 1.25rem 1rem;
            border-radius: 1rem;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 16px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
            min-height: 90px;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0.06;
            transform: translate(30%, -30%);
            pointer-events: none;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .stats-card-primary::before {
            background: #4f46e5;
        }

        .stats-card-success::before {
            background: #10b981;
        }

        .stats-card-warning::before {
            background: #f59e0b;
        }

        .stats-card-info::before {
            background: #06b6d4;
        }

        .stats-card-icon {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            color: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .stats-card:hover .stats-card-icon {
            transform: scale(1.05) rotate(-3deg);
        }

        .stats-card-primary .stats-card-icon {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
        }

        .stats-card-success .stats-card-icon {
            background: linear-gradient(135deg, #34d399, #10b981);
        }

        .stats-card-warning .stats-card-icon {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        .stats-card-info .stats-card-icon {
            background: linear-gradient(135deg, #22d3ee, #06b6d4);
        }

        .stats-card-content {
            flex: 1;
            min-width: 0;
        }

        .stats-card-label {
            display: block;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #94a3b8;
            margin-bottom: 0.1rem;
        }

        .stats-card-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 0.1rem;
            letter-spacing: -0.02em;
        }

        .stats-card-trend {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.65rem;
            font-weight: 600;
            color: #94a3b8;
            padding: 0.05rem 0.4rem;
            border-radius: 20px;
            background: #f1f5f9;
        }

        .stats-card-trend .fa-arrow-up {
            color: #10b981;
        }

        .stats-card-trend .fa-arrow-down {
            color: #ef4444;
        }

        .stats-card-trend .fa-calendar-check {
            color: #6366f1;
        }

        [data-theme-version="dark"] .stats-card {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.04);
        }

        [data-theme-version="dark"] .stats-card-value {
            color: #f1f5f9;
        }

        [data-theme-version="dark"] .stats-card-trend {
            background: #334155;
            color: #94a3b8;
        }

        /* ── Responsive Stats ────────────────────────────────────────────── */
        @media (max-width: 575.98px) {
            .stats-card {
                padding: 0.75rem 0.5rem;
                min-height: 70px;
                gap: 0.5rem;
                border-radius: 0.75rem;
            }

            .stats-card-icon {
                width: 36px;
                height: 36px;
                font-size: 0.95rem;
                border-radius: 8px;
            }

            .stats-card-value {
                font-size: 1.1rem;
            }

            .stats-card-label {
                font-size: 0.55rem;
            }

            .stats-card-trend {
                font-size: 0.55rem;
                padding: 0.05rem 0.3rem;
            }

            .stats-card .stats-card-trend .fa-arrow-up,
            .stats-card .stats-card-trend .fa-arrow-down {
                font-size: 0.5rem;
            }
        }

        @media (min-width: 576px) and (max-width: 767.98px) {
            .stats-card {
                padding: 1rem;
                min-height: 80px;
            }

            .stats-card-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .stats-card-value {
                font-size: 1.2rem;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .stats-card {
                padding: 1.25rem;
                min-height: 90px;
            }

            .stats-card-icon {
                width: 44px;
                height: 44px;
                font-size: 1.1rem;
            }

            .stats-card-value {
                font-size: 1.3rem;
            }
        }

        /* ─── General responsiveness ─────────────────────────────────────── */
        @media (max-width: 575.98px) {
            .card-body {
                padding: 1rem !important;
            }

            .card-header {
                padding: 0.75rem 1rem !important;
            }

            .list-group-item {
                flex-wrap: wrap;
            }

            .list-group-item .badge {
                margin-bottom: 0.25rem;
            }

            .list-group-item small {
                width: 100%;
                text-align: left;
                margin-top: 0.25rem;
            }
        }
    </style>
</div>