<?php

use \Xaircraft\App;

/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/11
 * Time: 18:29
 */

$app = App::instance();
$app->bindPath(require_once __DIR__.'/paths.php');

var_dump($app->path('base'));

$app->environment(\Xaircraft\Globals::ENV_MODE, \Xaircraft\Globals::MODE_DEV);

var_dump($app);


return $app;