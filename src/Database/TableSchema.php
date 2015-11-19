<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/14
 * Time: 21:06
 */

namespace Xaircraft\Database;


use Xaircraft\App;
use Xaircraft\Core\Container;
use Xaircraft\Core\IO\File;
use Xaircraft\DB;
use Xaircraft\Exception\DatabaseException;
use Xaircraft\Exception\DataTableException;

class TableSchema extends Container
{
    const SOFT_DELETE_FIELD = 'delete_at';
    const RESERVED_FIELD_UPDATE_AT = 'update_at';
    const RESERVED_FIELD_CREATE_AT = 'create_at';

    private $table;

    private $columns;

    private $source;

    private $canSoftDelete = false;

    private $autoIncrementField;

    public function __construct($table)
    {
        if (!isset($table) || !(preg_match('#[a-zA-Z][0-9a-zA-Z\_]#i', $table) > 0)) {
            throw new DataTableException($table, "Table name invalid - [$table]");
        }

        $this->table = $table;

        $schema = App::path('schema');
        if (!isset($schema)) {
            throw new DatabaseException("Can't find schema cache directory.");
        }

        $this->source = $schema . '/' . DB::getDatabaseName() . '/' . $table . '.dat';

        $this->initialize();
    }

    public function getTableName()
    {
        return $this->table;
    }

    public function getSoftDelete()
    {
        return $this->canSoftDelete;
    }

    public function fields()
    {
        return $this->columns;
    }

    public function columns()
    {
        $columns = array();
        foreach ($this->columns as $key => $value) {
            $columns[] = $key;
        }
        return $columns;
    }

    /**
     * @param $field
     * @return ColumnInfo
     */
    public function field($field)
    {
        return array_key_exists($field, $this->columns) ? $this->columns[$field] : null;
    }

    public function existsField($field)
    {
        if (isset($field)) {
            return array_key_exists($field, $this->columns);
        }

        return false;
    }

    private function initialize()
    {
        if (!$this->loadFromCache()) {
            $this->initializeColumns();
        }
    }

    private function initializeColumns()
    {
        $result = DB::query("SHOW FULL COLUMNS FROM $this->table");
        if (false === $result) {
            throw new DataTableException($this->table, "Table not exists - [$this->table]");
        }

        $this->columns = array();

        foreach ($result as $row) {
            $column = $this->parseColumn($row);
            $this->columns[$column->name] = $column;
        }
        $this->writeCache();
    }

    private function parseColumn($source)
    {
        $column = new ColumnInfo();
        $column->table = $this->table;
        $column->name = $source['Field'];
        $this->parseType($source['Type'], $column);
        $column->nullable = 'YES' === $source['Null'] ? true : false;
        $column->primary = 'PRI' === $source['Key'] ? true : false;
        $column->default = $source['Default'];
        $column->autoIncrement = !(false === strpos($source['Extra'], 'auto_increment'));
        $column->comment = $source['Comment'];
        $column->collationName = $source['Collation'];

        if (self::SOFT_DELETE_FIELD === $column->name) {
            $this->canSoftDelete = true;
        }

        if ($column->autoIncrement) {
            $this->autoIncrementField = $column->name;
        }

        return $column;
    }

    private function parseType($source, ColumnInfo &$column)
    {
        if (preg_match('#(?<type>[a-zA-Z][a-zA-Z]+)(\((?<length>\d+)(\,(?<precision>\d+))?\))?#i', $source, $matches)) {
            $column->type = array_key_exists('type', $matches) ? $matches['type'] : null;
            $column->length = array_key_exists('length', $matches) ? $matches['length'] : null;
            $column->numericPrecision = array_key_exists('precision', $matches) ? $matches['precision'] : null;
            $column->numericUnsigned = !(false === strpos($source, 'unsigned'));
            $column->numericPrecision = array_key_exists('precision', $matches) ? $matches['precision'] : null;

            if ('enum' === strtolower($column->type)) {
                if (preg_match('#enum\((?<enums>(\'([a-zA-Z0-9\_]+)\',?)+)\)#i', $source, $matches)) {
                    $enums = str_replace("'", "", $matches['enums']);
                    $column->enums = explode(',', $enums);
                }
            }
        }
    }

    private function loadFromCache()
    {
        $cache = File::readText($this->source);
        if (false !== $cache) {
            $result = unserialize($cache);

            if (isset($result) && $result instanceof TableSchema) {
                $this->table = $result->table;
                $this->columns = $result->columns;
                $this->source = $result->source;
                $this->autoIncrementField = $result->autoIncrementField;
                $this->canSoftDelete = $result->canSoftDelete;

                return true;
            }
        }
        return false;
    }

    private function writeCache()
    {
        File::writeText($this->source, serialize($this));
    }
}