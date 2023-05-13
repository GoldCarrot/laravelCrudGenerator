<?php

namespace Chatway\LaravelCrudGenerator;

use Chatway\LaravelCrudGenerator\Commands\GeneratorCommand;
use Chatway\LaravelCrudGenerator\Commands\MigrationCreatorCommand;
use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
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
                MigrationCreatorCommand::class
            ]);
        }
    }
}
