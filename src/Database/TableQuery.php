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
use Xaircraft\Exception\DataTableException;
use Xaircraft\Exception\QueryException;

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

    private $softDeleteLess = false;

    private $joins = array();

    private $orders = array();

    private $contextLock = false;

    private $groups = array();

    private $havings = array();

    private $selectQuerySettings = array();

    private $updates;

    private $inserts;

    public function __construct($table, QueryContext $context = null)
    {
        $this->schema = new TableSchema($table);

        $this->context = isset($context) ? $context : DI::get(QueryContext::class);
    }

    public function execute()
    {
        if ($this->contextLock) {
            throw new DataTableException(
                $this->schema->getTableName(),
                "Can't execute the TableQuery when the query has been run [getQueryString()] method.
                You should try [DB::getQueryString()]
                "
            );
        }

        $tableQueryExecutor = $this->parseTableQuery();

        $result = null;
        if (isset($tableQueryExecutor)) {
            $result = $tableQueryExecutor->execute();
        }
        unset($this->context);
        return $result;
    }

    private function parseTableQuery()
    {
        switch ($this->queryType) {
            case self::QUERY_SELECT:
                return TableQueryExecutor::makeSelect(
                    $this->schema,
                    $this->context,
                    $this->softDeleteLess,
                    $this->selectFields,
                    $this->conditions,
                    $this->joins,
                    $this->orders,
                    $this->groups,
                    $this->havings,
                    $this->selectQuerySettings
                );
            case self::QUERY_UPDATE:
                return TableQueryExecutor::makeUpdate(
                    $this->schema,
                    $this->context,
                    $this->updates,
                    $this->conditions
                );
            case self::QUERY_DELETE:
                return TableQueryExecutor::makeDelete(
                    $this->schema,
                    $this->context,
                    $this->conditions
                );
            case self::QUERY_INSERT:
                return TableQueryExecutor::makeInsert(
                    $this->schema,
                    $this->context,
                    $this->inserts
                );
        }

        return null;
    }

    public function update(array $updates)
    {
        $this->queryType = self::QUERY_UPDATE;

        $this->updates = $updates;

        return $this;
    }

    public function insert(array $inserts)
    {
        $this->queryType = self::QUERY_INSERT;

        $this->inserts = $inserts;

        return $this;
    }

    public function delete()
    {
        $this->queryType = self::QUERY_DELETE;

        return $this;
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

    public function take($count)
    {
        $this->queryType = self::QUERY_SELECT;

        if ($count <= 0) {
            throw new QueryException("Selection query error. take $count.");
        }

        $this->selectQuerySettings['take_count'] = $count;

        return $this;
    }

    public function skip($offset)
    {
        $this->queryType = self::QUERY_SELECT;

        if ($offset <= 0) {
            throw new QueryException("Selection query error. skip $offset.");
        }

        $this->selectQuerySettings['skip_offset'] = $offset;

        return $this;
    }

    public function pluck($field)
    {
        $this->queryType = self::QUERY_SELECT;

        $this->selectQuerySettings['pluck'] = true;

        $this->select($field)->take(1);

        return $this;
    }

    public function orderBy($field, $sort = OrderInfo::SORT_ASC)
    {
        $this->orders[] = OrderInfo::make($field, $sort);

        return $this;
    }

    public function groupBy()
    {
        $fields = func_get_args();
        $this->groups[] = GroupInfo::make($fields);

        return $this;
    }

    public function having($field)
    {
        $args = func_get_args();
        $argsLen = func_num_args();

        if (2 === $argsLen) {
            $this->havings[] = HavingInfo::make($field, '=', $args[1]);
        }

        if (3 === $argsLen) {
            $this->havings[] = HavingInfo::make($field, $args[1], $args[2]);
        }

        return $this;
    }

    public function join($table)
    {
        $args = func_get_args();
        $argsLen = func_num_args();

        $this->parseJoin($table, $args, $argsLen, false);

        return $this;
    }

    public function leftJoin($table)
    {
        $args = func_get_args();
        $argsLen = func_num_args();

        $this->parseJoin($table, $args, $argsLen, true);

        return $this;
    }

    private function parseJoin($table, $args, $argsLen, $leftJoin = false)
    {
        if (2 === $argsLen) {
            $clause = $args[1];
            if (isset($clause) && is_callable($clause)) {
                $this->joins[] = JoinInfo::makeClause($table, $clause, $leftJoin);
            } else {
                throw new QueryException("Join query error - it should be a sub-query clause here. [$table]");
            }
        }
        if (3 === $argsLen) {
            $this->joins[] = JoinInfo::makeNormal(
                $table,
                JoinConditionInfo::make(ConditionInfo::CONDITION_AND, $args[1], '=', $args[2], false),
                $leftJoin
            );
        }
        if (4 === $argsLen) {
            $this->joins[] = JoinInfo::makeNormal(
                $table,
                JoinConditionInfo::make(ConditionInfo::CONDITION_AND, $args[1], $args[2], $args[3], false),
                $leftJoin
            );
        }
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
        $this->softDeleteLess = true;

        return $this;
    }

    public function getQueryString()
    {
        $tableQueryExecutor = $this->parseTableQuery();

        $this->contextLock = true;

        if (isset($tableQueryExecutor)) {
            return $tableQueryExecutor->toQueryString();
        }
        return null;
    }

    private function addCondition($condition)
    {
        $this->conditions[] = $condition;
    }
}