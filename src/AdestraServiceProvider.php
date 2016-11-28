<?php

namespace Jzpeepz\Adestra;

use Illuminate\Support\ServiceProvider;

class AdestraServiceProvider extends ServiceProvider
{
    protected $commands = [];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/adestra.php' => config_path('adestra.php'),
        ], 'adestra');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
