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
        <h2 class="text-3xl font-bold text-gray-900">Create Your Account</h2>
        <p class="mt-2 text-gray-500">You've been invited to join Polysphere Tech</p>
    </div>

    {{-- Form --}}
    <form wire:submit.prevent="register" class="space-y-5" novalidate>

        {{-- Email (read-only) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span
                    class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <input type="email" wire:model="email" readonly
                    class="form-input w-full pl-10 pr-3 py-3 bg-gray-50 border border-gray-200 rounded-lg cursor-not-allowed text-gray-500">
            </div>
            <p class="mt-1 text-xs text-gray-400">This email is locked – it comes from your invitation.</p>
        </div>

        {{-- First Name & Last Name --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name <span
                        class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input id="first_name" type="text" wire:model.blur="first_name" autocomplete="given-name"
                        class="form-input w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-polysphere-500 focus:border-polysphere-500 transition-colors @error('first_name') border-red-300 @enderror"
                        placeholder="John">
                </div>
                @error('first_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name <span
                        class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input id="last_name" type="text" wire:model.blur="last_name" autocomplete="family-name"
                        class="form-input w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-polysphere-500 focus:border-polysphere-500 transition-colors @error('last_name') border-red-300 @enderror"
                        placeholder="Doe">
                </div>
                @error('last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Phone Number --}}
        <div wire:key="phone-field">
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span
                    class="text-red-500">*</span></label>

            {{-- Positioned wrapper: this (not the whole field block) is what
            the country dropdown anchors to, so it always renders directly
            under the input row regardless of what's above/below it. --}}
            <div class="relative">
                <div class="flex">
                    <button type="button" wire:click="toggleCountryDropdown"
                        class="flex-shrink-0 inline-flex items-center gap-1 px-3 py-3 bg-gray-50 border border-gray-300 border-r-0 rounded-l-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-polysphere-500">
                        <img src="{{ asset('flags/' . $selectedFlag) }}" class="w-5 h-4 rounded-sm object-cover">
                        <span class="text-sm font-medium text-gray-700">{{ $countryCode }}</span>
                        <i
                            class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200 {{ $showCountryDropdown ? 'rotate-180' : '' }}"></i>
                    </button>
                    <div class="relative flex-1">
                        <input type="tel" inputmode="numeric" wire:model.defer="phone"
                            placeholder="{{ $phoneExample ? 'e.g. ' . $phoneExample : 'Phone number' }}"
                            maxlength="{{ $countryInfo['maxLength'] ?? 15 }}"
                            class="form-input w-full px-3 py-3 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-polysphere-500 focus:border-polysphere-500 transition-colors @error('phone') border-red-300 @enderror"
                            x-data x-on:input="
                                let v = $el.value.replace(/[^0-9]/g, '');
                                let max = {{ $countryInfo['maxLength'] ?? 15 }};
                                if (v.length > max) v = v.substring(0, max);
                                $el.value = v;
                                $wire.setPhone(v);
                            ">
                    </div>
                </div>

                {{-- Country dropdown: anchored to the .relative wrapper above
                via top-full, so it always appears right below the
                button/input row instead of falling to its static
                position further down the page. --}}
                @if($showCountryDropdown)
                    <div class="absolute z-50 top-full left-0 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto"
                        x-data x-on:click.outside="$wire.closeCountryDropdown()">
                        <div class="sticky top-0 bg-white p-2 border-b border-gray-100">
                            <input type="text" wire:model.live.debounce.200ms="search" placeholder="Search country…"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-polysphere-500 focus:border-polysphere-500">
                        </div>
                        @forelse($filteredCountries as $country)
                            <button type="button" wire:click="selectCountry('{{ $country['code'] }}', '{{ $country['flag'] }}')"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-polysphere-50 transition-colors {{ $countryCode === $country['code'] ? 'bg-polysphere-50 font-medium' : '' }}">
                                <img src="{{ asset('flags/' . $country['flag']) }}" class="w-5 h-4 rounded-sm object-cover">
                                <span class="flex-1 text-left">{{ $country['name'] }}</span>
                                <span class="text-gray-500 tabular-nums">{{ $country['code'] }}</span>
                            </button>
                        @empty
                            <div class="px-4 py-2 text-sm text-gray-500">No countries found</div>
                        @endforelse
                    </div>
                @endif
            </div>

            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            @if($phone)
                <p class="mt-1 text-xs text-polysphere-600">Will be saved as: {{ $countryCode }}{{ $phone }}</p>
            @endif
        </div>

        {{-- Password --}}
        <div x-data="{ showPassword: false }">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span
                    class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input id="password" :type="showPassword ? 'text' : 'password'" wire:model.live="password"
                    autocomplete="new-password"
                    class="form-input w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-polysphere-500 focus:border-polysphere-500 transition-colors @error('password') border-red-300 @enderror"
                    placeholder="Create a strong password">
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                    tabindex="-1">
                    <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            {{-- Strength indicator --}}
            @if($password)
                <div class="mt-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Strength:</span>
                        <span style="color: {{ $passwordStrength['color'] }}">{{ $passwordStrength['label'] }}</span>
                    </div>
                    <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden mt-1">
                        <div class="h-full transition-all duration-300"
                            style="width: {{ $passwordStrength['percentage'] }}%; background-color: {{ $passwordStrength['color'] }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-1 mt-1 text-xs">
                        @foreach($passwordRequirements as $key => $req)
                            <span class="{{ $req['met'] ? 'text-green-600' : 'text-gray-400' }}">
                                <i class="fas {{ $req['met'] ? 'fa-check-circle' : 'fa-circle' }} mr-1"></i>
                                {{ $req['label'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password
                <span class="text-red-500">*</span></label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input id="password_confirmation" type="{{ $show_password ? 'text' : 'password' }}"
                    wire:model.blur="password_confirmation" autocomplete="new-password"
                    class="form-input w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-polysphere-500 focus:border-polysphere-500 transition-colors @error('password_confirmation') border-red-300 @enderror"
                    placeholder="Re-enter your password">
            </div>
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Terms --}}
        <div class="flex items-start">
            <input id="terms" type="checkbox" wire:model="terms"
                class="mt-1 h-4 w-4 text-polysphere-600 focus:ring-polysphere-500 border-gray-300 rounded">
            <label for="terms" class="ml-2 text-sm text-gray-700">
                I agree to the <a href="#" class="text-polysphere-600 hover:underline">Terms of Service</a> and
                <a href="#" class="text-polysphere-600 hover:underline">Privacy Policy</a>
                <span class="text-red-500">*</span>
            </label>
        </div>
        @error('terms')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        {{-- Submit --}}
        <button type="submit" wire:loading.attr="disabled" wire:target="register"
            class="btn-lift w-full flex justify-center py-3 px-4 bg-gradient-to-r from-polysphere-600 to-polysphere-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-polysphere-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed transition-all">
            <span wire:loading.remove wire:target="register">
                <i class="fas fa-user-plus mr-2"></i> Create Account
            </span>
            <span wire:loading wire:target="register">
                <i class="fas fa-spinner fa-spin mr-2"></i> Creating…
            </span>
        </button>
    </form>

    {{-- Footer --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" wire:navigate.hover
                class="font-medium text-polysphere-600 hover:text-polysphere-800 transition-colors">
                Sign in
            </a>
        </p>
    </div>

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