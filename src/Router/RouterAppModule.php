<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 9:59
 */

namespace Xaircraft\Router;


use Xaircraft\App;
use Xaircraft\Module\AppModule;
use Xaircraft\Globals;

class RouterAppModule extends AppModule
{
    public function handle()
    {
        if (Globals::RUNTIME_MODE_APACHE2HANDLER !== App::environment(Globals::ENV_RUNTIME_MODE)) {
            return;
        }


    }
}