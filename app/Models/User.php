<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;

#[Fillable([
    'google_id',
    'name',
    'username',
    'email',
    'password',
    'must_change_password',
    'status',
    'phone',
    'avatar',
    'email_verified_at',
    'email_verification_token',
    'email_verification_sent_at',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'two_factor_confirmed_at',
    'two_factor_enabled',
    'failed_login_attempts',
    'locked_until',
    'last_login_at',
    'last_login_ip',
    'totp_time_offset',
    'last_seen_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'locked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }
    public static function spotlightTeam(int $limit = 3)
    {
        return static::whereHas('profile', fn($q) => $q->spotlight())
            ->with('profile')
            ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
            ->orderBy('user_profiles.display_order', 'asc')
            ->select('users.*')
            ->limit($limit)
            ->get();
    }
    public function sendEmailVerificationNotification()
    {
        \Illuminate\Support\Facades\Notification::send(
            $this,
            new \App\Notifications\VerifyEmailNotification()
        );
    }

    public function getTwoFactorSecretAttribute($value): ?string
    {
        if (!$value) return null;
        try {
            return Crypt::decrypt($value);
        } catch (DecryptException $e) {
            Log::error('Failed to decrypt 2FA secret', ['user_id' => $this->id]);
            return null;
        }
    }

    public function setTwoFactorSecretAttribute($value): void
    {
        $this->attributes['two_factor_secret'] = $value ? Crypt::encrypt($value) : null;
    }

    public function getTwoFactorRecoveryCodesAttribute($value): ?array
    {
        if (!$value) return null;
        try {
            return json_decode(Crypt::decrypt($value), true);
        } catch (DecryptException $e) {
            Log::error('Failed to decrypt recovery codes', ['user_id' => $this->id]);
            return null;
        }
    }

    public function setTwoFactorRecoveryCodesAttribute($value): void
    {
        if ($value) {
            $json = is_array($value) ? json_encode($value) : $value;
            $this->attributes['two_factor_recovery_codes'] = Crypt::encrypt($json);
        } else {
            $this->attributes['two_factor_recovery_codes'] = null;
        }
    }

    public function enableTwoFactorAuthentication(): void
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey(32);

        $this->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => $this->generateRecoveryCodes(),
            'two_factor_confirmed_at' => null,
        ])->save();

        cache()->forget('user_2fa_' . $this->id);
    }

    public function disableTwoFactorAuthentication(): void
    {
        $this->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        cache()->forget('user_2fa_' . $this->id);
    }

    public function generateNewRecoveryCodes(): void
    {
        $this->forceFill([
            'two_factor_recovery_codes' => $this->generateRecoveryCodes(),
        ])->save();

        cache()->forget('user_2fa_' . $this->id);
    }

    protected function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5)));
        }
        return $codes;
    }

    public function twoFactorQrCodeSvg(): string
    {
        $secret = $this->two_factor_secret;
        if (empty($secret)) return '';

        try {
            $google2fa = new Google2FA();
            $issuer = rawurlencode(config('app.name', 'Polysphere Tech'));
            $accountName = rawurlencode($this->email);
            $qrCodeUrl = $google2fa->getQRCodeUrl($issuer, $accountName, $secret);

            $renderer = new ImageRenderer(
                new RendererStyle(300, 1),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $svg = $writer->writeString($qrCodeUrl);

            $svg = preg_replace('/(<svg[^>]*>)/', '$1<rect width="100%" height="100%" fill="white"/>', $svg, 1);
            return $svg ?: '';
        } catch (\Throwable $e) {
            Log::error('Failed to generate QR code', [
                'user_id' => $this->id,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            return '';
        }
    }

    public function twoFactorQrCodeUrl(): string
    {
        $secret = $this->two_factor_secret;
        if (empty($secret)) return '';

        try {
            $google2fa = new Google2FA();
            return $google2fa->getQRCodeUrl(
                config('app.name', 'Polysphere Tech'),
                $this->email,
                $secret
            );
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Verify a TOTP code.
     *
     * $debugMode widens the acceptance window (useful on localhost, where
     * clock drift between the dev machine and phone is common) — it does
     * NOT bypass verification, it just tolerates more drift.
     */
    public function verifyTwoFactorCode(string $code, bool $debugMode = false): bool
    {
        $secret = $this->two_factor_secret;
        if (empty($secret)) return false;

        try {
            $google2fa = new Google2FA();

            // window = number of 30s periods accepted on each side of "now".
            // 1 => ~90s total tolerance. 8 => ~8 min total, for local dev only.
            $window = $debugMode ? 8 : 1;

            // IMPORTANT: verifyKey()'s $timestamp argument is not raw Unix
            // seconds — it's a 30-second PERIOD COUNTER (Google2FA's own
            // getTimestamp() returns floor(time()/keyRegeneration)). Passing
            // raw seconds here was the actual bug: it made verification
            // compare against a period ~30x too large, so no window size
            // could ever bridge the gap. Only override the timestamp when
            // an explicit offset is set, and do it in period units.
            $offsetSteps = (int) ($this->totp_time_offset ?? 0);
            $timestamp = $offsetSteps !== 0
                ? $google2fa->getTimestamp() + $offsetSteps
                : null;

            return (bool) $google2fa->verifyKey($secret, $code, $window, $timestamp);
        } catch (\Exception $e) {
            Log::error('Failed to verify 2FA code', [
                'user_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Debug helper only — shows the code the server currently expects.
     * Note: does not account for totp_time_offset (library limitation),
     * so treat it as an approximation on localhost, not an exact match.
     */
    public function getCurrentTotpCode(): string
    {
        $secret = $this->two_factor_secret;
        if (empty($secret)) return '';

        try {
            $google2fa = new Google2FA();
            return (string) $google2fa->getCurrentOtp($secret);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Debug helper — server time / TOTP window info for the localhost panel.
     */
    public function getServerTimeInfo(): array
    {
        $now = time();
        $secondsIntoWindow = $now % 30;

        return [
            'server_time' => $now,
            'server_time_formatted' => date('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
            'totp_time_window' => (int) floor($now / 30),
            'seconds_into_window' => $secondsIntoWindow,
            'seconds_remaining' => 30 - $secondsIntoWindow,
        ];
    }

    public function confirmTwoFactor(): void
    {
        $this->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        cache()->forget('user_2fa_' . $this->id);
    }

    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && !empty($this->two_factor_confirmed_at);
    }

    public function hasTwoFactorSecret(): bool
    {
        return !empty($this->two_factor_secret);
    }

    public function getRemainingRecoveryCodes(): array
    {
        return is_array($this->two_factor_recovery_codes) ? $this->two_factor_recovery_codes : [];
    }

    public function getTwoFactorSecret(): ?string
    {
        return $this->two_factor_secret;
    }

    /**
     * Consume a recovery code. Returns true and removes it from the pool
     * if valid; false otherwise. Each code is single-use.
     */
    public function useRecoveryCode(string $code): bool
    {
        $codes = $this->getRemainingRecoveryCodes();
        $normalized = strtoupper(trim($code));

        if (!in_array($normalized, $codes, true)) {
            return false;
        }

        $remaining = array_values(array_diff($codes, [$normalized]));

        $this->forceFill([
            'two_factor_recovery_codes' => $remaining,
        ])->save();

        return true;
    }

    // ─── Login Security Helpers ─────────────────────────────────────

    public function lockAccount(int $minutes = 15): void
    {
        $this->forceFill([
            'locked_until' => now()->addMinutes($minutes),
            'failed_login_attempts' => 0,
        ])->save();
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function resetFailedLoginAttempts(): void
    {
        $this->forceFill(['failed_login_attempts' => 0])->save();
    }

    public function updateLastLogin(): void
    {
        $this->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ])->save();
    }

    /**
     * Lightweight login audit trail — logs to the app log rather than a
     * dedicated table, since none exists. Swap for a DB write later if
     * you need queryable history.
     */
    public function logLoginActivity(string $ip, bool $twoFactorStage = false, bool $success = true, ?string $reason = null): void
    {
        Log::info('Login activity', [
            'user_id' => $this->id,
            'ip' => $ip,
            'two_factor_stage' => $twoFactorStage,
            'success' => $success,
            'reason' => $reason,
            'time' => now()->toDateTimeString(),
        ]);
    }

    // ─── Relationships ───────────────────────────────────────────────

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function publishedPosts()
    {
        return $this->hasMany(Post::class, 'author_id')->where('status', 'published');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    // ─── Profile Field Accessors ────────────────────────────────────

    public function getAboutMeAttribute(): ?string
    {
        return $this->profile?->about_me;
    }

    public function getSkillsAttribute(): ?array
    {
        return $this->profile?->skills;
    }

    public function getEducationAttribute(): ?array
    {
        return $this->profile?->education;
    }

    public function getSocialLinksAttribute(): ?array
    {
        return $this->profile?->social_links;
    }

    public function getPositionAttribute(): ?string
    {
        return $this->profile?->position;
    }

    public function getIsFeaturedTeamAttribute(): bool
    {
        return $this->profile?->is_featured_team ?? false;
    }

    public function getOrCreateProfile(): UserProfile
    {
        if ($this->profile) {
            return $this->profile;
        }

        return $this->profile()->create([]);
    }

    // ─── Scopes ──────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=3b82f6&color=fff&size=100';
    }

    public function getInitialsAttribute(): string
    {
        $name = $this->name ?? '';
        $parts = explode(' ', trim($name));
        $initials = '';
        foreach ($parts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(mb_substr($part, 0, 1));
            }
        }
        return $initials ?: '?';
    }
    public function sendPasswordResetNotification($token)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $this->email,
        ], false));

        \Illuminate\Support\Facades\Mail::to($this->email)->send(
            new \App\Mail\ResetPasswordMail(
                name: $this->name ?? $this->email,
                email: $this->email,
                resetUrl: $resetUrl,
                expiresInMinutes: config('auth.passwords.users.expire', 60),
            )
        );
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(2));
    }

    public function lastSeenForHumans(): string
    {
        return $this->last_seen_at ? $this->last_seen_at->diffForHumans() : 'Unknown';
    }
    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }
}
