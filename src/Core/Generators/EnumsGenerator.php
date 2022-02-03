<?php

namespace Chatway\LaravelCrudGenerator\Core\Generators;

use Chatway\LaravelCrudGenerator\Core\Base\Generators\BaseEloquentGenerator;
use Chatway\LaravelCrudGenerator\Core\Base\Interfaces\GeneratorInterface;
use Chatway\LaravelCrudGenerator\Core\DTO\EnumParams;
use Chatway\LaravelCrudGenerator\Core\Entities\GeneratorForm;

/**
 * @property EnumParams $enum
 */
class EnumsGenerator extends BaseEloquentGenerator implements GeneratorInterface
{
    public function __construct(public GeneratorForm $generatorForm)
    {
    }

    public function generate()
    {
        foreach ($this->generatorForm->enums as $enum) {
            (new EnumGenerator($this->generatorForm, $enum))->generate();
        }
    }

    public function rollback()
    {
        foreach ($this->generatorForm->enums as $enum) {
            (new EnumGenerator($this->generatorForm, $enum))->rollback();
        }
    }
}
