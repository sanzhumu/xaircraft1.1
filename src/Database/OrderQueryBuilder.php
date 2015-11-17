<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/17
 * Time: 20:17
 */

namespace Xaircraft\Database;


class OrderQueryBuilder
{
    public static function toString(array $orders)
    {
        if (empty($orders)) {
            return null;
        }

        $statements = array();

        foreach ($orders as $item) {
            /**
             * @var OrderInfo $item
             */
            $statements[] = "$item->field $item->sort";
        }

        return implode(',', $statements);
    }
}