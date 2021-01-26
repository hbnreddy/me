<?php

namespace App\Console\Commands;

use App\Eloquent\City;
use Illuminate\Console\Command;

class SetCitiesCodesByName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:cities-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set cities codes by names because of no matching API.';

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
        $cities = City::query()
            ->whereNull('code')
            ->get();

        foreach ($cities as $city) {
            City::query()
                ->where('id', $city->id)
                ->update([
                    'code' => strtoupper(substr($city->name, 0, 3)),
                    'unsecure_code' => true,
                ]);
        }
    }
}
