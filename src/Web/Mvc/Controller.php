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
use Xaircraft\Mvc\Action\TextResult;
use Xaircraft\Web\Http\Request;
use Xaircraft\Web\Mvc\Action\JsonResult;
use Xaircraft\Web\Mvc\Action\LayoutResult;
use Xaircraft\Web\Mvc\Action\ObjectResult;
use Xaircraft\Web\Mvc\Action\StatusResult;
use Xaircraft\Web\Mvc\Action\ViewResult;

abstract class Controller
{
    /**
     * @var Request
     */
    public $req;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var bool 结束action执行并返回结果
     */
    private $isEnded = false;

    private $layoutName;

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

    public function json($object = null)
    {
        $result       = new JsonResult($object);
        $result->data = $this->data;
        return $result;
    }

    public function status($message, $statusCode, $object = null)
    {
        $result = new StatusResult($message, $statusCode, $object);
        $result->data = $this->data;
        return $result;
    }

    public function text($text)
    {
        $result = new TextResult($text);
        return $result;
    }

    public function view($viewName = null)
    {
        $viewResult       = new ViewResult($viewName);
        $viewResult->data = $this->data;
        if (!isset($this->layoutName)) {
            return $viewResult;
        } else {
            $layoutResult       = new LayoutResult($this->layoutName, $viewResult);
            $layoutResult->data = $this->data;
            return $layoutResult;
        }
    }

    public function object($object = null)
    {
        $result = new ObjectResult($object);
        return $result;
    }

    public function layout($layoutName)
    {
        $this->layoutName = $layoutName;
    }

    public function disableLayout()
    {
        unset($this->layoutName);
    }

    public function __set($key, $value)
    {
        if (isset($key) && is_string($key))
            $this->data[$key] = $value;
    }

    public function __get($key)
    {
        if (isset($key) && array_key_exists($key, $this->data))
            return $this->data[$key];
        else
            throw new WebException(
                $this->req->param('controller'),
                $this->req->param('action'),
                "Can't find [$key] in data.");
    }
}