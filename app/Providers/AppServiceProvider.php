<?php

namespace App\Providers;

use App\Http\Api\Kiwi\KiwiApi;
use App\Http\Api\Musement\MusementApi;
use App\Http\Api\Sygic\SygicApi;
use App\RideService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RideService::class, function ($api) {
            return new RideService();
        });

        $this->app->singleton(KiwiApi::class, function ($api) {
            return new KiwiApi([
                'api_url' => config('app.kiwi_api_url'),
                'api_key' => config('app.kiwi_client_key'),
                'api_multi_key' => config('app.kiwi_multi_client_key'),
            ]);
        });

        $this->app->singleton(SygicApi::class, function ($api) {
            return new SygicApi([
                'api_url' => config('app.sygic_api_url'),
                'api_key' => config('app.sygic_client_key'),
            ]);
        });

        $this->app->singleton(MusementApi::class, function ($api) {
            return new MusementApi([
                'api_url' => config('app.musement_api_url'),
                'api_key' => config('app.musement_client_id'),
                'api_secret' => config('app.musement_client_secret'),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(320);
    }
}
