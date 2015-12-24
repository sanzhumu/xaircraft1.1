<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/23
 * Time: 16:58
 */

namespace Xaircraft\Console\Daemon;


use Xaircraft\App;
use Xaircraft\Core\IO\File;

class IdleDaemon extends Daemon
{

    public function handle()
    {
        $this->fork(function () {
            for ($i = 0; $i < 30; $i++) {
                File::appendText(App::path('cache') . "/" . get_called_class() . ".log", $this->getPID() . "_" . posix_getpid() . "_" . $i . "_" . time() . "\r\n");
                sleep(1);
            }
        });
        $this->fork(function () {
            for ($i = 0; $i < 35; $i++) {
                $message = $this->getPID() . "_" . posix_getpid() . "_" . $i . "_" . time() . "\r\n";
                File::appendText(App::path('cache') . "/" . get_called_class() . ".log", $message);
                sleep(1);
            }
        });
    }

    public function beforeStart()
    {
        // TODO: Implement beforeStart() method.
    }

    public function beforeStop()
    {
        File::appendText(App::path('cache') . "/" . get_called_class() . "_stop.log", $this->getPID() . "_" . posix_getpid() . "_" . time() . "\r\n");
    }
}