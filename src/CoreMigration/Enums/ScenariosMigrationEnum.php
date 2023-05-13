<?php

namespace Chatway\LaravelCrudGenerator\CoreMigration\Enums;

use Chatway\LaravelCrudGenerator\CoreMigration\Scenarios\DefaultScenarioMigration;
use Chatway\LaravelCrudGenerator\CoreMigration\Scenarios\TemplateScenarioMigration;

class ScenariosMigrationEnum
{
    const DEFAULT = 'default';
    const TEMPLATE   = 'template';

    public array $scenarios = [
        self::DEFAULT => DefaultScenarioMigration::class,
        self::TEMPLATE   => TemplateScenarioMigration::class,
    ];

}
