<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/19
 * Time: 20:00
 */

namespace Xaircraft\Database;


use Xaircraft\DB;
use Xaircraft\Exception\DataTableException;

class DeleteTableQueryExecutor extends TableQueryExecutor
{
    /**
     * @var TableSchema
     */
    private $schema;
    /**
     * @var QueryContext
     */
    private $context;
    private $conditions;
    private $forceDelete = false;

    public function __construct(TableSchema $schema, QueryContext $context, $conditions, $forceDelete)
    {
        $this->schema = $schema;
        $this->context = $context;
        $this->conditions = $conditions;
        $this->forceDelete = $forceDelete;
    }

    public function execute()
    {
        if (!$this->forceDelete && $this->schema->getSoftDelete()) {
            $executor = TableQueryExecutor::makeUpdate($this->schema, $this->context, array(
                TableSchema::SOFT_DELETE_FIELD => time()
            ), $this->conditions);

            return $executor->execute();
        }

        $query = $this->toQueryString();

        return DB::delete($query, $this->context->getParams());
    }

    public function toQueryString()
    {
        if (empty($this->conditions)) {
            throw new DataTableException($this->schema->getSymbol(), "Can't execute DELETE query without conditions.");
        }

        $statements = array(
            "DELETE FROM",
            $this->schema->getSymbol(),
            "WHERE",
            ConditionQueryBuilder::toString($this->conditions)
        );

        return implode(' ', $statements);
    }
}