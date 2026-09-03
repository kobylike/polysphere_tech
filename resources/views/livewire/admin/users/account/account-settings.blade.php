<div>
    {{-- ─── PAGE TITLES ─────────────────────────────────────────────────────── --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li><h5 class="bc-title">Account</h5></li>
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.125 6.375L8.5 1.41667L14.875 6.375V14.1667C14.875 14.5424 14.7257 14.9027 14.4601 15.1684C14.1944 15.4341 13.8341 15.5833 13.4583 15.5833H3.54167C3.16594 15.5833 2.80561 15.4341 2.53993 15.1684C2.27426 14.9027 2.125 14.5424 2.125 14.1667V6.375Z" stroke="#2C2C2C" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M6.375 15.5833V8.5H10.625V15.5833" stroke="#2C2C2C" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Home
                </a>
            </li>
            <li class="breadcrumb-item active">
                <a href="javascript:void(0)">{{ ucfirst($tab) }}</a>
            </li>
        </ol>
        <a class="text-primary fs-13" data-bs-toggle="offcanvas" href="#offcanvasExample1" role="button" aria-controls="offcanvasExample1">+ Add Task</a>
    </div>

    {{-- ─── PROFILE HERO ───────────────────────────────────────────────────── --}}
    @php
        $roles = $user->roles->pluck('name')->map(fn($r) => ucfirst($r))->implode(', ');
        $statusClass = $user->status === 'active' ? 'success' : 'danger';
        $position = $user->position ?? '—';
        $isFeatured = $user->is_featured_team ?? false;
        $isVerified = $user->hasVerifiedEmail();

        $tickFile = 'silver-tick.png';
        if ($user->hasRole('Super Admin')) {
            $tickFile = 'gold-tick.png';
        } elseif ($user->hasRole('Admin')) {
            $tickFile = 'blue-tick.png';
        }
        $tickUrl = asset('assets/users/images/' . $tickFile);
        $roleNames = $user->roles->pluck('name')->map(fn($r) => ucfirst($r))->implode(', ');
    @endphp

    <div class="container-fluid">
        <div class="profile-hero-wrapper">
            {{-- Hero Card --}}
            <div class="profile-hero-card">
                <div class="profile-hero-content">
                    {{-- Avatar --}}
                    <div class="profile-avatar-wrapper">
                        <div class="profile-avatar-inner">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="profile-avatar-img">
                            <span class="profile-status-dot status-{{ $statusClass }}"></span>
                        </div>
                    </div>

                    {{-- User Info --}}
                    <div class="profile-user-info">
                        <div class="profile-name-wrapper">
                            <h2 class="profile-name">{{ $user->name }}</h2>
                            <img src="{{ $tickUrl }}" alt="Role badge" class="profile-role-badge-inline" title="{{ $roleNames ?: 'No role' }}">
                            @if($isFeatured)
                                <span class="badge bg-primary light border-0 profile-featured-badge">Featured</span>
                            @endif
                        </div>
                        <div class="profile-meta">
                            <span><i class="las la-envelope"></i> {{ $user->email }}</span>
                            <span><i class="las la-user-tag"></i> {{ $roles ?: 'No role' }}</span>
                            <span><i class="las la-briefcase"></i> {{ $position }}</span>
                            <span><i class="las la-calendar"></i> {{ $user->created_at->format('M d, Y') }}</span>
                        </div>

                        {{-- Stats Pills --}}
                        <div class="profile-stats-pills">
                            <div class="stat-pill stat-pill-primary">
                                <i class="las la-user-shield"></i>
                                <span class="stat-pill-label">Status</span>
                                <span class="stat-pill-value">{{ ucfirst($user->status ?? 'active') }}</span>
                            </div>
                            <div class="stat-pill stat-pill-success">
                                <i class="las la-check-circle"></i>
                                <span class="stat-pill-label">Email</span>
                                <span class="stat-pill-value">{{ $isVerified ? 'Verified' : 'Unverified' }}</span>
                            </div>
                            <div class="stat-pill stat-pill-info">
                                <i class="las la-tasks"></i>
                                <span class="stat-pill-label">Posts</span>
                                <span class="stat-pill-value">{{ $user->posts()->count() }}</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="profile-actions">
                            <a href="{{ route('account', ['tab' => 'profile']) }}" wire:navigate class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit Profile
                            </a>
                            <div class="profile-account-age">
                                <i class="las la-clock"></i>
                                <span>Joined {{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Progress Chart (optional) --}}
                <div class="profile-chart-wrapper">
                    <div id="chartProfileProgress" style="width: 80px; height: 80px;"></div>
                </div>
            </div>

            {{-- ─── TAB NAVIGATION ─────────────────────────────────────────────── --}}
            <div class="profile-tabs-wrapper">
                <ul class="nav nav-underline nav-underline-primary nav-underline-text-dark nav-underline-gap-x-0" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('account', ['tab' => 'overview']) }}" wire:navigate
                           class="nav-link py-3 border-3 text-dark {{ $tab === 'overview' ? 'active' : '' }}">Overview</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('account', ['tab' => 'profile']) }}" wire:navigate
                           class="nav-link py-3 border-3 text-dark {{ $tab === 'profile' ? 'active' : '' }}">Profile</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('account', ['tab' => 'security']) }}" wire:navigate
                           class="nav-link py-3 border-3 text-dark {{ $tab === 'security' ? 'active' : '' }}">Security</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('account', ['tab' => 'activity']) }}" wire:navigate
                           class="nav-link py-3 border-3 text-dark {{ $tab === 'activity' ? 'active' : '' }}">Activity</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('account', ['tab' => 'notifications']) }}" wire:navigate
                           class="nav-link py-3 border-3 text-dark {{ $tab === 'notifications' ? 'active' : '' }}">Notifications</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- ─── TAB CONTENT ─────────────────────────────────────────────────── --}}
        <div class="profile-tab-content mt-4">
            @switch($tab)
                @case('overview')
                    @livewire('admin.users.account.tabs.overview-tab', key('overview'))
                    @break
                @case('profile')
                    @livewire('admin.users.account.tabs.profile-tab', key('profile'))
                    @break
                @case('security')
                    @livewire('admin.users.account.tabs.security-tab', key('security'))
                    @break
                @case('activity')
                    @livewire('admin.users.account.tabs.activity-tab', key('activity'))
                    @break
                @case('notifications')
                    @livewire('admin.users.account.tabs.notification-tab', key('notifications'))
                    @break
                @default
                    @livewire('admin.users.account.tabs.overview-tab', key('overview'))
            @endswitch
        </div>
    </div>
    <style>
        /* ── Profile Hero Wrapper ──────────────────────────────────────────── */
        .profile-hero-wrapper {
            position: relative;
        }

        .profile-hero-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
            padding: 1.5rem 2rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(4px);
        }

        [data-theme-version="dark"] .profile-hero-card {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.04);
        }

        .profile-hero-content {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 2rem;
            flex: 1;
        }

        /* ── Avatar ──────────────────────────────────────────────────────────── */
        .profile-avatar-wrapper {
            flex-shrink: 0;
        }

        .profile-avatar-inner {
            position: relative;
            display: inline-block;
        }

        .profile-avatar-img {
            width: 100px;
            height: 120px;
            object-fit: cover;
            border-radius: 1.25rem;
            border: 3px solid #fff;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }

        .profile-avatar-img:hover {
            transform: scale(1.02);
        }

        .profile-status-dot {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid #fff;
            background: #94a3b8;
        }
        .status-success { background: #10b981; }
        .status-danger { background: #ef4444; }
        .status-warning { background: #f59e0b; }

        /* Role badge removed from avatar; now inline near name */

        /* ── User Info ────────────────────────────────────────────────────── */
        .profile-user-info {
            flex: 1;
            min-width: 200px;
        }

        .profile-name-wrapper {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }

        .profile-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: #0f172a;
        }

        [data-theme-version="dark"] .profile-name {
            color: #f1f5f9;
        }

        .profile-role-badge-inline {
            width: 20px;
            height: 20px;
            object-fit: contain;
            display: inline-block;
            vertical-align: middle;
            border-radius: 50%;
            background: #fff;
            padding: 2px;
            box-shadow: 0 0 0 1px #e2e8f0;
        }

        .profile-verified-icon {
            font-size: 1.1rem;
        }

        .profile-featured-badge {
            font-size: 0.7rem;
            padding: 0.15rem 0.6rem;
        }

        .profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem 1.5rem;
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
        }

        .profile-meta span {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .profile-meta i {
            font-size: 1rem;
        }

        .profile-stats-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #f8fafc;
            border-radius: 50px;
            padding: 0.3rem 0.8rem 0.3rem 0.6rem;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        [data-theme-version="dark"] .stat-pill {
            background: #334155;
            border-color: #475569;
        }

        .stat-pill i {
            font-size: 1rem;
        }
        .stat-pill-label {
            color: #94a3b8;
        }
        .stat-pill-value {
            color: #0f172a;
            font-weight: 600;
        }
        [data-theme-version="dark"] .stat-pill-value {
            color: #f1f5f9;
        }

        .stat-pill-primary i { color: #6366f1; }
        .stat-pill-success i { color: #10b981; }
        .stat-pill-info i { color: #06b6d4; }

        .profile-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .profile-account-age {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .profile-account-age i {
            font-size: 1rem;
        }

        /* ── Chart ────────────────────────────────────────────────────────── */
        .profile-chart-wrapper {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #chartProfileProgress {
            min-width: 70px;
            min-height: 70px;
        }

        /* ── Responsive Hero ──────────────────────────────────────────────── */
        @media (max-width: 767.98px) {
            .profile-hero-card {
                padding: 1.25rem;
                flex-direction: column;
                align-items: stretch;
            }
            .profile-hero-content {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
            .profile-avatar-img {
                width: 80px;
                height: 100px;
            }
            .profile-user-info {
                text-align: center;
                min-width: unset;
            }
            .profile-name-wrapper {
                justify-content: center;
            }
            .profile-meta {
                justify-content: center;
                font-size: 0.8rem;
                gap: 0.5rem 1rem;
            }
            .profile-stats-pills {
                justify-content: center;
            }
            .profile-actions {
                justify-content: center;
            }
            .profile-chart-wrapper {
                display: none;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .profile-hero-card {
                padding: 1.5rem;
            }
            .profile-avatar-img {
                width: 90px;
                height: 110px;
            }
        }

        /* ─── Tabs (Original Underline Style) ──────────────────────────────── */
        .profile-tabs-wrapper {
            margin-top: 1rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            padding-bottom: 2px;
        }
        .profile-tabs-wrapper::-webkit-scrollbar {
            height: 3px;
        }
        .profile-tabs-wrapper::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .nav-underline .nav-link {
            color: #64748b;
            font-weight: 500;
            border-bottom: 3px solid transparent;
            padding: 0.6rem 1rem;
            transition: all 0.2s ease;
            white-space: nowrap;
            font-size: 0.9rem;
        }
        .nav-underline .nav-link:hover {
            color: #1e293b;
            border-bottom-color: #cbd5e1;
        }
        .nav-underline .nav-link.active {
            color: #1e293b;
            border-bottom-color: var(--primary, #4f46e5);
            font-weight: 600;
        }

        [data-theme-version="dark"] .nav-underline .nav-link {
            color: #94a3b8;
        }
        [data-theme-version="dark"] .nav-underline .nav-link:hover {
            color: #f1f5f9;
        }
        [data-theme-version="dark"] .nav-underline .nav-link.active {
            color: #f1f5f9;
            border-bottom-color: #818cf8;
        }

        @media (max-width: 575.98px) {
            .nav-underline .nav-link {
                font-size: 0.8rem;
                padding: 0.5rem 0.8rem;
            }
        }

        /* ─── Tab Content ─────────────────────────────────────────────────── */
        .profile-tab-content {
            animation: fadeUp 0.3s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</div>