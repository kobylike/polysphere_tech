<div>
    <div class="text-center">
        <div class="mb-6">
            <i class="fas fa-envelope-circle-check text-6xl text-polysphere-500"></i>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-2">Verify Your Email</h2>

        <p class="text-gray-600 mb-4">
            We've sent a verification link to <strong>{{ Auth::user()->email }}</strong>.
            Please check your inbox and click the link to verify your email address.
        </p>

        <p class="text-sm text-gray-500 mb-6">
            If you didn't receive the email, you can request a new one below.
        </p>

        {{-- Livewire status messages --}}
        @if($status === 'success')
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i> {{ $message }}
            </div>
        @elseif($status === 'info')
            <div class="mb-4 p-3 bg-blue-100 text-blue-700 rounded-lg">
                <i class="fas fa-info-circle mr-2"></i> {{ $message }}
            </div>
        @elseif($status === 'error')
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ $message }}
            </div>
        @endif

        {{-- Resend button --}}
        <button wire:click="resendVerification" wire:loading.attr="disabled"
            class="btn-lift w-full flex justify-center py-3 px-4 bg-gradient-to-r from-polysphere-600 to-polysphere-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
            <span wire:loading.remove>
                <i class="fas fa-paper-plane mr-2"></i> Resend Verification Email
            </span>
            <span wire:loading>
                <i class="fas fa-spinner fa-spin mr-2"></i> Sending…
            </span>
        </button>

        {{-- Logout link as Livewire action --}}
        <div class="mt-4">
            <button wire:click="logout"
                class="text-sm text-polysphere-600 hover:underline bg-transparent border-none cursor-pointer">
                Sign out
            </button>
        </div>
    </div>
</div>