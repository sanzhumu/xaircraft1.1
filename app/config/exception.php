<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/8
 * Time: 14:25
 */

use Xaircraft\Exception\HttpAuthenticationException;

return array(
    HttpAuthenticationException::class => function ($ex) {
        var_dump("aa");
    }
);