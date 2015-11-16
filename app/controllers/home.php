<?php
use Xaircraft\Database\TableSchema;
use Xaircraft\Web\Mvc\Controller;
use Xaircraft\Web\Mvc\OutputStatusException;

/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 16:55
 */
class home_controller extends Controller
{
    /**
     * @param $id
     * @param $title
     * @return \Xaircraft\Web\Mvc\Action\TextResult
     */
    public function index($id, $title)
    {
        $query = \Xaircraft\DB::table('project')->select(array(
            'id', 'title',
            'user' => function (\Xaircraft\Database\WhereQuery $whereQuery) {
                $whereQuery->select()->from('user')->where('id', 1);
                //$whereQuery->where('id', 0);
            },
            '创建时间' => 'create_at'
        ))->where(function (\Xaircraft\Database\WhereQuery $whereQuery) {
            $whereQuery->where("id", 2)->orWhere('title', 3);
        })->orWhere('id', 'test')
            ->where('title', '>', \Xaircraft\DB::raw('hello world!'))
            ->whereIn('id', function (\Xaircraft\Database\WhereQuery $whereQuery) {
                $whereQuery->select('id')->from('user')->where('id', 4);
                //$whereQuery->where('id', 0);
            })
            ->whereBetween('id', array(5, 6))
            ->whereNotBetween('id', array(7, 8))
            ->whereIn('title', array('9', '10', 11))->execute();

        return $this->text('test');
    }
}