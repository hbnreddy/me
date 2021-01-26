<?php

namespace App\Console\Commands;

use App\Eloquent\Marker;
use App\Eloquent\Place;
use App\Eloquent\Venue;
use Illuminate\Console\Command;

class PlaceVenueLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'link:place-venue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link a venue to a place.';

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
        $places = Place::all();

        foreach ($places as $place) {
            $venue = Venue::query()
                ->where('poi_id', $place->sygic_id)
                ->first();

            if (! $venue) {
                continue;
            }

            $place->update([
                'venue_id' => $venue->foreign_id,
            ]);
        }
    }
}
