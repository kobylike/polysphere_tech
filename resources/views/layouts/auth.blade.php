<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
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

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/users/images/favicon.png') }}">

    <link href="{{ asset('assets/users/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/users/css/style.css') }}" rel="stylesheet">


    @livewireStyles
</head>

<body class="vh-100">
    <div class="authincation h-100">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <div class="col-lg-6 col-md-12 col-sm-12 mx-auto align-self-center">
                    @if(isset($slot))
                        {{ $slot }}
                    @endif
                    @yield('content')
                </div>
                <div class="col-xl-6 col-lg-6">
                    <div class="pages-left h-100">
                        <div class="login-content">
                            <a href="index.html"><img src="images/logo-full.png" class="mb-3 logo-dark" alt=""></a>
                            <a href="index.html"><img src="images/logi-white.png" class="mb-3 logo-light" alt=""></a>

                            <p>CRM dashboard uses line charts to visualize customer-related metrics and trends over
                                time.</p>
                        </div>
                        <div class="login-media text-center">
                            <img src="images/login.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @livewireScripts


    <script src="{{ asset('assets/users/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/users/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/users/js/custom.js') }}"></script>
    <script src="{{ asset('assets/users/js/deznav-init.js') }}"></script>
    <script src="{{ asset('assets/users/js/demo.js') }}"></script>
    <script src="{{ asset('assets/users/js/styleSwitcher.js') }}"></script>



</body>

</html>