<?php

namespace Chatway\LaravelCrudGenerator;

use Chatway\LaravelCrudGenerator\Commands\GeneratorAdminCommand;
use Chatway\LaravelCrudGenerator\Commands\GeneratorCommand;
use Chatway\LaravelCrudGenerator\Commands\GeneratorControllerCommand;
use Chatway\LaravelCrudGenerator\Commands\GeneratorModelCommand;
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
                GeneratorAdminCommand::class,
                GeneratorModelCommand::class,
                GeneratorControllerCommand::class,
            ]);
        }
    }
}
