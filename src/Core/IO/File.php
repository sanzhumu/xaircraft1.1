<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 6:53
 */

namespace Xaircraft\Core\IO;


class File
{
    public static function writeText($path, $content)
    {
        $dir = dirname($path);
        if (!file_exists($dir)) {
            Directory::makeDir($dir);
        }
        $handler = fopen($path, 'w+');
        fwrite($handler, $content);
        fclose($handler);
    }

    public static function readText($path)
    {
        if (isset($path) && file_exists($path) && is_readable($path)) {
            return file_get_contents($path);
        }

        return false;
    }
}