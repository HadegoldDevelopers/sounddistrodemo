<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

/**
 * License enforcement client for Hade Gold Media products.
 *
 * The buyer never sees the Envato token. This service talks only to the
 * license server (license.hadegoldmedia.com) using a shared secret, and
 * every response is HMAC-signed so it cannot be tampered with in transit.
 *
 * Behavior:
 *  - verifyOnInstall(): called from the installer with the purchase code.
 *  - checkNow():        invoked on boot/requests; re-verifies the stored
 *                       code at most once per 24h (result cached).
 *  - 1-2 failures       → admin is logged out and blocked, public site OK.
 *  - 3+ failures        → full lockdown (public maintenance page).
 */
class LicenseService
{
    /**
     * Obfuscated license server endpoint.
     *
     * @var string
     */
    protected const ENDPOINT = 'aHR0cHM6Ly9saWNlbnNlLmhhZGVnb2xkbWVkaWEuY29tL2xpY2Vuc2Utc2VydmVyL2luZGV4LnBocA==';

    /**
     * Obfuscated shared secret (also configured on the license server).
     *
     * @var string
     */
    protected const KEY = 'NTFhMGI4MTQ5NGQ2M2FlZjYyZGIyMmYyNjFhZmM5ZGMwMmM4MmUzYjE1OTM0MmYxYWI1YWJjZjk5MGQyNDFkYQ==';

    /**
     * Reason from the last license server response (valid, invalid,
     * domain_limit, envato_error, server_unreachable…).
     *
     * @var string|null
     */
    public ?string $lastReason = null;

    /**
     * The decoded license server URL.
     *
     * @return string
     */
    protected function serverUrl(): string
    {
        return rtrim(env('LICENSE_SERVER_URL', base64_decode(static::ENDPOINT)), '/');
    }

    /**
     * The decoded shared secret.
     *
     * @return string
     */
    protected function sharedSecret(): string
    {
        return base64_decode(static::KEY);
    }

    /**
     * Verify a purchase code at install time.
     *
     * @param  string  $code  CodeCanyon purchase code.
     * @return bool
     */
    public function verifyOnInstall(string $code): bool
    {
        return $this->verify($code) === true;
    }

    /**
     * Verify a purchase code and, on success, mark the license as valid.
     * Used by both the installer and the admin restore page.
     *
     * @param  string  $code
     * @return bool
     */
    public function restore(string $code): bool
    {
        if (!$this->verify($code)) {
            return false;
        }

        Setting::setValue('license_code', $code);
        Setting::setValue('license_failures', 0);
        Setting::setValue('license_state', 'valid');
        Setting::setValue('license_cache', json_encode(['checked_at' => time(), 'valid' => true]));

        return true;
    }

    /**
     * Ask the license server whether a purchase code is valid.
     *
     * @param  string  $code
     * @return bool|null  null when the server was unreachable / unsigned.
     */
    public function verify(string $code): ?bool
    {
        $payload = $this->request($code);

        if ($payload === null) {
            $this->lastReason = 'server_unreachable';
            return null;
        }

        $this->lastReason = $payload['reason'] ?? ($payload['valid'] ? 'valid' : 'invalid');

        return $payload['valid'] === true;
    }

    /**
     * Re-verify the stored purchase code when the 24h cache is stale.
     *
     * @return void
     */
    public function checkNow(): void
    {
        $code = Setting::getValue('license_code', '');

        if ($code === '') {
            $this->recordFailure();
            return;
        }

        $cache = Setting::getValue('license_cache', '');

        if ($cache) {
            $cached = json_decode($cache);

            if (is_array($cached) && $cached['checked_at'] && (time() - (int) $cached['checked_at']) < 86400) {
                return;
            }
        }

        $valid = $this->verify($code);

        Setting::setValue('license_cache', json_encode(['checked_at' => time(), 'valid' => $valid === true]));

        if ($valid === true) {
            Setting::setValue('license_failures', 0);
            Setting::setValue('license_state', 'valid');
        } elseif ($valid === false) {
            // Explicit "invalid" from the license server → escalate.
            $this->recordFailure();
        }
        // null (server unreachable) → leave the current state unchanged so a
        // temporary network blip never locks the site down.
    }

    /**
     * Record a failed verification and escalate the license state.
     *
     * @return void
     */
    public function recordFailure(): void
    {
        $failures = (int) Setting::getValue('license_failures', 0) + 1;

        Setting::setValue('license_failures', $failures);

        if ($failures >= 3) {
            Setting::setValue('license_state', 'lockdown');
        } else {
            Setting::setValue('license_state', 'admin_blocked');
        }
    }

    /**
     * Determine if the license is in full lockdown (public maintenance).
     *
     * @return bool
     */
    public function isLockedDown(): bool
    {
        return Setting::getValue('license_state', 'valid') === 'lockdown';
    }

    /**
     * Determine if the admin area is blocked by a license failure.
     *
     * @return bool
     */
    public function isAdminBlocked(): bool
    {
        return in_array(Setting::getValue('license_state', 'valid'), ['admin_blocked', 'lockdown']);
    }

    /**
     * Get the current number of consecutive failures.
     *
     * @return int
     */
    public function failureCount(): int
    {
        return (int) Setting::getValue('license_failures', 0);
    }

    /**
     * Call the license server for the given purchase code.
     *
     * @param  string  $code
     * @return array|null  null when unreachable or the signature is invalid.
     */
    protected function request(string $code): ?array
    {
        try {
            $response = Http::timeout(15)
                ->asJson()
                ->post($this->serverUrl(), [
                    'code'   => $code,
                    'domain' => config('app.url', ''),
                    'secret' => $this->sharedSecret(),
                ]);

            $payload = $response->json();

            if (!is_array($payload) || !$this->verifySignature($payload)) {
                return null;
            }

            return $payload;
        } catch (Exception $e) {
            \Log::error('License server request failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify the HMAC-SHA256 signature on a license server payload.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function verifySignature(array $payload): bool
    {
        $signature = $payload['signed'] ?? null;

        if (!is_string($signature) || $signature === '') {
            return false;
        }

        $expected = base64_encode(hash_hmac('sha256', $this->canonical($payload), $this->sharedSecret(), true));

        return hash_equals($expected, $signature);
    }

    /**
     * Build the canonical string for a payload (sorted key=value pairs,
     * excluding the signature). Must match the license server exactly.
     *
     * @param  array  $payload
     * @return string
     */
    protected function canonical(array $payload): string
    {
        $map = collect($payload)->except('signed')->toArray();
        $keys = array_keys($map);
        sort($keys);

        $parts = [];

        foreach ($keys as $key) {
            $parts[] = $key . '=' . (is_array($map[$key]) ? json_encode($map[$key]) : (string) $map[$key]);
        }

        return implode('&', $parts);
    }
}