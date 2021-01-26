<?php

namespace App\Console\Commands;

use App\Eloquent\City;
use App\Http\Api\Musement\MusementApi;
use Illuminate\Console\Command;

class ComputeCheapestExperience extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compute:cheapest-experience';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Computes cheapest experience for each city';

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
    public function handle()
    {
        $musementApi = resolve(MusementApi::class);

        $cities = City::query()
            ->with([
                'places' => function($query) {
                    return $query
                        ->with(['venue']);
                }
            ])
            ->where('enabled', true)
            ->get();

        foreach ($cities as $city) {
            $price = 99999999;
            foreach ($city->places as $place) {
                if ($place->venue) {
                    $activities = json_decode($musementApi->getVenueActivities($place->venue->foreign_id), true);

                    foreach ($activities as $activity) {
                        if ($activity['retail_price']['value'] < $price) {
                            $price = $activity['retail_price']['value'];
                        }
                    }
                }
            }

            if ($price !== 99999999) {
                City::query()
                    ->where('id', $city->id)
                    ->update([
                        'cheapest_experience_price' => $price,
                    ]);
            }
        }
    }
}
