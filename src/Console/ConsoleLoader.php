<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/4
 * Time: 17:39
 */

namespace Xaircraft\Console;


use Xaircraft\App;
use Xaircraft\Globals;
use Xaircraft\Module\AppModule;

class ConsoleLoader extends AppModule
{

    public function appStart()
    {
        // TODO: Implement appStart() method.
    }

    public function handle()
    {
        if (Globals::RUNTIME_MODE_CLI !== App::environment(Globals::ENV_RUNTIME_MODE)) {
            return;
        }

        var_dump($_SERVER['argc']);
        var_dump($_SERVER['argv']);
    }

    public function appEnd()
    {
        // TODO: Implement appEnd() method.
    }
}