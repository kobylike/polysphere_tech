<div class="w-full max-w-md mx-auto">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Set a New Password</h1>
        <p class="mt-2 text-sm text-gray-600">
            Your account was created by an administrator. For security, you must
            set a personal password before continuing to your dashboard.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="updatePassword" class="space-y-5">
        {{-- Temporary / current password --}}
        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                Temporary Password
            </label>
            <div class="relative">
                <input
                    wire:model="current_password"
                    id="current_password"
                    type="{{ $showCurrentPassword ? 'text' : 'password' }}"
                    autocomplete="current-password"
                    class="w-full rounded-lg border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 @error('current_password') border-red-400 @enderror"
                    placeholder="Enter the password you were given"
                >
                <button
                    type="button"
                    wire:click="togglePasswordVisibility('current')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    tabindex="-1"
                >
                    @if ($showCurrentPassword)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" /><path d="M2.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303l-1.628-1.628a4.002 4.002 0 01-5.336-5.336l-2.033-2.033a9.99 9.99 0 00-2.999 4.3z" /></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                    @endif
                </button>
            </div>
            @error('current_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- New password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                New Password
            </label>
            <div class="relative">
                <input
                    wire:model.live.debounce.200ms="password"
                    id="password"
                    type="{{ $showPassword ? 'text' : 'password' }}"
                    autocomplete="new-password"
                    class="w-full rounded-lg border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 @error('password') border-red-400 @enderror"
                    placeholder="Choose a strong password"
                >
                <button
                    type="button"
                    wire:click="togglePasswordVisibility('password')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    tabindex="-1"
                >
                    @if ($showPassword)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" /><path d="M2.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303l-1.628-1.628a4.002 4.002 0 01-5.336-5.336l-2.033-2.033a9.99 9.99 0 00-2.999 4.3z" /></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                    @endif
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            {{-- Strength meter --}}
            @if ($password)
                <div class="mt-2">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-500">Password strength</span>
                        <span class="text-xs font-medium {{ $this->getPasswordStrengthColor() }}">
                            {{ $this->getPasswordStrengthLabel() }}
                        </span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-gray-200 overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-300 {{ $this->getPasswordStrengthClass() }}"
                            style="width: {{ $this->getPasswordStrength() }}%"
                        ></div>
                    </div>
                </div>

                {{-- Requirements checklist --}}
                <ul class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1 text-xs">
                    @foreach ([
                        'hasMinLength'    => 'At least 8 characters',
                        'hasUppercase'    => 'One uppercase letter',
                        'hasLowercase'    => 'One lowercase letter',
                        'hasNumber'       => 'One number',
                        'hasSpecialChar'  => 'One special character',
                    ] as $method => $label)
                        <li class="flex items-center gap-1.5 {{ $this->{$method}() ? 'text-green-600' : 'text-gray-400' }}">
                            @if ($this->{$method}())
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9a1 1 0 112 0v4a1 1 0 11-2 0V9zm1-4a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" /></svg>
                            @endif
                            {{ $label }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Confirm password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Confirm New Password
            </label>
            <div class="relative">
                <input
                    wire:model="password_confirmation"
                    id="password_confirmation"
                    type="{{ $showConfirmation ? 'text' : 'password' }}"
                    autocomplete="new-password"
                    class="w-full rounded-lg border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 @error('password') border-red-400 @enderror"
                    placeholder="Re-enter your new password"
                >
                <button
                    type="button"
                    wire:click="togglePasswordVisibility('confirmation')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    tabindex="-1"
                >
                    @if ($showConfirmation)
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" /><path d="M2.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303l-1.628-1.628a4.002 4.002 0 01-5.336-5.336l-2.033-2.033a9.99 9.99 0 00-2.999 4.3z" /></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                    @endif
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="updatePassword"
            class="w-full flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed transition"
        >
            <svg wire:loading wire:target="updatePassword" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span wire:loading.remove wire:target="updatePassword">Set Password &amp; Continue</span>
            <span wire:loading wire:target="updatePassword">Updating…</span>
        </button>
    </form>
</div>