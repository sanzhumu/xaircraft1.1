<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/19
 * Time: 17:05
 */

namespace Xaircraft\Database;


use Xaircraft\Exception\DataTableException;
use Xaircraft\Exception\QueryException;

class UpdatetionQueryBuilder
{
    public static function toString(TableSchema $schema, QueryContext $context, array $updates)
    {
        if (empty($updates)) {
            throw new DataTableException("What do you want to update?");
        }

        if ($schema->existsField(TableSchema::RESERVED_FIELD_UPDATE_AT) &&
            !array_key_exists(TableSchema::RESERVED_FIELD_UPDATE_AT, $updates)) {
            $updates[TableSchema::RESERVED_FIELD_UPDATE_AT] = time();
        }

        $statements = array();

        foreach ($updates as $key => $value) {
            if (!$schema->existsField($key)) {
                throw new QueryException("Not exists field [$key] in table [" . $schema->getTableName() . "].");
            }
            if ($schema->field($key)->autoIncrement) {
                throw new QueryException("Can't update auto-increment field [$key].");
            }
            //TODO: Here can validate field by TableSchema which can parse comment with validation patterns.
            $statements[] = "$key = ?";
            $context->param($value);
        }

        return !empty($statements) ? "SET " . implode(',', $statements) : null;
    }
}