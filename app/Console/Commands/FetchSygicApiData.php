<?php

namespace App\Console\Commands;

use App\Eloquent\EntityState;
use App\Http\Api\Sygic\SygicApi;
use Illuminate\Console\Command;

class FetchSygicApiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch-api:sygic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from Sygic API and process it to database.';

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

        $sygicApi = resolve(SygicApi::class);
        $entities = [];
        $limit = 100;

        $dataFromApi = json_decode($sygicApi->getContinents(), true);

        $continents = $dataFromApi['data']['places'];
        if (! $continents) {
            $continents = [];
        }

        $entities = array_merge($entities, $continents);

        foreach ($continents as $continent) {
            $countries = json_decode(
                $sygicApi->getCountries([
                    'parents' => [$continent['id']],
                    'limit' => $limit,
                ]),
                true)['data']['places'];

            $entities = array_merge($entities, $countries);

            foreach ($countries as $country) {
                $cities = json_decode(
                    $sygicApi->getCities([
                        'parents' => [$country['id']],
                        'limit' => $limit,
                    ]),
                    true)['data']['places'];

                $entities = array_merge($entities, $cities);
            }
        }

        EntityState::query()->delete();
        foreach ($entities as $entity) {
            EntityState::query()
                ->create([
                    'entity_id' => $entity['id'],
                    'enabled' => false,
                ]);
        }
    }
}
