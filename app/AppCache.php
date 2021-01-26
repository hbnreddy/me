<?php

namespace App;

use App\Eloquent\City;
use App\Eloquent\Currency;
use App\Eloquent\Language;
use App\Eloquent\Theme;
use App\Http\Api\Musement\MusementApi;
use Illuminate\Support\Facades\Cache;

class AppCache
{
    public static function themes()
    {
        return Cache::rememberForever('themes', function () {
            return Theme::all();
        });
    }

    public static function currencies()
    {
        return Cache::rememberForever('currencies', function () {
            return Currency::all();
        });
    }

    public static function languages()
    {
        return Cache::rememberForever('languages', function () {
            return Language::all();
        });
    }

    public static function cityCheapestExperience($id)
    {
        return Cache::remember('city.'.$id.'cheapest-'.AppConfig::getCurrency(), 60 * 60 * 2, function () use ($id) {
            $musementApi = resolve(MusementApi::class);

            $places = City::query()
                ->find($id)
                ->places;

            $price = 99999999;
            foreach ($places as $place) {
                if ($place->venue) {
                    $activities = json_decode($musementApi->getVenueActivities($place->venue->foreign_id), true);

                    foreach ($activities as $activity) {
                        if ($activity['retail_price']['value'] < $price) {
                            $price = $activity['retail_price']['value'];
                        }
                    }
                }
            }

            return $price;
        });
    }

    public static function musementCredentials()
    {
        if (! Cache::get('musement.credentials')) {
            Cache::forget('musement.credentials');
        }

        return Cache::remember('musement.credentials', 3600, function () {
            $musementApi = resolve(MusementApi::class);

            return json_decode($musementApi->login(), true);
        });
    }
}
