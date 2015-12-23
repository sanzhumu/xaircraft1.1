<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/23
 * Time: 19:47
 */

namespace Xaircraft\Console\IPC;


use Xaircraft\Exception\ConsoleException;

class MessageQueue
{
    private static $queues = array();

    public static function register($key, $filePath)
    {
        self::$queues[$key] = $filePath;
    }

    public static function send($key, $msgtype, $message, $serialize = true, $blocking = true, &$errorcode = null)
    {
        $queue = self::getQueue($key);
        return msg_send($queue, $msgtype, $message, $serialize, $blocking, $errorcode);
    }

    public static function receive($key, $desiredmsgtype, &$msgtype, $maxsize, &$message, $unserialize = true, $flags = 0, &$errorcode = null)
    {
        $queue = self::getQueue($key);
        return msg_receive($queue, $desiredmsgtype, $msgtype, $maxsize, $message, $unserialize, $flags, $errorcode);
    }

    public static function getMsgCount($key)
    {
        $queue = self::getQueue($key);
        $messageQueueState = msg_stat_queue($queue);
        return $messageQueueState['msg_qnum'];
    }

    private static function getQueue($key)
    {
        $file = self::$queues[$key];

        if (!isset($file) || !file_exists($file)) {
            throw new ConsoleException("Invalid message queue key.");
        }

        $queueKey = ftok(self::$queues[$key], "x");
        if (!msg_queue_exists($queueKey)) {
            throw new ConsoleException("Message queue not exists.");
        }
        return msg_get_queue($queueKey, 0666);
    }
}