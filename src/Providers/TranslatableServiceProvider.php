<?php

namespace Avram\Translatable\Providers;

use Avram\Translatable\Translatable;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class TranslatableServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(Translatable::class, function (Application $app) {
            return new Translatable($app->make(Request::class));
        });

        $this->publishes([
            realpath( __DIR__ . ('/../../migrations')) => base_path('database/migrations'),
            realpath( __DIR__ . ('/../../seeds')) => base_path('database/seeds'),
        ], 'migrations');

        $this->publishes([
            realpath( __DIR__ . ('/../../config')) => base_path('config'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
