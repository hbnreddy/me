<?php

namespace App;

use Illuminate\Support\Facades\Session;

class AppConfig
{
    public static function setCurrency($currency)
    {
        $currency = strtoupper($currency);

        $currencies = AppCache::currencies()
            ->pluck('code')
            ->toArray();

        if (! in_array($currency, $currencies)) {
            Session::put('currency', strtoupper(config('app.fallback_currency')));

            return true;
        }

        Session::put('currency', strtoupper($currency));

        return true;
    }

    public static function getCurrency()
    {
        if (! Session::has('currency')) {
            Session::put('currency', strtoupper(config('app.currency')));
        }

        return Session::get('currency');
    }

    public static function setLocale($locale)
    {
        $locale = strtoupper($locale);

        $languages = AppCache::languages()
            ->pluck('code')
            ->toArray();

        $locale = strtolower($locale);

        $putLocale = config('app.fallback_locale');
        if (in_array($locale, $languages)) {
            $putLocale = $locale;
        }

        Session::put('locale', strtolower($putLocale));

        return true;
    }

    public static function getLocale()
    {
        if (! Session::has('locale')) {
            Session::put('locale', config('app.locale'));
        }

        return Session::get('locale');
    }
}
