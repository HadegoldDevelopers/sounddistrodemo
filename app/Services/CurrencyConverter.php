<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CurrencyConverter
{
    public static function convert()
    {
        // Logged-in user currency
        if (Auth::check() && Auth::user()->currency) {
            return Auth::user()->currency;
        }
        if (Session::has('currency_id')) {
            return Currency::find(Session::get('currency_id'));
        }
        $countryCode = request()->header('CF-IPCountry', 'US');

        $currency = Currency::where('cf_code', $countryCode)
            ->where('is_active', 1)
            ->first();
        if (! $currency) {
            $currency = Currency::where('code', 'USD')->first();
        }

        // Cache for future requests
        Session::put('currency_id', $currency->id);

        return $currency;
    }
    
     public static function getByCode(string $code)
    {
        return Currency::where('code', strtoupper($code))->firstOrFail();
    }
}
