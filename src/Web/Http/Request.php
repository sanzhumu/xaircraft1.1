<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 15:10
 */

namespace Xaircraft\Web\Http;


use Xaircraft\Core\Strings;

class Request
{
    private $params = array();

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function param($key, $htmlFilter = true)
    {
        if (array_key_exists($key, $this->params)) {
            if (!$htmlFilter) {
                return $this->params[$key];
            }
            return Strings::htmlFilter($this->params[$key]);
        }
        return null;
    }

    public function params($htmlFilter = true)
    {
        $params = array();
        foreach ($this->params as $key => $value) {
            if (!$htmlFilter) {
                $params[$key] = $value;
            } else {
                $params[$key] = Strings::htmlFilter($value);
            }
        }
        return $params;
    }

    public function post($key, $htmlFilter = true)
    {
        if (isset($key) && isset($_POST[$key])) {
            if (!$htmlFilter) {
                return $_POST[$key];
            }
            return Strings::htmlFilter($_POST[$key]);
        }
        return null;
    }

    public function isPost()
    {
        return isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post';
    }

    public function isXMLHttpRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function isPJAX()
    {
        $pjax = $this->param('_pjax');
        return ((isset($_SERVER['HTTP_X_PJAX']) && strtolower($_SERVER['HTTP_X_PJAX']) === 'true') || isset($pjax));
    }
}