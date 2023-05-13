<?php

namespace Chatway\LaravelCrudGenerator\CoreMigration\DTO;

class ColumnMigrationDTO
{
    public function __construct(
        public $name,
        public $type = 'string',
        public $nullable = true,
        public $foreignUuid = false,
        public $foreignId = false,
        public $default = null,
        public $templateForCreate = null,
        public $templateForDrop = null,

    )
    {
    }
}
