<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/19
 * Time: 20:25
 */

namespace Xaircraft\Database;


use Xaircraft\DB;
use Xaircraft\Exception\DataTableException;
use Xaircraft\Exception\FieldValidateException;

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
    private $insertGetId;

    /**
     * InsertTableQueryExecutor constructor.
     * @param $schema
     * @param $context
     * @param $inserts
     */
    public function __construct(TableSchema $schema, QueryContext $context, array $inserts, $insertGetId)
    {
        $this->schema = $schema;
        $this->context = $context;
        $this->inserts = $inserts;
        $this->insertGetId = $insertGetId;
    }

    public function execute()
    {
        $query = $this->toQueryString();

        if (true === $this->insertGetId) {
            if (true === DB::insert($query, $this->context->getParams())) {
                return DB::table($this->schema->getTableName())
                    ->orderBy($this->schema->getAutoIncrementField(), 'DESC')
                    ->pluck($this->schema->getAutoIncrementField())
                    ->execute();
            }
        }
        return DB::insert($query, $this->context->getParams());
    }

    public function toQueryString()
    {
        if (empty($this->inserts)) {
            throw new DataTableException($this->schema->getTableName(), "Can't execute INSERT without inserts.");
        }

        if ($this->schema->existsField(TableSchema::RESERVED_FIELD_CREATE_AT) &&
            !array_key_exists(TableSchema::RESERVED_FIELD_CREATE_AT, $this->inserts)) {
            $this->inserts[TableSchema::RESERVED_FIELD_CREATE_AT] = time();
        }

        $columns = $this->schema->columns();
        foreach ($columns as $item) {
            if (!array_key_exists($item, $this->inserts) &&
                !$this->schema->field($item)->nullable &&
                !$this->schema->field($item)->autoIncrement) {
                throw new FieldValidateException(
                    $this->schema->getTableName(),
                    $item,
                    "Field [$item] can't be null."
                );
            }
        }

        $fields = array();
        $values = array();

        foreach ($this->inserts as $key => $value) {
            if (!$this->schema->existsField($key)) {
                throw new FieldValidateException(
                    $this->schema->getTableName(),
                    $key,
                    "Not exists field [$key] in table [" . $this->schema->getTableName() . "]."
                );
            }
            $field = $this->schema->field($key);
            if ($field->autoIncrement) {
                throw new FieldValidateException(
                    $this->schema->getTableName(),
                    $key,
                    "Can't set auto-increment field [$key] in insert query."
                );
            }
            if (ColumnInfo::FIELD_TYPE_ENUM === $field->type) {
                if (false === array_search($value, $field->enums)) {
                    throw new FieldValidateException(
                        $this->schema->getTableName(),
                        $key,
                        "Not exists enum value [$value] in insert query. " .
                        "The value must be one of (" . implode(',', $field->enums) . ")."
                    );
                }
            }
            if (isset($field->validation) && !$field->validation->valid($value)) {
                throw new FieldValidateException(
                    $this->schema->getTableName(),
                    $key,
                    "Field value validation error. [$key]"
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