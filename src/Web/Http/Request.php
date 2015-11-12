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
}