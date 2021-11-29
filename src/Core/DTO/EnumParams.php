<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

/**
 * This is the model class for table "event".
 * Class Event
 *
 * @package  Chatway\LaravelCrudGenerator\Core\DTO\EnumParams
 */
class EnumParams
{
    public mixed $enumName;
    public mixed $name;
    public array $types;

    public function __construct($data, $defaultValues)
    {
        $data = explode('-', $data);
        $this->name = \Arr::get($data, 0);
        $this->types = isset($data[1]) && $data[1] && strlen($data[1]) > 0 ? explode(',', $data[1]) : [];
        $this->types = array_diff($this->types, $defaultValues);
    }
}
