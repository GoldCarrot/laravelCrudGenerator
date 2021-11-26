<?php

namespace Chatway\LaravelCrudGenerator\Core\DTO;

use Arr;

/**
 * @property bool $inlet    Внутренний параметр: id, created_at, updated_at, deleted_at
 * @property string $name     Название
 * @property string $type     Тип
 * @property bool $nullable Может быть Null
 * @property string $class    Может быть Null
 */
class PropertyDTO
{
    public $inlet = false;
    public $name;
    public $type;
    public $nullable;
    public $class;
    public $classTable;

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
    }

    public function getNameForSelect()
    {
        return str_replace('_id', '', $this->name);
    }

    public function getForModel()
    {
    }
}
