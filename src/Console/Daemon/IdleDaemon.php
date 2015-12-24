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
use Xaircraft\Core\IO\File;

class IdleDaemon extends Daemon
{

    public function handle()
    {
        $messageQueueKey = ftok(App::path('cache') . "/queue/daemon.queue", "a");
        $messageQueue = msg_get_queue($messageQueueKey, 0666);

        for ($i = 0; $i < 60; $i++) {
            $message = new MessageQueue();
            $message->pid = $this->getPID();
            $message->name = "IdleDaemon";
            $message->timestamp = time();

            msg_send($messageQueue, 1, $message);

            sleep(1);
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