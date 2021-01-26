<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StoreConfigFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It stores the JSON config file for the application.';

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
        //
    }
}
