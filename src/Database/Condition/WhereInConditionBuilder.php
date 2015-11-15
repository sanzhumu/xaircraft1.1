<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 19:51
 */

namespace Xaircraft\Database\Condition;


use Xaircraft\Database\QueryContext;

class WhereInConditionBuilder implements ConditionBuilder
{
    public $field;

    public $clause;

    public $range;

    public $notIn = false;

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
            $statements[] = "$this->field IN (";
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