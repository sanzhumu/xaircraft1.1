<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/13
 * Time: 10:03
 */

namespace Xaircraft\Module;


use Xaircraft\Configuration\Settings;

class AppModuleLoader extends AppModule
{

    public function handle()
    {
        Settings::load('module');
    }
}