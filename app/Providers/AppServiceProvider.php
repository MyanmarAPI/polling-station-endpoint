<?php namespace App\Providers;

use App\DataVersion;
use Illuminate\Support\ServiceProvider;
use Hexcores\MongoLite\Connection as MongoConnection;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $connection = MongoConnection::instance();

        $this->app->instance('connection', $connection);

        // Register DataVersion Handler as a singleton
        $this->app->singleton('data_version', function($app) {
            return new DataVersion($app['files']);
        });
    }
}
