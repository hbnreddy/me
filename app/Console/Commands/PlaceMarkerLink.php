<?php

namespace App\Console\Commands;

use App\Eloquent\Marker;
use App\Eloquent\Place;
use Illuminate\Console\Command;

class PlaceMarkerLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'link:place-marker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link a marker to a place.';

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
        $places = Place::query()
            ->whereNull('marker_id')
            ->get();

        foreach ($places as $place) {
            $marker = Marker::query()
                ->where('name', $place->marker)
                ->first();

            if (! $marker) {
                $marker = Marker::query()
                    ->create([
                        'name' => $place->marker,
                    ]);
            }

            $place->update([
                'marker_id' => $marker->id,
            ]);
        }
    }
}
