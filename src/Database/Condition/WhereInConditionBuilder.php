<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 19:51
 */

namespace Xaircraft\Database\Condition;


use Xaircraft\Database\FieldInfo;
use Xaircraft\Database\QueryContext;
use Xaircraft\Database\WhereQuery;
use Xaircraft\Exception\QueryException;

class WhereInConditionBuilder extends ConditionBuilder
{
    /**
     * @var FieldInfo
     */
    public $field;

    public $clause;

    public $range;

    public $notIn = false;

    public function getQueryString()
    {
        $field = $this->field->getName($this->context);
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

    public static function makeNormal(QueryContext $context, $field, $range, $notIn = false, $isSubQuery = false)
    {
        $condition = new WhereInConditionBuilder($context);
        $condition->field = FieldInfo::make($field, null, null, $isSubQuery);
        $condition->range = $range;
        $condition->notIn = $notIn;

        return $condition;
    }

    public static function makeClause(QueryContext $context, $field, $clause, $notIn = false, $isSubQuery = false)
    {
        $condition = new WhereInConditionBuilder($context);
        $condition->field = FieldInfo::make($field, null, null, $isSubQuery);
        $condition->clause = $clause;
        $condition->notIn = $notIn;

        return $condition;
    }
}