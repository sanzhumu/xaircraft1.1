<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/7
 * Time: 1:03
 */

namespace Xaircraft\Web\Mvc;


class ActionContext
{
    /**
     * @var Controller
     */
    public $controller;

    public $action;

    public $outputStatusException = false;

    public function __construct(Controller $controller)
    {
        $this->controller = $controller;
    }
}