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
            __DIR__.'/../config/rating.php' => config_path('rating.php'),
        ], 'config');

        if (! class_exists('CreateRatingsTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_ratings_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_ratings_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rating.php', 'rating');
    }
}
