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

    public $alasName;

    public $queryHandler;

    public static function make($name, $alasName = null, $queryHandler = null)
    {
        $field = new FieldInfo();
        $field->name = $name;
        $field->alasName = $alasName;
        $field->queryHandler = $queryHandler;

        return $field;
    }
}