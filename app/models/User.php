<?php
use Xaircraft\Nebula\Model;

/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/21
 * Time: 22:12
 * @property int id
 */
class User extends Model
{
    public function beforeSave()
    {
        $this->id = 0;
    }

    public function afterSave()
    {

    }

    public function beforeDelete()
    {

    }

    public function afterDelete($fields)
    {

    }

    public function afterForceDelete($fields)
    {
        var_dump($fields);
    }
}