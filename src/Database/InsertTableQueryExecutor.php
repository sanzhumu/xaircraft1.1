<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/19
 * Time: 20:25
 */

namespace Xaircraft\Database;


use Xaircraft\DB;
use Xaircraft\Exception\DatabaseException;
use Xaircraft\Exception\DataTableException;

class InsertTableQueryExecutor extends TableQueryExecutor
{
    /**
     * @var TableSchema
     */
    private $schema;
    /**
     * @var QueryContext
     */
    private $context;
    private $inserts;

    /**
     * InsertTableQueryExecutor constructor.
     * @param $schema
     * @param $context
     * @param $inserts
     */
    public function __construct(TableSchema $schema, QueryContext $context, array $inserts)
    {
        $this->schema = $schema;
        $this->context = $context;
        $this->inserts = $inserts;
    }

    public function execute()
    {
        $query = $this->toQueryString();

        return DB::insert($query, $this->context->getParams());
    }

    public function toQueryString()
    {
        if (empty($this->inserts)) {
            throw new DataTableException($this->schema->getTableName(), "Can't execute INSERT without inserts.");
        }

        $columns = $this->schema->columns();
        foreach ($columns as $item) {
            if (!array_key_exists($item, $this->inserts) &&
                !$this->schema->field($item)->nullable &&
                !$this->schema->field($item)->autoIncrement) {
                throw new DataTableException($this->schema->getTableName(), "Field [$item] can't be null.");
            }
        }

        $fields = array();
        $values = array();

        foreach ($this->inserts as $key => $value) {
            if (!$this->schema->existsField($key)) {
                throw new DataTableException(
                    $this->schema->getTableName(),
                    "Not exists field [$key] in table [" . $this->schema->getTableName() . "]."
                );
            }
            if ($this->schema->field($key)->autoIncrement) {
                throw new DataTableException(
                    $this->schema->getTableName(),
                    "Can't set auto-increment field [$key] in insert query."
                );
            }

            $fields[] = $key;
            $values[] = "?";
            $this->context->param($value);
        }

        $statements = array(
            "INSERT INTO",
            $this->schema->getTableName(),
            "(",
            implode(',', $fields),
            ")VALUES(",
            implode(',', $values),
            ")"
        );

        return implode(' ', $statements);
    }
}