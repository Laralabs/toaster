<?php

namespace Laralabs\Toaster;

use Illuminate\Support\ServiceProvider;

class ToasterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            'Laralabs\Toaster\Interfaces\SessionStore',
            'Laralabs\Toaster\LaravelSessionStore'
        );

        $this->app->singleton('toaster', function () {
            return $this->app->make('Laralabs\Toaster\Toaster');
        });

        $this->app->singleton('toasterConverter', function ($app) {
            $view = config('toaster.bind_js_vars_to_this_view');
            $namespace = config('toaster.js_namespace');

            $binder = new ToasterViewBinder($app['events'], $view);

            return new ToasterConverter($binder, $namespace);
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/toaster.php', 'toaster'
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/toaster.php'  => config_path('toaster.php'),
        ], 'config');
    }
}
