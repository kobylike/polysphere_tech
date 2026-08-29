<div>
    <div class="animate-fade-in">
        <!-- Back Button -->
        <div class="mb-6">
            <button type="button" wire:click="logout" wire:loading.attr="disabled"
                class="inline-flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to login
            </button>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-block p-3 bg-indigo-100 rounded-full mb-4">
                <i class="fas fa-shield-alt text-indigo-600 text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Two-Factor Verification</h2>
            <p class="mt-2 text-gray-600">Enter the code from your authenticator app</p>
        </div>

        <!-- Security Alert -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Security Notice:</strong> This login attempt is from
                        <strong>{{ $deviceName }}</strong> (IP: {{ $ipAddress }}).
                    </p>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-indigo-600"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-900">{{ $userName }}</p>
                    <p class="text-sm text-gray-600">{{ $userEmail }}</p>
                </div>
            </div>
        </div>

        <!-- Mode Tabs -->
        <div class="flex border-b border-gray-200 mb-6">
            <button type="button" wire:click="switchMode('totp')" class="flex-1 py-3 px-4 text-center font-medium text-sm border-b-2 transition-all
                   @if($mode === 'totp') border-indigo-500 text-indigo-600
                   @else border-transparent text-gray-500 hover:text-gray-700 @endif">
                <i class="fas fa-mobile-alt mr-2"></i>
                Authenticator App
            </button>
            <button type="button" wire:click="switchMode('recovery')" class="flex-1 py-3 px-4 text-center font-medium text-sm border-b-2 transition-all
                   @if($mode === 'recovery') border-indigo-500 text-indigo-600
                   @else border-transparent text-gray-500 hover:text-gray-700 @endif">
                <i class="fas fa-key mr-2"></i>
                Recovery Code
            </button>
        </div>

        <!-- TOTP Code Entry -->
        @if($mode === 'totp')
            <!-- Timer -->
            <div class="mb-6 text-center" wire:poll.1000ms="decrementTimer">
                <div class="inline-flex items-center space-x-2">
                    <div class="relative">
                        <div
                            class="w-12 h-12 rounded-full border-2 @if($timeLeft < 10) border-red-500 @else border-indigo-500 @endif
                                                                                                                                        flex items-center justify-center">
                            <span id="timer"
                                class="text-xl font-bold @if($timeLeft < 10) text-red-500 @else text-gray-900 @endif">
                                {{ str_pad($timeLeft, 2, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        @if($timerActive && $timeLeft > 0 && $timeLeft <= 10)
                            <div class="absolute inset-0 rounded-full border-2 border-red-500 opacity-50 animate-ping"></div>
                        @endif
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-medium text-gray-900">Code expires in</p>
                        <p class="text-xs text-gray-600">Seconds</p>
                    </div>
                </div>

                @if(!$timerActive || $timeLeft === 0)
                    <button type="button" wire:click="resendCode" wire:loading.attr="disabled"
                        class="mt-3 text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                        <i class="fas fa-redo mr-1"></i> Get new code
                    </button>
                @endif
            </div>

            <!-- Code Inputs -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3 text-center">
                    Enter the 6-digit code from your authenticator app
                </label>

                <div class="flex justify-center space-x-2 mb-4" id="code-container">
                    @for($i = 1; $i <= 6; $i++)
                        @php $codeField = 'code' . $i; @endphp
                        <input type="text" wire:model.live="{{ $codeField }}" maxlength="1" id="code-{{ $i }}"
                            data-index="{{ $i }}"
                            class="code-input w-14 h-14 text-center text-2xl font-bold border-2 rounded-lg
                                                         @if($errorMessage) border-red-300 bg-red-50
                                                          @else border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200
                                                           @endif

                                                                                                                                                                                                              transition-all duration-200"
                            x-data
                            x-on:input="if(/^\d$/.test($event.target.value) && {{ $i }} < 6) {
                                                                                                                                                                                                           setTimeout(() => document.getElementById('code-{{ $i + 1 }}')?.focus(), 50);
                                                                                                                                                                                                       }"
                            x-on:keydown="if($event.key === 'Backspace' && $event.target.value === '' && {{ $i }} > 1) {
                                                                                                                                                                                                           setTimeout(() => document.getElementById('code-{{ $i - 1 }}')?.focus(), 50);
                                                                                                                                                                                                       }"
                            x-on:keydown.left="if({{ $i }} > 1) document.getElementById('code-{{ $i - 1 }}')?.focus()"
                            x-on:keydown.right="if({{ $i }} < 6) document.getElementById('code-{{ $i + 1 }}')?.focus()"
                            x-on:paste="$event.preventDefault();
                                                                                                                                                                                                                   const pasteData = $event.clipboardData.getData('text');
                                                                                                                                                                                                                   const digits = pasteData.replace(/\D/g, '').split('').slice(0, 6);
                                                                                                                                                                                                                   digits.forEach((digit, index) => {
                                                                                                                                                                                                                       const input = document.getElementById('code-' + (index + 1));
                                                                                                                                                                                                                       if(input) {
                                                                                                                                                                                                                           input.value = digit;
                                                                                                                                                                                                                           $wire.set('code' + (index + 1), digit);
                                                                                                                                                                                                                       }
                                                                                                                                                                                                                   });
                                                                                                                                                                                                                   if(digits.length === 6) {
                                                                                                                                                                                                                       setTimeout(() => $wire.verifyTotp(), 300);
                                                                                                                                                                                                                   }"
                            autocomplete="off">
                    @endfor
                </div>

                <!-- Auto-Submit Helper -->
                <p class="text-xs text-gray-500 text-center mb-4">
                    Code will auto-submit when all digits are entered
                </p>

                <!-- Submit Button -->
                <button type="button" wire:click="verifyTotp" wire:loading.attr="disabled"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm
                                                                                                       text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600
                                                                                                       hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2
                                                                                                       focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <span wire:loading.remove wire:target="verifyTotp">
                        <i class="fas fa-check-circle mr-2"></i>
                        Verify & Continue
                    </span>
                    <span wire:loading wire:target="verifyTotp">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Verifying...
                    </span>
                </button>
            </div>
        @endif

        <!-- Recovery Code Entry -->
        @if($mode === 'recovery')
            <div class="mb-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Enter Recovery Code
                    </label>
                    <input type="text" wire:model="recoveryCode" placeholder="XXXXX-XXXXX" id="recovery-input"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500
                                                                                                          focus:border-indigo-500 text-center font-mono tracking-wider" autocomplete="off">
                    <p class="mt-2 text-sm text-gray-600">
                        Enter one of your 10-character recovery codes
                    </p>
                </div>

                <button type="button" wire:click="verifyRecovery" wire:loading.attr="disabled"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm
                                                                                                   text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600
                                                                                                   hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2
                                                                                                   focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <span wire:loading.remove wire:target="verifyRecovery">
                        <i class="fas fa-key mr-2"></i>
                        Verify Recovery Code
                    </span>
                    <span wire:loading wire:target="verifyRecovery">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Verifying...
                    </span>
                </button>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-blue-800 font-medium mb-1">Where to find recovery codes?</p>
                        <p class="text-xs text-blue-700">
                            Recovery codes were shown when you enabled 2FA. You can also find them in your account security
                            settings.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if($errorMessage)
            <div class="mb-6 animate-shake">
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ $errorMessage }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Debug Information for Localhost -->
        @if($debugInfo)
            <div class="mb-4 p-3 bg-gray-100 border border-gray-300 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-700">
                        <i class="fas fa-bug mr-1"></i> Debug Info
                    </span>
                    <button wire:click="$set('debugInfo', '')" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-600 font-mono break-all">{{ $debugInfo }}</p>
            </div>
        @endif

        <!-- Time Synchronization Warning -->
        @if($showTimeSyncInfo)
            <div class="mb-6 animate-slide-down">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-blue-500"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            @if($isLocalhost)
                                <p class="text-sm text-blue-700 font-medium">Localhost Detected</p>
                                <p class="text-xs text-blue-600 mt-1">
                                    Time sync issues are common in local development. Using extended verification window.
                                </p>
                            @else
                                <p class="text-sm text-blue-700 font-medium">Time Synchronization Issue</p>
                                <p class="text-xs text-blue-600 mt-1">
                                    {{ $timeSyncStatus }}
                                </p>
                            @endif
                            <div class="mt-2 flex space-x-2">
                                <button type="button" wire:click="checkTimeSync" wire:loading.attr="disabled"
                                    class="text-xs text-blue-700 hover:text-blue-800 font-medium underline">
                                    Check Server Time
                                </button>
                                <button type="button" wire:click="getExpectedCode" wire:loading.attr="disabled"
                                    class="text-xs text-blue-700 hover:text-blue-800 font-medium underline">
                                    Get Expected Code
                                </button>
                                <button type="button" wire:click="resetTimeOffset" wire:loading.attr="disabled"
                                    class="text-xs text-blue-700 hover:text-blue-800 font-medium underline">
                                    Reset Time Offset
                                </button>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('showTimeSyncInfo', false)"
                            class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif
        <!-- Help Section -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <button type="button" wire:click="toggleHelp"
                class="w-full flex items-center justify-between text-sm text-gray-600 hover:text-gray-900">
                <span class="font-medium">
                    <i class="fas fa-question-circle mr-2"></i>
                    Need help with verification?
                </span>
                <i class="fas fa-chevron-{{ $showHelp ? 'up' : 'down' }} transition-transform"></i>
            </button>

            @if($showHelp)
                <div class="mt-4 space-y-3 animate-slide-down">
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-mobile-alt text-indigo-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Don't have your authenticator app?</p>
                            <p class="text-xs text-gray-600">Use a recovery code instead.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-sync-alt text-indigo-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Code not working?</p>
                            <p class="text-xs text-gray-600">Wait for the timer to reset and try the new code.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-indigo-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Check your device time</p>
                            <p class="text-xs text-gray-600">Ensure your device's time is synchronized correctly.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Security Badges -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="p-3">
                    <div class="w-8 h-8 mx-auto mb-2 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shield-alt text-green-600"></i>
                    </div>
                    <p class="text-xs text-gray-600">2FA Protected</p>
                </div>
                <div class="p-3">
                    <div class="w-8 h-8 mx-auto mb-2 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-lock text-blue-600"></i>
                    </div>
                    <p class="text-xs text-gray-600">End-to-End Encrypted</p>
                </div>
                <div class="p-3">
                    <div class="w-8 h-8 mx-auto mb-2 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-shield text-purple-600"></i>
                    </div>
                    <p class="text-xs text-gray-600">Privacy First</p>
                </div>
            </div>
        </div>
        @push('scripts')
            <script>
                // Simple Alpine.js for input handling
                document.addEventListener('livewire:init', () => {
                    // Focus management
                    Livewire.on('focus-next', (data) => {
                        const input = document.getElementById('code-' + data.input);
                        if (input) {
                            input.focus();
                            input.select();
                        }
                    });

                    Livewire.on('focus-previous', (data) => {
                        const input = document.getElementById('code-' + data.input);
                        if (input) {
                            input.focus();
                            input.select();
                        }
                    });

                    Livewire.on('shake-inputs', () => {
                        const inputs = document.querySelectorAll('.code-input');
                        inputs.forEach(input => {
                            input.classList.add('shake');
                            setTimeout(() => input.classList.remove('shake'), 500);
                        });
                    });

                    Livewire.on('verification-success', () => {
                        const container = document.querySelector('.animate-fade-in');
                        if (container) {
                            container.innerHTML = `
                                                                <div class="text-center py-12">
                                                                    <div class="inline-block mb-6">
                                                                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                                                            <i class="fas fa-check text-green-600 text-3xl"></i>
                                                                        </div>
                                                                        <div class="absolute inset-0 rounded-full border-4 border-green-500 opacity-50 animate-ping"></div>
                                                                    </div>
                                                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Verification Successful!</h3>
                                                                    <p class="text-gray-600">Redirecting to your dashboard...</p>
                                                                    <div class="mt-6">
                                                                        <div class="w-48 h-1 bg-gray-200 rounded-full mx-auto overflow-hidden">
                                                                            <div class="h-full bg-green-500 rounded-full animate-progress"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            `;
                        }
                    });

                    Livewire.on('show-toast', (data) => {
                        // Simple toast implementation
                        const toast = document.createElement('div');
                        toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white z-50 ${data.type === 'info' ? 'bg-blue-500' : 'bg-green-500'
                            }`;
                        toast.innerHTML = `
                                                            <div class="flex items-center">
                                                                <i class="fas fa-${data.type === 'info' ? 'info-circle' : 'check-circle'} mr-2"></i>
                                                                <span>${data.message}</span>
                                                            </div>
                                                        `;
                        document.body.appendChild(toast);

                        setTimeout(() => {
                            toast.remove();
                        }, data.duration || 3000);
                    });
                });

                // Auto-focus first input on load
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(() => {
                        @if($mode === 'totp')
                            const firstInput = document.getElementById('code-1');
                            if (firstInput) firstInput.focus();
                        @else
                                                                                                                                        const recoveryInput = document.getElementById('recovery-input');
                            if (recoveryInput) recoveryInput.focus();
                        @endif
                                                    }, 100);
                });

                // Keyboard shortcuts
                document.addEventListener('keydown', (e) => {
                    // Submit with Enter
                    if (e.key === 'Enter' && !e.target.classList.contains('code-input')) {
                        @if($mode === 'totp')
                            @this.verifyTotp();
                        @else
                            @this.verifyRecovery();
                        @endif
                        e.preventDefault();
                    }

                    // Switch to recovery with Alt+R
                    if (e.altKey && e.key === 'r') {
                        @this.switchMode('recovery');
                        e.preventDefault();
                    }

                    // Switch to TOTP with Alt+T
                    if (e.altKey && e.key === 't') {
                        @this.switchMode('totp');
                        e.preventDefault();
                    }

                    // Resend code with Alt+N
                    if (e.altKey && e.key === 'n') {
                        @this.resendCode();
                        e.preventDefault();
                    }
                });
            </script>

            <style>
                .animate-shake {
                    animation: shake 0.5s ease-in-out;
                }

                @keyframes shake {

                    0%,
                    100% {
                        transform: translateX(0);
                    }

                    10%,
                    30%,
                    50%,
                    70%,
                    90% {
                        transform: translateX(-5px);
                    }

                    20%,
                    40%,
                    60%,
                    80% {
                        transform: translateX(5px);
                    }
                }

                .animate-progress {
                    animation: progress 1s linear forwards;
                }

                @keyframes progress {
                    from {
                        width: 0%;
                    }

                    to {
                        width: 100%;
                    }
                }

                .animate-slide-down {
                    animation: slideDown 0.3s ease-out;
                }

                @keyframes slideDown {
                    from {
                        opacity: 0;
                        transform: translateY(-10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                /* Animation for timer when time is low */
                @keyframes pulse-red {

                    0%,
                    100% {
                        opacity: 1;
                    }

                    50% {
                        opacity: 0.5;
                    }
                }

                .border-red-500 {
                    animation: pulse-red 1s ease-in-out infinite;
                }
            </style>
        @endpush
    </div>