<?php
use Xaircraft\DI;
use Xaircraft\Web\Mvc\Controller;
use Xaircraft\Web\Mvc\OutputStatusException;

/**
 * @output_status_exception
 */
class user_home_controller extends Controller implements OutputStatusException
{
    public function test_error()
    {
        $query = \Xaircraft\DB::table('user')->whereIn('id', function (\Xaircraft\Database\WhereQuery $whereQuery) {
            $whereQuery->from('user')->select('id');
        });
        $query1 = $query;
        $list = $query->select()->execute();
        $list = $query1->select()->execute();

        var_dump($list);
        var_dump(\Xaircraft\DB::getQueryLog());
    }
}