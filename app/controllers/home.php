<?php
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
 * @auth LoginAuthorize2(userID=123, permission='admin;normal')
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
        $query = \Xaircraft\DB::table('user')->select()->format(array(
            'create_at' => FieldType::DATE,
            'id' => FieldType::NUMBER
        ));

        //$queryString = $query->getQueryString();
        $queryString = $query->execute();

        var_dump($queryString);

        var_dump(DB::getQueryLog());

        return $this->text('test');
    }

    public function test_entity()
    {
        $entity = \Xaircraft\DB::entity(\Xaircraft\DB::table('user')->select()->where('id', 43));

        $entity->name = '3';
        $entity->password = 'asdf';
        $entity->save();

        $entity->password = 'asdf';
        $entity->save();

        var_dump($entity->fields());

        var_dump(\Xaircraft\DB::getQueryLog());
    }

    public function test_model()
    {
        var_dump(Strings::camelToSnake("UserProjectUserNameJake"));
        $user = Account\User::find(43);
        /** @var Account\User $user */
        $user->password = 'asdf';
        $user->name = '3';
        $user->level = 'admin';
        $user->save();

        var_dump(\Xaircraft\DB::getQueryLog());
    }

    public function test_auth()
    {
        $user = CurrentUser::create(1, 'test', 'test2', 'a@a.xn', array());
        \Xaircraft\Web\Session::put('test', $user);

        var_dump(\Xaircraft\Web\Session::get('test'));
    }

    /**
     * @output_status_exception
     */
    public function test_ref()
    {
        var_dump('hello');
    }
}