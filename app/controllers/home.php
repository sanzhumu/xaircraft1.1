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
     * @output_status_exception
     */
    public function index($id, $title)
    {
        $query = \Xaircraft\DB::table('project')->select(array(
            'id', 'title',
            'user' => function (\Xaircraft\Database\WhereQuery $whereQuery) {
                $whereQuery->select('title')->from('user')->whereIn('id', array(1));
                //$whereQuery->where('id', 0);
            },
            '创建时间' => 'create_at'
        ))->where(function (\Xaircraft\Database\WhereQuery $whereQuery) {
            $whereQuery->where("id", 4)->orWhere('title', 5);
        })->orWhere('id', 'test')
            ->where('title', '>', 2)
            ->whereIn('id', function (\Xaircraft\Database\WhereQuery $whereQuery) {
                $whereQuery->select('id')->from('user')->where('id', 6);
                //$whereQuery->where('id', 0);
            })
            ->whereBetween('id', array(7, 8))
            ->whereNotBetween('id', array(9, 10))
            ->whereIn('title', array('11', '12', 13))->execute();

        var_dump($query);

        return $this->text('test');
    }
}