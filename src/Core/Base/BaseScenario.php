<?php

namespace Chatway\LaravelCrudGenerator\Core\Base;

use Chatway\LaravelCrudGenerator\Core\DTO\ScenarioItem;

/**
 * @property ScenarioItem[] $generators
 */
abstract class BaseScenario
{
    public string $name;

    public array $generators;
}