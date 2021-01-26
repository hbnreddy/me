<?php

namespace App\Console\Commands;

use App\Eloquent\Continent;
use App\Eloquent\Country;
use App\Eloquent\EntityBound;
use App\Eloquent\EntityLocation;
use App\EntityType;
use App\Http\Api\Sygic\SygicApi;
use Illuminate\Console\Command;

class FetchExtraCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sygic-fetch:extra-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch extra countries which are not provided by default by Sygic API.';

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
        $extraCountriesIds = [
            'country:37',
            'country:36',
            'country:14',
            'country:13',
            'country:4',
            'country:16',
            'country:34',
        ];

        $europe = Continent::query()
            ->where('name', 'Europe')
            ->first();

        $europeId = $europe->id;

        $entities = json_decode(
            $sygicApi->getPlacesByIds($extraCountriesIds),
            true)['data']['places'];

        foreach ($entities as $entity) {
            $data = [
                'sygic_id' => $entity['id'],
                'continent_id' => $europeId,
                'name' => $entity['name'],
                'name_suffix' => $entity['name_suffix'],
                'description' => $entity['perex'],
                'level' => $entity['level'],
                'rating' => $entity['rating'],
                'rating_local' => $entity['rating_local'],
                'marker' => $entity['marker'],
                'thumbnail_url' => $entity['thumbnail_url'],
            ];

            $temp = Country::query()
                ->updateOrCreate([
                    'sygic_id' => $entity['id'],
                ], $data);

            $location = [
                'entity_id' => $temp->id,
                'entity_type' => EntityType::COUNTRY,
                'lat' => $entity['location']['lat'],
                'lng' => $entity['location']['lng'],
            ];
            EntityLocation::query()
                ->updateOrCreate([
                    'entity_id' => $temp->id,
                    'entity_type' => EntityType::COUNTRY,
                ], $location);

            $bounds = [
                'entity_id' => $temp->id,
                'entity_type' => EntityType::COUNTRY,
                'south' => $entity['bounding_box']['south'],
                'west' => $entity['bounding_box']['west'],
                'north' => $entity['bounding_box']['north'],
                'east' => $entity['bounding_box']['east'],
            ];
            EntityBound::query()
                ->updateOrCreate([
                    'entity_id' => $temp->id,
                    'entity_type' => EntityType::COUNTRY,
                ], $bounds);
        }
    }
}
