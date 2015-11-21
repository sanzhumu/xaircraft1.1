<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/16
 * Time: 20:01
 */

namespace Xaircraft\Database;


use Xaircraft\Database\Condition\WhereConditionBuilder;
use Xaircraft\DB;

class SelectTableQueryExecutor extends TableQueryExecutor
{
    private $schema;

    private $selectFields;

    private $conditions;

    private $softDeleteLess;

    /**
     * @var QueryContext
     */
    private $context;

    private $joins;

    private $orders;

    private $groups;

    private $havings;

    private $selectQuerySettings;

    private $takeCount;

    private $skipOffset;

    private $pluck;

    private $limit;

    public function __construct(
        TableSchema $schema,
        QueryContext $context,
        $softDeleteLess,
        array $selectFields,
        array $conditions,
        array $joins,
        array $orders,
        array $groups,
        array $havings,
        array $selectQuerySettings)
    {
        $this->schema = $schema;
        $this->selectFields = $selectFields;
        $this->conditions = $conditions;
        $this->softDeleteLess = $softDeleteLess;
        $this->context = $context;
        $this->joins = $joins;
        $this->orders = $orders;
        $this->groups = $groups;
        $this->havings = $havings;
        $this->selectQuerySettings = $selectQuerySettings;

        $this->parseSettings();
    }

    public function execute()
    {
        $query = $this->toQueryString();

        return $this->getQueryResult($query);
    }

    private function getQueryResult($query)
    {
        $result = DB::select($query, $this->context->getParams());

        if (!empty($result)) {
            if ($this->pluck) {
                $field = $this->selectFields[0];
                if (isset($field->alias)) {
                    return $result[0][$field->alias];
                }
                return $result[0][$field->name];
            }
        }

        return $result;
    }

    public function toQueryString()
    {
        if ($this->schema->getSoftDelete() && !$this->softDeleteLess) {
            $this->conditions[] = ConditionInfo::make(
                ConditionInfo::CONDITION_AND,
                WhereConditionBuilder::makeNormal($this->context, TableSchema::SOFT_DELETE_FIELD, '=', 0)
            );
        }

        $selection = SelectionQueryBuilder::toString($this->context, $this->selectFields) . ' FROM ' . $this->schema->getTableName();
        $join = JoinQueryBuilder::toString($this->context, $this->joins);
        $condition = ConditionQueryBuilder::toString($this->conditions);
        $orders = OrderQueryBuilder::toString($this->orders);
        $groups = GroupQueryBuilder::toString($this->groups);
        $havings = HavingQueryBuilder::toString($this->havings);

        $statements = array($selection);

        if (isset($join)) {
            $statements[] = $join;
        }

        if (isset($condition)) {
            $statements[] = "WHERE $condition";
        }

        if (isset($orders)) {
            $statements[] = "ORDER BY $orders";
        }

        if (isset($groups)) {
            $statements[] = "GROUP BY $groups";
        }

        if (isset($havings)) {
            $statements[] = "HAVING ($havings)";
        }

        if (isset($this->limit)) {
            if ($this->skipOffset > 0 && $this->takeCount > 0) {
                $statements[] = "LIMIT $this->skipOffset,$this->takeCount";
            } else if ($this->takeCount > 0) {
                $statements[] = "LIMIT $this->takeCount";
            }
        }

        return implode(' ', $statements);
    }

    private function parseSettings()
    {
        if (isset($this->selectQuerySettings)) {
            $settings = $this->selectQuerySettings;
            $this->takeCount = array_key_exists('take_count', $settings) ? $settings['take_count'] : null;
            $this->skipOffset = array_key_exists('skip_offset', $settings) ? $settings['skip_offset'] : null;
            $this->pluck = array_key_exists('pluck', $settings) ? $settings['pluck'] : null;

            if (isset($this->takeCount) || isset($this->skipOffset)) {
                $this->limit = true;
            }
        }
    }
}