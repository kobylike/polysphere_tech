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
        @livewire('admin.partials.navbar')
        @livewire('admin.partials.sidebar')

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

</body>

</html>