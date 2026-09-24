<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LeadSpamFilter
{
    /**
     * A small list of common disposable / throwaway email domains.
     * Extend this over time based on what actually reaches your inbox.
     */
    protected const DISPOSABLE_DOMAINS = [
        // Big generic disposables
        'mailinator.com',
        'guerrillamail.com',
        'guerrillamail.net',
        'guerrillamail.org',
        'sharklasers.com',
        'grr.la',
        'spam4.me',
        '10minutemail.com',
        '10minutemail.net',
        '10minutesemail.net',
        'tempmail.com',
        'temp-mail.org',
        'tempmail.net',
        'tempr.email',
        'throwawaymail.com',
        'throwaway.email',
        'yopmail.com',
        'yopmail.fr',
        'yopmail.net',
        'maildrop.cc',
        'mailnesia.com',
        'mailcatch.com',
        'getnada.com',
        'nada.email',
        'trashmail.com',
        'trashmail.net',
        'fakeinbox.com',
        'spamgourmet.com',
        'discard.email',
        'mintemail.com',
        'mytrashmail.com',
        'wegwerfmail.de',
        'mailtemp.info',
        'moakt.com',
        'mohmal.com',
        'tempinbox.com',
        'tempemail.com',
        '1secmail.com',
        'emailondeck.com',
        'mytemp.email',
        'burnermail.io',
    ];

    /**
     * Local-part patterns that are almost always bot/fake.
     * Kept small — only the obvious ones. Anything wider risks
     * rejecting real users who happen to have short addresses.
     */
    protected const FAKE_LOCAL_PARTS = [
        'test',
        'tester',
        'testing',
        'asdf',
        'asdfasdf',
        'qwerty',
        'qwertyuiop',
        'aaa',
        'zzz',
        'xxx',
        'noreply',
        'no-reply',
        'donotreply',
        'do-not-reply',
        'admin',
        'administrator',
        'example',
        'yourname',
        'firstname',
        'lastname',
        'aaaa',
        'bbbb',
        'cccc',
        '123456',
        '12345',
        'null',
        'undefined',
        'none',
        'na',
    ];

    /**
     * Domains that look real but are reserved for testing/documentation.
     */
    protected const RESERVED_DOMAINS = [
        'example.com',
        'example.org',
        'example.net',
        'test.com',
        'test.org',
        'test.net',
        'localhost',
        'invalid',
    ];

    /**
     * Return the spam verdict for a given email/message pair.
     *
     * @return array{is_spam: bool, reason: ?string}
     */
    public function evaluate(string $email, string $message = ''): array
    {
        $email = strtolower(trim($email));

        if ($email === '' || ! str_contains($email, '@')) {
            return ['is_spam' => true, 'reason' => 'Malformed email'];
        }

        [$local, $domain] = explode('@', $email, 2);
        $local  = (string) $local;
        $domain = (string) $domain;

        // ─── 1. Reserved test domains ──────────────────────────────
        if (in_array($domain, self::RESERVED_DOMAINS, true)) {
            return ['is_spam' => true, 'reason' => 'Reserved test domain'];
        }

        // ─── 2. Disposable email providers ─────────────────────────
        if (in_array($domain, self::DISPOSABLE_DOMAINS, true)) {
            return ['is_spam' => true, 'reason' => 'Disposable email domain'];
        }

        // ─── 3. Fake local-part patterns ───────────────────────────
        if (in_array($local, self::FAKE_LOCAL_PARTS, true)) {
            return ['is_spam' => true, 'reason' => 'Fake local part'];
        }

        // Numeric-only local part (12345@)
        if (preg_match('/^\d+$/', $local)) {
            return ['is_spam' => true, 'reason' => 'Numeric-only local part'];
        }

        // Repeated single character (aaa@, 111@)
        if (preg_match('/^(.)\1{3,}$/', $local)) {
            return ['is_spam' => true, 'reason' => 'Repeated character local part'];
        }

        // ─── 4. Domain format sanity ───────────────────────────────
        if (! preg_match('/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9\-]*[a-z0-9])?)+$/i', $domain)) {
            return ['is_spam' => true, 'reason' => 'Malformed domain'];
        }

        // TLD must be at least 2 alpha characters
        $tld = substr(strrchr($domain, '.'), 1);
        if (! preg_match('/^[a-z]{2,}$/', (string) $tld)) {
            return ['is_spam' => true, 'reason' => 'Invalid TLD'];
        }

        // ─── 5. MX record check (cached) ────────────────────────────
        // If the domain has no MX records it cannot receive mail,
        // which almost always means a fake. Skipped silently if
        // the lookup itself fails (DNS issues, offline, etc.).
        if (! $this->domainAcceptsMail($domain)) {
            return ['is_spam' => true, 'reason' => 'Domain cannot receive email (no MX records)'];
        }

        // ─── 6. Very short obvious spam messages ────────────────────
        // (Optional heuristic — disable if it causes false positives.)
        $message = trim($message);
        if ($message !== '' && mb_strlen($message) < 3) {
            return ['is_spam' => true, 'reason' => 'Message too short'];
        }

        return ['is_spam' => false, 'reason' => null];
    }

    /**
     * Cached MX lookup. Returns true when we're not sure (fail-open),
     * so a flaky DNS server never causes real leads to be rejected.
     */
    protected function domainAcceptsMail(string $domain): bool
    {
        $cacheKey = 'lead-mx-check:' . $domain;
        $ttl      = now()->addHours(24);

        return Cache::remember($cacheKey, $ttl, function () use ($domain) {
            try {
                $records = @dns_get_record($domain, DNS_MX);
            } catch (\Throwable $e) {
                Log::warning('MX lookup threw', ['domain' => $domain, 'error' => $e->getMessage()]);
                return true; // fail-open
            }

            if ($records === false) {
                // Lookup itself failed — do NOT mark as spam
                return true;
            }

            // Any MX record means the domain can receive mail
            return ! empty($records);
        });
    }
}
