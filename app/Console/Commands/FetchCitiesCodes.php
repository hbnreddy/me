<?php

namespace App\Console\Commands;

use App\Eloquent\City;
use App\Http\Api\Kiwi\KiwiApi;
use Illuminate\Console\Command;

class FetchCitiesCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kiwi:fetch-cities-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch cities codes using Kiwi API and add them into database';

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
        set_time_limit(0);

        $kiwiApi = resolve(KiwiApi::class);

        $cities = City::all();

        foreach ($cities as $city) {
            $locations = \GuzzleHttp\json_decode($kiwiApi->locations([
                'term' => strtolower($city->name),
            ]), true);

            $code = $locations && count($locations['locations'])
                ? $locations['locations'][0]['code']
                : false;

            if ($code) {
                City::query()
                    ->where('id', $city->id)
                    ->update([
                        'code' => $code,
                    ]);
            }
        }
    }
}
