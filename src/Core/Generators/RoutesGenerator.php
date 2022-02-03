<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;

class RoutesGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public function __construct(public GeneratorForm $generatorForm)
    {
    }

    public function generate()
    {
        foreach ($this->generatorForm->routeTemplates as $routeTemplate) {
            (new RouteGenerator($this->generatorForm, $routeTemplate))->generate();
        }
    }

    public function rollback()
    {
        foreach ($this->generatorForm->routeTemplates as $routeTemplate) {
            (new RouteGenerator($this->generatorForm, $routeTemplate))->rollback();
        }
    }
}
