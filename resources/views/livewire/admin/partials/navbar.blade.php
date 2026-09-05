<div>
    <div class="nav-header">
        <a wire:navigate.hover href="{{ route('dashboard') }}" class="brand-logo">
            <img src="{{ asset('assets/main/imgs/logo/logo-white.png') }}" alt="Polysphere Tech logo"
                class="brand-logo-full">
            <img src="{{ asset('assets/main/imgs/logo/mobile-logo.png') }}" alt="Polysphere Tech icon"
                class="brand-logo-icon-only">
        </a>
        <style>
            .brand-logo-full {
                height: 32px;
                width: auto;
                display: inline-block;
            }

            .brand-logo-icon-only {
                height: 32px;
                width: auto;
                display: none;
            }

            @media (max-width: 991px) {
                .brand-logo-full {
                    display: none !important;
                }

                .brand-logo-icon-only {
                    display: inline-block !important;
                }
            }
        </style>
        <div class="nav-control">
            <div class="hamburger">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </div>
        </div>
    </div>

    {{-- Badge styles for message icon --}}
    <style>
        .bell-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .dz-msg-badge {
            position: absolute;
            top: -6px;
            right: -8px;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 999px;
            background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
            color: #ffffff !important;
            font-size: 11px;
            font-weight: 700;
            line-height: 20px;
            text-align: center;
            letter-spacing: -0.2px;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.15), 0 2px 8px rgba(76, 175, 80, 0.5);
            pointer-events: none;
            transition: transform 0.15s ease;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .dz-msg-badge-pop {
            animation: dzBadgePop .3s cubic-bezier(.34, 1.56, .64, 1);
        }

        @keyframes dzBadgePop {
            0% {
                transform: scale(.4);
                opacity: 0;
            }

            70% {
                transform: scale(1.15);
                opacity: 1;
            }

            100% {
                transform: scale(1);
            }
        }
    </style>

    {{-- Chat Widget (persisted) --}}
    @persist('chat-widget')
    @auth
        @livewire('admin.messenger.chat-messenger-component')
    @endauth
    @endpersist

    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <nav class="navbar navbar-expand">
                <div class="collapse navbar-collapse justify-content-between">
                    <div class="header-left">
                        <form>
                            <div class="input-group search-area">
                                <span class="input-group-text">
                                    <button class="bg-transparent border-0">
                                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="8.78605" cy="8.78605" r="8.23951" stroke="white"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M14.5168 14.9447L17.7471 18.1667" stroke="white"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </span>
                                <input type="text" class="form-control" placeholder="Search">
                            </div>
                        </form>
                    </div>

                    <ul class="navbar-nav header-right">
                        {{-- Timeline dropdown (static) --}}
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M20.8067 7.62358L20.1842 6.54349C19.6577 5.62957 18.4907 5.31429 17.5755 5.83869V5.83869C17.1399 6.09531 16.6201 6.16812 16.1307 6.04106C15.6413 5.91399 15.2226 5.59749 14.9668 5.16134C14.8023 4.88412 14.7139 4.56836 14.7105 4.24601V4.24601C14.7254 3.72919 14.5304 3.22837 14.17 2.85764C13.8096 2.48691 13.3145 2.27783 12.7975 2.27805H11.5435C11.037 2.27804 10.5513 2.47988 10.194 2.83891C9.83669 3.19795 9.63717 3.68456 9.63961 4.19109V4.19109C9.6246 5.23689 8.77248 6.07678 7.72657 6.07667C7.40421 6.07332 7.08846 5.98491 6.81123 5.82038V5.82038C5.89606 5.29598 4.72911 5.61126 4.20254 6.52519L3.53435 7.62358C3.00841 8.53636 3.3194 9.70258 4.23 10.2323V10.2323C4.8219 10.574 5.18653 11.2056 5.18653 11.889C5.18653 12.5725 4.8219 13.204 4.23 13.5458V13.5458C3.32056 14.0719 3.00923 15.2353 3.53435 16.1453V16.1453L4.16593 17.2346C4.41265 17.6798 4.8266 18.0083 5.31619 18.1474C5.80578 18.2866 6.33064 18.2249 6.77462 17.976V17.976C7.21108 17.7213 7.73119 17.6515 8.21934 17.7822C8.70749 17.9128 9.12324 18.233 9.37416 18.6716C9.5387 18.9489 9.62711 19.2646 9.63046 19.587V19.587C9.63046 20.6435 10.487 21.5 11.5435 21.5H12.7975C13.8505 21.5 14.7055 20.6491 14.7105 19.5961V19.5961C14.7081 19.088 14.9089 18.6 15.2682 18.2407C15.6275 17.8814 16.1155 17.6806 16.6236 17.6831C16.9452 17.6917 17.2596 17.7797 17.5389 17.9394V17.9394C18.4517 18.4653 19.6179 18.1543 20.1476 17.2437V17.2437L20.8067 16.1453C21.0618 15.7075 21.1318 15.186 21.0012 14.6963C20.8706 14.2067 20.5502 13.7893 20.111 13.5366V13.5366C19.6718 13.2839 19.3514 12.8665 19.2208 12.3769C19.0902 11.8873 19.1603 11.3658 19.4154 10.9279C19.5812 10.6383 19.8214 10.3982 20.111 10.2323V10.2323C21.0161 9.70286 21.3264 8.54346 20.8067 7.63274V7.63274V7.62358Z"
                                        stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="12.1751" cy="11.889" r="2.63616" stroke="white" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div id="DZ_W_TimeLine02" class="widget-timeline dz-scroll style-1 p-3 height370">
                                    <ul class="timeline">
                                        <li>
                                            <div class="timeline-badge primary"></div>
                                            <a class="timeline-panel text-muted" href="javascript:void(0);">
                                                <span>10 minutes ago</span>
                                                <h6 class="mb-0">Youtube, a video-sharing website, goes live <strong
                                                        class="text-primary">$500</strong>.</h6>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="timeline-badge info"></div>
                                            <a class="timeline-panel text-muted" href="javascript:void(0);">
                                                <span>20 minutes ago</span>
                                                <h6 class="mb-0">New order placed <strong
                                                        class="text-info">#XF-2356.</strong></h6>
                                                <p class="mb-0">Quisque a consequat ante Sit amet magna at volutapt...
                                                </p>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="timeline-badge danger"></div>
                                            <a class="timeline-panel text-muted" href="javascript:void(0);">
                                                <span>30 minutes ago</span>
                                                <h6 class="mb-0">john just buy your product <strong
                                                        class="text-warning">Sell $250</strong></h6>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="timeline-badge success"></div>
                                            <a class="timeline-panel text-muted" href="javascript:void(0);">
                                                <span>15 minutes ago</span>
                                                <h6 class="mb-0">StumbleUpon is acquired by eBay.</h6>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="timeline-badge warning"></div>
                                            <a class="timeline-panel text-muted" href="javascript:void(0);">
                                                <span>20 minutes ago</span>
                                                <h6 class="mb-0">Mashable, a news website and blog, goes live.</h6>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="timeline-badge dark"></div>
                                            <a class="timeline-panel text-muted" href="javascript:void(0);">
                                                <span>20 minutes ago</span>
                                                <h6 class="mb-0">Mashable, a news website and blog, goes live.</h6>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>

                        {{-- Notification Bell --}}
                        @auth
                            @livewire('admin.partials.notification-bell')
                        @endauth

                        {{-- Chat Messenger Icon --}}
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link bell-link" href="javascript:void(0);"
                                onclick="Alpine.store('chat').open = true">
                                <svg width="20" height="22" viewBox="0 0 22 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M16.9026 6.85114L12.4593 10.4642C11.6198 11.1302 10.4387 11.1302 9.59922 10.4642L5.11844 6.85114"
                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M15.9089 19C18.9502 19.0084 21 16.5095 21 13.4384V6.57001C21 3.49883 18.9502 1 15.9089 1H6.09114C3.04979 1 1 3.49883 1 6.57001V13.4384C1 16.5095 3.04979 19.0084 6.09114 19H15.9089Z"
                                        stroke="white" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                @php
                                    $__unreadCount = auth()->check()
                                        ? \App\Models\Message::where('receiver_id', auth()->id())
                                            ->where('read', false)
                                            ->count()
                                        : 0;
                                @endphp
                                <span id="dz-msg-unread-badge" class="dz-msg-badge"
                                    style="{{ $__unreadCount > 0 ? '' : 'display:none;' }}">
                                    {{ $__unreadCount > 99 ? '99+' : $__unreadCount }}
                                </span>
                            </a>
                        </li>

                        {{-- Fullscreen Toggle --}}
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link bell dz-fullscreen" href="javascript:void(0);">
                                <svg id="icon-full" viewBox="0 0 24 24" width="20" height="20" stroke="currentColor"
                                    stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"
                                    class="css-i6dzq1">
                                    <path
                                        d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"
                                        style="stroke-dasharray: 37, 57; stroke-dashoffset: 0;"></path>
                                </svg>
                                <svg id="icon-minimize" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="#A098AE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-minimize">
                                    <path
                                        d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"
                                        style="stroke-dasharray: 37, 57; stroke-dashoffset: 0;"></path>
                                </svg>
                            </a>
                        </li>

                        {{-- User Profile Dropdown --}}
                        @auth
                            <li class="nav-item ps-3">
                                <div class="dropdown header-profile2">
                                    <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <div class="header-info2 d-flex align-items-center">
                                            <div class="header-media">
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                                            </div>
                                            <div class="header-info">
                                                <h6>{{ $user->name }}</h6>
                                                <p>{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" wire:ignore>
                                        <div class="card border-0 mb-0">
                                            <div class="card-header py-2">
                                                <div class="products">
                                                    <img src="{{ $user->avatar_url }}" class="avatar avatar-md"
                                                        alt="{{ $user->name }}">
                                                    <div>
                                                        <h6>{{ $user->name }}</h6>
                                                        <span>{{ $user->profile?->position ?? 'Member' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body px-0 py-2">
                                                {{-- 🔥 NEW: My Dashboard link --}}
                                                <a href="{{ route('dashboard.user') }}" wire:navigate.hover
                                                    class="dropdown-item ai-icon">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M3 9.5L12 3L21 9.5V19.5C21 20.0304 20.7893 20.5391 20.4142 20.9142C20.0391 21.2893 19.5304 21.5 19 21.5H5C4.46957 21.5 3.96086 21.2893 3.58579 20.9142C3.21071 20.5391 3 20.0304 3 19.5V9.5Z"
                                                            stroke="var(--primary)" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M9 21.5V12.5H15V21.5" stroke="var(--primary)"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                    <span class="ms-2">My Dashboard</span>
                                                </a>

                                                <a href="{{ route('account', ['tab' => 'overview']) }}" wire:navigate.hover
                                                    class="dropdown-item ai-icon">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M11.9848 15.3462C8.11714 15.3462 4.81429 15.931 4.81429 18.2729C4.81429 20.6148 8.09619 21.2205 11.9848 21.2205C15.8524 21.2205 19.1543 20.6348 19.1543 18.2938C19.1543 15.9529 15.8733 15.3462 11.9848 15.3462Z"
                                                            stroke="var(--primary)" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M11.9848 12.0059C14.5229 12.0059 16.58 9.94779 16.58 7.40969C16.58 4.8716 14.5229 2.81445 11.9848 2.81445C9.44667 2.81445 7.38857 4.8716 7.38857 7.40969C7.38 9.93922 9.42381 11.9973 11.9524 12.0059H11.9848Z"
                                                            stroke="var(--primary)" stroke-width="1.42857"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span class="ms-2">My Profile</span>
                                                </a>

                                                <a href="{{ route('account', ['tab' => 'profile']) }}" wire:navigate.hover
                                                    class="dropdown-item ai-icon">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M12 15.5H7.5C6.10444 15.5 5.40665 15.5 4.83886 15.6722C3.56045 16.06 2.56004 17.0605 2.17224 18.3389C2 18.9067 2 19.6044 2 21M16 15.5H18.5M14 11.5C14 13.7091 12.2091 15.5 10 15.5C7.79086 15.5 6 13.7091 6 11.5C6 9.29086 7.79086 7.5 10 7.5C12.2091 7.5 14 9.29086 14 11.5ZM22 10.5C22 12.9853 20.9853 15.5 18.5 15.5C16.0147 15.5 15 12.9853 15 10.5C15 8.01472 16.0147 5.5 18.5 5.5C20.9853 5.5 22 8.01472 22 10.5Z"
                                                            stroke="var(--primary)" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span class="ms-2">Edit Profile</span>
                                                </a>

                                                <a href="{{ route('account', ['tab' => 'security']) }}" wire:navigate.hover
                                                    class="dropdown-item ai-icon">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 22C12 22 20 18 20 12V5L12 2L4 5V12C4 18 12 22 12 22Z"
                                                            stroke="var(--primary)" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z"
                                                            stroke="var(--primary)" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span class="ms-2">Security</span>
                                                </a>

                                                <a href="{{ route('account', ['tab' => 'notifications']) }}"
                                                    wire:navigate.hover class="dropdown-item ai-icon">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z"
                                                            stroke="var(--primary)" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path
                                                            d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21"
                                                            stroke="var(--primary)" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <span class="ms-2">Notifications</span>
                                                </a>
                                            </div>
                                            <div class="card-footer px-0 py-2">
                                                <a href="#" wire:click="logout" class="dropdown-item ai-icon">
                                                    <svg class="profle-logout" xmlns="http://www.w3.org/2000/svg" width="18"
                                                        height="18" viewBox="0 0 24 24" fill="none" stroke="#ff7979"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                        <polyline points="16 17 21 12 16 7"></polyline>
                                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                                    </svg>
                                                    <span class="ms-2 text-danger">Logout</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endauth
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</div>