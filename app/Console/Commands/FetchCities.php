<?php

namespace App\Console\Commands;

use App\Eloquent\City;
use App\Eloquent\Country;
use App\Eloquent\EntityBound;
use App\Eloquent\EntityLocation;
use App\EntityType;
use App\Http\Api\Sygic\SygicApi;
use Illuminate\Console\Command;

class FetchCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sygic-fetch:cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch cities from Sygic API.';

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
        $entityType = EntityType::CITY;

        City::query()->delete();

        EntityBound::query()
            ->where('entity_type', $entityType)
            ->delete();

        EntityLocation::query()
            ->where('entity_type', $entityType)
            ->delete();

        $countries = Country::all();

        foreach ($countries as $country) {
            $entities = json_decode(
                $sygicApi->getCities([
                    'parents' => [$country->sygic_id],
                ]),
                true)['data']['places'];

            if (! $entities) {
                continue;
            }

            foreach ($entities as $entity) {
                $data = [
                    'sygic_id' => $entity['id'],
                    'country_id' => $country->id,
                    'name' => $entity['name'],
                    'name_suffix' => $entity['name_suffix'],
                    'description' => $entity['perex'],
                    'level' => $entity['level'],
                    'rating' => $entity['rating'],
                    'rating_local' => $entity['rating_local'],
                    'marker' => $entity['marker'],
                    'thumbnail_url' => $entity['thumbnail_url'],
                ];

                $temp = City::query()
                    ->updateOrCreate([
                        'sygic_id' => $entity['id'],
                    ], $data);

                $location = [
                    'entity_id' => $temp->id,
                    'entity_type' => $entityType,
                    'lat' => $entity['location']['lat'],
                    'lng' => $entity['location']['lng'],
                ];
                EntityLocation::query()
                    ->updateOrCreate([
                        'entity_id' => $temp->id,
                        'entity_type' => $entityType,
                    ], $location);

                $bounds = [
                    'entity_id' => $temp->id,
                    'entity_type' => $entityType,
                    'south' => $entity['bounding_box']['south'],
                    'west' => $entity['bounding_box']['west'],
                    'north' => $entity['bounding_box']['north'],
                    'east' => $entity['bounding_box']['east'],
                ];
                EntityBound::query()
                    ->updateOrCreate([
                        'entity_id' => $temp->id,
                        'entity_type' => $entityType,
                    ], $bounds);
            }
        }
    }
}
