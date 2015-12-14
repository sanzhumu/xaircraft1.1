<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/13
 * Time: 22:50
 */

namespace Xaircraft\Nebula;


use Xaircraft\Core\Strings;
use Xaircraft\Database\TableQuery;
use Xaircraft\DB;
use Xaircraft\DI;

trait BaseTree
{
    public static function children($parentID, array $selections, TableQuery $query = null)
    {
        /** @var Model $model */
        $model = DI::get(__CLASS__);
        if (!isset($query)) {
            $query = DB::table($model->getSchema()->getName());
        }
        return $query->where($model->getParentIDField(), $parentID)
            ->select($selections)
            ->execute();
    }

    public static function makeTrees($parentID, array $selections, TableQuery $query = null)
    {
        $children = self::children($parentID, $selections, $query);

        if (!empty($children)) {
            $nodes = array();
            foreach ($children as $item) {
                $node = array();
                foreach ($selections as $field) {
                    $node[$field] = $item[$field];
                }
                $node['children'] = self::makeTrees($item['id'], $selections, $query);
                $nodes[] = $node;
            }
            return $nodes;
        }
        return null;
    }

    public static function makeTrace($id, TableQuery $query = null)
    {
        $traces = array();
        self::getParent($id, $traces, $query);
    }

    private static function getParent($id, array &$traces, TableQuery $query = null)
    {
        /** @var Model $model */
        $model = DI::get(__CLASS__);

        if (!isset($query)) {
            $query = DB::table($model->getSchema()->getName());
        }

        $current = $query->where('id', $id)->select()->detail()->execute();
        if (!isset($traces)) {
            $traces = array();
        }
        $traces[] = $current['name'];
        $parentID = $current[$model->getParentIDField()];
        if ($parentID > 0) {
            self::getParent($parentID, $traces, $query);
        }
    }

    public abstract function getParentIDField();
}