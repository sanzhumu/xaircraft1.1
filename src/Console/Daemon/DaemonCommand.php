<?php

namespace Xaircraft\Console\Daemon;
use Xaircraft\Console\Command;


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
        if (function_exists("pcntl_signal")) {
            var_dump("support pcntl");
        }
    }
}

 