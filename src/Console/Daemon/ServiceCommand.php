<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/24
 * Time: 15:59
 */

namespace Xaircraft\Console\Daemon;


use Xaircraft\App;
use Xaircraft\Console\Command;
use Xaircraft\Console\Console;
use Xaircraft\Console\IPC\MessageQueue;
use Xaircraft\Exception\ConsoleException;

class ServiceCommand extends Command
{

    public function handle()
    {
        if (isset($this->args[0])) {
            switch (strtolower($this->args[0])) {
                case "--a":
                    $this->showAllDaemon();
                    return;
            }
        }
        throw new ConsoleException("Please input service command arguments: [--a].");
    }

    private function showAllDaemon()
    {
        $messageQueueKey = ftok(App::path('cache') . "/queue/daemon.queue", "a");
        $messageQueue = msg_get_queue($messageQueueKey, 0666);

        $messageQueueState = msg_stat_queue($messageQueue);
        $msgCount = $messageQueueState['msg_qnum'];

        if (0 === $msgCount) {
            Console::line("None service is running.");
        }

        while ($msgCount > 0) {
            /** @var MessageQueue $message */
            msg_receive($messageQueue, 0, $messageType, 1024, $message, true, MSG_IPC_NOWAIT);
            Console::line("PID:$message->pid,NAME:$message->name,TIME:" . date("Y-m-d H:i:s", $message->timestamp) . "Alive.");

            $messageQueueState = msg_stat_queue($messageQueue);
            $msgCount = $messageQueueState['msg_qnum'];
        };
    }
}