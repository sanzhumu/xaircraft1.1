<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/17
 * Time: 15:55
 */

namespace Xaircraft\Database;


use Xaircraft\Exception\QueryException;

class JoinInfo
{
    public $table;

    public $name;

    public $alisa;

    public $condition;

    public $clause;

    public $leftJoin = false;

    public static function makeNormal($table, JoinConditionInfo $condition, $leftJoin = false)
    {
        $joinInfo = new JoinInfo();
        $joinInfo->table = $table;
        $joinInfo->condition = $condition;
        $joinInfo->leftJoin = $leftJoin;

        $joinInfo->parseTable();

        return $joinInfo;
    }

    public static function makeClause($table, $clause, $leftJoin = false)
    {
        $joinInfo = new JoinInfo();
        $joinInfo->table = $table;
        $joinInfo->clause = $clause;
        $joinInfo->leftJoin = $leftJoin;

        $joinInfo->parseTable();

        return $joinInfo;
    }

    private function parseTable()
    {
        if (preg_match('#(?<name>[a-zA-Z][a-zA-Z0-9\_]*)([ ]+AS[ ]+(?<alias>[a-zA-Z][a-zA-Z0-9\_]*))?#i', $this->table, $matches)) {
            $this->name = array_key_exists('name', $matches) ? $matches['name'] : null;
            $this->alias = array_key_exists('alias', $matches) ? $matches['alias'] : null;
        } else {
            throw new QueryException("Join table name error. [$this->table]");
        }
        if (!isset($this->name)) {
            throw new QueryException("Join table name error. [$this->table]");
        }
    }
}