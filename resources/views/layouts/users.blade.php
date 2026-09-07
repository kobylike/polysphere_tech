<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $title ?? 'Polysphere Tech - IT Solutions & Software Development' }}</title>

    <!-- Primary Meta Tags -->
    <meta name="description"
        content="Polysphere Tech delivers custom software development, SaaS platforms, and digital transformation solutions. We build future-ready technology for modern businesses.">
    <meta name="keywords"
        content="IT solutions, software development, SaaS platform, digital transformation, custom software, IT consulting, Polysphere Tech">

    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="Polysphere Tech - IT Solutions & Software Development">
    <meta property="og:description"
        content="Custom software development, SaaS platforms, and digital transformation solutions for modern businesses.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/main/imgs/favicon.svg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Vendor CSS -->
    <link href="{{ asset('assets/users/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/users/vendor/swiper/css/swiper-bundle.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.4/nouislider.min.css">
    <link href="{{ asset('assets/users/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/users/vendor/jvmap/jquery-jvectormap.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('assets/users/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('assets/users/vendor/tagify/dist/tagify.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/users/css/style.css') }}?v={{ filemtime(public_path('assets/users/css/style.css')) }}"
        rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

    {{-- ═══════════════════════════════════════════════════════════════════════════
    RESPONSIVE OVERRIDES — custom breakpoint fixes on top of the theme's own CSS.
    Two ranges handled separately:
    - Tablet (768px–991px): handled via JS further down this file, which
    just adds the theme's OWN `menu-toggle` class to #main-wrapper
    automatically (the same class the desktop hamburger already adds
    manually). Since data-sidebar-style stays "full" the whole time,
    this activates the theme's own already-correct CSS for that
    combination — icon-only rail width, plus the existing
    `.metismenu > li:hover > ul` flyout rules — with zero new CSS of
    our own needed. The hamburger itself is hidden at this width (CSS
    below) since there's nothing left to manually toggle.
    - Phone (<=767px): sidebar fully hidden off-canvas, hamburger slides it in/out via the `mobile-sidebar-open` class
        we control ourselves (see the hamburger script near the bottom of this file).
        ═══════════════════════════════════════════════════════════════════════════ --}} <style>
        /* Tablet: hide the hamburger — menu-toggle is applied automatically
        via JS, so there's nothing for the user to manually click here. */
        @media (min-width: 768px) and (max-width: 991px) {
        .nav-control {
        display: none !important;
        }
        }

        /* Phone: fully hidden, hamburger-controlled */
        @media (max-width: 767px) {
        .deznav {
        left: -260px !important;
        transition: left 0.3s ease;
        z-index: 9999;
        }
        #main-wrapper.mobile-sidebar-open .deznav {
        left: 0 !important;
        }
        .content-body {
        margin-left: 0 !important;
        }
        }

        /* Notification dropdown: matches the theme's own widget-timeline
        pattern exactly (see the vanilla "height370" utility + dz-scroll
        class) — just a fixed height with an internal scrollbar, no
        custom width, z-index, or positioning. Every earlier attempt to
        add more than this was solving problems that came from adding
        too much custom CSS in the first place, not from anything
        actually wrong with a plain Bootstrap dropdown. */
        .notif-dropdown-panel {
        max-height: 460px;
        overflow-y: auto;
        }
        @media (max-width: 991px) {
        .notif-dropdown-panel {
        max-height: 320px;
        }
        }
        @media (max-width: 480px) {
        .notif-dropdown-panel {
        max-height: 280px;
        }
        }
        .notif-dropdown-menu {
        width: 340px;
        }
        .notif-dropdown-panel > div {
        transition: background-color 0.15s ease;
        border-radius: 0.375rem;
        }
        .notif-dropdown-panel > div:hover {
        background-color: rgba(0, 0, 0, 0.035);
        }

        /* Navbar avatar (the small toggle icon only — NOT the larger
        avatar shown inside the open profile card, which uses its own
        "avatar avatar-md" class and is untouched by this rule).
        Scoped to .js-dropdown-toggle .header-media specifically so it
        can never apply to a .header-media used elsewhere (e.g. inside
        the dropdown card itself) at a different intended size.
        Sized to sit visually level with the other navbar icons
        (bell/gear/fullscreen) rather than the theme's default 40px,
        which reads oversized in the compact header. The container owns
        the fixed size + circular clip; the <img> just fills it at 100%
        and lets object-fit:cover handle non-square source photos. */
        .header-profile2 .js-dropdown-toggle .header-media {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        }
        .header-profile2 .js-dropdown-toggle .header-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        border-radius: 50%;
        }

        /* Profile dropdown panel: give it an explicit width instead of
        letting it shrink-wrap inside the flex navbar. Without this it
        can collapse to a sliver and clip every menu label — the panel
        is positioned absolute (see .js-dropdown-menu.show further down)
        but was never given a width of its own, so it inherited
        whatever narrow space was left in the flex row it started from.
        max-width keeps it from ever overflowing a narrow/mobile
        viewport regardless of where the toggle happens to sit. */
        .header-profile2 .js-dropdown-menu {
        width: 280px;
        max-width: calc(100vw - 24px);
        }

        /* JS-driven dropdowns (notification bell, profile menu): force
        right-edge alignment ourselves. Bootstrap's own .dropdown-menu-end
        CSS only applies when the dropdown has a [data-bs-popper]
        attribute, which Bootstrap's JS adds automatically when it opens
        a dropdown via Popper — since we toggle the "show" class with our
        own plain JS instead, that attribute never appears, so the
        built-in end-alignment rule silently never fires. Without this,
        the menu falls back to default static/left-aligned positioning,
        which is exactly what made it render out of place — pushed off
        to the side and clipped instead of anchored under the toggle. */
        .js-dropdown-menu.show {
        position: absolute;
        top: 100%;
        right: 0;
        left: auto;
        }

        /* Logo: full lockup on desktop, icon-only crop on phone */
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
        @media (max-width: 767px) {
        .brand-logo-full {
        display: none !important;
        }
        .brand-logo-icon-only {
        display: inline-block !important;
        }
        }
        /* Also switch to icon-only whenever the sidebar is collapsed via
        menu-toggle — this covers desktop (manual hamburger click) and
        tablet (auto-applied menu-toggle), both of which narrow the
        nav-header area and would otherwise clip the full logo. */
        #main-wrapper.menu-toggle .brand-logo-full {
        display: none !important;
        }
        #main-wrapper.menu-toggle .brand-logo-icon-only {
        display: inline-block !important;
        }
        </style>

        @livewireStyles

        <!-- Vendor Scripts (deferred) -->
        <script src="{{ asset('assets/users/vendor/global/global.min.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/chart.js/Chart.bundle.min.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"
            defer></script>
        <script src="{{ asset('assets/users/vendor/apexchart/apexchart.js') }}" defer></script>
        <script src="{{ asset('assets/users/js/dashboard/dashboard-1.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/draggable/draggable.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/tagify/dist/tagify.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/datatables/js/jquery.dataTables.min.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/datatables/js/dataTables.buttons.min.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/datatables/js/buttons.html5.min.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/datatables/js/jszip.min.js') }}" defer></script>
        <script src="{{ asset('assets/users/js/plugins-init/datatables.init.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/bootstrap-datetimepicker/js/moment.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"
            defer></script>
        <script src="{{ asset('assets/users/vendor/jqvmap/js/jquery.vmap.min.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/jqvmap/js/jquery.vmap.world.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/jqvmap/js/jquery.vmap.usa.js') }}" defer></script>
        <script
            src="{{ asset('assets/users/js/custom.js') }}?v={{ filemtime(public_path('assets/users/js/custom.js')) }}"
            defer></script>
        <script src="{{ asset('assets/users/js/deznav-init.js') }}" defer></script>
        <script src="{{ asset('assets/users/js/demo.js') }}" defer></script>
        <script src="{{ asset('assets/users/js/styleSwitcher.js') }}" defer></script>
        <script src="{{ asset('assets/users/vendor/ckeditor/ckeditor.js') }}" defer></script>
        <script src="{{ asset('assets/users/js/dashboard/cms.js') }}" defer></script>
</head>

<body data-typography="poppins" data-theme-version="light" data-layout="vertical" data-nav-headerbg="color_4"
    data-headerbg="color_4" data-primary="color_1" data-secondary="color_1" data-sidebarbg="color_1"
    data-sidebar-style="full" data-sidebar-position="fixed" data-header-position="fixed" data-container="full">

    <div id="main-wrapper">
        <script>
            // Synchronous, non-deferred: runs immediately as the parser
            // reaches this point, before the navbar/sidebar markup below is
            // painted. This prevents the "full sidebar flashes then snaps
            // to collapsed" FOUC that happens if we wait for
            // DOMContentLoaded to add the menu-toggle class instead.
            (function () {
                var w = window.innerWidth;
                if (w >= 768 && w <= 991) {
                    document.getElementById('main-wrapper')?.classList.add('menu-toggle');
                }
            })();
        </script>
        @auth
            @persist('navbar')
            @livewire('admin.partials.navbar')
            @endpersist
            @livewire('admin.partials.sidebar')
        @endauth
        <div class="content-body">
            @if(isset($slot))
                {{ $slot }}
            @endif
            @yield('content')
        </div>

        <!-- Offcanvases and Modals -->
        <div class="offcanvas offcanvas-end customeoff" tabindex="-1" id="offcanvasExample">
            <div class="offcanvas-header">
                <h5 class="modal-title" id="#gridSystemModal">Add Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="offcanvas-body">
                <div class="container-fluid">
                    <!-- Existing content -->
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-end customeoff" tabindex="-1" id="offcanvasExample1">
            <div class="offcanvas-header">
                <h5 class="modal-title" id="#gridSystemModal1">Add New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="offcanvas-body">
                <div class="container-fluid">
                    <!-- Existing content -->
                </div>
            </div>
        </div>

        <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel1"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel1">Invite Employee</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Existing content -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Component -->
        @livewire('admin.partials.footer')
    </div>

    @livewireScripts
    @vite('resources/js/app.js')

    {{-- ─── Direct Echo Listener for Permissions & Profile ─── --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userId = {{ Auth::id() ?? 'null' }};
            if (!userId) return;

            // Wait for Echo to be ready
            const checkEcho = setInterval(() => {
                if (typeof window.Echo !== 'undefined') {
                    clearInterval(checkEcho);

                    console.log('✅ Setting up listeners for user:', userId);

                    // ─── Permissions updated ──────────────────────────────────────
                    window.Echo.private(`App.Models.User.${userId}`)
                        .listen('permissions.updated', (e) => {
                            console.log('✅ permissions.updated received', e);
                            Livewire.dispatch('permissions-updated', {
                                userId: e.user_id,
                                roles: e.roles,
                                permissions: e.permissions,
                            });
                            Livewire.dispatch('$refresh');
                        });

                    // ─── Profile updated ──────────────────────────────────────────
                    window.Echo.private(`App.Models.User.${userId}`)
                        .listen('.profile.updated', (e) => {
                            console.log('✅ profile.updated received', e);
                            Livewire.dispatch('own-profile-updated', {
                                userId: e.user?.id,
                                name: e.user?.name,
                                avatarUrl: e.user?.avatar_url,
                                profile: e.profile,
                                profileData: e.profile_data,
                            });
                        });
                }
            }, 300);
        });
    </script>

    {{-- ═══════════════════════════════════════════════════════════════════════════
    ChatBridge — Central Echo manager for Chat, System Notifications & Friend Profile updates
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <script>
        window.ChatBridge = window.ChatBridge || (function () {
            const userId = {{ Auth::id() ?? 'null' }};

            let notificationChannel = null;
            const chatChannels = {};
            const profileChannels = {};

            function ensureEcho(retryFn) {
                if (typeof window.Echo === 'undefined') {
                    setTimeout(retryFn, 300);
                    return false;
                }
                return true;
            }

            // ─── Notifications (chat messages + system/bell notifications) ─────
            function subscribeToNotifications() {
                if (!userId) return;
                if (!ensureEcho(subscribeToNotifications)) return;
                if (notificationChannel) return;

                notificationChannel = window.Echo.private(`notifications.${userId}`);

                // Friend list refresh on new message
                notificationChannel.listen('.message.sent', () => {
                    window.Livewire?.dispatch('friend-list-refresh-needed');
                });

                // New system/bell notification
                notificationChannel.listen('.new.notification', (data) => {
                    window.Livewire?.dispatch('notification-received', {
                        notification: data.notification ?? data
                    });
                });
            }

            // ─── Chat channels ─────────────────────────────────────────────────
            function subscribeToChat(friendId) {
                if (!userId || !friendId) return;
                if (!ensureEcho(() => subscribeToChat(friendId))) return;

                const ids = [userId, friendId].sort((a, b) => a - b);
                const channelName = `chat.${ids[0]}.${ids[1]}`;

                if (chatChannels[channelName]) return;

                const channel = window.Echo.private(channelName);
                channel.listen('.message.sent', (payload) => {
                    window.Livewire?.dispatch('message-received', {
                        senderId: parseInt(payload.sender_id, 10),
                        receiverId: parseInt(payload.receiver_id, 10),
                    });
                });

                chatChannels[channelName] = channel;
            }

            // ─── Friend profile updates ────────────────────────────────────────
            function subscribeToProfileUpdates(friendIds) {
                if (!userId) return;
                if (!ensureEcho(() => subscribeToProfileUpdates(friendIds))) return;

                // Leave channels for friends no longer in the list
                Object.keys(profileChannels).forEach((id) => {
                    if (!friendIds.includes(parseInt(id, 10))) {
                        window.Echo.leave(profileChannels[id].name);
                        delete profileChannels[id];
                    }
                });

                friendIds.forEach((friendId) => {
                    if (profileChannels[friendId]) return;

                    const channelName = `App.Models.User.${friendId}`;
                    const channel = window.Echo.private(channelName);
                    channel.listen('.profile.updated', (e) => {
                        window.Livewire?.dispatch('friend-profile-updated', {
                            userId: e.user?.id,
                            name: e.user?.name,
                            avatarUrl: e.user?.avatar_url,
                        });
                    });
                    profileChannels[friendId] = channel;
                });
            }

            return {
                subscribeToNotifications,
                subscribeToChat,
                subscribeToProfileUpdates,
            };
        })();

        // ─── Initialise ChatBridge ──────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            window.ChatBridge.subscribeToNotifications();
        });
        document.addEventListener('livewire:navigated', () => {
            window.ChatBridge.subscribeToNotifications();
        });
    </script>

    @stack('scripts')

    <style>
        #main-wrapper {
            opacity: 1 !important;
        }
    </style>

    {{-- ─── Datepicker & image preview ────────────────────────────────────────── --}}
    <script>
        function initDatepicker() {
            if (typeof $.fn.datepicker === 'function') {
                $("#datepicker").datepicker({
                    autoclose: true,
                    todayHighlight: true
                }).datepicker('update', new Date());
            }
        }
        document.addEventListener('DOMContentLoaded', initDatepicker);
        document.addEventListener('livewire:navigated', initDatepicker);

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            $(document).on('change', '#imageUpload', function () {
                readURL(this);
            });
            $(document).on('click', '.remove-img', function () {
                var imageUrl = "images/no-img-avatar.png";
                $('.avatar-preview, #imagePreview').removeAttr('style');
                $('#imagePreview').css('background-image', 'url(' + imageUrl + ')');
            });
        });
    </script>

    {{--
    ═══════════════════════════════════════════════════════════════════════════
    HAMBURGER + SIDEBAR TOGGLE — single source of truth.
    - Tablet (768–991px): hamburger is hidden entirely via CSS above, so this
    never fires in that range anyway; sidebar just stays as the icon rail.
    - Phone (<=767px): toggles our own `mobile-sidebar-open` class, which the CSS above uses to slide `.deznav` on/off
        screen. - Desktop (>991px): falls back to the theme's original `menu-toggle`
        class, completely untouched from vanilla behavior.
        (Note: this replaces the old unconditional hamburger-binding script that
        used to sit here — do not re-add a second copy of it.)
        ═══════════════════════════════════════════════════════════════════════════ --}}
        <script>
            (function () {
                function isMobile() {
                    return window.innerWidth <= 767;
                }

                function bindHamburger() {
                    jQuery(document).off('click.hamburgerFix').on('click.hamburgerFix', '.nav-control', function () {
                        if (isMobile()) {
                            jQuery('#main-wrapper').toggleClass('mobile-sidebar-open');
                            jQuery('.hamburger').toggleClass('is-active');
                        } else {
                            jQuery('#main-wrapper').toggleClass('menu-toggle');
                            jQuery('.hamburger').toggleClass('is-active');
                        }
                    });
                }

                function resetOnNavigate() {
                    if (isMobile()) {
                        document.getElementById('main-wrapper')?.classList.remove('mobile-sidebar-open');
                        document.querySelector('.hamburger')?.classList.remove('is-active');
                    }
                }

                document.addEventListener('DOMContentLoaded', bindHamburger);
                document.addEventListener('livewire:navigated', function () {
                    bindHamburger();
                    resetOnNavigate();
                });

                if (document.readyState !== 'loading') {
                    bindHamburger();
                }
            })();
        </script>

        <script>
            (function () {
                function bindFullscreen() {
                    jQuery(document).off('click.dzFullscreenFix').on('click.dzFullscreenFix', '.dz-fullscreen', function (e) {
                        e.preventDefault();

                        var isFullscreen = document.fullscreenElement || document.webkitFullscreenElement ||
                            document.mozFullScreenElement || document.msFullscreenElement;

                        if (isFullscreen) {
                            if (document.exitFullscreen) {
                                document.exitFullscreen();
                            } else if (document.msExitFullscreen) {
                                document.msExitFullscreen();
                            } else if (document.mozCancelFullScreen) {
                                document.mozCancelFullScreen();
                            } else if (document.webkitExitFullscreen) {
                                document.webkitExitFullscreen();
                            }
                        } else {
                            var el = document.documentElement;
                            if (el.requestFullscreen) {
                                el.requestFullscreen();
                            } else if (el.webkitRequestFullscreen) {
                                el.webkitRequestFullscreen();
                            } else if (el.mozRequestFullScreen) {
                                el.mozRequestFullScreen();
                            } else if (el.msRequestFullscreen) {
                                el.msRequestFullscreen();
                            }
                        }

                        jQuery('.dz-fullscreen').toggleClass('active');
                    });
                }

                document.addEventListener('DOMContentLoaded', bindFullscreen);
                document.addEventListener('livewire:navigated', bindFullscreen);

                if (document.readyState !== 'loading') {
                    bindFullscreen();
                }
            })();
        </script>

        <script>
            window.GlobalToast = window.GlobalToast || (function () {
                let hideTimeout = null;

                function getEl() {
                    return document.getElementById('global-toast');
                }

                function build() {
                    const el = document.createElement('div');
                    el.id = 'global-toast';
                    el.style.cssText = [
                        'position:fixed', 'top:16px', 'right:16px', 'z-index:99999',
                        'max-width:420px', 'width:calc(100% - 32px)',
                        'display:none', 'pointer-events:none'
                    ].join(';');
                    el.innerHTML = `
                <div id="global-toast-inner" style="pointer-events:auto; display:flex; align-items:center; padding:1rem; border-radius:1rem; box-shadow:0 10px 30px rgba(0,0,0,.2); color:#fff; gap:.75rem; backdrop-filter:blur(8px);">
                    <div id="global-toast-icon" style="flex-shrink:0; font-size:1.5rem;"></div>
                    <div style="flex-grow:1;">
                        <div id="global-toast-title" style="font-weight:700;"></div>
                        <div id="global-toast-message" style="font-size:.875rem; opacity:.9;"></div>
                    </div>
                    <button id="global-toast-close" style="background:none; border:0; color:#fff; opacity:.75; cursor:pointer; font-size:1rem;">✕</button>
                </div>
            `;
                    document.body.appendChild(el);
                    return el;
                }

                function ensureToastEl() {
                    return getEl() || build();
                }

                function show(detail) {
                    const el = ensureToastEl();
                    const type = detail.type || 'success';
                    const title = detail.title || (type === 'success' ? 'Success!' : 'Error!');
                    const message = detail.message || '';

                    const inner = el.querySelector('#global-toast-inner');
                    inner.style.background = type === 'success'
                        ? 'linear-gradient(135deg, #10b981, #059669)'
                        : 'linear-gradient(135deg, #ef4444, #dc2626)';

                    el.querySelector('#global-toast-icon').innerHTML = type === 'success' ? '✓' : '⚠';
                    el.querySelector('#global-toast-title').textContent = title;
                    el.querySelector('#global-toast-message').innerHTML = message;

                    el.style.display = 'block';

                    clearTimeout(hideTimeout);
                    hideTimeout = setTimeout(hide, 4000);
                }

                function hide() {
                    // Fully remove the node instead of just hiding it — no stale/orphaned
                    // toast can ever survive a wire:navigate this way, so there's nothing
                    // left for `document.body.contains(toastEl)`-style checks to get wrong.
                    const el = getEl();
                    if (el) el.remove();
                    clearTimeout(hideTimeout);
                }

                // Delegated on `document`, bound exactly once, forever. Doesn't care which
                // toast instance is currently on screen — so it can never go stale.
                document.addEventListener('click', function (e) {
                    if (e.target.closest('#global-toast-close')) hide();
                });

                function bindNotifyListener() {
                    if (typeof window.Livewire === 'undefined') {
                        setTimeout(bindNotifyListener, 300);
                        return;
                    }
                    window.Livewire.on('notify', (detail) => {
                        const payload = Array.isArray(detail) ? detail[0] : detail;
                        show(payload || {});
                    });
                }

                return { show, hide, bindNotifyListener };
            })();

            document.addEventListener('livewire:navigated', function () {
                window.GlobalToast.hide();
            });

            document.addEventListener('DOMContentLoaded', function () {
                window.GlobalToast.bindNotifyListener();
            });
        </script>
        {{-- ═══════════════════════════════════════════════════════════════════════════
        UNSTICK — clears any leftover Bootstrap dropdown/modal backdrop state and
        forces pointer-events back on after every wire:navigate. This targets the
        "page becomes unresponsive after an action, only a refresh fixes it" bug:
        Bootstrap's dropdown/modal JS attaches internal click-outside listeners
        and body classes (e.g. modal-open, overflow:hidden) directly to DOM nodes
        it opened; if Livewire's morph step then replaces/removes those nodes
        during a re-render (e.g. right after a wire:click that also dispatches
        `notify`), Bootstrap's own cleanup never runs, and the leftover state
        (a transparent backdrop, or body overflow/pointer-events left blocked)
        silently eats every click until a hard refresh resets the DOM from
        scratch.
        ═══════════════════════════════════════════════════════════════════════════ --}}
        <script>
            function unstickPage() {
                // Remove any stray Bootstrap backdrops left behind.
                document.querySelectorAll('.modal-backdrop, .dropdown-backdrop, .offcanvas-backdrop')
                    .forEach(el => el.remove());

                // Restore body state Bootstrap may have left locked.
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');

                // Close any dropdown left in an inconsistent "show" state
                // without its trigger (which Livewire may have replaced).
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    const trigger = menu.previousElementSibling;
                    if (!trigger || trigger.getAttribute('aria-expanded') !== 'true') {
                        menu.classList.remove('show');
                    }
                });

                // Last-resort safety net: never leave the wrapper itself
                // non-interactive.
                const wrapper = document.getElementById('main-wrapper');
                if (wrapper) wrapper.style.pointerEvents = '';
            }

            document.addEventListener('livewire:navigated', unstickPage);
            // Also run right after any Livewire commit finishes, since the
            // freeze happens after an in-page action (e.g. delete/save), not
            // only after navigation.
            document.addEventListener('livewire:init', () => {
                window.Livewire.hook('commit', ({ succeed }) => {
                    succeed(() => {
                        // Small delay so this runs after Livewire's own DOM
                        // morph/cleanup for this commit has finished.
                        setTimeout(unstickPage, 50);
                    });
                });
            });
        </script>

        {{--
        ═══════════════════════════════════════════════════════════════════════════
        TABLET SIDEBAR MODE — handles re-applying menu-toggle after Livewire
        navigation and window resize. The very first page load is already
        handled by a synchronous inline script right inside #main-wrapper
        (see near the top of

        <body>), which prevents a flash of the full
            sidebar before this collapsed state kicks in.
            ═══════════════════════════════════════════════════════════════════════════ --}}
            <script>
                (function () {
                    function applyTabletMenuToggle() {
                        const width = window.innerWidth;
                        if (width >= 768 && width <= 991) {
                            document.getElementById('main-wrapper')?.classList.add('menu-toggle');
                        }
                    }

                    document.addEventListener('livewire:navigated', applyTabletMenuToggle);
                    window.addEventListener('resize', applyTabletMenuToggle);
                })();
            </script>

            {{--
            ═══════════════════════════════════════════════════════════════════════════
            JS-DRIVEN DROPDOWNS (notification bell + profile menu) — replaces the
            earlier Alpine-store approach, which needed x-data scoping and ran into
            persist/morph edge cases. This uses the exact same proven pattern already
            used elsewhere in this file (hamburger, fullscreen toggle): delegated
            jQuery event handlers bound to `document`, so they survive wire:navigate
            without rebinding, plus a plain JS variable (window.__openDropdownId) to
            remember which dropdown was open across navigation — since that variable
            lives in the page's JS memory, not the DOM, it isn't affected by Livewire
            tearing down and rebuilding the navbar on each navigate.

            Each toggle link gets class "js-dropdown-toggle". Each dropdown panel
            gets class "js-dropdown-menu" plus a unique `data-dropdown-id` (e.g.
            "notif", "profile") so we know which one to re-open after navigation.
            ═══════════════════════════════════════════════════════════════════════════ --}}
            <script>
                (function () {
                    function closeAllDropdowns(except) {
                        document.querySelectorAll('.js-dropdown-menu.show').forEach(function (el) {
                            if (el !== except) el.classList.remove('show');
                        });
                    }

                    function bindDropdowns() {
                        jQuery(document).off('click.jsDropdownToggle').on('click.jsDropdownToggle', '.js-dropdown-toggle', function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            var $menu = jQuery(this).closest('li').find('.js-dropdown-menu').first();
                            var isOpen = $menu.hasClass('show');

                            closeAllDropdowns();

                            if (!isOpen) {
                                $menu.addClass('show');
                                window.__openDropdownId = $menu.attr('data-dropdown-id') || null;
                            } else {
                                window.__openDropdownId = null;
                            }
                        });

                        jQuery(document).off('click.jsDropdownOutside').on('click.jsDropdownOutside', function (e) {
                            if (!jQuery(e.target).closest('.js-dropdown-menu, .js-dropdown-toggle').length) {
                                closeAllDropdowns();
                                window.__openDropdownId = null;
                            }
                        });
                    }

                    function restoreOpenDropdown() {
                        if (!window.__openDropdownId) return;
                        var $menu = jQuery('[data-dropdown-id="' + window.__openDropdownId + '"]');
                        if ($menu.length) $menu.addClass('show');
                    }

                    document.addEventListener('DOMContentLoaded', bindDropdowns);
                    document.addEventListener('livewire:navigated', function () {
                        bindDropdowns();
                        restoreOpenDropdown();
                    });

                    if (document.readyState !== 'loading') {
                        bindDropdowns();
                    }
                })();
            </script>

        </body>

</html>