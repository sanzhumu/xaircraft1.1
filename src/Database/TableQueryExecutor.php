<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/16
 * Time: 20:05
 */

namespace Xaircraft\Database;


abstract class TableQueryExecutor
{
    public static function makeSelect(
        TableSchema $schema,
        QueryContext $context,
        $softDeleteLess,
        array $selectFields,
        array $conditions,
        array $joins,
        array $orders,
        array $groups,
        array $havings,
        array $selectQuerySettings)
    {
        return new SelectTableQueryExecutor(
            $schema,
            $context,
            $softDeleteLess,
            $selectFields,
            $conditions,
            $joins,
            $orders,
            $groups,
            $havings,
            $selectQuerySettings
        );
    }

    public static function makeUpdate($schema, $context, $updates, $conditions)
    {
        return new UpdateTableQueryExecutor($schema, $context, $updates, $conditions);
    }

    public static function makeDelete($schema, $context, $conditions, $forceDelete)
    {
        return new DeleteTableQueryExecutor($schema, $context, $conditions, $forceDelete);
    }

    public static function makeInsert($schema, $context, $inserts, $insertGetId)
    {
        return new InsertTableQueryExecutor($schema, $context, $inserts, $insertGetId);
    }

    public abstract function execute();

    public abstract function toQueryString();
}