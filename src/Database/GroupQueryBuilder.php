<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/17
 * Time: 20:25
 */

namespace Xaircraft\Database;


class GroupQueryBuilder
{
    public static function toString(array $groups)
    {
        if (empty($groups)) {
            return null;
        }

        return implode(',', $groups);
    }
}