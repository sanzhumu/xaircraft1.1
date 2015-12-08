<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/6
 * Time: 20:26
 */

namespace Xaircraft\Authentication\Contract;


use Xaircraft\Core\Container;

class CurrentUser extends Container
{
    private function __construct($id, $username, $name, $email, $extra)
    {
        $this['id'] = $id;
        $this['username'] = $username;
        $this['name'] = $name;
        $this['email'] = $email;
        $this['extra'] = $extra;
    }

    public static function create($id, $username, $name, $email, $extra)
    {
        return new CurrentUser($id, $username, $name, $email, $extra);
    }

    public function getID()
    {
        return $this['id'];
    }

    public function getUsername()
    {
        return $this['username'];
    }

    public function getName()
    {
        return $this['name'];
    }

    public function getEmail()
    {
        return $this['email'];
    }

    public function getExtra()
    {
        return $this['extra'];
    }
}