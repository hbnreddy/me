<?php

namespace App\Console\Commands;

use App\Eloquent\City;
use App\Eloquent\Place;
use Illuminate\Console\Command;

class ProcessCityPlacesCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:places-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch places count for each city and save it to database.';

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
        $cities = City::all();

        foreach ($cities as $city) {
            $city->update([
                'places_count' => Place::query()
                    ->where('city_id', $city->id)
                    ->distinct()
                    ->count(),
            ]);
        }
    }
}
