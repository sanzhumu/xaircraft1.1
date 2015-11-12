<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/1/4
 * Time: 16:18
 *
 * @var $router \Xaircraft\Router\Router
 */

$router->mappings['monitor'] = array(
    'expression' => '/monitor/{controller}?/{action}?/{id}?',
    'default' => array(
        'controller' => 'weather',
        'action' => 'index'
    )
);
