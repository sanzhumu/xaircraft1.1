<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 19:54
 */

namespace Xaircraft\Database\Condition;


use Xaircraft\Database\QueryContext;

class WhereExistsConditionBuilder extends  ConditionBuilder
{
    public $clause;

    public function getQueryString()
    {
        // TODO: Implement getQueryString() method.
    }

    public static function make(QueryContext $context, $clause)
    {
        $condition = new WhereExistsConditionBuilder($context);
        $condition->clause = $clause;

        return $condition;
    }
}