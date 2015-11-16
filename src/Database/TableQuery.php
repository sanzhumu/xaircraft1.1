<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/14
 * Time: 20:55
 */

namespace Xaircraft\Database;


use Xaircraft\Database\Condition\WhereBetweenConditionBuilder;
use Xaircraft\Database\Condition\WhereConditionBuilder;
use Xaircraft\Database\Condition\WhereExistsConditionBuilder;
use Xaircraft\Database\Condition\WhereInConditionBuilder;
use Xaircraft\DI;

class TableQuery implements QueryStringBuilder
{
    /**
     * @var TableSchema
     */
    private $schema;

    /**
     * @var QueryContext
     */
    private $context;

    const QUERY_SELECT = 'QUERY_SELECT';
    const QUERY_INSERT = 'QUERY_INSERT';
    const QUERY_UPDATE = 'QUERY_UPDATE';
    const QUERY_DELETE = 'QUERY_DELETE';
    const QUERY_TRUNCATE = 'QUERY_TRUNCATE';

    private $queryType;

    private $conditions = array();

    private $selectFields = array();

    private $softDeleteLess = true;

    public function __construct($table)
    {
        $this->schema = new TableSchema($table);

        $this->context = DI::get(QueryContext::class);
    }

    public function execute()
    {
        $result = SelectionQueryBuilder::toString($this->context, $this->selectFields) . ' FROM ' . $this->schema->getTableName() . ' WHERE ' . ConditionQueryBuilder::toString($this->conditions);
        var_dump($result);
        var_dump($this->context);
    }

    public function select()
    {
        $this->queryType = self::QUERY_SELECT;

        $fields = array();
        if (func_num_args() > 0) {
            foreach (func_get_args() as $item) {
                $fields[] = FieldInfo::make($item);
            }
        }
        if (1 === func_num_args()) {
            $params = func_get_arg(0);
            if (isset($params) && is_array($params)) {
                $fields = array();
                foreach ($params as $key => $value) {
                    if (!is_string($key)) {
                        $fields[] = FieldInfo::make($value);
                    } else {
                        if (is_callable($value)) {
                            $fields[] = FieldInfo::make($key, $key, $value);
                        } else {
                            $fields[] = FieldInfo::make($value, $key);
                        }
                    }
                }
            }
        }
        $this->selectFields = $fields;

        return $this;
    }

    private function parseWhere($args, $argsLen, $orAnd)
    {
        if (1 === $argsLen) {
            $handler = $args[0];
            if (is_callable($handler)) {
                $this->addCondition(ConditionInfo::make(
                    $orAnd, WhereConditionBuilder::makeClause($this->context, $handler)));
            }
        } else {
            $field = $args[0];
            if (2 === $argsLen) {
                $this->addCondition(ConditionInfo::make(
                    $orAnd, WhereConditionBuilder::makeNormal($this->context, $field, '=', $args[1])));
            }
            if (3 === $argsLen) {
                $this->addCondition(ConditionInfo::make(
                    $orAnd, WhereConditionBuilder::makeNormal($this->context, $field, $args[1], $args[2])
                ));
            }
        }
    }

    public function where()
    {
        $args = func_get_args();
        $argsLen = func_num_args();

        $this->parseWhere($args, $argsLen, ConditionInfo::CONDITION_AND);

        return $this;
    }

    public function orWhere()
    {
        $args = func_get_args();
        $argsLen = func_num_args();

        $this->parseWhere($args, $argsLen, ConditionInfo::CONDITION_OR);

        return $this;
    }

    public function whereBetween($field, array $ranges)
    {
        if (2 === count($ranges)) {
            $this->addCondition(ConditionInfo::make(
                ConditionInfo::CONDITION_AND,
                WhereBetweenConditionBuilder::makeBetween($this->context, $field, $ranges)));
        }

        return $this;
    }

    public function whereNotBetween($field, array $ranges)
    {
        if (2 === count($ranges)) {
            $this->addCondition(ConditionInfo::make(
                ConditionInfo::CONDITION_AND,
                WhereBetweenConditionBuilder::makeNotBetween($this->context, $field, $ranges)));
        }

        return $this;
    }

    private function parseWhereIn($field, $params, $notIn = false)
    {
        if (isset($params) && is_array($params)) {
            $this->addCondition(ConditionInfo::make(
                ConditionInfo::CONDITION_AND,
                WhereInConditionBuilder::makeNormal($this->context, $field, $params, $notIn)
            ));
        } else if (isset($params) && is_callable($params)) {
            $this->addCondition(ConditionInfo::make(
                ConditionInfo::CONDITION_AND,
                WhereInConditionBuilder::makeClause($this->context, $field, $params, $notIn)
            ));
        }
    }

    public function whereIn($field, $params)
    {
        $this->parseWhereIn($field, $params);

        return $this;
    }

    public function whereNotIn($field, $params)
    {
        $this->parseWhereIn($field, $params, true);

        return $this;
    }

    public function whereExists($clause)
    {
        if (isset($clause) && is_callable($clause)) {
            $this->addCondition(ConditionInfo::make(
                ConditionInfo::CONDITION_AND,
                WhereExistsConditionBuilder::make($this->context, $clause)
            ));
        }

        return $this;
    }

    public function orWhereExists($clause)
    {
        if (isset($clause) && is_callable($clause)) {
            $this->addCondition(ConditionInfo::make(
                ConditionInfo::CONDITION_OR,
                WhereExistsConditionBuilder::make($this->context, $clause)
            ));
        }

        return $this;
    }

    public function softDeleteLess()
    {
        $this->softDeleteLess = false;

        return $this;
    }

    public function getQueryString()
    {
        // TODO: Implement getQueryString() method.
    }

    private function addCondition($condition)
    {
        $this->conditions[] = $condition;
    }
}