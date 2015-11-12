<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 15:25
 */

namespace Xaircraft\Core;


class Strings
{
    public static function htmlFilter($str)
    {
        if (is_string($str) && is_null(json_decode($str))) {
            return htmlspecialchars($str);
        } else {
            return $str;
        }
    }
}