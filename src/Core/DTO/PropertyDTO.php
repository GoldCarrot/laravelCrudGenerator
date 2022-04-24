<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

use Arr;

class PropertyDTO
{
    public bool $inlet = false; //Внутренний параметр: id, created_at, updated_at, deleted_at
    public string $name; //Название
    public string $type; //Тип
    public bool $nullable;
    public string|null $class;
    public string|null $classTable;
    public bool $isEnum;
    public EnumParams $enum;
    public bool $foreignKeyExists;

    const INLET_PROPERTIES = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function __construct($data)
    {
        $this->name = Arr::get($data, 'name');
        $this->inlet = in_array($this->name, self::INLET_PROPERTIES);
        $this->type = Arr::get($data, 'type');
        $this->nullable = Arr::get($data, 'nullable');
        $this->class = Arr::get($data, 'class');
        $this->classTable = Arr::get($data, 'classTable');
        $this->isEnum = Arr::get($data, 'isEnum', false);
        $this->foreignKeyExists = Arr::get($data, 'foreignKeyExists', false);
    }
}
