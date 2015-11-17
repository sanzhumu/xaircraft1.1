<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/17
 * Time: 20:40
 */

namespace Xaircraft\Database;


class HavingInfo
{
    public $field;

    public $operator;

    public $value;

    public static function make($field, $operator, $value)
    {
        $info = new HavingInfo();
        $info->field = $field;
        $info->operator = $operator;
        $info->value = $value;

        return $info;
    }
}