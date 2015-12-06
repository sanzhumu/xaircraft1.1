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
    private function __construct($id, $username, $name, $email, array $permissions)
    {
        $this['id'] = $id;
        $this['username'] = $username;
        $this['name'] = $name;
        $this['email'] = $email;
        $this['permissions'] = $permissions;
    }

    public static function create($id, $username, $name, $email, array $permissions)
    {
        return new CurrentUser($id, $username, $name, $email, $permissions);
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

    public function getPermissions()
    {
        return $this['permissions'];
    }
}