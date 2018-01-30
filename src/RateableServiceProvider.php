<?php

namespace Yoeunes\Rateable;

use Illuminate\Support\ServiceProvider;

class RateableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/rateable.php' => config_path('rateable.php'),
        ], 'config');

        if (! class_exists('CreateRatingsTable')) {
            $this->publishes([
                __DIR__.'/../migrations/create_ratings_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_ratings_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rateable.php', 'rateable');
    }
}
