<div>
    {{-- ─── PAGE TITLES ─────────────────────────────────────────────────────── --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li><h5 class="bc-title">Dashboard</h5></li>
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

    {{-- ─── PROFILE OVERVIEW CARD ─────────────────────────────────────────── --}}
    @php
    $user = auth()->user();
    $roles = $user->roles->pluck('name')->map(fn($r) => ucfirst($r))->implode(', ');
    $statusClass = $user->status === 'active' ? 'success' : 'danger';
    $position = $user->position ?? '—';
    $isFeatured = $user->is_featured_team ?? false;
    $isVerified = $user->hasVerifiedEmail();

    // ─── Determine which tick to show based on role ───────────────────
    $tickFile = 'silver-tick.png' ; // default
    if ($user->hasRole('Super Admin')) {
        $tickFile = 'gold-tick.png';
    } elseif ($user->hasRole('Admin')) {
        $tickFile = 'blue-tick.png';
    }
    $tickUrl = asset('assets/users/images/' . $tickFile);
@endphp

    <div class="container-fluid">
        <div class="card profile-overview">
            <div class="card-body d-flex">
                <div class="clearfix">
                    <div class="d-inline-block position-relative me-sm-4 me-3 mb-3 mb-lg-0">
                        {{-- Avatar with correct styling --}}
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                             class="rounded-4 profile-avatar"
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <span class="fa fa-circle border border-3 border-white text-{{ $statusClass }} position-absolute bottom-0 end-0 rounded-circle"></span>
                    </div>
                </div>
                <div class="clearfix d-xl-flex flex-grow-1">
                    <div class="clearfix pe-md-5">
                        <h3 class="fw-semibold mb-1">{{ $user->name }}
                            <img src="{{ $tickUrl }}" alt="Role badge" style="width: 20px; height: 20px; display: inline-block; margin-left: 2px;" >
                            {{-- Email verified indicator (small green check) --}}
                            {{-- @if($isVerified)
                                <i class="fa-regular fa-circle-check text-success ms-1" style="font-size: 0.9rem;" title="Email verified"></i>
                            @endif --}}
                        </h3>
                        <ul class="d-flex flex-wrap fs-6 align-items-center">
                            <li class="me-3 d-inline-flex align-items-center">
                                <i class="las la-envelope me-1 fs-18"></i> {{ $user->email }}
                            </li>
                            <li class="me-3 d-inline-flex align-items-center">
                                <i class="las la-user-tag me-1 fs-18"></i> {{ $roles ?: 'No role assigned' }}
                            </li>
                            <li class="me-3 d-inline-flex align-items-center">
                                <i class="las la-briefcase me-1 fs-18"></i> {{ $position }}
                            </li>
                            <li class="me-3 d-inline-flex align-items-center">
                                <i class="las la-calendar me-1 fs-18"></i> Joined {{ $user->created_at->format('M d, Y') }}
                            </li>
                        </ul>
                        <div class="d-md-flex d-none flex-wrap">
                            <div class="border outline-dashed rounded p-2 d-flex align-items-center me-3 mt-3">
                                <div class="avatar avatar-md bg-primary-light text-primary rounded d-flex align-items-center justify-content-center">
                                    <i class="las la-user-shield fs-20"></i>
                                </div>
                                <div class="clearfix ms-2">
                                    <h3 class="mb-0 fw-semibold lh-1">{{ ucfirst($user->status ?? 'active') }}</h3>
                                    <span class="fs-14">Account Status</span>
                                </div>
                            </div>
                            <div class="border outline-dashed rounded p-2 d-flex align-items-center me-3 mt-3">
                                <div class="avatar avatar-md bg-success-light text-success rounded d-flex align-items-center justify-content-center">
                                    <i class="las la-check-circle fs-20"></i>
                                </div>
                                <div class="clearfix ms-2">
                                    <h3 class="mb-0 fw-semibold lh-1">{{ $user->hasVerifiedEmail() ? 'Verified' : 'Unverified' }}</h3>
                                    <span class="fs-14">Email Status</span>
                                </div>
                            </div>
                            <div class="border outline-dashed rounded p-2 d-flex align-items-center me-3 mt-3">
                                <div class="avatar avatar-md bg-info-light text-info rounded d-flex align-items-center justify-content-center">
                                    <i class="las la-tasks fs-20"></i>
                                </div>
                                <div class="clearfix ms-2">
                                    <h3 class="mb-0 fw-semibold lh-1">{{ $user->posts()->count() }}</h3>
                                    <span class="fs-14">Posts</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix mt-3 mt-xl-0 ms-auto d-flex flex-column col-xl-3">
                        <div class="clearfix mb-3 text-xl-end">
                            <a href="{{ route('account', ['tab' => 'profile']) }}" wire:navigate class="btn btn-primary">Edit Profile</a>
                        </div>
                        <div class="mt-auto d-flex align-items-center">
                            <div class="clearfix me-3">
                                <span class="fw-medium text-black d-block mb-1">Account Age</span>
                                <p class="mb-0 d-flex">
                                    <i class="las la-clock fs-18 me-1 text-primary"></i>
                                    <span>{{ $user->created_at->diffForHumans() }}</span>
                                </p>
                            </div>
                            <div id="chartProfileProgress" style="width:80px;height:80px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── TAB NAVIGATION ─────────────────────────────────────────── --}}
            <div class="card-footer py-0 d-flex flex-wrap justify-content-between align-items-center">
                <ul class="nav nav-underline nav-underline-primary nav-underline-text-dark nav-underline-gap-x-0" role="tablist">
                    <li class="nav-item ms-1" role="presentation">
                        <a href="{{ route('account', ['tab' => 'overview']) }}" wire:navigate
                           class="nav-link py-3 border-3 text-dark {{ $tab === 'overview' ? 'active' : '' }}">Overview</a>
                    </li>
                    <li class="nav-item ms-1" role="presentation">
                        <a href="{{ route('account', ['tab' => 'profile']) }}" wire:navigate
                           class="nav-link py-3 border-3 text-dark {{ $tab === 'profile' ? 'active' : '' }}">Profile</a>
                    </li>
                    <li class="nav-item ms-1" role="presentation">
                        <a href="{{ route('account', ['tab' => 'security']) }}" wire:navigate
                           class="nav-link py-3 border-3 text-dark {{ $tab === 'security' ? 'active' : '' }}">Security</a>
                    </li>
                    <li class="nav-item ms-1" role="presentation">
                        <a href="{{ route('account', ['tab' => 'activity']) }}" wire:navigate
                           class="nav-link py-3 border-3 text-dark {{ $tab === 'activity' ? 'active' : '' }}">Activity</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- ─── TAB CONTENT (Livewire child components) ──────────────────── --}}
        <div class="tab-content" id="tabContentMyProfileBottom">
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
                @default
                    @livewire('admin.users.account.tabs.overview-tab', key('overview'))
            @endswitch
        </div>
    </div>
</div>