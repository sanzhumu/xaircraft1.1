<?php
use Account\User;
use Xaircraft\Authentication\Auth;
use Xaircraft\Authentication\Contract\CurrentUser;
use Xaircraft\Core\Strings;
use Xaircraft\Database\Data\FieldType;
use Xaircraft\Database\Func\Func;
use Xaircraft\Database\WhereQuery;
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
        //$query = \Xaircraft\DB::table('user AS u')->select('u.id')->join('project AS p', 'p.id', 'u.id')->where('p.id', '>', 0);
        //$query = \Xaircraft\DB::table('user AS u')->select('u.id')->join('project AS p', 'p.id', 'u.id')->where('u.id', '>', 0);
        $query = \Xaircraft\DB::table('user')->select('name')->whereIn('id', function (WhereQuery $whereQuery) {
            $whereQuery->select('u.id')->from('user AS u')->where('u.id', 9);
        })->groupBy('user.id', 'user.name')->having('user.id', 0);
//        $query = DB::table('user')->update(array(
//            'name' => '5',
//            'password' => 'adf',
//            'level' => 'admin'
//        ))->where('id', 9);

        $queryString = $query->execute();
        //$queryString = $query->getQueryString();

        var_dump($queryString);

        var_dump(DB::getQueryLog());
    }

    public function test_model()
    {
        /** @var User $user */
        $user = \Account\User::find(140);
        $user->name = "3";
        $user->password = "test";
        $user->level = "admin";
        $user->save();

        var_dump($user->isModified("name"));

        $user->name = "4";
        $user->level = "normal";

        var_dump($user->isModified("name"));
        var_dump($user->isModified("level"));

        $user->save();

        var_dump(DB::getQueryLog());
    }

    public function test_trait()
    {
        User::children(0, array());
    }

    public function test_single()
    {
        $list = DB::table('user')->select()->format(array(
            'create_at' => FieldType::DATE
        ))->execute();
        var_dump($list);
    }

    public function test_detail()
    {
        $query = DB::table('user')->select()->take(1)->detail();

        $detail = $query->execute();
        var_dump($detail);

        $detail = $query->execute();
        var_dump($detail);
    }
}