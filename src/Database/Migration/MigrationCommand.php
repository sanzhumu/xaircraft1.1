<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/5
 * Time: 17:07
 */

namespace Xaircraft\Database\Migration;


use Xaircraft\App;
use Xaircraft\Console\Command;
use Xaircraft\Console\Console;
use Xaircraft\Core\IO\File;
use Xaircraft\Core\Strings;
use Xaircraft\Exception\ConsoleException;

class MigrationCommand extends Command
{

    public function handle()
    {
        if (isset($this->args[0])) {
            switch (strtolower($this->args[0])) {
                case "--make":
                    $this->makeMigration();
                    return;
            }
        }
        throw new ConsoleException("Please input migration command arguments: [--make].");
    }

    private function makeMigration()
    {
        if (!isset($this->args[1])) {
            throw new ConsoleException("Invalid migration argument in [--make].");
        }
        $migration = $this->args[1];
        $author = isset($this->args[2]) ? $this->args[2] : "Unknow";
        $class = 'm' . time() . Strings::snakeToCamel($migration);
        $path = App::path('migration') . "/$class.php";
        File::writeText($path, Template::generate($class, $author));

        Console::line("migration [$migration] finished in [$class.php].");
    }
}