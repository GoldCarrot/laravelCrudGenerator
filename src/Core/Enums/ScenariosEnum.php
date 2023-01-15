<?php

namespace Chatway\LaravelCrudGenerator\Core\Enums;

use Chatway\LaravelCrudGenerator\Core\Scenarios\AdminScenario;
use Chatway\LaravelCrudGenerator\Core\Scenarios\DefaultScenario;
use Chatway\LaravelCrudGenerator\Core\Scenarios\ModelScenario;

class ScenariosEnum
{
    const DEFAULT = 'default';
    const ADMIN   = 'admin';
    const MODEL   = 'model';

    public array $scenarios = [
        self::DEFAULT => DefaultScenario::class,
        self::ADMIN   => AdminScenario::class,
        self::MODEL   => ModelScenario::class,
    ];
}