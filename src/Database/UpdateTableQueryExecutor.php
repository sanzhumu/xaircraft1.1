<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/19
 * Time: 16:58
 */

namespace Xaircraft\Database;


use Xaircraft\DB;
use Xaircraft\Exception\DataTableException;

class UpdateTableQueryExecutor extends TableQueryExecutor
{
    /**
     * @var TableSchema
     */
    private $schema;
    /**
     * @var QueryContext
     */
    private $context;
    private $updates;
    private $conditions;

    public function __construct(TableSchema $schema, QueryContext $context, array $updates, array $conditions)
    {
        $this->schema = $schema;
        $this->context = $context;
        $this->updates = $updates;
        $this->conditions = $conditions;

        if (empty($conditions)) {
            throw new DataTableException($schema->getSymbol(), "Can't execute UPDATE query without conditions.");
        }
    }

    public function execute()
    {
        $query = $this->toQueryString();

        return DB::update($query, $this->context->getParams());
    }

    public function toQueryString()
    {
        $updatetion = UpdatetionQueryBuilder::toString($this->schema, $this->context, $this->updates);
        $condition = ConditionQueryBuilder::toString($this->conditions);

        $statements = array();

        $statements[] = "UPDATE " . $this->schema->getSymbol();
        $statements[] = $updatetion;
        $statements[] = "WHERE " . $condition;

        return implode(' ', $statements);
    }
}