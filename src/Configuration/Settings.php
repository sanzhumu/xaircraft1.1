<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 12:30
 */

namespace Xaircraft\Configuration;


use Xaircraft\App;

class Settings
{
    public static function load($key)
    {
        $path = App::path($key);

        if (isset($path) && is_readable($path)) {
            return require_once $path;
        }
        return null;
    }
}