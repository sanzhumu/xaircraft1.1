<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 19:51
 */

namespace Xaircraft\Database\Condition;


use Xaircraft\Database\QueryContext;
use Xaircraft\Database\WhereQuery;
use Xaircraft\Exception\QueryException;

class WhereInConditionBuilder extends ConditionBuilder
{
    public $field;

    public $clause;

    public $range;

    public $notIn = false;

    public function getQueryString()
    {
        $field = $this->context->getField($this->field);
        $statements = array();
        if (!isset($this->clause)) {
            $statements[] = "$field IN (";
                if (!empty($this->range)) {
                    $values = array();
                    foreach ($this->range as $item) {
                        $values[] = '?';
                        $this->context->param($item);
                    }
                    $statements[] = implode(',', $values);
                } else {
                    $statements[] = 'NULL';
                }
            $statements[] = ")";
        } else {
            $whereQuery = new WhereQuery($this->context, true);
            call_user_func($this->clause, $whereQuery);
            $statements[] = "$field IN (";
            $item = $whereQuery->getQueryString();
            if (!isset($item)) {
                throw new QueryException("WhereIn Condition build error.");
            }
            $statements[] = $item;
            $statements[] = ")";
        }

        return !empty($statements) ? '(' . implode(' ', $statements) . ')' : null;
    }

    public static function makeNormal(QueryContext $context, $field, $range, $notIn = false)
    {
        $condition = new WhereInConditionBuilder($context);
        $condition->field = $field;
        $condition->range = $range;
        $condition->notIn = $notIn;

        return $condition;
    }

    public static function makeClause(QueryContext $context, $field, $clause, $notIn = false)
    {
        $condition = new WhereInConditionBuilder($context);
        $condition->field = $field;
        $condition->clause = $clause;
        $condition->notIn = $notIn;

        return $condition;
    }
}