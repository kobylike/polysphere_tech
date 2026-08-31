<div>
    <div class="animate-fade-in">
        <!-- Back Button -->
        <div class="mb-8">
            <a href="{{ route('login') }}" wire:navigate
                class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to login
            </a>
        </div>

        <!-- Header -->
        <div class="text-center mb-10">
            <div
                class="mx-auto w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-key text-2xl text-indigo-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Reset Your Password</h2>
            <p class="mt-2 text-gray-600">
                @if($resetSent)
                    Check your email for reset instructions
                @else
                    Enter your email to receive a password reset link
                @endif
            </p>
        </div>

        @if(!$resetSent)
            <!-- Reset Form -->
            <form wire:submit.prevent="sendResetLink" class="space-y-6" novalidate>
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" type="email" wire:model.live="email" autocomplete="email"
                            class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('email') border-red-300 @enderror"
                            placeholder="you@school.edu" :disabled="loading" autofocus>
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Security Notice -->
                <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
                    <div class="flex">
                        <i class="fas fa-shield-check text-blue-500 mt-1 mr-3"></i>
                        <div>
                            <h4 class="text-sm font-medium text-blue-800">Security Notice</h4>
                            <p class="text-xs text-blue-600 mt-1">
                                For your security, we limit reset requests to 3 per hour per email.
                                Reset links expire after 60 minutes.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" wire:loading.attr="disabled" wire:target="sendResetLink"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:-translate-y-0.5"
                        :disabled="loading">
                        <span wire:loading.remove wire:target="sendResetLink">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Reset Link
                        </span>
                        <span wire:loading wire:target="sendResetLink">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Sending...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Alternative Actions -->
            <div class="mt-6 space-y-3">
                <a href="{{ route('login') }}" wire:navigate
                    class="block w-full text-center py-3 px-4 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Return to Sign In
                </a>


                < </div>
        @else
                    <!-- Success State -->
                    <div class="text-center space-y-8">
                        <!-- Success Animation/Icon -->
                        <div class="relative">
                            <div
                                class="mx-auto w-24 h-24 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-4xl text-green-500"></i>
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div
                                    class="w-24 h-24 border-4 border-green-200 border-t-green-500 rounded-full animate-spin">
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="space-y-4">
                            <div class="p-4 bg-green-50 border border-green-100 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-envelope-open-text text-green-500 mt-1 mr-3"></i>
                                    <div class="text-left">
                                        <h4 class="text-sm font-medium text-green-800">Check Your Inbox</h4>
                                        <p class="text-sm text-green-700 mt-1">
                                            We've sent a password reset link to <strong
                                                class="font-semibold">{{ $email }}</strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-clock text-blue-500 mt-1 mr-3"></i>
                                    <div class="text-left">
                                        <h4 class="text-sm font-medium text-blue-800">Important Notes</h4>
                                        <ul class="text-xs text-blue-700 mt-2 space-y-1 list-disc list-inside">
                                            <li>The reset link will expire in 60 minutes</li>
                                            <li>Check your spam folder if you don't see the email</li>
                                            <li>For security, the link can only be used once</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <button type="button" wire:click="resetForm"
                                class="w-full py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                                <i class="fas fa-redo mr-2"></i>
                                Request Another Reset Link
                            </button>

                            <button type="button" wire:click="redirectToLogin" wire:loading.attr="disabled"
                                class="w-full py-3 px-4 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Return to Sign In
                            </button>
                        </div>

                        <!-- Need Help -->
                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                Didn't receive the email?
                                <button type="button" wire:click="resetForm"
                                    class="font-medium text-indigo-600 hover:text-indigo-500">
                                    Try again
                                </button>
                                or
                                <a href="/contact" class="font-medium text-indigo-600 hover:text-indigo-500">
                                    contact support
                                </a>
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Security Badges -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="p-3">
                            <i class="fas fa-lock text-green-500 text-xl mb-2"></i>
                            <p class="text-xs text-gray-600">Secure Reset</p>
                        </div>
                        <div class="p-3">
                            <i class="fas fa-clock text-blue-500 text-xl mb-2"></i>
                            <p class="text-xs text-gray-600">60-min Expiry</p>
                        </div>
                        <div class="p-3">
                            <i class="fas fa-shield-alt text-purple-500 text-xl mb-2"></i>
                            <p class="text-xs text-gray-600">Encrypted Link</p>
                        </div>
                    </div>
                </div>
        </div>

        @push('scripts')
            <script>
                // Toast notification handler (same as login)
                Livewire.on('show-toast', ({ type, message, duration = 3000 }) => {
                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ${getToastClass(type)}`;
                    toast.innerHTML = `
                                                            <div class="flex items-center">
                                                                <i class="${getToastIcon(type)} mr-2"></i>
                                                                <span>${message}</span>
                                                                <button class="ml-4" onclick="this.parentElement.parentElement.remove()">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        `;

                    document.body.appendChild(toast);

                    // Animate in
                    setTimeout(() => {
                        toast.classList.remove('translate-x-full');
                        toast.classList.add('translate-x-0');
                    }, 10);

                    // Auto remove
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.classList.add('opacity-0');
                            setTimeout(() => toast.remove(), 300);
                        }
                    }, duration);
                });

                function getToastClass(type) {
                    const classes = {
                        success: 'bg-green-500',
                        error: 'bg-red-500',
                        warning: 'bg-yellow-500',
                        info: 'bg-blue-500'
                    };
                    return classes[type] || 'bg-gray-800';
                }

                function getToastIcon(type) {
                    const icons = {
                        success: 'fas fa-check-circle',
                        error: 'fas fa-exclamation-circle',
                        warning: 'fas fa-exclamation-triangle',
                        info: 'fas fa-info-circle'
                    };
                    return icons[type] || 'fas fa-info-circle';
                }

                // Auto-focus email field when resetting form
                document.addEventListener('livewire:init', () => {
                    Livewire.on('resetFormFocused', () => {
                        setTimeout(() => {
                            const emailInput = document.querySelector('#email');
                            if (emailInput) emailInput.focus();
                        }, 100);
                    });
                });

                // Keyboard shortcuts
                document.addEventListener('keydown', (e) => {
                    // Submit form with Ctrl+Enter
                    if (e.ctrlKey && e.key === 'Enter') {
                        const submitBtn = document.querySelector('[wire\\:submit]');
                        if (submitBtn) submitBtn.click();
                    }

                    // Focus email field with Alt+E
                    if (e.altKey && e.key === 'e') {
                        const emailInput = document.querySelector('#email');
                        if (emailInput) emailInput.focus();
                        e.preventDefault();
                    }

                    // Reset form with Alt+R
                    if (e.altKey && e.key === 'r' && !Livewire.find('resetSent')) {
                        Livewire.dispatch('resetForm');
                        e.preventDefault();
                    }

                    // Go back to login with Escape
                    if (e.key === 'Escape') {
                        window.location.href = '{{ route("login") }}';
                    }
                });
            </script>
        @endpush
    </div>