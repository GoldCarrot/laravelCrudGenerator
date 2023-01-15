<?php

namespace Chatway\LaravelCrudGenerator;

use Chatway\LaravelCrudGenerator\Commands\GeneratorCommand;
use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GeneratorCommand::class,
            ]);
        }
    }
}
