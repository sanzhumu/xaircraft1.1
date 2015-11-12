<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 10:22
 */

namespace Xaircraft\Module;


use Xaircraft\App;
use Xaircraft\Configuration\Settings;

class AppModuleLoader extends AppModule
{
    public function handle()
    {
        Settings::load('module');
    }
}