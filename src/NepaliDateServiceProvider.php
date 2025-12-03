<?php

namespace Jeeven\NepaliDateConverter;

use Illuminate\Support\ServiceProvider;

class NepaliDateServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/nepali-date.php', 'nepali-date');

        $this->app->singleton('nepali-date', function () {
            return new NepaliDateConverter();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/nepali-date.php' => config_path('nepali-date.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/js/nepaliDateConverter.js' => public_path('vendor/nepali-date/nepaliDateConverter.js'),
        ], 'assets');
    }
}
