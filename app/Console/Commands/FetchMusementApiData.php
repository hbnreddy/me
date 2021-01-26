<?php

namespace App\Console\Commands;

use App\Eloquent\Venue;
use App\Http\Api\Musement\MusementApi;
use App\Http\Api\Musement\MusementConfig;
use Illuminate\Console\Command;

class FetchMusementApiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch-api:musement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from Musement API and process it to database.';

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
        set_time_limit(0); // This can be remove on live server.

        $musementApi = resolve(MusementApi::class);
        $limit = MusementConfig::DEFAULT_LIMIT;
        $offset = 0;

        $venues = \GuzzleHttp\json_decode($musementApi->getVenues([
            'limit' => $limit,
            'offset' => $offset,
        ]), true);
        while ($venues) {
            foreach ($venues as $venue) {
                Venue::query()
                    ->create([
                        'name' => $venue['name'],
                        'foreign_id' => $venue['id'],
                    ]);
            }

            $offset += $limit;

            $venues = \GuzzleHttp\json_decode($musementApi->getVenues([
                'limit' => $limit,
                'offset' => $offset,
            ]), true);
        }
    }
}
