<?php

namespace Xaircraft\Console\Daemon;
use Xaircraft\App;
use Xaircraft\Console\Command;
use Xaircraft\Console\Console;
use Xaircraft\Console\IPC\MessageQueue;
use Xaircraft\Exception\ConsoleException;
use Xaircraft\Exception\DaemonException;
use Xaircraft\Globals;


/**
 * Class DaemonCommand
 *
 * @package Xaircraft\Console\Daemon
 * @author skyweo created at 15/12/19 21:41
 */
class DaemonCommand extends Command
{
    public function handle()
    {
        if (Globals::RUNTIME_MODE_CLI !== App::environment(Globals::ENV_RUNTIME_MODE)) {
            throw new ConsoleException("Only run in command line mode");
        }

        //加载Worker并为每个Worker创建一个子进程，然后进入休眠，当接收到信号量时，则执行相应的进程调度操作。

        if (!function_exists("pcntl_signal")) {
            throw new ConsoleException("PHP does not appear to be compiled with the PCNTL extension.This is neccesary for daemonization");
        }
        if (function_exists("gc_enable")) {
            gc_enable();
        }

        $daemon = Daemon::make($_SERVER['argc'], $_SERVER['argv']);

        try {
            /**
             * @var $daemon Daemon
             */
            if (isset($daemon)) {
                $daemon->start();
                sleep(1);
                Console::line("Daemon [" . $daemon->getPID() . "] started.");
            }
        } catch (\Exception $ex) {
            throw new DaemonException($daemon->getName(), $ex->getMessage(), $ex);
        }
    }
}

 