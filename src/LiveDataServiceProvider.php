<?php

namespace Kingsley\LiveData;

use Illuminate\Support\ServiceProvider;
use Kingsley\LiveData\Commands\PullDatabase;

class LiveDataServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/livedata.php' => config_path('livedata.php')
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                PullDatabase::class
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/livedata.php', 'livedata');
    }
}
