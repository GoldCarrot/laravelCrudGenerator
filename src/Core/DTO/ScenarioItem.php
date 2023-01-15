<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

class ScenarioItem
{
    public function __construct(
        public string $abstract,
        public array $options
    )
    {
    }
}