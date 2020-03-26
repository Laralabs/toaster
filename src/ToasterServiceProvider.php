<?php

namespace Laralabs\Toaster;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ToasterServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Laralabs\Toaster\Interfaces\SessionStore',
            'Laralabs\Toaster\LaravelSessionStore'
        );

        $this->app->singleton('toaster', function () {
            return $this->app->make('Laralabs\Toaster\Toaster');
        });

        $this->app->singleton('toasterViewBinder', function () {
            return $this->app->make('Laralabs\Toaster\ToasterViewBinder');
        });

        $this->mergeConfigFrom(
            __DIR__.'/../config/toaster.php',
            'toaster'
        );
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/toaster.php'  => config_path('toaster.php'),
        ], 'config');

        Blade::directive('toaster', function () {
            return "<?php echo app('".ToasterViewBinder::class."')->bind(); ?>";
        });

        Blade::directive('toastcomponent', function () {
            return "<?php echo app('".ToasterViewBinder::class."')->component(); ?>";
        });
    }
}
