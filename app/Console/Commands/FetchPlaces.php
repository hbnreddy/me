<?php

namespace App\Console\Commands;

use App\CityPlace;
use App\Eloquent\City;
use App\Eloquent\EntityBound;
use App\Eloquent\EntityLocation;
use App\Eloquent\EntityReference;
use App\Eloquent\EntityState;
use App\Eloquent\MediaResource;
use App\Eloquent\Place;
use App\Eloquent\PlaceDetail;
use App\Eloquent\PlaceTag;
use App\EntityType;
use App\Http\Api\Sygic\SygicApi;
use App\Http\Api\Sygic\SygicConfig;
use Illuminate\Console\Command;

class FetchPlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sygic-fetch:places';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all places setting up the city which belongs to.';

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
        echo 'Fetch Sygic Places started.'.PHP_EOL;

        $cityPlaces = CityPlace::query()
            ->join('places', 'places.sygic_id', '=', 'city_places.sygic_place_id')
            ->get()
            ->pluck('sygic_id')
            ->toArray();

        $cityPlaces = CityPlace::query()
            ->whereNotIn('sygic_place_id', $cityPlaces)
            ->get();

        foreach ($cityPlaces as $cityPlace) {
            $entity = json_decode(
                $sygicApi->getPlace($cityPlace->sygic_place_id),
                true)['data']['place'];

            if (! $entity) {
                continue;
            }

            $temp = Place::query()
                ->updateOrCreate(
                    [
                        'sygic_id' => $cityPlace->sygic_place_id,
                    ],
                    [
                        'city_id' => $cityPlace->city_id,
                        'sygic_id' => $cityPlace->sygic_place_id,
                        'name' => $entity['name'],
                        'name_suffix' => $entity['name_suffix'],
                        'short_description' => $entity['perex'],
                        'description' => $entity['description']['text'],
                        'level' => $entity['level'],
                        'rating' => $entity['rating'],
                        'rating_local' => $entity['rating_local'],
                        'marker' => $entity['marker'],
                        'thumbnail_url' => $entity['thumbnail_url'],
                    ]
                );

            PlaceDetail::query()
                ->updateOrCreate(
                    [
                        'place_id' => $temp->id,
                    ],
                    [
                        'place_id' => $temp->id,
                        'address' => $entity['address'],
                        'email' => $entity['email'],
                        'phone' => $entity['phone'],
                        'opening_hours' => $entity['opening_hours'],
                        'is_deleted' => $entity['is_deleted'],
                    ]
                );

            if (isset($entity['location']['lat'])) {
                EntityLocation::query()
                    ->updateOrCreate(
                        [
                            'entity_id' => $temp->id,
                        ],
                        [
                            'entity_id' => $temp->id,
                            'entity_type' => EntityType::PLACE,
                            'lat' => $entity['location']['lat'],
                            'lng' => $entity['location']['lng'],
                        ]);
            }

            if (isset($entity['bounding_box']['south']) && $entity['bounding_box']['south']) {
                EntityBound::query()
                    ->updateOrCreate(
                        [
                            'entity_id' => $temp->id,
                        ],
                        [
                            'entity_id' => $temp->id,
                            'entity_type' => EntityType::PLACE,
                            'south' => $entity['bounding_box']['south'],
                            'west' => $entity['bounding_box']['west'],
                            'north' => $entity['bounding_box']['north'],
                            'east' => $entity['bounding_box']['east'],
                        ]);
            }

            foreach ($entity['references'] as $reference) {
                EntityReference::query()
                    ->updateOrCreate([
                        'entity_id' => $temp->id,
                        'entity_type' => EntityType::PLACE,
                        'url' => $reference['url'],
                    ],
                        [
                            'entity_id' => $temp->id,
                            'entity_type' => EntityType::PLACE,
                            'title' => $reference['title'],
                            'type' => $reference['type'],
                            'url' => $reference['url'],
                        ]
                    );
            }

            if (isset($entity['main_media']['media'])) {
                foreach ($entity['main_media']['media'] as $media) {
                    if (! isset($media['url'])) {
                        continue;
                    }

                    MediaResource::query()
                        ->updateOrCreate(
                            [
                                'entity_id' => $temp->id,
                                'entity_type' => EntityType::PLACE,
                                'url' => $media['url'],
                            ],
                            [
                                'entity_id' => $temp->id,
                                'entity_type' => EntityType::PLACE,
                                'url' => $media['url'],
                                'type' => $media['type'],
                            ]
                        );
                }
            }
        }

        echo 'DONE!'.PHP_EOL;
    }
}
