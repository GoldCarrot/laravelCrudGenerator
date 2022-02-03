<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;

/**
 * @property string $viewName
 */
class ViewsGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public mixed $viewName;

    public function __construct(public GeneratorForm $generatorForm, private $viewList = ['create', 'form', 'index', 'show', 'update'], $config = [])
    {
        $this->viewName = \Arr::get($config, 'viewName');
    }

    public function generate()
    {
        foreach ($this->viewList as $item) {
            (new ViewGenerator($this->generatorForm, ['viewName' => $item]))->generate();
        }
    }

    public function rollback()
    {
        foreach ($this->viewList as $item) {
            (new ViewGenerator($this->generatorForm, ['viewName' => $item]))->rollback();
        }
    }
}
