<?php

namespace App\Console\Commands;

use App\Eloquent\City;
use App\Eloquent\Continent;
use App\Eloquent\EntityBound;
use App\Eloquent\EntityLocation;
use App\EntityType;
use App\Http\Api\Sygic\SygicApi;
use Illuminate\Console\Command;

class FetchContinents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sygic-fetch:continents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch continents from Sygic API.';

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
        $entityType = EntityType::CONTINENT;

        Continent::query()->delete();

        EntityBound::query()
            ->where('entity_type', $entityType)
            ->delete();

        EntityLocation::query()
            ->where('entity_type', $entityType)
            ->delete();

        $entities = json_decode(
            $sygicApi->getContinents(),
            true)['data']['places'];

        if (! $entities) {
            return false;
        }

        foreach ($entities as $entity) {
            $data = [
                'sygic_id' => $entity['id'],
                'name' => $entity['name'],
                'name_suffix' => $entity['name_suffix'],
                'description' => $entity['perex'],
                'level' => $entity['level'],
                'rating' => $entity['rating'],
                'rating_local' => $entity['rating_local'],
                'marker' => $entity['marker'],
                'thumbnail_url' => $entity['thumbnail_url'],
            ];

            $temp = Continent::query()
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
