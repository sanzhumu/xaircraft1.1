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
        $query = \Xaircraft\DB::table('user')->select()->format(array(
            'create_at' => \Xaircraft\Database\Data\FieldType::DATE,
            'id' => \Xaircraft\Database\Data\FieldType::NUMBER
        ));

        //$queryString = $query->getQueryString();
        $queryString = $query->execute();

        var_dump($queryString);

        var_dump(\Xaircraft\DB::getQueryLog());

        return $this->text('test');
    }

    public function test_entity()
    {
        $entity = \Xaircraft\DB::entity(\Xaircraft\DB::table('user')->select()->where('id', 43));

        $entity->name = '3';
        $entity->password = 'asdf';
        $entity->level = 'admin';
        $entity->save();

        $entity->password = 'asdf';
        $entity->save();

        var_dump($entity->fields());

        var_dump(\Xaircraft\DB::getQueryLog());
    }

    public function test_model()
    {
        $user = User::find(43);
        $user->password = 'asdf';
        $user->name = '3';
        $user->level = 'admin';
        $user->save();

        var_dump(\Xaircraft\DB::getQueryLog());
    }
}