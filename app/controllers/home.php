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
        $query = \Xaircraft\DB::table('user')->update(array(
            'name' => 'test'
        ))->where('id', 'test');

        $queryString = $query->getQueryString();
        //$queryString = $query->execute();

        var_dump($queryString);

        $query = \Xaircraft\DB::query('SHOW FULL COLUMNS FROM user');

        foreach ($query as $row) {
            var_dump($row);
        }


        return $this->text('test');
    }
}