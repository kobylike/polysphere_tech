<?php

namespace App\Livewire\Admin\Users\Account\Tabs;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SecurityTab extends Component
{
    // ─── Change Password ────────────────────────────────────────────
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // ─── Two-Factor Authentication ──────────────────────────────────
    public bool $showingQrCode = false;
    public bool $showingRecoveryCodes = false;
    public bool $enabling = false;
    public string $code = '';
    public string $qrCodeSvg = '';
    public string $setupKey = '';
    public bool $qrCodeError = false;

    // ─── Deactivation ───────────────────────────────────────────────
    public bool $confirmingDeactivation = false;
    public bool $deactivationAcknowledged = false;

    protected function rules()
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
            'code' => 'required|string|size:6|regex:/^[0-9]+$/',
            'deactivationAcknowledged' => 'accepted',
        ];
    }

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasTwoFactorSecret() && !$user->hasTwoFactorEnabled()) {
            $this->showingQrCode = true;
            $this->loadQrCode();
        }
    }

    // ─── Change Password ────────────────────────────────────────────

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch(
            'notify',
            type: 'success',
            title: 'Password updated!',
            message: 'Your password has been changed successfully.',
        );
    }

    // ─── Two-Factor Authentication ──────────────────────────────────

    /**
     * Called from the "Show QR" button once 2FA is already enabled.
     * Distinct from enableTwoFactor(): it never touches the secret,
     * it just (re)loads the existing one so it can be rendered.
     */
    public function showQrCode()
    {
        $this->showingQrCode = true;
        $this->loadQrCode();
    }

    public function loadQrCode()
    {
        $this->qrCodeError = false;
        $this->qrCodeSvg = '';
        $this->setupKey = '';

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasTwoFactorSecret()) {
            $this->qrCodeError = true;
            return;
        }

        try {
            $user->refresh();

            $svg = $user->twoFactorQrCodeSvg();

            if (empty($svg)) {
                Log::error('Empty QR code SVG generated', ['user_id' => $user->id]);

                $this->qrCodeError = true;

                $this->dispatch(
                    'notify',
                    type: 'error',
                    title: 'QR generation failed',
                    message: 'Could not generate the QR code. Check storage/logs/laravel.log for the underlying error.',
                );

                return;
            }

            $this->qrCodeSvg = $svg;
            $this->setupKey = $user->getTwoFactorSecret() ?? '';

            Log::info('QR code loaded successfully', [
                'user_id' => $user->id,
                'svg_length' => strlen($svg),
                'setup_key_length' => strlen($this->setupKey),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load QR code', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id,
            ]);

            $this->qrCodeError = true;
            $this->dispatch(
                'notify',
                type: 'error',
                title: 'QR Code Error',
                message: $e->getMessage(),
            );
        }
    }

    public function enableTwoFactor()
    {
        if ($this->enabling) return;

        $this->enabling = true;

        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            if ($user->hasTwoFactorSecret()) {
                $user->disableTwoFactorAuthentication();
            }

            $user->enableTwoFactorAuthentication();
            $user->refresh();

            $this->showingQrCode = true;
            $this->loadQrCode();

            $this->dispatch(
                'notify',
                type: 'info',
                title: 'Scan QR Code',
                message: 'Scan the QR code with your authenticator app, then enter the 6‑digit code below.',
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                title: 'Failed to enable 2FA',
                message: $e->getMessage(),
            );
        }

        $this->enabling = false;
    }

    public function regenerateSecret()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            $user->disableTwoFactorAuthentication();
            $user->enableTwoFactorAuthentication();
            $user->refresh();

            $this->loadQrCode();
            $this->showingQrCode = true;

            $this->dispatch(
                'notify',
                type: 'success',
                title: 'Secret Regenerated',
                message: 'A new secret key has been generated. Scan the QR code again.',
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                title: 'Failed to regenerate',
                message: $e->getMessage(),
            );
        }
    }

    public function confirmTwoFactor()
    {
        $this->validate(['code' => 'required|string|size:6|regex:/^[0-9]+$/']);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // debugMode widens the TOTP acceptance window to ~8 minutes and is
        // only ever active outside production — it compensates for local
        // machine clock drift (a common Windows dev-environment issue)
        // without loosening verification in production, where the window
        // stays at the strict ~90s default.
        $debugMode = app()->environment('local');

        if ($user->verifyTwoFactorCode($this->code, $debugMode)) {
            $user->confirmTwoFactor();

            $this->showingQrCode = false;
            $this->showingRecoveryCodes = true;
            $this->code = '';

            $this->dispatch(
                'notify',
                type: 'success',
                title: '2FA Enabled!',
                message: 'Two‑factor authentication has been enabled successfully. Save your recovery codes.',
            );
        } else {
            $this->addError('code', 'Invalid verification code. Please try again.');
        }
    }

    public function disableTwoFactor()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            $user->disableTwoFactorAuthentication();
            $this->showingQrCode = false;
            $this->showingRecoveryCodes = false;
            $this->qrCodeSvg = '';
            $this->setupKey = '';

            $this->dispatch(
                'notify',
                type: 'success',
                title: '2FA Disabled',
                message: 'Two‑factor authentication has been disabled.',
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                title: 'Failed to disable 2FA',
                message: $e->getMessage(),
            );
        }
    }

    public function regenerateRecoveryCodes()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        try {
            $user->generateNewRecoveryCodes();
            $this->dispatch(
                'notify',
                type: 'success',
                title: 'Recovery Codes Regenerated',
                message: 'New recovery codes have been generated.',
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                title: 'Failed to regenerate codes',
                message: $e->getMessage(),
            );
        }
    }

    // ─── Account Deactivation ──────────────────────────────────────

    public function confirmDeactivation()
    {
        $this->confirmingDeactivation = true;
    }

    public function cancelDeactivation()
    {
        $this->confirmingDeactivation = false;
        $this->deactivationAcknowledged = false;
    }

    public function deactivateAccount()
    {
        $this->validate(['deactivationAcknowledged' => 'accepted']);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->status = 'suspended';
        $user->save();

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }

    // ─── Computed Properties ────────────────────────────────────────

    #[Computed]
    public function recoveryCodes()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->getRemainingRecoveryCodes();
    }

    #[Computed]
    public function twoFactorEnabled()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasTwoFactorEnabled();
    }

    #[Computed]
    public function hasSecret()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasTwoFactorSecret();
    }

    public function render()
    {
        return view('livewire.admin.users.account.tabs.security-tab', [
            'twoFactorEnabled' => $this->twoFactorEnabled,
            'hasSecret' => $this->hasSecret,
            'recoveryCodes' => $this->recoveryCodes,
        ]);
    }
}
