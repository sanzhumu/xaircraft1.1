<?php

/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 16:55
 */
class home_controller extends \Xaircraft\Web\Mvc\Controller
{
    public function index($id, $title)
    {
        var_dump($id);
        var_dump($title);
        var_dump($this->req->params());

        return $this->view();
    }
}