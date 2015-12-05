<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/5
 * Time: 0:06
 */

namespace Xaircraft\Nebula\Console;


use Xaircraft\Console\Command;
use Xaircraft\Console\Console;

class ModelCommand extends Command
{

    public function handle()
    {
        Console::line('Command [model] start:');

        $command = ModelCommandExecutor::make($this, $this->args);
        if (!isset($command)) {
            Console::error('Please input first argument: [--create] or [--update]');
            return;
        }
        $command->handle();
        Console::line('Command [model] finished.');
    }
}