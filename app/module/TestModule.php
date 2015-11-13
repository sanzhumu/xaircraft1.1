<?php
use Xaircraft\Module\AppModuleLoader;
use Xaircraft\Module\AppModuleState;

/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/13
 * Time: 9:50
 */
class TestModule extends \Xaircraft\Module\AppModule
{
    public function appStart()
    {
        // TODO: Implement appStart() method.
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();
    }

    public function handle()
    {
        // TODO: Implement handle() method.
    }

    public function appEnd()
    {
        // TODO: Implement appEnd() method.
    }
}