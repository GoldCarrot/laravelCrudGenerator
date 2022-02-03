<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;

class ControllersGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public function __construct(private GeneratorForm $generatorForm)
    {
    }

    public function generate()
    {
        foreach ($this->generatorForm->controllers as $controller) {
            (new ControllerGenerator($this->generatorForm, $controller))->generate();
        }
    }

    public function rollback()
    {
        foreach ($this->generatorForm->controllers as $controller) {
            (new ControllerGenerator($this->generatorForm, $controller))->rollback();
        }
    }
}
