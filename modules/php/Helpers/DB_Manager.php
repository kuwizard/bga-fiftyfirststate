<?php

namespace Bga\Games\Fiftyfirststate\Helpers;

use APP_DbObject;
use feException;

class DB_Manager extends APP_DbObject
{
    protected static $table = null;
    protected static $primary = null;
    protected static $log = null;

    protected static function cast($row)
    {
        return $row;
    }

    public static function DB($table = null)
    {
        if (is_null($table)) {
            if (is_null(static::$table)) {
                throw new feException('You must specify the table you want to do the query on');
            }
            $table = static::$table;
        }

        $log = null;
        return new QueryBuilder(
            $table,
            function ($row) {
                return static::cast($row);
            },
            static::$primary,
            $log
        );
    }
}
