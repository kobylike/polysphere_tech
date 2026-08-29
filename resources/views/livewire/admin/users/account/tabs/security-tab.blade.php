<div x-data="securityHandler()" @notify.window="showToast($event.detail)" class="position-relative">

    {{-- ─── Toast ────────────────────────────────────────────────────────── --}}
    <div x-show="toastVisible" x-cloak x-transition:enter.duration.300ms.opacity.scale
        x-transition:leave.duration.200ms.opacity.scale class="position-fixed top-0 end-0 p-3"
        style="z-index: 9999; max-width: 420px; width: 100%;">
        <div class="d-flex align-items-center p-3 rounded-4 shadow-lg border-0 text-white gap-3"
            :class="toastType === 'success' ? 'bg-gradient-success' : 'bg-gradient-danger'"
            style="backdrop-filter: blur(8px); background: linear-gradient(135deg, #10b981, #059669);">
            <div class="flex-shrink-0">
                <i class="fas fa-2x" :class="toastType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold" style="color: #ffffff;" x-text="toastTitle"></h6>
                <p class="mb-0 small" style="color: #ffffff; opacity: 0.9;" x-html="toastMessage"></p>
            </div>
            <button @click="dismissToast()" class="btn btn-sm btn-link text-white p-0 opacity-75">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- ─── Deactivation Confirmation Modal ────────────────────────────── --}}
    <div x-data="{ open: @entangle('confirmingDeactivation') }" x-show="open" x-cloak
        @keydown.escape.window="open = false; $wire.cancelDeactivation()">

        <div class="modal-backdrop fade show" x-show="open" x-transition:enter.duration.200ms.opacity
            x-transition:leave.duration.150ms.opacity style="background: rgba(0,0,0,0.5); z-index: 1050;"
            @click="open = false; $wire.cancelDeactivation()"></div>

        <div class="modal fade show d-block" x-show="open" x-transition:enter.duration.200ms.opacity.scale
            x-transition:leave.duration.150ms.opacity.scale tabindex="-1" style="z-index: 1060;"
            @click.self="open = false; $wire.cancelDeactivation()">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger">Deactivate Account?</h5>
                        <button type="button" class="btn-close"
                            @click="open = false; $wire.cancelDeactivation()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-3">
                            <i class="fas fa-user-slash text-danger" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-secondary">This action is <strong>permanent</strong> and cannot be
                                undone. All your data will be removed.</p>
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" id="confirmDeactivate"
                                    wire:model="deactivationAcknowledged">
                                <label class="form-check-label" for="confirmDeactivate">
                                    I confirm that I want to deactivate my account.
                                </label>
                            </div>
                            @error('deactivationAcknowledged')
                                <span class="text-danger small d-block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-center gap-3">
                        <button type="button" class="btn btn-light px-4 rounded-pill"
                            @click="open = false; $wire.cancelDeactivation()">Cancel</button>
                        <button type="button" class="btn btn-danger px-4 rounded-pill"
                            @click="$wire.deactivateAccount()">
                            <i class="fas fa-user-slash me-1"></i> Yes, Deactivate
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── MAIN CONTENT ─────────────────────────────────────────────────── --}}
    <div class="row g-4">

        {{-- ─── Change Password ─────────────────────────────────────────── --}}
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title fw-bold"><i class="fas fa-key text-primary me-2"></i> Change Password</h6>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="changePassword">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                wire:model="current_password" placeholder="Enter current password">
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Password</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                wire:model="new_password" placeholder="Enter new password (min 8 chars)">
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password"
                                class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                wire:model="new_password_confirmation" placeholder="Confirm new password">
                            @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                            wire:target="changePassword">
                            <span wire:loading.remove wire:target="changePassword"><i class="fas fa-save me-1"></i>
                                Update Password</span>
                            <span wire:loading wire:target="changePassword"><i class="fas fa-spinner fa-spin me-1"></i>
                                Updating…</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ─── Two-Factor Authentication ──────────────────────────────── --}}
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title fw-bold"><i class="fas fa-shield-alt text-primary me-2"></i> Two-Factor
                        Authentication</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="fw-semibold">Status</span>
                            <div class="mt-1">
                                @if($twoFactorEnabled)
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                                @elseif($hasSecret)
                                    <span class="badge bg-warning"><i class="fas fa-clock me-1"></i> Setup in
                                        progress</span>
                                @else
                                    <span class="badge bg-secondary"><i class="fas fa-minus-circle me-1"></i> Not
                                        enabled</span>
                                @endif
                            </div>
                        </div>
                        @if($twoFactorEnabled)
                            <button type="button" wire:click="disableTwoFactor" wire:loading.attr="disabled"
                                wire:target="disableTwoFactor" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-times me-1"></i> Disable
                            </button>
                        @else
                            <button type="button" wire:click="enableTwoFactor" wire:loading.attr="disabled"
                                wire:target="enableTwoFactor" class="btn btn-primary btn-sm">
                                <span wire:loading.remove wire:target="enableTwoFactor"><i class="fas fa-plus me-1"></i>
                                    Enable</span>
                                <span wire:loading wire:target="enableTwoFactor"><i class="fas fa-spinner fa-spin me-1"></i>
                                    Enabling…</span>
                            </button>
                        @endif
                    </div>

                    @if($twoFactorEnabled)
                        <div class="d-flex gap-3 mt-3">
                            {{-- FIX: was $set('showingQrCode', true), which only flipped the
                            flag without ever loading the SVG — qrCodeSvg stays empty because
                            it gets cleared to '' the moment 2FA is confirmed. Now calls a
                            dedicated method that loads the QR before showing it. --}}
                            <button type="button" wire:click="showQrCode" wire:loading.attr="disabled"
                                wire:target="showQrCode" class="btn btn-outline-primary btn-sm flex-grow-1">
                                <span wire:loading.remove wire:target="showQrCode"><i class="fas fa-qrcode me-1"></i> Show
                                    QR</span>
                                <span wire:loading wire:target="showQrCode"><i class="fas fa-spinner fa-spin me-1"></i>
                                    Loading…</span>
                            </button>
                            <button type="button" wire:click="$set('showingRecoveryCodes', true)"
                                class="btn btn-outline-warning btn-sm flex-grow-1">
                                <i class="fas fa-key me-1"></i> Recovery Codes
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ─── QR Code Setup / Re-view ──────────────────────────────────── --}}
        {{-- FIX: dropped the "!$twoFactorEnabled" requirement — that made this whole
        block invisible once 2FA was confirmed, which is exactly when "Show QR" needs
        it to appear. The verify-code form below is still hidden once already enabled,
        since re-confirming isn't needed at that point. --}}
        @if($showingQrCode && $hasSecret)
            <div class="col-12" wire:key="qr-setup-section">
                <div class="card border-0 shadow-lg" style="border-left: 4px solid #6366f1;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="fw-bold"><i class="fas fa-qrcode text-primary me-2"></i>
                                {{ $twoFactorEnabled ? 'Authenticator QR Code' : 'Authenticator Setup' }}
                            </h5>
                            <div class="d-flex gap-2">
                                @unless($twoFactorEnabled)
                                    <button type="button" wire:click="regenerateSecret"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-redo me-1"></i> Regenerate
                                    </button>
                                @endunless
                                <button type="button" wire:click="$set('showingQrCode', false)" class="btn-close"></button>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-5 text-center">
                                @if($qrCodeError)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Unable to generate QR code.
                                        <button type="button" wire:click="regenerateSecret"
                                            class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fas fa-redo me-1"></i> Regenerate
                                        </button>
                                    </div>
                                @elseif($qrCodeSvg)
                                    <div class="bg-white d-inline-block p-3 rounded-3 shadow-sm">
                                        {!! $qrCodeSvg !!}
                                    </div>
                                @else
                                    <div class="alert alert-info">Loading QR code...</div>
                                @endif
                                <p class="text-muted small mt-2">Scan with your authenticator app</p>
                            </div>
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Manual Setup Key</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ $setupKey }}" readonly
                                            id="setupKey">
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                @unless($twoFactorEnabled)
                                    <form wire:submit.prevent="confirmTwoFactor">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Verification Code</label>
                                            <input type="text" wire:model="code" maxlength="6"
                                                class="form-control form-control-lg text-center @error('code') is-invalid @enderror"
                                                placeholder="123456" autofocus>
                                            @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check me-1"></i> Verify & Enable
                                        </button>
                                        <button type="button" wire:click="$set('showingQrCode', false)"
                                            class="btn btn-link text-muted">Cancel</button>
                                    </form>
                                @else
                                    <button type="button" wire:click="$set('showingQrCode', false)"
                                        class="btn btn-outline-secondary">Close</button>
                                @endunless
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── Recovery Codes ──────────────────────────────────────────── --}}
        @if($showingRecoveryCodes && $twoFactorEnabled)
            <div class="col-12" wire:key="recovery-codes-section">
                <div class="card border-0 shadow-lg" style="border-left: 4px solid #f59e0b;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="fw-bold"><i class="fas fa-key text-warning me-2"></i> Recovery Codes</h5>
                            <button type="button" wire:click="$set('showingRecoveryCodes', false)"
                                class="btn-close"></button>
                        </div>
                        <p class="text-muted small mb-3">Save these codes securely. Each code can be used once.</p>
                        @if(count($recoveryCodes) > 0)
                            <div class="row g-2 mb-3">
                                @foreach($recoveryCodes as $index => $code)
                                    <div class="col-sm-6" wire:key="recovery-code-{{ $index }}">
                                        <div class="border rounded p-2 bg-light d-flex justify-content-between align-items-center">
                                            <code class="fw-bold">{{ $code }}</code>
                                            <span class="badge bg-secondary rounded-pill">#{{ $index + 1 }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="downloadRecoveryCodes()">
                                    <i class="fas fa-download me-1"></i> Download
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printRecoveryCodes()">
                                    <i class="fas fa-print me-1"></i> Print
                                </button>
                                <button type="button" wire:click="regenerateRecoveryCodes" wire:loading.attr="disabled"
                                    wire:target="regenerateRecoveryCodes" class="btn btn-outline-danger btn-sm">
                                    <span wire:loading.remove wire:target="regenerateRecoveryCodes"><i
                                            class="fas fa-redo me-1"></i> Regenerate</span>
                                    <span wire:loading wire:target="regenerateRecoveryCodes"><i
                                            class="fas fa-spinner fa-spin me-1"></i> Generating…</span>
                                </button>
                            </div>
                        @else
                            <div class="alert alert-warning">No recovery codes available.</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- ─── Account Deactivation ─────────────────────────────────────── --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-left: 4px solid #ef4444;">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title fw-bold text-danger"><i class="fas fa-user-slash me-2"></i> Deactivate Account
                    </h6>
                </div>
                <div class="card-body">
                    <div
                        class="alert alert-warning border-warning outline-dashed py-3 px-3 mb-3 text-dark d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fs-30 text-warning me-3"></i>
                        <div>
                            <h6 class="mb-0 fw-semibold">This action is permanent</h6>
                            <p class="mb-0 fs-13">Your account will be suspended and all data will be removed.</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger" wire:click="confirmDeactivation">
                        <i class="fas fa-user-slash me-1"></i> Deactivate Account
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ─── Styles ─────────────────────────────────────────────────────────────── --}}
<style>
    [x-cloak] {
        display: none !important;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .outline-dashed {
        border: 1px dashed #f59e0b;
    }
</style>

{{-- ─── Alpine Handler ────────────────────────────────────────────────────── --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('securityHandler', () => ({
            toastVisible: false,
            toastType: 'success',
            toastTitle: '',
            toastMessage: '',
            toastTimeout: null,

            init() {
                window.addEventListener('livewire:navigate', () => {
                    this.toastVisible = false;
                    clearTimeout(this.toastTimeout);
                });
            },

            showToast(detail) {
                this.toastType = detail.type || 'success';
                this.toastTitle = detail.title || (this.toastType === 'success' ? 'Success!' : 'Error!');
                this.toastMessage = detail.message || '';
                this.toastVisible = true;
                clearTimeout(this.toastTimeout);
                this.toastTimeout = setTimeout(() => {
                    this.dismissToast();
                }, 4000);
            },

            dismissToast() {
                this.toastVisible = false;
                clearTimeout(this.toastTimeout);
            }
        }));
    });

    // ─── Copy / Download / Print helpers ──────────────────────────────────
    function copyToClipboard() {
        const el = document.getElementById('setupKey');
        if (!el) return;
        const text = el.value;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => showToast('Setup key copied!'));
        } else {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-999999px';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            showToast('Setup key copied!');
        }
    }

    function showToast(msg) {
        const el = document.querySelector('[x-data]');
        if (el && el.__x && el.__x.$data.showToast) {
            el.__x.$data.showToast({ type: 'success', title: 'Copied!', message: msg });
        } else {
            const div = document.createElement('div');
            div.className = 'position-fixed top-0 end-0 m-3 p-3 rounded-4 shadow-lg bg-success text-white';
            div.textContent = msg;
            div.style.zIndex = 9999;
            document.body.appendChild(div);
            setTimeout(() => div.remove(), 3000);
        }
    }

    function downloadRecoveryCodes() {
        const codes = @json($recoveryCodes);
        if (!codes.length) { alert('No recovery codes.'); return; }
        const content = `Polysphere Tech – Recovery Codes\n\n${codes.map((c, i) => `${i + 1}. ${c}`).join('\n')}\n\nGenerated: ${new Date().toLocaleString()}\nAccount: {{ auth()->user()->email }}`;
        const blob = new Blob([content], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'polysphere-recovery-codes.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function printRecoveryCodes() {
        const codes = @json($recoveryCodes);
        if (!codes.length) { alert('No recovery codes.'); return; }
        const html = `<!DOCTYPE html>
            <html><head><title>Recovery Codes</title>
            <style>body{font-family:Arial;padding:20px;} .code{background:#f8f9fa;padding:10px;margin:8px 0;border:1px solid #ddd;border-radius:4px;}</style>
            </head><body>
            <h1>Polysphere Tech – Recovery Codes</h1>
            <p><strong>Account:</strong> {{ auth()->user()->email }}</p>
            <p><strong>Generated:</strong> ${new Date().toLocaleString()}</p>
            <hr>
            ${codes.map((c, i) => `<div class="code"><strong>#${i + 1}:</strong> ${c}</div>`).join('')}
            <p><em>Store these securely. Each code can be used once.</em></p>
            </body></html>`;
        const win = window.open('', '_blank');
        win.document.write(html);
        win.document.close();
        win.print();
        win.close();
    }
</script>