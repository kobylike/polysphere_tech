<div class="login-form" x-data="{ showPassword: false }">
    <div class="text-center">
        <h3 class="title">Sign In</h3>
        <p>Sign in to your account to start using Polysphere Tech</p>
    </div>

    <form wire:submit.prevent="login">
        <!-- Email -->
        <div class="mb-4">
            <label class="mb-1 text-dark">Email</label>
            <input type="email" class="form-control form-control @error('email') is-invalid @enderror"
                wire:model.live.debounce.400ms="email" placeholder="Enter your email">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Password with Alpine toggle -->
        <div class="mb-4 position-relative">
            <label class="mb-1 text-dark">Password</label>
            <input :type="showPassword ? 'text' : 'password'" id="dz-password"
                class="form-control @error('password') is-invalid @enderror" wire:model.live.debounce.400ms="password"
                placeholder="Enter your password">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div> @enderror

            <span class="show-pass eye" @click="showPassword = !showPassword" style="cursor:pointer;">
                <i class="fa" :class="showPassword ? 'fa-eye' : 'fa-eye-slash'"></i>
            </span>
        </div>

        <!-- Remember & Forgot -->
        <div class="form-row d-flex justify-content-between mt-4 mb-2">
            <div class="mb-4">
                <div class="form-check custom-checkbox mb-3">
                    <input type="checkbox" class="form-check-input" id="customCheckBox1" wire:model="remember">
                    <label class="form-check-label" for="customCheckBox1">Remember my preference</label>
                </div>
            </div>
            <div class="mb-4">
                <a href="{{ route('password.request') }}" class="btn-link text-primary">Forgot Password?</a>
            </div>
        </div>

        <!-- Submit -->
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-block" wire:loading.attr="disabled">
                <span wire:loading.remove>Sign In</span>
                <span wire:loading><i class="fa fa-spinner fa-spin"></i> Signing in…</span>
            </button>
        </div>

        <!-- Social Login -->
        <h6 class="login-title"><span>Or continue with</span></h6>
        <div class="mb-3">
            <ul class="d-flex align-self-center justify-content-center">
                <li><a target="_blank" href="#" class="fab fa-facebook-f btn-facebook"></a></li>
                <li><a target="_blank" href="#" class="fab fa-google-plus-g btn-google-plus mx-2"></a></li>
                <li><a target="_blank" href="#" class="fab fa-linkedin-in btn-linkedin me-2"></a></li>
                <li><a target="_blank" href="#" class="fab fa-twitter btn-twitter"></a></li>
            </ul>
        </div>

        <p class="text-center">Not registered?
            <a class="btn-link text-primary" href="{{ route('register') }}">Register</a>
        </p>
    </form>
</div>