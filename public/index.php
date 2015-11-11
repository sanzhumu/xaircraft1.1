<?php
//require __DIR__.'/../vendor/autoload.php';

//$app = require __DIR__.'/../bootstrap/start.php';

//$app->run();

$app = new \Xaircraft\App();
$app->run();

$router = \Xaircraft\Router\Router::getInstance();

$router->routing('/home/index');

var_dump($router);