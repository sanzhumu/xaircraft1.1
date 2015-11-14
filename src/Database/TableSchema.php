<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/14
 * Time: 21:06
 */

namespace Xaircraft\Database;


use Xaircraft\DB;
use Xaircraft\Exception\DatabaseException;
use Xaircraft\Exception\DataTableException;

class TableSchema
{
    private $table;

    private $columns;

    public function __construct($table)
    {
        if (!isset($table) || !(preg_match('#[a-zA-Z][0-9a-zA-Z\_]#i', $table) > 0)) {
            throw new DataTableException($table, "Table name invalid - [$table]");
        }

        $this->table = $table;

        $this->initializeColumns();
    }

    private function initializeColumns()
    {
        $result = DB::query("SHOW COLUMNS FROM $this->table");
        if (false === $result) {
            throw new DataTableException($this->table, "Table not exists - [$this->table]");
        }
    }
}