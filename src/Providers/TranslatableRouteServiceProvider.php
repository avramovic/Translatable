<?php

namespace Avram\Translatable\Providers;

use Avram\Translatable\Translatable;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Routing\Router;

class TranslatableRouteServiceProvider extends RouteServiceProvider
{

    public $routeFiles = ['Http/routes.php'];
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param Router $router
     *
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
     */
    public function map(Router $router)
    {
        /** @var Translatable $translatable */
        $translatable = app(Translatable::class);

        if ($translatable->hasValidLanguageSegment()) {
            $locale = $translatable->getLanguageSegment();
            $router->group(['namespace' => $this->namespace, 'prefix' => $locale], function () {
                foreach ($this->routeFiles as $file) {
                    require app_path($file);
                }
            });
        } else {
            $router->group(['namespace' => $this->namespace], function () {
                foreach ($this->routeFiles as $file) {
                    require app_path($file);
                }
            });
        }

    }
}
