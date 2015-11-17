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
    public static function makeSelect(TableSchema $schema, QueryContext $context, $softDeleteLess, array $selectFields, array $conditions, array $joins)
    {
        return new SelectTableQueryExecutor($schema, $context, $softDeleteLess, $selectFields, $conditions, $joins);
    }

    public abstract function execute();

    public abstract function toQueryString();
}