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
    <link href="{{ asset('assets/users/css/style.css') }}" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

    <style>
        .notif-dropdown-panel {
            width: 380px;
            max-width: calc(100vw - 2rem);
            max-height: 400px;
            overflow-y: auto;
        }

        @media (max-width: 480px) {
            .notif-dropdown-panel {
                width: calc(100vw - 2rem);
                left: 1rem !important;
                right: 1rem !important;
            }
        }

        .notif-dropdown-panel .flex-grow-1 {
            min-width: 0;
            overflow: hidden;
        }

        .notif-dropdown-panel .flex-grow-1 p,
        .notif-dropdown-panel .flex-grow-1 div {
            overflow-wrap: break-word;
            word-break: break-word;
        }
    </style>
    @livewireStyles

    <!-- Vendor Scripts (deferred) -->
    <script src="{{ asset('assets/users/vendor/global/global.min.js') }}" defer></script>
    <script src="{{ asset('assets/users/vendor/chart.js/Chart.bundle.min.js') }}" defer></script>
    <script src="{{ asset('assets/users/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}" defer></script>
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
    <script src="{{ asset('assets/users/js/custom.js') }}?v={{ filemtime(public_path('assets/users/js/custom.js')) }}"
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
        @auth


            @livewire('admin.partials.navbar')
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

    <script>
        (function () {
            function bindHamburger() {
                jQuery(document).off('click.hamburgerFix').on('click.hamburgerFix', '.nav-control', function () {
                    jQuery('#main-wrapper').toggleClass('menu-toggle');
                    jQuery('.hamburger').toggleClass('is-active');
                });
            }

            document.addEventListener('DOMContentLoaded', bindHamburger);
            document.addEventListener('livewire:navigated', bindHamburger);

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
            let toastEl = null;
            let hideTimeout = null;

            function ensureToastEl() {
                if (toastEl && document.body.contains(toastEl)) return toastEl;

                toastEl = document.createElement('div');
                toastEl.id = 'global-toast';
                toastEl.style.cssText = [
                    'position:fixed', 'top:16px', 'right:16px', 'z-index:99999',
                    'max-width:420px', 'width:calc(100% - 32px)',
                    'display:none', 'pointer-events:none'
                ].join(';');
                toastEl.innerHTML = `
                <div id="global-toast-inner" style="pointer-events:auto; display:flex; align-items:center; padding:1rem; border-radius:1rem; box-shadow:0 10px 30px rgba(0,0,0,.2); color:#fff; gap:.75rem; backdrop-filter:blur(8px);">
                    <div id="global-toast-icon" style="flex-shrink:0; font-size:1.5rem;"></div>
                    <div style="flex-grow:1;">
                        <div id="global-toast-title" style="font-weight:700;"></div>
                        <div id="global-toast-message" style="font-size:.875rem; opacity:.9;"></div>
                    </div>
                    <button id="global-toast-close" style="background:none; border:0; color:#fff; opacity:.75; cursor:pointer; font-size:1rem;">✕</button>
                </div>
            `;
                document.body.appendChild(toastEl);

                toastEl.querySelector('#global-toast-close').addEventListener('click', hide);

                return toastEl;
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

                el.querySelector('#global-toast-icon').innerHTML =
                    type === 'success' ? '✓' : '⚠';
                el.querySelector('#global-toast-title').textContent = title;
                el.querySelector('#global-toast-message').innerHTML = message;

                el.style.display = 'block';

                clearTimeout(hideTimeout);
                hideTimeout = setTimeout(hide, 4000);
            }

            function hide() {
                if (toastEl) toastEl.style.display = 'none';
                clearTimeout(hideTimeout);
            }

            function bindNotifyListener() {
                if (typeof window.Livewire === 'undefined') {
                    setTimeout(bindNotifyListener, 300);
                    return;
                }
                // Livewire.on is safe to call once; it does not duplicate
                // across wire:navigate since this whole IIFE runs only once
                // per real page load, not per Livewire component mount.
                window.Livewire.on('notify', (detail) => {
                    // Livewire v3 sometimes wraps single-array payloads
                    const payload = Array.isArray(detail) ? detail[0] : detail;
                    show(payload || {});
                });
            }

            return { show, hide, bindNotifyListener };
        })();

        document.addEventListener('livewire:navigated', function () {
            // Belt-and-braces: hide any stale toast on navigation.
            window.GlobalToast.hide();
        });

        document.addEventListener('DOMContentLoaded', function () {
            window.GlobalToast.bindNotifyListener();
        });
    </script>


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
    <script>
        (function () {
            function collapseMobileSidebarByDefault() {
                if (window.innerWidth <= 991) {
                    document.getElementById('main-wrapper')?.classList.add('menu-toggle');
                    document.querySelector('.hamburger')?.classList.add('is-active');
                }
            }

            document.addEventListener('DOMContentLoaded', collapseMobileSidebarByDefault);
            document.addEventListener('livewire:navigated', collapseMobileSidebarByDefault);

            if (document.readyState !== 'loading') {
                collapseMobileSidebarByDefault();
            }
        })();
    </script>

</body>

</html>