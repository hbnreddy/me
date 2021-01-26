<?php

namespace App\Console\Commands;

use App\Eloquent\City;
use App\Eloquent\Continent;
use App\Eloquent\Country;
use App\Eloquent\EntityState;
use App\Eloquent\Place;
use App\EntityType;
use Illuminate\Console\Command;

class RestoreStates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:states';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the states of all entities from previous tables.';

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
        $entityTypes = [
            EntityType::CONTINENT,
            EntityType::COUNTRY,
            EntityType::CITY,
        ];

        foreach ($entityTypes as $entityType) {
            $states = EntityState::query()
                ->where('entity_id', 'LIKE', $entityType.'%')
                ->get();

            foreach ($states as $state) {
                switch ($entityType) {
                    case EntityType::CONTINENT:
                        Continent::query()
                            ->where('sygic_id', $state->entity_id)
                            ->update([
                                'enabled' => $state->enabled,
                            ]);
                        break;
                    case EntityType::COUNTRY:
                        Country::query()
                            ->where('sygic_id', $state->entity_id)
                            ->update([
                                'enabled' => $state->enabled,
                            ]);
                        break;
                    case EntityType::CITY:
                        City::query()
                            ->where('sygic_id', $state->entity_id)
                            ->update([
                                'enabled' => $state->enabled,
                            ]);
                        break;
                    default:
                        break;
                }
            }
        }
    }
}
