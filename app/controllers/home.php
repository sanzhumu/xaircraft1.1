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
            'user' => function () {

            },
            '创建时间' => 'create_at'
        ))->where(function () {

        })->orWhere('id', 'test')
            ->where('title', '>', \Xaircraft\DB::raw('hello world!'))
            ->whereIn('id', function () {})->whereIn('title', array('asdf', 'sfsdfsfd', 234))->execute();

        return $this->text('test');
    }
}