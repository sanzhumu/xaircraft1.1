<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/17
 * Time: 16:11
 */

namespace Xaircraft\Database;


class JoinQueryBuilder
{
    public static function toString(QueryContext $context, array $joins)
    {
        $statements = array();

        foreach ($joins as $join) {
            $statements[] = self::parseJoinInfo($context, $join);
        }
        return count($statements) > 0 ? implode(' ', $statements) : null;
    }

    private static function parseJoinInfo(QueryContext $context, JoinInfo $join)
    {
        $schema = new TableSchema($join->name);
        $conditions = array();
        if (isset($join->clause)) {
            $joinQuery = new JoinQuery();
            call_user_func($join->clause, $joinQuery);
            $conditions = $joinQuery->getConditions();
            if (!$joinQuery->getSoftDeleteLess() && $schema->getSoftDelete()) {
                $conditions[] = JoinConditionInfo::make(ConditionInfo::CONDITION_AND, TableSchema::SOFT_DELETE_FIELD, '=', 0, false);
            }
        } else {
            $conditions[] = $join->condition;
        }

        $statements = array();

        $statements[] = $join->leftJoin ? "LEFT JOIN" : "JOIN";
        $statements[] = $join->table;

        if (count($conditions) > 0) {
            $statements[] = "ON (";
            $first = false;
            foreach ($conditions as $item) {
                $items = array();
                /**
                 * @var JoinConditionInfo $item
                 */
                if (!$first) {
                    $first = true;
                } else {
                    $items[] = $item->orAnd;
                }
                if (!$item->whereCondition) {
                    $items[] = "$item->onField $item->onOperator $item->onValue";
                } else {
                    if ($item->onValue instanceof Raw) {
                        $items[] = "$item->onField $item->onOperator " . $item->onValue->getValue();
                    } else {
                        $items[] = "$item->onField $item->onOperator ?";
                        $context->param($item->onValue);
                    }
                }

                $statements[] = implode(' ', $items);
            }
            $statements[] = ")";
        }

        return implode(' ', $statements);
    }
}