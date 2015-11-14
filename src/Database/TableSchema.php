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

        $this->columns = array();

        foreach ($result as $row) {
            $this->columns[] = $this->parseColumn($row);
        }
        var_dump($this->columns);
    }

    private function parseColumn($source)
    {
        //var_dump($source);
        $column = new ColumnInfo();
        $column->table = $this->table;
        $column->name = $source['Field'];
        $this->parseType($source['Type'], $column);
        $column->nullable = 'YES' === $source['Null'] ? true : false;
        $column->primary = 'PRI' === $source['Key'] ? true : false;
        $column->default = $source['Default'];

        return $column;
    }

    private function parseType($source, ColumnInfo &$column)
    {
        if (preg_match('#(?<type>[a-zA-Z][a-zA-Z]+)(\((?<length>\d+)(\,(?<precision>\d+))?\))?#i', $source, $matches)) {
            $column->type = array_key_exists('type', $matches) ? $matches['type'] : null;
            $column->length = array_key_exists('length', $matches) ? $matches['length'] : null;
            $column->numericPrecision = array_key_exists('precision', $matches) ? $matches['precision'] : null;
            $column->numericUnsigned = !(false === strpos($source, 'unsigned'));
        }

        return null;
    }
}