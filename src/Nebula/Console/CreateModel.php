<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/5
 * Time: 8:29
 */

namespace Xaircraft\Nebula\Console;


use Xaircraft\Console\Console;
use Xaircraft\Core\IO\File;
use Xaircraft\Core\Strings;
use Xaircraft\Database\TableSchema;

class CreateModel extends ModelCommandExecutor
{
    public function handle()
    {
        $this->checkArgs(array('table'));

        $namespace = array_key_exists('namespace', $this->args) ? $this->args['namespace'] : null;
        $table = $this->args['table'];
        $class = array_key_exists('class', $this->args) ? $this->args['class'] : Strings::snakeToCamel($table);
        $path = $this->path($class, $namespace);
        if (file_exists($path)) {

        } else {
            $schema = new TableSchema($table);
            File::writeText(
                $path,
                Template::generateModel($table, $class, $schema->columns(), $namespace)
            );
            Console::line("Model [$class] created in path [$path].");
        }
    }
}