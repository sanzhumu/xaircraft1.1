<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/23
 * Time: 19:30
 */

namespace Xaircraft\Console\Daemon;


use Xaircraft\App;
use Xaircraft\Console\IPC\MessageQueue;
use Xaircraft\Core\IO\File;

class ScheduleDaemon extends Daemon
{
    private $daemons = array();

    public function handle()
    {
        while (true) {
            if (MessageQueue::getMsgCount('schedule_daemon') > 0) {
                MessageQueue::receive('schedule_daemon', 0, $messageType, 1024, $message, true, MSG_IPC_NOWAIT);
                File::appendText(App::path('cache') . "/" . get_called_class() . ".log", $message . "/" . time());
                if ($message == "stop") {
                    break;
                }
            }
            sleep(2);
        }
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