<?php
use Xaircraft\Nebula\Model;

/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/21
 * Time: 22:12
 */
class User extends Model
{
    public function beforeSave()
    {
        var_dump($this->id);
    }

    public function afterSave()
    {
        var_dump($this->id);
    }
}