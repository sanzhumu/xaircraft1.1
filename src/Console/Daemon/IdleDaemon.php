<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/23
 * Time: 16:58
 */

namespace Xaircraft\Console\Daemon;


use Xaircraft\App;
use Xaircraft\Console\Console;
use Xaircraft\Core\IO\File;

class IdleDaemon extends Daemon
{

    public function handle()
    {
        Console::line("Daemon [" . $this->getPID() . "] started.");
        for ($i = 0; $i < 60; $i++) {
            File::appendText(App::path('cache') . "/" . get_called_class() . ".log", $this->getPID() . "_" . $i . "_" . time() . "\r\n");
            sleep(1);
        }
        Console::line("Daemon [" . $this->getPID() . "] end.");
    }
}