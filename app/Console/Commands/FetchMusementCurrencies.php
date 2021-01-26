<?php

namespace App\Console\Commands;

use App\Eloquent\Currency;
use App\Http\Api\Musement\MusementApi;
use Illuminate\Console\Command;

class FetchMusementCurrencies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch currencies from Musement API.';

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

        $currencies = json_decode($musementApi->getCurrencies(), true);

        Currency::query()->delete();
        foreach ($currencies as $currency) {
            Currency::query()
                ->create($currency);
        }
    }
}
