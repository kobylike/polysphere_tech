<?php

namespace App\Livewire\Main\Partials;

use App\Models\CookieConsent as CookieConsentModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class CookieConsent extends Component
{
    /**
     * Bump this whenever the cookie policy meaningfully changes.
     * Existing consent (DB or cookie) on an older version is treated as stale.
     */
    public const CONSENT_VERSION = 1;

    public const COOKIE_NAME = 'ps_cookie_consent';

    public const COOKIE_LIFETIME_MINUTES = 60 * 24 * 365; // 1 year

    public const DEFAULT_CATEGORIES = [
        'necessary'  => true,
        'functional' => false,
        'analytics'  => false,
        'marketing'  => false,
    ];

    public array $categories = self::DEFAULT_CATEGORIES;

    public bool $showBanner = false;
    public bool $showModal = false;
    public bool $hasConsented = false;

    public function mount(): void
    {
        // 1. Logged-in users: DB is the source of truth, so consent follows them across devices.
        if (Auth::check() && $this->restoreFromDatabase()) {
            return;
        }

        // 2. No DB record (guest, or a user who consented before logging in on this device):
        //    fall back to the real HTTP cookie set by a previous visit.
        if ($this->restoreFromCookie()) {
            return;
        }

        // 3. Nobody's consented yet on this account or this device.
        $this->showBanner = true;
    }

    protected function restoreFromDatabase(): bool
    {
        $existing = CookieConsentModel::query()
            ->latestFor(Auth::id(), self::CONSENT_VERSION)
            ->first();

        if (! $existing) {
            return false;
        }

        $this->applyCategories($existing->categories);
        $this->hasConsented = true;
        $this->showBanner = false;

        // Keep this device's cookie in sync with the DB record.
        $this->writeCookie();

        $this->dispatch('cookie-consent-restored', categories: $this->categories, version: self::CONSENT_VERSION);

        return true;
    }

    protected function restoreFromCookie(): bool
    {
        $cookie = request()->cookie(self::COOKIE_NAME);

        if (! $cookie) {
            return false;
        }

        $data = json_decode($cookie, true);

        if (! is_array($data) || ($data['version'] ?? null) !== self::CONSENT_VERSION || ! is_array($data['categories'] ?? null)) {
            return false;
        }

        $this->applyCategories($data['categories']);
        $this->hasConsented = true;
        $this->showBanner = false;

        // They just logged in and had a valid guest consent — migrate it into the DB now.
        if (Auth::check()) {
            $this->persistToDatabase(CookieConsentModel::METHOD_RESTORED);
        }

        return true;
    }

    #[On('open-cookie-settings')]
    public function openModal(): void
    {
        $this->showModal = true;
        $this->showBanner = false;

        $this->dispatch('cookie-consent-modal-toggled', open: true);
    }

    public function closeModal(): void
    {
        $this->showModal = false;

        if (! $this->hasConsented) {
            $this->showBanner = true;
        }

        $this->dispatch('cookie-consent-modal-toggled', open: false);
    }

    public function toggleCategory(string $key): void
    {
        if ($key === 'necessary' || ! array_key_exists($key, $this->categories)) {
            return;
        }

        $this->categories[$key] = ! $this->categories[$key];
    }

    public function acceptAll(): void
    {
        $this->applyCategories(array_fill_keys(array_keys(self::DEFAULT_CATEGORIES), true));
        $this->commit(CookieConsentModel::METHOD_ACCEPT_ALL);
    }

    public function rejectAll(): void
    {
        $this->applyCategories(self::DEFAULT_CATEGORIES);
        $this->commit(CookieConsentModel::METHOD_REJECT_ALL);
    }

    public function savePreferences(): void
    {
        $this->applyCategories($this->categories);
        $this->commit(CookieConsentModel::METHOD_CUSTOM);
    }

    /**
     * Withdraw everything but strictly necessary cookies. Wire this up to a
     * "Manage cookies" / privacy-settings page for GDPR's right to withdraw.
     */
    public function revoke(): void
    {
        $this->applyCategories(self::DEFAULT_CATEGORIES);
        $this->hasConsented = false;

        Cookie::queue(Cookie::forget(self::COOKIE_NAME));

        if (Auth::check()) {
            CookieConsentModel::query()
                ->latestFor(Auth::id(), self::CONSENT_VERSION)
                ->update(['revoked_at' => now()]);
        }

        $this->dispatch('cookie-consent-revoked');
        $this->showBanner = true;
    }

    protected function applyCategories(array $categories): void
    {
        $this->categories = array_merge(self::DEFAULT_CATEGORIES, $categories);
        $this->categories['necessary'] = true;
    }

    protected function commit(string $method): void
    {
        $this->hasConsented = true;
        $this->showBanner = false;
        $this->showModal = false;

        $this->dispatch('cookie-consent-modal-toggled', open: false);

        $this->writeCookie();
        $this->persistToDatabase($method);

        Log::info('cookie-consent commit', [
            'method'     => $method,
            'auth_check' => Auth::check(),
            'user_id'    => Auth::id(),
            'categories' => $this->categories,
        ]);

        // Guests and authenticated users both get the browser-side copy —
        // JS listens for this to write localStorage and let other scripts
        // (analytics/marketing tags) know what's allowed.
        $this->dispatch(
            'cookie-consent-saved',
            categories: $this->categories,
            version: self::CONSENT_VERSION,
        );
    }

    /**
     * Set the real, server-side HTTP cookie. Doing this in PHP (rather than
     * relying only on JS after the dispatch) means consent still persists
     * even if a script blocker, ad blocker, or JS error stops the browser
     * listener from running.
     */
    protected function writeCookie(): void
    {
        Cookie::queue(
            name: self::COOKIE_NAME,
            value: json_encode([
                'version'    => self::CONSENT_VERSION,
                'categories' => $this->categories,
            ]),
            minutes: self::COOKIE_LIFETIME_MINUTES,
            path: '/',
            domain: null,
            secure: request()->secure(),
            httpOnly: false, // readable client-side so tag-manager scripts can respect it
            sameSite: 'lax',
        );
    }

    protected function persistToDatabase(string $method): void
    {
        CookieConsentModel::create([
            'user_id'      => Auth::id(),
            'session_id'   => Auth::check() ? null : session()->getId(),
            'version'      => self::CONSENT_VERSION,
            'categories'   => $this->categories,
            'method'       => $method,
            'ip_address'   => request()->ip(),
            'user_agent'   => substr((string) request()->userAgent(), 0, 255),
            'consented_at' => now(),
        ]);
    }

    public function render()
    {
        return view('livewire.main.partials.cookie-consent');
    }
}
