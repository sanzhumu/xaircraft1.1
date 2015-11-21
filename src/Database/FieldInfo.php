<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 15:31
 */

namespace Xaircraft\Database;


class FieldInfo
{
    public $name;

    public $alias;

    public $queryHandler;

    public static function make($name, $alias = null, $queryHandler = null)
    {
        $field = new FieldInfo();
        $field->name = $name;
        $field->alias = $alias;
        $field->queryHandler = $queryHandler;

        return $field;
    }
}