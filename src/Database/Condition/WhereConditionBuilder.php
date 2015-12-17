<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 19:49
 */

namespace Xaircraft\Database\Condition;


use Xaircraft\Database\FieldInfo;
use Xaircraft\Database\QueryContext;
use Xaircraft\Database\Raw;
use Xaircraft\Database\WhereQuery;
use Xaircraft\DI;

class WhereConditionBuilder extends ConditionBuilder
{
    /**
     * @var FieldInfo
     */
    public $field;

    public $operator;

    public $value;

    public $clause;

    public function getQueryString()
    {
        $statements = array();
        $field = $this->field->getName($this->context);
        if (!isset($this->clause)) {
            if ($this->value instanceof Raw) {
                $statements[] = "$field $this->operator " . $this->value->getValue();
            } else {
                $statements[] = "$field $this->operator ?";
                $this->context->param($this->value);
            }
        } else {
            $whereQuery = new WhereQuery($this->context);
            call_user_func($this->clause, $whereQuery);
            $statements[] = $whereQuery->getQueryString();
        }

        return !empty($statements) ? '(' . implode(' ', $statements) . ')' : null;
    }

    public static function makeNormal(QueryContext $context, $field, $operator, $value, $isSubQuery = false)
    {
        $builder = new WhereConditionBuilder($context);
        $builder->field = FieldInfo::make($field, null, null, $isSubQuery);
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