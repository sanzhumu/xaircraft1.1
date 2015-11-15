<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 19:53
 */

namespace Xaircraft\Database\Condition;


use Xaircraft\Database\QueryContext;

class WhereBetweenConditionBuilder implements ConditionBuilder
{
    public $field;

    public $range;

    public $notBetween = false;

    /**
     * @var QueryContext
     */
    private $context;

    public function __construct(QueryContext $context)
    {
        $this->context = $context;
    }

    public function getQueryString()
    {
        // TODO: Implement getQueryString() method.
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