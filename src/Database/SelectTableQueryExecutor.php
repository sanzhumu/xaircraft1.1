<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/16
 * Time: 20:01
 */

namespace Xaircraft\Database;


use Xaircraft\Database\Condition\WhereConditionBuilder;
use Xaircraft\DB;

class SelectTableQueryExecutor extends TableQueryExecutor
{
    private $schema;

    private $selectFields;

    private $conditions;

    private $softDeleteLess;

    /**
     * @var QueryContext
     */
    private $context;

    private $joins;

    private $orders;

    private $groups;

    private $havings;

    public function __construct(
        TableSchema $schema,
        QueryContext $context,
        $softDeleteLess,
        array $selectFields,
        array $conditions,
        array $joins,
        array $orders,
        array $groups,
        array $havings)
    {
        $this->schema = $schema;
        $this->selectFields = $selectFields;
        $this->conditions = $conditions;
        $this->softDeleteLess = $softDeleteLess;
        $this->context = $context;
        $this->joins = $joins;
        $this->orders = $orders;
        $this->groups = $groups;
        $this->havings = $havings;
    }

    public function execute()
    {
        $query = $this->toQueryString();

        return DB::select($query, $this->context->getParams());
    }

    public function toQueryString()
    {
        if ($this->schema->getSoftDelete() && !$this->softDeleteLess) {
            $this->conditions[] = ConditionInfo::make(
                ConditionInfo::CONDITION_AND,
                WhereConditionBuilder::makeNormal($this->context, TableSchema::SOFT_DELETE_FIELD, '=', 0)
            );
        }

        $selection = SelectionQueryBuilder::toString($this->context, $this->selectFields) . ' FROM ' . $this->schema->getTableName();
        $join = JoinQueryBuilder::toString($this->context, $this->joins);
        $condition = ConditionQueryBuilder::toString($this->conditions);
        $orders = OrderQueryBuilder::toString($this->orders);
        $groups = GroupQueryBuilder::toString($this->groups);
        $havings = HavingQueryBuilder::toString($this->havings);

        $statements = array($selection);

        if (isset($join)) {
            $statements[] = $join;
        }

        if (isset($condition)) {
            $statements[] = "WHERE $condition";
        }

        if (isset($orders)) {
            $statements[] = "ORDER BY $orders";
        }

        if (isset($groups)) {
            $statements[] = "GROUP BY $groups";
        }

        if (isset($havings)) {
            $statements[] = "HAVING ($havings)";
        }

        return implode(' ', $statements);
    }
}