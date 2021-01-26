<?php

namespace App\Console\Commands;

use App\Eloquent\Venue;
use App\Http\Api\Musement\MusementApi;
use Illuminate\Console\Command;

class FetchVenues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'musement-fetch:venues';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all venues from musement api.';

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
    public function handle(MusementApi $musementApi)
    {
        $count = 100;
        $offset = 0;
        while ($count >= 100) {
            $entities = \GuzzleHttp\json_decode($musementApi->getVenues([
                'offset' => $offset,
            ]), true);

            foreach ($entities as $entity) {
                $data = [
                    'foreign_id' => $entity['id'],
                    'name' => $entity['name'],
                    'country_name' => $entity['city']['country']['name'],
                    'city_name' => $entity['city']['name'],
                    'place_id' => 0,
                ];

                Venue::query()
                    ->updateOrCreate(['foreign_id' => $entity['id']], $data);
            }

            $offset += 100;
            $count = count($entities);
        }

        echo 'DONE::'.$offset.PHP_EOL;
    }
}
