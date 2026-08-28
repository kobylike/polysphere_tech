<div class="animate-fade-in">
    <!-- Back Button -->
    <div class="mb-8">
        <a href="{{ url('/') }}"
            class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to home
        </a>
    </div>

    <!-- Header -->
    <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-gray-900">Create Your Account</h2>
        <p class="mt-2 text-gray-600">You've been invited to join Polysphere Tech</p>
    </div>

    <!-- Social Sign Up (disabled for invitation-only) -->
    <div class="mb-8">
        <div class="grid grid-cols-2 gap-3">
            <button type="button" disabled
                class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed opacity-60">
                <i class="fab fa-google text-red-500 mr-2"></i>
                <span class="text-sm font-medium">Google</span>
            </button>
            <button type="button" disabled
                class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed opacity-60">
                <i class="fab fa-microsoft text-blue-500 mr-2"></i>
                <span class="text-sm font-medium">Microsoft</span>
            </button>
        </div>

        <div class="flex items-center my-6">
            <div class="flex-1 border-t border-gray-200"></div>
            <span class="px-4 text-sm text-gray-500">Complete your profile</span>
            <div class="flex-1 border-t border-gray-200"></div>
        </div>
    </div>

    <!-- Registration Form -->
    <form wire:submit.prevent="register" class="space-y-5">

        <!-- Email (read-only, prefilled from invitation) -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email Address <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                </div>
                <input id="email" type="email" wire:model="email" readonly
                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
            </div>
            <p class="mt-1 text-sm text-gray-500">This email is locked – it comes from your invitation.</p>
        </div>

        <!-- Full Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Full Name <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <input id="name" type="text" wire:model.blur="name" autocomplete="name"
                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('name') border-red-300 @enderror"
                    placeholder="John Doe">
            </div>
            @error('name')
                <p class="mt-1 text-sm text-red-600 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Phone Number -->
        <div wire:key="phone-field" class="relative">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                Phone Number <span class="text-red-500">*</span>
            </label>

            <div class="flex">
                {{-- Country-code button --}}
                <button type="button" wire:click="toggleCountryDropdown" class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-3
                           bg-gray-50 border border-gray-300 border-r-0
                           rounded-l-lg hover:bg-gray-100 focus:outline-none
                           focus:ring-2 focus:ring-inset focus:ring-indigo-500
                           whitespace-nowrap">
                    <img src="{{ asset('flags/' . $selectedFlag) }}" class="w-5 h-4 rounded-sm object-cover" />
                    <span class="text-sm font-medium text-gray-700">{{ $countryCode }}</span>
                    <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200
                              {{ $showCountryDropdown ? 'rotate-180' : '' }}"></i>
                </button>

                {{-- Phone input --}}
                <div class="relative flex-1">
                    <input id="phone" type="tel" inputmode="numeric" autocomplete="tel-national" value="{{ $phone }}"
                        placeholder="{{ $phoneExample ? 'e.g. ' . $phoneExample : 'Phone number' }}"
                        maxlength="{{ $countryInfo['maxLength'] ?? 15 }}" x-data x-on:input="
                            let v = $el.value.replace(/[^0-9]/g, '');
                            let max = {{ $countryInfo['maxLength'] ?? 15 }};
                            if (v.length > max) v = v.substring(0, max);
                            if ($el.value !== v) {
                                let pos = $el.selectionStart - ($el.value.length - v.length);
                                $el.value = v;
                                $el.setSelectionRange(pos, pos);
                            }
                            $wire.setPhone(v);
                        " class="w-full px-4 py-3 border border-gray-300 rounded-r-lg
                               focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                               transition-all
                               @error('phone') border-red-300 @enderror">

                    @if($phone)
                        <button type="button" wire:click="$set('phone', '')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    @endif
                </div>
            </div>

            @if($phoneExample && !$phone)
                <p class="mt-1 text-xs text-gray-500">
                    {{ $countryInfo['name'] ?? '' }} numbers:
                    {{ $countryInfo['minLength'] ?? 5 }}–{{ $countryInfo['maxLength'] ?? 15 }} digits
                </p>
            @endif

            @if($phone)
                <p class="mt-1 text-xs text-indigo-600 font-medium">
                    Will be saved as: {{ $countryCode }}{{ $phone }}
                </p>
            @endif

            {{-- Country dropdown --}}
            @if($showCountryDropdown)
                <div class="absolute z-50 left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden"
                    x-data x-on:click.outside="$wire.closeCountryDropdown()">
                    <div class="p-2 border-b border-gray-100">
                        <input type="text" wire:model.live.debounce.200ms="search"
                            placeholder="Search country or dial code…" autofocus
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md
                                                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <ul class="max-h-60 overflow-y-auto divide-y divide-gray-50">
                        @forelse($filteredCountries as $country)
                            <li>
                                <button type="button"
                                    wire:click="selectCountry('{{ $country['code'] }}', '{{ $country['flag'] }}')"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm
                                                                                                           hover:bg-indigo-50 transition-colors
                                                                                                           {{ $countryCode === $country['code'] ? 'bg-indigo-50 font-medium' : '' }}">
                                    <img src="{{ asset('flags/' . $country['flag']) }}"
                                        class="w-5 h-4 rounded-sm object-cover flex-shrink-0" />
                                    <span class="flex-1 text-left text-gray-800">{{ $country['name'] }}</span>
                                    <span class="text-gray-500 tabular-nums">{{ $country['code'] }}</span>
                                </button>
                            </li>
                        @empty
                            <li class="px-4 py-4 text-sm text-gray-500 text-center">No countries found</li>
                        @endforelse
                    </ul>
                </div>
            @endif

            @error('phone')
                <p class="mt-1 text-sm text-red-600 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input id="password" type="{{ $show_password ? 'text' : 'password' }}" wire:model.live="password"
                    autocomplete="new-password"
                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password') border-red-300 @enderror"
                    placeholder="Create a strong password">
                <button type="button" wire:click="togglePasswordVisibility"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                    <i class="fas {{ $show_password ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                </button>
            </div>

            @if($password)
                <div class="mt-3 space-y-2">
                    <!-- Strength bar -->
                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-500"
                            style="width: {{ $passwordStrength['percentage'] }}%; background-color: {{ $passwordStrength['color'] }}">
                        </div>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Password strength:</span>
                        <span class="font-medium" style="color: {{ $passwordStrength['color'] }}">
                            {{ $passwordStrength['label'] }}
                        </span>
                    </div>

                    <!-- Requirements checklist -->
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        @foreach($passwordRequirements as $key => $req)
                            <div wire:key="pwreq-{{ $key }}" class="flex items-center">
                                <i
                                    class="fas {{ $req['met'] ? 'fa-check-circle text-green-500' : 'fa-circle text-gray-300' }} mr-2"></i>
                                <span class="{{ $req['met'] ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $req['label'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @error('password')
                <p class="mt-1 text-sm text-red-600 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Confirm Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input id="password_confirmation" type="{{ $show_password ? 'text' : 'password' }}"
                    wire:model.blur="password_confirmation" autocomplete="new-password"
                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all @error('password_confirmation') border-red-300 @enderror"
                    placeholder="Re-enter your password">
            </div>
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Terms -->
        <div>
            <div class="flex items-start">
                <input id="terms" wire:model="terms" type="checkbox"
                    class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="terms" class="ml-3 text-sm text-gray-700">
                    I agree to the
                    <a href="{{ route('terms')}}" class="text-indigo-600 hover:text-indigo-500">Terms of
                        Service</a>
                    and
                    <a href="{{ route('privacy')}}" class="text-indigo-600 hover:text-indigo-500">Privacy
                        Policy</a>
                    <span class="text-red-500">*</span>
                </label>
            </div>
            @error('terms')
                <p class="mt-1 text-sm text-red-600 flex items-center">
                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Submit -->
        <div class="sticky bottom-0 bg-white pt-4 pb-2 -mb-2">
            <button type="submit" wire:loading.attr="disabled" wire:target="register"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:-translate-y-0.5">
                <span wire:loading.remove wire:target="register">
                    <i class="fas fa-user-plus mr-2"></i> Create Account
                </span>
                <span wire:loading wire:target="register">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Creating…
                </span>
            </button>
        </div>
    </form>

    <!-- Footer -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Already have an account?
            <a wire:navigate.hover href="{{ route('login') }}"
                class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                Sign in
            </a>
        </p>
    </div>

    <!-- Security Badges -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div class="p-3">
                <i class="fas fa-shield-alt text-green-500 text-xl mb-2"></i>
                <p class="text-xs text-gray-600">SSL Secure</p>
            </div>
            <div class="p-3">
                <i class="fas fa-bolt text-blue-500 text-xl mb-2"></i>
                <p class="text-xs text-gray-600">Instant Top-Up</p>
            </div>
            <div class="p-3">
                <i class="fas fa-headset text-indigo-500 text-xl mb-2"></i>
                <p class="text-xs text-gray-600">24/7 Support</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', function () {
            Livewire.on('show-toast', function (data) {
                var type = data.type || 'info';
                var message = data.message || '';
                var duration = data.duration || 3000;
                var colors = { success: 'bg-green-500', error: 'bg-red-500', warning: 'bg-yellow-500', info: 'bg-blue-500' };
                var icons = { success: 'fas fa-check-circle', error: 'fas fa-exclamation-circle', warning: 'fas fa-exclamation-triangle', info: 'fas fa-info-circle' };

                var toast = document.createElement('div');
                toast.className = 'fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ' + (colors[type] || 'bg-gray-800');
                toast.innerHTML = '<div class="flex items-center"><i class="' + (icons[type] || 'fas fa-info-circle') + ' mr-2"></i><span>' + message + '</span><button class="ml-4" onclick="this.parentElement.parentElement.remove()"><i class="fas fa-times"></i></button></div>';
                document.body.appendChild(toast);
                setTimeout(() => toast.classList.replace('translate-x-full', 'translate-x-0'), 10);
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.classList.add('opacity-0');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, duration);
            });
        });
    </script>
@endpush