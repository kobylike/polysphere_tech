<div>
    @if($showSuccess)
        <div
            style="background: rgba(16, 185, 129, 0.15); border: none; color: #34d399; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 12px;">
            <i class="fas fa-check-circle me-2"></i>
            <span style="display: block; margin-top: 4px; font-size: 13px; color: rgba(255,255,255,0.7);">
                Thanks for subscribing! Please check your email to confirm.
            </span>
        </div>
    @else
        <form wire:submit.prevent="subscribe">
            <div style="position: relative;">
                <input type="email" wire:model="email" placeholder="Your email address" required
                    style="width: 100%; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; border-radius: 8px; font-size: 14px; margin-bottom: 12px; outline: none; transition: border-color 0.3s; {{ $errorMessage ? 'border-color: #ef4444;' : '' }}">
                @error('email')
                    <span style="color: #ef4444; font-size: 12px; display: block; margin-bottom: 8px;">{{ $message }}</span>
                @enderror
                @if($errorMessage)
                    <span
                        style="color: #ef4444; font-size: 12px; display: block; margin-bottom: 8px;">{{ $errorMessage }}</span>
                @endif
            </div>
            <button type="submit" class="primary-btn-1 btn-hover"
                style="width: 100%; padding: 12px; text-align: center; font-size: 13px; letter-spacing: 1px; border: none; cursor: pointer; position: relative; overflow: hidden;">
                SUBSCRIBE NOW
                <span style="top: 147.172px; left: 108.5px;"></span>
            </button>
        </form>
    @endif
</div>