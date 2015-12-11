<?php
use Xaircraft\Authentication\Auth;
use Xaircraft\Authentication\Contract\CurrentUser;
use Xaircraft\Core\Strings;
use Xaircraft\Database\Data\FieldType;
use Xaircraft\DB;
use Xaircraft\Web\Mvc\Controller;

/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 16:55
 * @auth LoginAuthorize
 * @auth LoginAuthorize2(userID=123, permission='admin;normal.aa')
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
        var_dump(Auth::check());

        $query = \Xaircraft\DB::table('user AS u')->select('COUNT(u.id)')->join('project AS p', 'p.id', 'u.id')->where('id', '>', 0);
//        $query = DB::table('user')->update(array(
//            'name' => '5',
//            'password' => 'adf',
//            'level' => 'admin'
//        ))->where('id', 9);

        //$queryString = $query->execute();
        $queryString = $query->getQueryString();

        var_dump($queryString);

        //var_dump(DB::getQueryLog());
    }
}