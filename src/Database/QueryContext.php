<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/14
 * Time: 20:32
 */

namespace Xaircraft\Database;


use Xaircraft\Exception\QueryException;

class QueryContext
{
    private $schemas = array();

    private $parameters = array();

    public function param($value)
    {
        $this->parameters[] = $value;
    }

    public function getParams()
    {
        return $this->parameters;
    }

    public function schema(TableSchema $schema)
    {
        $this->schemas[$schema->getTableName()] = $schema;
    }

    public function getField($field, $prefix = null)
    {
        if (empty($this->schemas)) {
            return $field;
        }

        $count = $this->fieldExistsCountInSchemas($field, $prefix);
        if (0 === $count) {
            throw new QueryException("Field [$field] not exists in any schema.");
        }
        var_dump($field);
        var_dump($prefix);
        var_dump($count);
        if (1 < $count) {
            throw new QueryException("Field [$field] ambiguous.");
        }
        /** @var TableSchema $schema */
        foreach ($this->schemas as $key => $schema) {
            if (isset($prefix) && $prefix !== $schema->getFieldPrefix(true)) {
                continue;
            }
            $result = $this->parseField($schema, $field);
            if (false !== $result) {
                return $result;
            }
        }
        return $field;
    }

    private function fieldExistsCountInSchemas($field, $prefix = null)
    {
        if (empty($this->schemas)) {
            return true;
        }

        $count = 0;

        /** @var TableSchema $schema */
        foreach ($this->schemas as $key => $schema) {
            if (false !== array_search($field, $schema->columns())) {
                if (isset($prefix) && $prefix !== $schema->getFieldPrefix(true)) {
                    continue;
                }
                $count++;
            }
        }
        return $count;
    }

    private function parseField(TableSchema $schema, $field)
    {
        if (false === array_search($field, $schema->columns())) {
            return false;
        }
        return $schema->getFieldPrefix() . ".`" . $field . "`";
    }
}