<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/23
 * Time: 19:30
 */

namespace Xaircraft\Console\Daemon;


use Xaircraft\Console\IPC\MessageQueue;

class ScheduleDaemon extends Daemon
{
    private $daemons = array();

    public function handle()
    {
        MessageQueue::register('schedule_daemon', __FILE__);

        while (true) {
            if (MessageQueue::getMsgCount('schedule_daemon') > 0) {
                MessageQueue::receive('schedule_daemon', 0, $messageType, 1024, $message, true, MSG_IPC_NOWAIT);
                
            }
            sleep(2);
        }
    }
}