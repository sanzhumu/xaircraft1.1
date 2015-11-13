<?php

/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 17:44
 */
class user_home_controller extends \Xaircraft\Web\Mvc\Controller
{

    public function index($username, $password)
    {
        var_dump($username);
        var_dump($password);

        return $this->status('test', 200, array('username' => \Xaircraft\Web\Session::get('test')));
    }
}