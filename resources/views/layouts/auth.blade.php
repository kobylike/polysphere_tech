<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Polysphere Tech - IT Solutions & Software Development' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome 7  (Free) -->
    <link rel="stylesheet" href="{{ asset('assets/auth/css/all.min.css')}}">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Tailwind Custom Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        'polysphere': {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                    }
                }
            }
        }
    </script>

    <!-- Livewire Styles -->
    @livewireStyles


</head>

<body class="bg-gray-50 font-sans antialiased">



    {{-- Main Container --}}
    <div class="min-h-screen lg:h-screen lg:overflow-hidden flex flex-col lg:flex-row">

        {{-- Left Panel --}}
        <div
            class="lg:w-1/2 lg:h-full auth-hero relative overflow-x-hidden overflow-y-auto flex justify-center p-6 lg:p-12">

            {{-- Glow overlay --}}
            <div class="absolute inset-0 auth-hero-glow"></div>

            {{-- Floating shapes --}}
            <div class="shape-blob shape-blob--1"></div>
            <div class="shape-blob shape-blob--2"></div>
            <div class="shape-blob shape-blob--3"></div>

            {{-- Content --}}
            <div class="relative z-10 max-w-lg w-full text-white">

                {{-- Logo --}}
                <div class="flex items-center space-x-3 mb-4 lg:mb-12">
                    <img src="{{ asset('assets/main/imgs/logo/logo-white.png') }}" alt="Polysphere Tech"
                        class="h-9 lg:h-16 w-auto object-contain">
                </div>

                {{-- Mobile welcome (shown only on small screens) --}}
                <div class="lg:hidden mb-4">
                    <h1 class="text-xl font-bold">Welcome to Polysphere Tech</h1>
                    <p class="text-white/70 text-sm">IT solutions & software development</p>
                </div>

                {{-- Desktop content --}}
                <div class="hidden lg:block">
                    <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight">
                        Build <span class="text-gradient">Smarter</span>,<br>
                        Scale <span class="text-gradient">Faster</span>
                    </h1>
                    <p class="mt-4 text-lg text-white/80 leading-relaxed">
                        Polysphere Tech delivers cutting-edge software development, SaaS engineering, and digital
                        transformation strategies that empower businesses to innovate and scale.
                    </p>

                    {{-- Features --}}
                    <div class="mt-8 space-y-5">
                        <div class="flex items-start space-x-4">
                            <div
                                class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-rocket text-polysphere-300 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">Innovation-Driven</h3>
                                <p class="text-white/60 text-sm">Cutting-edge technology tailored to your needs</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div
                                class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shield-alt text-polysphere-300 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">Enterprise-Grade Security</h3>
                                <p class="text-white/60 text-sm">Bank-level encryption and compliance</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div
                                class="feature-icon w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-headset text-polysphere-300 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-white">24/7 Expert Support</h3>
                                <p class="text-white/60 text-sm">Dedicated team, always ready to help</p>
                            </div>
                        </div>
                    </div>

                    {{-- Testimonial --}}
                    <div class="testimonial-card mt-10 p-5 rounded-2xl">
                        <p class="text-white/90 italic text-sm leading-relaxed">
                            "Polysphere Tech delivered our SaaS platform ahead of schedule. Their architecture is
                            rock-solid, and their team is a true extension of ours."
                        </p>
                        <div class="flex items-center mt-4">
                            <div
                                class="w-10 h-10 rounded-full bg-polysphere-500/30 flex items-center justify-center text-white font-bold text-sm">
                                SO
                            </div>
                            <div class="ml-3">
                                <div class="font-medium text-white">Samuel Ofori</div>
                                <div class="text-white/50 text-sm">CTO, FinVault</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="lg:w-1/2 lg:h-full bg-white flex flex-col p-6 lg:p-12 auth-form-panel">
            <div class="w-full max-w-md mx-auto animate-fade-in auth-form-panel-inner">
                @if(isset($slot))
                    {{ $slot }}
                @endif
                @yield('content')
            </div>
        </div>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('livewire:init', () => {
            let scrollPos = 0;

            Livewire.hook('morph.updating', () => {
                const panel = document.querySelector('.auth-form-panel');
                if (panel) scrollPos = panel.scrollTop;
            });

            Livewire.hook('morph.updated', () => {
                const panel = document.querySelector('.auth-form-panel');
                if (panel) panel.scrollTop = scrollPos;
            });
        });
    </script>

    @stack('scripts')
</body>

</html>