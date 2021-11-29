<?php

namespace Chatway\LaravelCrudGenerator\Core\Helpers\DB;

use DB;

class ForeignKeyService
{
    private function getDBName()
    {
        return config('database.connections.mysql.database');
    }

    /**
     * Description возвращает все ключи объявленные в таблице
     * @param $tableName
     * @return array
     */
    public function getExternalKeys($tableName): array
    {
        $query = "SELECT * ";
        $query .= "FROM information_schema.KEY_COLUMN_USAGE ";
        $query .= " WHERE ";
        $query .= "(REFERENCED_TABLE_NAME = '$tableName')";
        $query .= " AND ";
        $query .= "TABLE_SCHEMA = '{$this->getDBName()}' AND CONSTRAINT_NAME <>'PRIMARY' AND REFERENCED_TABLE_NAME is not null;";
        return DB::select($query);
    }

    /**
     * Description возвращает все ключи объявленные в других таблицах, но связанные с запрашиваемой
     *
     * @param $tableName
     *
     * @return \Illuminate\Support\Collection
     */
    public function getInternalKeys($tableName)
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_NAME', $tableName)
            ->where('TABLE_SCHEMA', $this->getDBName())
            ->where('CONSTRAINT_NAME', '!=', 'PRIMARY')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->get();

    }
}
