<?php
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
     * @throws Exception
     * @output_status_exception
     */
    public function index($id, $title)
    {
        throw new \Exception("test", 200);
    }
}