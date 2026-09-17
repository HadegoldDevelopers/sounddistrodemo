<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Self-contained Envato purchase-code verification for Distro Supawave.
 *
 * Verifies a CodeCanyon purchase code directly against Envato's API
 * (api.envato.com) on the buyer's own server. There is no third-party
 * license server, no phone-home, no domain-locking and no lockdown —
 * once a valid code is confirmed the result is stored locally and the
 * application runs fully standalone.
 *
 * Configuration (.env):
 *   ENVATO_PERSONAL_TOKEN  Your Envato personal API token.
 *   ENVATO_ITEM_ID         Your CodeCanyon item id (verifies the code
 *                          belongs to this item).
 *
 * If the token is not configured, verification is skipped and reported
 * as unavailable so a fresh installation is never blocked.
 */
class LicenseService
{
    /**
     * Reason from the last verification attempt (valid, invalid,
     * wrong_item, envato_error, unavailable…).
     *
     * @var string|null
     */
    public ?string $lastReason = null;

    /**
     * The configured Envato personal token, or null when unset.
     */
    protected function token(): ?string
    {
        $token = config('services.envato.token');

        return is_string($token) && $token !== '' ? $token : null;
    }

    /**
     * The configured CodeCanyon item id, or null when unset.
     */
    protected function itemId(): ?int
    {
        $id = config('services.envato.item_id');

        return $id ? (int) $id : null;
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
     * Used by the installer. The result is stored locally only.
     *
     * @param  string  $code
     * @return bool
     */
    public function restore(string $code): bool
    {
        if (!$this->verify($code)) {
            return false;
        }

        \App\Models\Setting::setValue('license_code', $code);
        \App\Models\Setting::setValue('license_state', 'valid');

        return true;
    }

    /**
     * Ask Envato whether a purchase code is valid.
     *
     * @param  string  $code
     * @return bool|null  true = valid, false = invalid, null = unavailable / unreachable.
     */
    public function verify(string $code): ?bool
    {
        $token = $this->token();

        if (!$token) {
            $this->lastReason = 'unavailable';
            Log::info('Envato verification skipped: no ENVATO_PERSONAL_TOKEN configured.');

            return null;
        }

        try {
            $response = Http::timeout(15)
                ->withToken($token)
                ->acceptJson()
                ->get('https://api.envato.com/v3/market/author/sale', ['code' => $code]);
        } catch (\Throwable $e) {
            $this->lastReason = 'envato_error';
            Log::error('Envato verification request failed: ' . $e->getMessage());

            return null;
        }

        if ($response->status() === 404) {
            $this->lastReason = 'invalid';

            return false;
        }

        if (!$response->successful()) {
            $this->lastReason = 'envato_error';
            Log::warning('Envato verification failed', ['status' => $response->status()]);

            return null;
        }

        $sale = $response->json();

        if (($sale['item']['id'] ?? null) != $this->itemId()) {
            $this->lastReason = 'wrong_item';

            return false;
        }

        $this->lastReason = 'valid';

        return true;
    }
}