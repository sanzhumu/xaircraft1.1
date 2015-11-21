<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/20
 * Time: 15:10
 */

namespace Xaircraft\Nebula;


use Xaircraft\Core\Container;
use Xaircraft\Database\TableQuery;
use Xaircraft\DB;
use Xaircraft\Exception\EntityException;

class Entity
{
    private $fields = array();

    private $exists = false;

    private $shadows;

    /**
     * @var TableQuery
     */
    private $query;

    private $updates = array();
    /**
     * @var \Xaircraft\Database\TableSchema
     */
    private $schema;

    private $autoIncrementField;

    public function __construct($arg)
    {
        if (!isset($arg)) {
            throw new EntityException("Undefined table name or query.");
        }

        if (is_string($arg)) {
            $this->query = DB::table($arg);
        }

        if ($arg instanceof TableQuery) {
            if (TableQuery::QUERY_SELECT !== $this->query->getQueryType()) {
                throw new EntityException("Must be selection table query.");
            }

            $this->query = $arg;
            $this->load();
        }

        $this->schema = $this->query->getTableSchema();
        $this->autoIncrementField = $this->schema->getAutoIncrementField();
    }

    public function save(array $fields = null)
    {
        $this->parseUpdateFields($fields);

        if ($this->exists) {

        } else {

        }
    }

    private function parseUpdateFields($fields)
    {
        if (!empty($fields)) {
            foreach ($fields as $key => $value) {
                $this->setField($key, $value);
            }
        }
    }

    private function setField($field, $value)
    {
        if (!$this->schema->existsField($field)) {
            throw new EntityException("Can't find field [$field] in table [" . $this->schema->getTableName());
        }

        if ($value != $this->shadows[$field]) {
            if ($this->autoIncrementField === $field) {
                if (!$this->exists) {
                    $this->query = DB::table($this->schema->getTableName())->where($field, $value);
                }
            }
            $this->fields[$field] = $value;
            $this->updates[$field] = $value;
        }
    }

    private function load()
    {
        if (isset($this->query)) {
            $result = $this->query->execute();
            if (!empty($result)) {
                if (count($result) > 0) {
                    throw new EntityException("More than one row in table query.");
                }
                $row = $result[0];
                foreach ($row as $key => $value) {
                    $this->fields[$key] = $value;
                }

                $this->shadows = $row;
                $this->exists = true;
            } else {
                $this->exists = false;
            }
        }
    }

    public function __get($field)
    {
        return !empty($this->fields) && array_key_exists($field, $this->fields) ? $this->fields[$field] : null;
    }

    public function __set($field, $value)
    {
        $this->setField($field, $value);
    }
}