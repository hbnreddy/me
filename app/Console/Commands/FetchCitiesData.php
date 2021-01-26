<?php

namespace App\Console\Commands;

use App\Eloquent\City;
use App\Eloquent\MediaResource;
use App\EntityType;
use App\Http\Api\Sygic\SygicApi;
use Illuminate\Console\Command;

class FetchCitiesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sygic-fetch:cities-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch sygic api media for city entities.';

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
     * @return mixed
     */
    public function handle(SygicApi $sygicApi)
    {
        echo 'Start fetching..'.PHP_EOL;

        $cities = City::all();

        MediaResource::query()
            ->where('entity_type', EntityType::CITY)
            ->delete();

        foreach ($cities as $city) {
            $exists = MediaResource::query()
                ->where('entity_id', $city->id)
                ->where('entity_type', EntityType::CITY)
                ->exists();

            if ($exists) {
                continue;
            }

            $entity = json_decode(
                $sygicApi->getPlace($city->sygic_id),
                true)['data']['place'];

            if (! isset($entity['main_media']['media'])) {
                continue;
            }

            foreach ($entity['main_media']['media'] as $media) {
                MediaResource::query()
                    ->create([
                        'entity_id' => $city->id,
                        'entity_type' => EntityType::CITY,
                        'url' => $media['url'],
                        'type' => $media['type'],
                    ]);
            }
        }
    }
}
