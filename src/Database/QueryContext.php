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

    /**
     * @var TableSchema[]
     */
    private $subQuerySchemas = array();

    private $parameters = array();

    public function param($value)
    {
        $this->parameters[] = $value;
    }

    public function getParams()
    {
        return $this->parameters;
    }

    public function schema(TableSchema $schema, $isSubSchema = false)
    {
        if (!$isSubSchema) {
            $this->schemas[$schema->getSymbol()] = $schema;
        } else {
            $this->subQuerySchemas[$schema->getSymbol()] = $schema;
        }
    }

    public function getField($field, $prefix = null, $isSubQueryField = false)
    {
        if (!isset($field)) {
            throw new QueryException("Invalid field.");
        }

        $schemas = $isSubQueryField ? $this->subQuerySchemas : $this->schemas;

        if (empty($schemas)) {
            return $field;
        }

        $count = $this->fieldExistsCountInSchemas($field, $prefix, $isSubQueryField);
        if (0 === $count) {
            throw new QueryException("Field [$field] not exists in any schema.");
        }
        if (1 < $count) {
            throw new QueryException("Field [$field] ambiguous.");
        }
        /** @var TableSchema $schema */
        foreach ($schemas as $key => $schema) {
            if (isset($prefix) && $prefix !== $schema->getPrefix(false)) {
                continue;
            }
            $result = $this->parseField($schema, $field);
            if (false !== $result) {
                return $result;
            }
        }
        return $field;
    }

    private function fieldExistsCountInSchemas($field, $prefix = null, $isSubQueryField = false)
    {
        $schemas = $isSubQueryField ? $this->subQuerySchemas : $this->schemas;

        if (empty($schemas)) {
            return true;
        }

        $count = 0;

        /** @var TableSchema $schema */
        foreach ($schemas as $key => $schema) {
            if (false !== array_search($field, $schema->columns())) {
                if (isset($prefix) && $prefix !== $schema->getPrefix(false)) {
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
        return $schema->getFieldSymbol($field);
    }
}