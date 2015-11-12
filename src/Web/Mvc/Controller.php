<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 15:55
 */

namespace Xaircraft\Web\Mvc;


use Xaircraft\DI;
use Xaircraft\Exception\WebException;
use Xaircraft\Web\Http\Request;

abstract class Controller
{
    /**
     * @var Request
     */
    public $req;

    private $isEnded = false;

    public static function invoke($controller, $action, $namespace)
    {
        if (isset($namespace)) {
            $controller = str_replace('/', '_', $namespace) . '_' . $controller;
        }

        $actionResult = self::getActionResult($controller, $action);
        if (isset($actionResult) && $actionResult instanceof Action\ActionResult) {
            return $actionResult->execute();
        }
        return null;
    }

    private static function getActionResult($controller, $action)
    {
        $controller = $controller . '_controller';

        if (!class_exists($controller)) {
            throw new WebException($controller, $action, "Can't find controller - [$controller]");
        }
        if (!method_exists($controller, $action)) {
            throw new WebException($controller, $action, "Can't find action [$action] in [$controller]");
        }

        /**
         * @var Controller $controller
         */
        $controller = DI::get($controller);
        $controller->req = DI::get(Request::class);
        $pageLoadResult = $controller->onPageLoad();
        if (isset($pageLoadResult)) {
            return $pageLoadResult;
        }
        if (!$controller->isEnded) {
            $reflector = new \ReflectionMethod($controller, $action);
            $parameters = $reflector->getParameters();
            $args = array();
            $params = $controller->req->params();
            foreach ($parameters as $parameter) {
                if (array_key_exists($parameter->name, $params)) {
                    $args[$parameter->name] = $params[$parameter->name];
                } else {
                    $args[$parameter->name] = null;
                }
            }

            $actionResult = $reflector->invokeArgs($controller, $args);
            return $actionResult;
        }
        return null;
    }

    public function onPageLoad()
    {
        return null;
    }

    public function end()
    {
        $this->isEnded = true;
    }
}