<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/14
 * Time: 20:32
 */

namespace Xaircraft\Database;


class QueryContext
{
    private $parameters = array();

    public function param($value)
    {
        $this->parameters[] = $value;
    }

    public function getParams()
    {
        return $this->parameters;
    }
}