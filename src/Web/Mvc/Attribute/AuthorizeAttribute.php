<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/7
 * Time: 23:10
 */

namespace Xaircraft\Web\Mvc\Attribute;


class AuthorizeAttribute extends Attribute
{
    public function initialize($value)
    {
        if (preg_match('#(?<authorize>[a-zA-Z][a-zA-Z0-9\_\\\]+)(\((?<arguments>[^\)]+)\))?#i', $value, $matches)) {

        }
    }

    /**
     * @return mixed
     */
    public function invoke()
    {
        // TODO: Implement execute() method.
    }
}