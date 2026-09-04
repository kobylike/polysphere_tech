<?php

namespace App\Livewire\Admin\Users\Account\Tabs;

use App\Helpers\ActivityLogger; // <-- Added
use App\Helpers\NotificationHelper;
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

        // 🔥 Log password change
        ActivityLogger::log('Password changed', [
            'user_id' => $user->id,
            'email'   => $user->email,
        ], 'security');

        // 🔥 Send notification
        NotificationHelper::sendToUser($user, [
            'title' => 'Password Changed',
            'body' => 'Your password was successfully changed.',
            'type' => 'info',
            'icon' => 'fa-key',
            'link' => route('account', ['tab' => 'security']),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch(
            'notify',
            type: 'success',
            title: 'Password updated!',
            message: 'Your password has been changed successfully.',
        );
    }

    // ─── Two-Factor Authentication ──────────────────────────────────

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

            // 🔥 Log 2FA setup started
            ActivityLogger::log('Two‑factor authentication setup started', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ], 'security');

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

            // 🔥 Log secret regeneration
            ActivityLogger::log('Two‑factor secret regenerated', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ], 'security');

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

        $debugMode = app()->environment('local');

        if ($user->verifyTwoFactorCode($this->code, $debugMode)) {
            $user->confirmTwoFactor();

            // 🔥 Log 2FA enabled
            ActivityLogger::log('Two‑factor authentication enabled', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ], 'security');

            // 🔥 Send notification (2FA enabled)
            NotificationHelper::sendToUser($user, [
                'title' => 'Two-Factor Authentication Enabled',
                'body' => 'Two-factor authentication has been successfully enabled on your account.',
                'type' => 'success',
                'icon' => 'fa-shield-alt',
                'link' => route('account', ['tab' => 'security']),
            ]);

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

            // 🔥 Log 2FA disabled
            ActivityLogger::log('Two‑factor authentication disabled', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ], 'security');

            // 🔥 Send notification (2FA disabled)
            NotificationHelper::sendToUser($user, [
                'title' => 'Two-Factor Authentication Disabled',
                'body' => 'Two-factor authentication has been disabled on your account.',
                'type' => 'warning',
                'icon' => 'fa-shield-halved',
                'link' => route('account', ['tab' => 'security']),
            ]);

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

            // 🔥 Log recovery codes regenerated
            ActivityLogger::log('Recovery codes regenerated', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ], 'security');

            // 🔥 Send notification
            NotificationHelper::sendToUser($user, [
                'title' => 'Recovery Codes Regenerated',
                'body' => 'Your two-factor authentication recovery codes have been regenerated.',
                'type' => 'info',
                'icon' => 'fa-key',
                'link' => route('account', ['tab' => 'security']),
            ]);

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

        // 🔥 Log deactivation
        ActivityLogger::log('Account deactivated', [
            'user_id' => $user->id,
            'email'   => $user->email,
        ], 'security');

        // 🔥 Send notification before logout
        NotificationHelper::sendToUser($user, [
            'title' => 'Account Deactivated',
            'body' => 'Your account has been deactivated. You can reactivate it by contacting support.',
            'type' => 'warning',
            'icon' => 'fa-user-slash',
            'link' => url('/'),
        ]);

        $user->status = 'suspended';
        $user->save();

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect('/login');
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
