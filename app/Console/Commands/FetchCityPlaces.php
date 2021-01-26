<?php

namespace App\Console\Commands;

use App\CityPlace;
use App\Eloquent\City;
use App\Http\Api\Sygic\SygicApi;
use Illuminate\Console\Command;

class FetchCityPlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sygic-fetch:city-places';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and map city to places from Sygic API.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param SygicApi $sygicApi
     * @return mixed
     */
    public function handle(SygicApi $sygicApi)
    {
        $cities = City::all();

        foreach ($cities as $city) {
            $poiIds = [];

            if (! isset($poiIds)) {
                $poiIds = [];
            }

            $collections = json_decode($sygicApi
                ->getCollections([
                    'parent_place_id' => $city->sygic_id,
                ]), true)['data']['collections'];

            if (! $collections) {
                $collections = [];
            }

            foreach ($collections as $collection) {
                $filteredPlaces = array_filter($collection['place_ids'], function ($el) {
                    return strpos($el, 'poi:') !== false;
                });

                $poiIds = array_merge($poiIds, $filteredPlaces);
            }

            foreach ($poiIds as $poiId) {
                CityPlace::query()
                    ->updateOrCreate(
                      [
                          'city_id' => $city->id,
                          'sygic_place_id' => $poiId,
                      ],
                      [
                          'city_id' => $city->id,
                          'sygic_city_id' => $city->sygic_id,
                          'sygic_place_id' => $poiId,
                      ]
                    );
            }
        }
    }
}
