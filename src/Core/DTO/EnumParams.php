<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

/**
 * @package  Chatway\LaravelCrudGenerator\Core\DTO\EnumParams
 */
class EnumParams
{
    public mixed $enumName;
    public mixed $name;
    public array $types;
    public bool  $isDefaultStatus = false;

    public function __construct($data, $defaultValues, $isDefaultStatus = false)
    {
        $data = explode('-', $data);
        $this->name = \Arr::get($data, 0);
        $this->types = isset($data[1]) && $data[1] && strlen($data[1]) > 0 ? explode(',', $data[1]) : [];
        $this->types = count($this->types) > 0 ? $this->types : $defaultValues;
        $this->isDefaultStatus = $isDefaultStatus;
    }

    public function getFirstType()
    {
        return $this->types[0] ?? null;
    }
}
