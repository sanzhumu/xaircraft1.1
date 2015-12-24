<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/23
 * Time: 16:58
 */

namespace Xaircraft\Console\Daemon;


use Xaircraft\App;
use Xaircraft\Console\IPC\MessageQueue;
use Xaircraft\Console\Process;
use Xaircraft\Core\IO\File;

class IdleDaemon extends Daemon
{

    public function handle()
    {
        Process::fork(function () {
            for ($i = 0; $i < 30; $i++) {
                File::appendText(App::path('cache') . "/" . get_called_class() . ".log", $this->getPID() . "_" . posix_getpid() . "_" . $i . "_" . time() . "\r\n");
                sleep(1);
            }
        });
        Process::fork(function () {
            for ($i = 0; $i < 35; $i++) {
                $message = $this->getPID() . "_" . posix_getpid() . "_" . $i . "_" . time() . "\r\n";
                File::appendText(App::path('cache') . "/" . get_called_class() . ".log", $message);
                MessageQueue::send("schedule_daemon", 1, $message);
                sleep(1);
            }
            MessageQueue::send("schedule_daemon", 1, "stop");
        });

        sleep(60);
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