<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/13
 * Time: 22:50
 */

namespace Xaircraft\Nebula;


use Xaircraft\Core\Strings;
use Xaircraft\DB;
use Xaircraft\DI;

trait BaseTree
{
    public static function children($parentID, array $selections)
    {
        /** @var Model $model */
        $model = DI::get(__CLASS__);
        var_dump($model);
    }
}