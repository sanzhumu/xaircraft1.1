<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 19:53
 */

namespace Xaircraft\Database\Condition;


use Xaircraft\Database\QueryContext;

class WhereBetweenConditionBuilder extends ConditionBuilder
{
    public $field;

    public $range;

    public $notBetween = false;

    public function getQueryString()
    {
        $statements = array();
        if (!$this->notBetween) {
            $statements[] = "$this->field BETWEEN ? AND ?";
        } else {
            $statements[] = "$this->field < ? OR $this->field > ?";
        }
        $this->context->param($this->range[0]);
        $this->context->param($this->range[1]);

        return !empty($statements) ? '(' . implode(' ', $statements) . ')' : null;
    }

    public static function makeBetween(QueryContext $context, $field, $range)
    {
        $condition = new WhereBetweenConditionBuilder($context);
        $condition->field = $field;
        $condition->range = $range;
        $condition->notBetween = false;

        return $condition;
    }

    public static function makeNotBetween(QueryContext $context, $field, $range)
    {
        $condition = new WhereBetweenConditionBuilder($context);
        $condition->field = $field;
        $condition->range = $range;
        $condition->notBetween = true;

        return $condition;
    }
}