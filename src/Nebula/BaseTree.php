<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/13
 * Time: 22:50
 */

namespace Xaircraft\Nebula;


use Xaircraft\Core\Strings;
use Xaircraft\DB;
use Xaircraft\DI;

trait BaseTree
{
    public static function children($parentID, array $selections)
    {
        /** @var Model $model */
        $model = DI::get(__CLASS__);
        return DB::table($model->getSchema()->getName())
            ->where($model->getParentIDField(), $parentID)
            ->select($selections)
            ->execute();
    }

    public static function makeTrees($parentID, array $selections)
    {
        $children = self::children($parentID, $selections);

        if (!empty($children)) {
            $nodes = array();
            foreach ($children as $item) {
                $node = array();
                foreach ($selections as $field) {
                    $node[$field] = $item[$field];
                }
                $node['children'] = self::makeTrees($item['id'], $selections);
                $nodes[] = $node;
            }
            return $nodes;
        }
        return null;
    }

    public abstract function getParentIDField();
}