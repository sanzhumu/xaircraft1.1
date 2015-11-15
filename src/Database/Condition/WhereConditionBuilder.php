<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 19:49
 */

namespace Xaircraft\Database\Condition;


use Xaircraft\Database\QueryContext;
use Xaircraft\DI;

class WhereConditionBuilder implements ConditionBuilder
{
    public $field;

    public $operator;

    public $value;

    public $clause;

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
        $statements = array();
        if (!isset($this->clause)) {
            $statements[] = "$this->field $this->operator ?";
            $this->context->param($this->value);
        }

        return !empty($statements) ? '(' . implode(' ', $statements) . ')' : null;
    }

    public static function makeNormal(QueryContext $context, $field, $operator, $value)
    {
        $builder = new WhereConditionBuilder($context);
        $builder->field = $field;
        $builder->operator = $operator;
        $builder->value = $value;

        return $builder;
    }

    public static function makeClause(QueryContext $context, $clause)
    {
        $builder = new WhereConditionBuilder($context);
        $builder->clause = $clause;

        return $builder;
    }
}