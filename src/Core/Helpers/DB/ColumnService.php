<?php

namespace Chatway\LaravelCrudGenerator\Core\Helpers\DB;

class ColumnService
{
    const TYPE_PK = 'pk';
    const TYPE_UPK = 'upk';
    const TYPE_BIGPK = 'bigpk';
    const TYPE_UBIGPK = 'ubigpk';
    const TYPE_CHAR = 'char';
    const TYPE_STRING = 'string';
    const TYPE_TEXT = 'text';
    const TYPE_TINYINT = 'tinyint';
    const TYPE_SMALLINT = 'smallint';
    const TYPE_INTEGER = 'integer';
    const TYPE_INT = 'int';
    const TYPE_BIGINT = 'bigint';
    const TYPE_BIGINT_UNSIGNED = 'bigint unsigned';
    const TYPE_FLOAT = 'float';
    const TYPE_DOUBLE = 'double';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_TIME = 'time';
    const TYPE_DATE = 'date';
    const TYPE_BINARY = 'binary';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_MONEY = 'money';
    const TYPE_JSON = 'json';

    public $name;
    public $type;
    public $nulable;

    public static function getColumnsByTableName($tableName)
    {
        return \DB::select('show columns from `' . config('database.connections.mysql.database') . '`.`' . $tableName . '`');
    }
}
