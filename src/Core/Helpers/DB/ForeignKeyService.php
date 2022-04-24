<?php

namespace Chatway\LaravelCrudGenerator\Core\Helpers\DB;

use DB;
use Illuminate\Support\Collection;

class ForeignKeyService
{
    private function getDBName()
    {
        return config('database.connections.mysql.database');
    }

    /**
     * Description возвращает все ключи объявленные в таблице
     *
     * @param $tableName
     *
     * @return Collection
     */
    public function getExternalKeys($tableName): Collection
    {
        //$query = "SELECT * ";
        //$query .= "FROM information_schema.KEY_COLUMN_USAGE ";
        //$query .= " WHERE ";
        //$query .= "(REFERENCED_TABLE_NAME = '$tableName')";
        //$query .= " AND ";
        //$query .= "TABLE_SCHEMA = '{$this->getDBName()}' AND CONSTRAINT_NAME <>'PRIMARY' AND REFERENCED_TABLE_NAME is not null;";
        //return DB::select($query);
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_NAME', '<>', $tableName)
            ->where('TABLE_SCHEMA', $this->getDBName())
            ->where('CONSTRAINT_NAME', '!=', 'PRIMARY')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->get();
    }

    /**
     * Description возвращает все ключи объявленные в других таблицах, но связанные с запрашиваемой
     *
     * @param $tableName
     *
     * @return Collection
     */
    public function getInternalKeys($tableName): Collection
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_NAME', $tableName)
            ->where('TABLE_SCHEMA', $this->getDBName())
            ->where('CONSTRAINT_NAME', '!=', 'PRIMARY')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->get();

    }
}
