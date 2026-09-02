<div>
    <div class="animate-fade-in">
        <!-- Header -->
        <div class="text-center mb-8">
            <div
                class="w-16 h-16 mx-auto mb-4 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shield-halved text-2xl text-indigo-600"></i>
            </div>

            <h2 class="text-2xl font-bold text-gray-900">Set a New Password</h2>
            <p class="mt-2 text-gray-600">
                Your account was created by an administrator. For security, you must
                set a personal password before continuing to your dashboard.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 text-center">
                <p class="text-sm text-green-700">{{ session('status') }}</p>
            </div>
        @endif

        <form wire:submit.prevent="updatePassword" class="space-y-6">
            <!-- Temporary Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="current_password" class="block text-sm font-medium text-gray-700">
                        Temporary Password
                    </label>
                    <button type="button" wire:click="togglePasswordVisibility('current')"
                        class="text-sm text-indigo-600 hover:text-indigo-500 transition-colors" tabindex="-1">
                        {{ $showCurrentPassword ? 'Hide' : 'Show' }}
                    </button>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input id="current_password" type="{{ $showCurrentPassword ? 'text' : 'password' }}"
                        wire:model="current_password"
                        class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('current_password') border-red-300 @enderror"
                        placeholder="Enter the password you were given" autocomplete="current-password" autofocus>
                    <button type="button" wire:click="togglePasswordVisibility('current')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center" tabindex="-1">
                        <i
                            class="fas fa-eye{{ $showCurrentPassword ? '-slash' : '' }} text-gray-400 hover:text-gray-600"></i>
                    </button>
                </div>
                @error('current_password')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
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
                    <input id="password" type="{{ $showPassword ? 'text' : 'password' }}"
                        wire:model.live.debounce.200ms="password"
                        class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password') border-red-300 @enderror"
                        placeholder="Choose a strong password" autocomplete="new-password">
                    <button type="button" wire:click="togglePasswordVisibility('password')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center" tabindex="-1">
                        <i class="fas fa-eye{{ $showPassword ? '-slash' : '' }} text-gray-400 hover:text-gray-600"></i>
                    </button>
                </div>

                <!-- Password Strength Meter -->
                @if ($password)
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

            <!-- Confirm New Password -->
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
                        wire:model="password_confirmation"
                        class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password_confirmation') border-red-300 @enderror"
                        placeholder="Re-enter your new password" autocomplete="new-password">
                    <button type="button" wire:click="togglePasswordVisibility('confirmation')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center" tabindex="-1">
                        <i
                            class="fas fa-eye{{ $showConfirmation ? '-slash' : '' }} text-gray-400 hover:text-gray-600"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:-translate-y-0.5">
                    <span wire:loading.remove wire:target="updatePassword">
                        <i class="fas fa-key mr-2"></i>
                        Set Password &amp; Continue
                    </span>
                    <span wire:loading wire:target="updatePassword">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Updating Password…
                    </span>
                </button>
            </div>
        </form>

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
                    <i class="fas fa-user-shield text-purple-500 text-xl mb-2"></i>
                    <p class="text-xs text-gray-600">One-Time Setup</p>
                </div>
            </div>
        </div>
    </div>
</div>