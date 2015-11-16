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

    public function __construct(TableSchema $schema, QueryContext $context, $softDeleteLess, array $selectFields, array $conditions)
    {
        $this->schema = $schema;
        $this->selectFields = $selectFields;
        $this->conditions = $conditions;
        $this->softDeleteLess = $softDeleteLess;
        $this->context = $context;
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
        $condition = ConditionQueryBuilder::toString($this->conditions);

        if (isset($condition)) {
            $condition = " WHERE $condition";
        }

        return $selection . $condition;
    }
}