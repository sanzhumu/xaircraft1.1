<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/16
 * Time: 13:34
 */

namespace Xaircraft\Database;


use Xaircraft\Database\Condition\WhereConditionBuilder;
use Xaircraft\Database\Condition\WhereInConditionBuilder;
use Xaircraft\Exception\QueryException;

class WhereQuery implements QueryStringBuilder
{
    /**
     * @var QueryContext
     */
    private $context;

    private $subQuery = false;

    private $conditions = array();

    private $selectFields = array();

    /**
     * @var TableSchema
     */
    private $subQueryTableSchema;

    private $softDeleteLess = false;

    private $limit = false;

    private $limitCount;

    private $subQueryLimit = false;

    public function __construct(QueryContext $context, $subQueryLimit = false)
    {
        $this->context = $context;
        $this->subQueryLimit = $subQueryLimit;
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

    public function select()
    {
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

    public function top()
    {
        $this->limit = true;
        $this->limitCount = 1;

        return $this;
    }

    public function from($table)
    {
        $this->subQuery = true;
        $this->subQueryTableSchema = new TableSchema($table);
        $this->context->schema($this->subQueryTableSchema);

        return $this;
    }

    public function softDeleteLess()
    {
        $this->softDeleteLess = true;

        return $this;
    }

    public function getQueryString()
    {
        if (!$this->subQuery) {
            if ($this->subQueryLimit) {
                throw new QueryException("Must be sub-query in [" . ConditionQueryBuilder::toString($this->conditions) . "]");
            }
            return ConditionQueryBuilder::toString($this->conditions);
        } else {
            if (!$this->softDeleteLess) {
                $this->addCondition(ConditionInfo::make(
                    ConditionInfo::CONDITION_AND,
                    WhereConditionBuilder::makeNormal($this->context, $this->subQueryTableSchema->getFieldSymbol(TableSchema::SOFT_DELETE_FIELD, false), '=', 0)
                ));
            }

            $statements = array();
            $statements[] = SelectionQueryBuilder::toString($this->context, $this->selectFields);
            $statements[] = 'FROM ' . $this->subQueryTableSchema->getSymbol();
            $condition = ConditionQueryBuilder::toString($this->conditions);
            if (isset($condition)) {
                $statements[] = "WHERE " . $condition;
            }

            return implode(' ', $statements);
        }
    }

    private function addCondition($condition)
    {
        $this->conditions[] = $condition;
    }
}