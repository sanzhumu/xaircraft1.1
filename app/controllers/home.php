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
            '用户.id', 'title',
            'user' => function (\Xaircraft\Database\WhereQuery $whereQuery) {
                $whereQuery->select('title')->from('user')->whereIn('id', array(1));
                //$whereQuery->where('id', 0);
            },
            '创建时间' => '用户.create_at'
        ))->join('user', 'user.id', 'project.create_by')->leftJoin('user AS 用户', function (\Xaircraft\Database\JoinQuery $joinQuery) {
            $joinQuery->on('用户.id', 'project.create_by')->on('用户.id', '>', 'project.create_by')->where('用户.id', 9)->orWhere('用户.id', '=', 'asdf');
        })->orderBy('user.id');

        $queryString = $query->getQueryString();

        var_dump($queryString);

        return $this->text('test');
    }
}