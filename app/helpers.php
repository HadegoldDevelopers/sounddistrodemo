<?php
use App\Helpers\ArtistMatcher; 
use App\Services\CurrencyConverter;
use Illuminate\Support\Facades\Artisan;

if (!function_exists('assetPath')) {
    /**
     * Build a public asset URL that works whether the document root is the
     * project root (cPanel-style) or the public/ directory (local / artisan serve).
     */
    function assetPath(?string $path): ?string
    {
        if (!$path || $path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        $publicDir = rtrim(public_path(), '/');
        $docRoot = rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), '/');

        // If the document root is the project root (not public/), files live under /public
        if ($docRoot !== '' && $docRoot !== $publicDir && file_exists($publicDir . '/' . $path)) {
            return asset('public/' . $path);
        }

        return asset($path);
    }
}


if (!function_exists('match_artist_user')) {
    /**
     * Helper function to find user by artist name or IDs.
     */
    function match_artist_user(string $artistName, ?string $spotifyId = null, ?string $appleId = null)
    {
        return ArtistMatcher::matchArtistUser($artistName, $spotifyId, $appleId);
    }
}
if (!function_exists('updateEnv')) {
    function updateEnv($key, $value)
    {
        $path = base_path('.env');
        if (!file_exists($path)) {
            return false;
        }

        // Wrap in quotes if value contains spaces or special characters
        $needsQuotes = preg_match('/[\s#"\'\\\\]/', (string) $value);
        if ($needsQuotes) {
            $value = '"' . str_replace('"', '\\"', $value) . '"';
        }

        $env = file_get_contents($path);

        if (strpos($env, $key . '=') !== false) {
            $env = preg_replace(
                '/^' . preg_quote($key) . '=.*$/m',
                $key . '=' . $value,
                $env
            );
        } else {
            $env .= "\n$key=$value\n";
        }

        file_put_contents($path, $env);

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return true;
    }
}

if (! function_exists('formatCurrency')) {
    /**
     * Format amount in a given currency.
     * 
     * @param float $amount
     * @param string|null $currencyCode Currency code like USD, NGN. If null, use logged-in / session currency.
     * @param int $decimals
     */
    function formatCurrency($amount, $currencyCode = null, $decimals = 2)
    {
        // If currency code provided, use it
        if ($currencyCode) {
            $currency = \App\Services\CurrencyConverter::getByCode($currencyCode);
            $converted = $amount; // Already in that currency
        } else {
            // fallback: use user/session currency
            $currency = \App\Services\CurrencyConverter::convert();
            $converted = $amount * $currency->conversion_rate; // Convert to user currency
        }

        return $currency->symbol . number_format($converted, $decimals);
    }
}

if (! function_exists('convertCurrency')) {

    function convertCurrency(float $amount, string $fromCode, string $toCode): float
    {
        $from = CurrencyConverter::getByCode($fromCode);
        $to   = CurrencyConverter::getByCode($toCode);

        $fromRate = (float) ($from->conversion_rate ?? 0);
        $toRate   = (float) ($to->conversion_rate ?? 0);

        // Guard against zero/missing rates so conversions never divide by
        // zero or silently produce bogus amounts.
        if ($fromRate <= 0 || $toRate <= 0) {
            return $amount;
        }

        $amountInUSD = $amount / $fromRate;

        return $amountInUSD * $toRate;
    }

}