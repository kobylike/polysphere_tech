<div>
    {{-- Back to home --}}
    <div class="mb-6">
        <a href="{{ url('/') }}" wire:navigate.hover
            class="inline-flex items-center text-sm text-gray-500 hover:text-polysphere-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to home
        </a>
    </div>

    {{-- Header --}}
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Welcome Back</h2>
        <p class="mt-2 text-gray-500">Sign in to your Polysphere Tech account</p>
    </div>

    {{-- Social Login --}}
    {{-- <div class="mb-6">
        <div class="grid grid-cols-2 gap-3">
            <button type="button"
                class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fab fa-google text-red-500 mr-2"></i>
                <span class="text-sm font-medium text-gray-700">Google</span>
            </button>
            <button type="button"
                class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fab fa-microsoft text-blue-500 mr-2"></i>
                <span class="text-sm font-medium text-gray-700">Microsoft</span>
            </button>
        </div>
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500">Or continue with</span>
            </div>
        </div>
    </div> --}}

    {{-- Form --}}
    <form wire:submit.prevent="login" class="space-y-5" novalidate>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <input id="email" type="email" wire:model.live.debounce.400ms="email" autocomplete="email"
                    class="form-input w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-polysphere-500 focus:border-polysphere-500 transition-colors @error('email') border-red-300 @enderror"
                    placeholder="you@example.com">
            </div>
            @error('email')
                <p class="mt-1 text-sm text-red-600 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div x-data="{ showPassword: false }">
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <button type="button" @click="showPassword = !showPassword"
                    class="text-sm text-polysphere-600 hover:text-polysphere-800 transition-colors" tabindex="-1">
                    <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                </button>
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input id="password" :type="showPassword ? 'text' : 'password'"
                    wire:model.live.debounce.400ms="password" autocomplete="current-password"
                    class="form-input w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-polysphere-500 focus:border-polysphere-500 transition-colors @error('password') border-red-300 @enderror"
                    placeholder="••••••••">
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                    tabindex="-1">
                    <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Remember & Forgot --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" type="checkbox" wire:model="remember"
                    class="h-4 w-4 text-polysphere-600 focus:ring-polysphere-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
            </div>
            <a href="{{ route('password.request') }}" wire:navigate.hover
                class="text-sm text-polysphere-600 hover:text-polysphere-800 transition-colors">
                Forgot password?
            </a>
        </div>

        {{-- Submit --}}
        <button type="submit" wire:loading.attr="disabled" wire:target="login"
            class="btn-lift w-full flex justify-center py-3 px-4 bg-gradient-to-r from-polysphere-600 to-polysphere-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-polysphere-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed transition-all">
            <span wire:loading.remove wire:target="login">
                <i class="fas fa-sign-in-alt mr-2"></i> Sign In
            </span>
            <span wire:loading wire:target="login">
                <i class="fas fa-spinner fa-spin mr-2"></i> Authenticating…
            </span>
        </button>
    </form>

    {{-- Footer --}}
    {{-- <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Don't have an account?
            <a href="{{ route('register') }}" wire:navigate.hover
                class="font-medium text-polysphere-600 hover:text-polysphere-800 transition-colors">
                Get started
            </a>
        </p>
    </div> --}}

    {{-- Security badges --}}
    <div class="mt-8 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <i class="fas fa-shield-alt text-green-500 text-xl"></i>
                <p class="text-xs text-gray-500 mt-1">SSL Secure</p>
            </div>
            <div>
                <i class="fas fa-lock text-blue-500 text-xl"></i>
                <p class="text-xs text-gray-500 mt-1">Encrypted</p>
            </div>
            <div>
                <i class="fas fa-headset text-purple-500 text-xl"></i>
                <p class="text-xs text-gray-500 mt-1">24/7 Support</p>
            </div>
        </div>
    </div>
</div>