<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/13
 * Time: 14:26
 */

namespace Xaircraft\Web\Mvc;


use Xaircraft\DI;
use Xaircraft\Exception\HttpAuthenticationException;

class ActionInfo
{
    /**
     * @var Controller
     */
    private $controller;

    private $name;

    private $parameters;

    private $docComment;

    private $outputStatusException = false;

    /**
     * @var \ReflectionMethod
     */
    private $reflector;

    /**
     * @var ActionContext
     */
    private $context;

    public function __construct(Controller $controller, $action, ActionContext &$context)
    {
        $this->controller = $controller;
        $this->name = $action;
        $this->reflector = new \ReflectionMethod($controller, $action);
        $this->docComment = $this->reflector->getDocComment();
        $this->parameters = $this->reflector->getParameters();
        $this->context = $context;

        $this->initializeDocComment();
    }

    public function invoke($params)
    {
        AuthorizeHelper::authorizeController($this->controller);
        $args = array();
        foreach ($this->parameters as $parameter) {
            if (array_key_exists($parameter->name, $params)) {
                $args[$parameter->name] = $params[$parameter->name];
            } else {
                $args[$parameter->name] = null;
            }
        }
        return $this->reflector->invokeArgs($this->controller, $args);
    }

    public function getIfOutputStatusException()
    {
        return $this->outputStatusException;
    }

    private function initializeDocComment()
    {
        if (preg_match('#@output_status_exception#i', $this->docComment) > 0) {
            $this->outputStatusException = true;
            $this->context->outputStatusException = true;
        }
        AuthorizeHelper::authorize($this->docComment);
    }
}