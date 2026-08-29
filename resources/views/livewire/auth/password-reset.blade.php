<div>
    <div class="animate-fade-in">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('login') }}" wire:navigate
                class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to login
            </a>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div
                class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-key text-2xl text-indigo-600"></i>
            </div>

            @if($token && $isValidToken)
                <h2 class="text-2xl font-bold text-gray-900">Create New Password</h2>
                <p class="mt-2 text-gray-600">Choose a strong, secure password for your account</p>

                @if($expiresIn && $expiresIn > 0)
                    <div class="mt-3 inline-flex items-center px-3 py-1 bg-amber-50 border border-amber-200 rounded-full">
                        <i class="fas fa-clock text-amber-500 text-sm mr-2"></i>
                        <span class="text-sm text-amber-700">Link expires in {{ $expiresIn }} minutes</span>
                    </div>
                @endif
            @else
                <h2 class="text-2xl font-bold text-gray-900">Reset Your Password</h2>
                <p class="mt-2 text-gray-600">Enter your email to receive a reset link</p>
            @endif
        </div>

        @if($token && !$isValidToken)
            <!-- Invalid Token State -->
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-red-800 mb-2">Invalid or Expired Link</h3>
                <p class="text-red-700 mb-4">This password reset link is no longer valid. It may have expired or already
                    been used.</p>
                <a href="{{ route('password.request') }}" wire:navigate
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Request New Reset Link
                </a>
            </div>
        @elseif(session('reset_link_sent'))
            <!-- Reset Link Sent State -->
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                <div class="w-12 h-12 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-envelope-open-text text-xl text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-green-800 mb-2">Check Your Email</h3>
                <p class="text-green-700 mb-4">
                    We've sent password reset instructions to <span
                        class="font-semibold">{{ session('email') ?? 'your email' }}</span>.
                </p>
                <div class="space-y-3 text-sm text-gray-600">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-clock mr-2"></i>
                        <span>Reset link expires in 60 minutes</span>
                    </div>
                    <div class="flex items-center justify-center">
                        <i class="fas fa-shield-alt mr-2"></i>
                        <span>For security, use the link immediately</span>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="{{ route('login') }}" wire:navigate class="text-indigo-600 hover:text-indigo-700 font-medium">
                        Return to login
                    </a>
                </div>
            </div>
        @elseif($token && $isValidToken)
            <!-- Reset Password Form -->
            <form wire:submit.prevent="resetPassword" class="space-y-6">
                <!-- Email (readonly) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" value="{{ $email }}" readonly
                            class="pl-10 w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-gray-600">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">This email is verified and associated with your account</p>
                </div>

                <!-- New Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            New Password
                        </label>
                        <button type="button" wire:click="togglePasswordVisibility('password')"
                            class="text-sm text-indigo-600 hover:text-indigo-500 transition-colors" tabindex="-1">
                            {{ $showPassword ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" type="{{ $showPassword ? 'text' : 'password' }}" wire:model.live="password"
                            class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password') border-red-300 @enderror"
                            placeholder="Enter new password" autocomplete="new-password" autofocus>
                        <button type="button" wire:click="togglePasswordVisibility('password')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center" tabindex="-1">
                            <i class="fas fa-eye{{ $showPassword ? '-slash' : '' }} text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>

                    <!-- Password Strength Meter -->
                    @if($password)
                        <div class="mt-2">
                            <div class="flex items-center mb-1">
                                <div class="h-1 flex-1 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full {{ $this->getPasswordStrengthClass() }} transition-all duration-500"
                                        style="width: {{ $this->getPasswordStrength() }}%"></div>
                                </div>
                                <span class="ml-2 text-xs font-medium {{ $this->getPasswordStrengthColor() }}">
                                    {{ $this->getPasswordStrengthLabel() }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Password Requirements -->
                    <div class="mt-3 space-y-1">
                        <p class="text-xs font-medium text-gray-700">Password must contain:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li class="flex items-center">
                                <i
                                    class="fas fa-{{ $this->hasMinLength() ? 'check-circle text-green-500' : 'times-circle text-gray-300' }} mr-2"></i>
                                At least 8 characters
                            </li>
                            <li class="flex items-center">
                                <i
                                    class="fas fa-{{ $this->hasUppercase() ? 'check-circle text-green-500' : 'times-circle text-gray-300' }} mr-2"></i>
                                One uppercase letter
                            </li>
                            <li class="flex items-center">
                                <i
                                    class="fas fa-{{ $this->hasLowercase() ? 'check-circle text-green-500' : 'times-circle text-gray-300' }} mr-2"></i>
                                One lowercase letter
                            </li>
                            <li class="flex items-center">
                                <i
                                    class="fas fa-{{ $this->hasNumber() ? 'check-circle text-green-500' : 'times-circle text-gray-300' }} mr-2"></i>
                                One number
                            </li>
                            <li class="flex items-center">
                                <i
                                    class="fas fa-{{ $this->hasSpecialChar() ? 'check-circle text-green-500' : 'times-circle text-gray-300' }} mr-2"></i>
                                One special character
                            </li>
                        </ul>
                    </div>

                    @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm New Password
                        </label>
                        <button type="button" wire:click="togglePasswordVisibility('confirmation')"
                            class="text-sm text-indigo-600 hover:text-indigo-500 transition-colors" tabindex="-1">
                            {{ $showConfirmation ? 'Hide' : 'Show' }}
                        </button>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password_confirmation" type="{{ $showConfirmation ? 'text' : 'password' }}"
                            wire:model.live="password_confirmation"
                            class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password_confirmation') border-red-300 @enderror"
                            placeholder="Confirm new password" autocomplete="new-password">
                        <button type="button" wire:click="togglePasswordVisibility('confirmation')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center" tabindex="-1">
                            <i
                                class="fas fa-eye{{ $showConfirmation ? '-slash' : '' }} text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" wire:loading.attr="disabled" wire:target="resetPassword"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:-translate-y-0.5"
                        :disabled="loading">
                        <span wire:loading.remove wire:target="resetPassword">
                            <i class="fas fa-key mr-2"></i>
                            Reset Password
                        </span>
                        <span wire:loading wire:target="resetPassword">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Updating Password…
                        </span>
                    </button>
                </div>
            </form>
        @else
            <!-- Request Reset Link Form -->
            <form wire:submit.prevent="sendResetLink" class="space-y-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input id="email" type="email" wire:model="email" autocomplete="email"
                            class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('email') border-red-300 @enderror"
                            placeholder="Enter your account email" autofocus>
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
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
                            Sending Link…
                        </span>
                    </button>
                </div>
            </form>

            <!-- Help Text -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">What happens next?</p>
                        <p>We'll email you a secure link to reset your password. For security, the link expires in 60
                            minutes and can only be used once.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Security Information -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="p-3">
                    <i class="fas fa-shield-alt text-green-500 text-xl mb-2"></i>
                    <p class="text-xs text-gray-600">End-to-End Encrypted</p>
                </div>
                <div class="p-3">
                    <i class="fas fa-user-secret text-blue-500 text-xl mb-2"></i>
                    <p class="text-xs text-gray-600">No Data Retention</p>
                </div>
                <div class="p-3">
                    <i class="fas fa-clock text-purple-500 text-xl mb-2"></i>
                    <p class="text-xs text-gray-600">60-Minute Expiry</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Password strength helper functions
            Livewire.on('password-strength-updated', (strength) => {
                // This can be used for additional client-side effects
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Submit form with Ctrl+Enter
                if (e.ctrlKey && e.key === 'Enter') {
                    const submitBtn = document.querySelector('[wire\\:submit]');
                    if (submitBtn) submitBtn.click();
                }

                // Toggle password visibility with Alt+P
                if (e.altKey && e.key === 'p') {
                    Livewire.dispatch('togglePasswordVisibility', { field: 'password' });
                    e.preventDefault();
                }

                // Toggle confirmation visibility with Alt+C
                if (e.altKey && e.key === 'c') {
                    Livewire.dispatch('togglePasswordVisibility', { field: 'confirmation' });
                    e.preventDefault();
                }

                // Focus email field with Alt+E
                if (e.altKey && e.key === 'e' && !@json($token && $isValidToken)) {
                    const emailField = document.querySelector('[name="email"]');
                    if (emailField) emailField.focus();
                    e.preventDefault();
                }
            });

            // Auto-focus password field on token page
            @if($token && $isValidToken)
                document.addEventListener('livewire:load', function () {
                    const passwordField = document.querySelector('[name="password"]');
                    if (passwordField) {
                        passwordField.focus();
                    }
                });
            @endif
        </script>
    @endpush
</div>