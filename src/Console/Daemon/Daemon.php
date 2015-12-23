<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/23
 * Time: 16:07
 */

namespace Xaircraft\Console\Daemon;


use Xaircraft\App;
use Xaircraft\Core\IO\File;
use Xaircraft\Core\Strings;
use Xaircraft\DI;
use Xaircraft\Exception\ConsoleException;

abstract class Daemon
{
    private $started = false;

    private $pidFilePath;

    protected $singleton = true;

    protected $args;

    private static $daemons = array();

    public function __construct(array $args)
    {
        $this->pidFilePath = App::path('runtime') . '/daemon/' . get_called_class() . '.pid';
        $this->args = $args;

        $this->initialize();
    }

    public static function bind($name, $implement)
    {
        if (!isset(self::$daemons)) {
            self::$daemons = array();
        }
        self::$daemons[$name] = $implement;
    }

    public static function make($argc, array $argv)
    {
        if ($argc > 1) {
            $cmd = $name = $argv[2];
            if (array_key_exists($name, self::$daemons)) {
                $name = self::$daemons[$name];
            } else {
                $name = $name . 'Daemon';
            }
            unset($argv[0]);
            unset($argv[1]);
            unset($argv[2]);
            try {
                $daemon = DI::get($name, array('args' => self::parseArgs($argv)));
                if ($daemon instanceof Daemon) {
                    return $daemon;
                }
            } catch (\Exception $ex) {
                throw new ConsoleException("Daemon [$cmd] undefined.");
            }
            throw new ConsoleException("Class [$name] is not a Daemon.");
        }
        return null;
    }

    private static function parseArgs(array $args)
    {
        $results = array();
        foreach ($args as $arg) {
            if (preg_match('#(?<key>[a-zA-Z][a-zA-Z0-9\_]+)\=(?<value>[a-zA-Z0-9\_\\\/]+)#i', $arg, $matches)) {
                if (array_key_exists('key', $matches)) {
                    $results[$matches['key']] = array_key_exists('value', $matches) ? $matches['value'] : null;
                }
            } else {
                $results[] = $arg;
            }
        }

        return $results;
    }

    public function start()
    {
        if ($this->started) {
            return;
        }
        $this->started = true;

        global $stdin, $stdout, $stderr;

        if ($this->singleton) {
            $this->checkPidFile();
        }

        umask(0);

        if (pcntl_fork() === 0) {
            posix_setsid();

            if (pcntl_fork() === 0) {
                chdir("/");

                fclose(STDIN);
                fclose(STDOUT);
                fclose(STDERR);

                $stdin = fopen("/dev/null", "r");
                $stdout = fopen("/dev/null", "a");
                $stderr = fopen("/dev/null", "a");

                if ($this->singleton) {
                    $this->createPidFile();
                }

                $this->handle();
                $this->onStop();
            }
            App::end();
        }
    }

    public abstract function handle();

    public function stop()
    {
        if (!file_exists($this->pidFilePath)) {
            return true;
        }
        $pid = file_get_contents($this->pidFilePath);
        $pid = intval($pid);
        if ($pid > 0 && posix_kill($pid, SIGKILL)) {
            $this->onStop();
            return true;
        }
        throw new ConsoleException("The daemon process end abnormally.");
    }

    public function getPID()
    {
        if (!file_exists($this->pidFilePath)) {
            return false;
        }
        $pid = file_get_contents($this->pidFilePath);
        $pid = intval($pid);
        return $pid;
    }

    private function initialize()
    {
        if (!function_exists("pcntl_signal_dispatch")) {
            declare(ticks = 10);
        }

        $this->registeSignalHandler(function ($signal) {
            switch ($signal) {
                case SIGTERM:
                case SIGHUP:
                case SIGQUIT:
                    $this->onStop();
                    break;
                default:
                    return false;
            }
            return true;
        });

        if (function_exists("gc_enable")) {
            gc_enable();
        }
    }

    private function checkPidFile()
    {
        if (!file_exists($this->pidFilePath)) {
            return true;
        }
        $pid = file_get_contents($this->pidFilePath);
        $pid = intval($pid);
        if ($pid > 0 && posix_kill($pid, 0)) {
            throw new ConsoleException("The daemon process is already started.");
        } else {
            throw new ConsoleException("The daemon process end abnormally.");
        }
    }

    private function createPidFile()
    {
        File::writeText($this->pidFilePath, posix_getpid());
    }

    private function onStop()
    {
        if (file_exists($this->pidFilePath)) {
            unlink($this->pidFilePath);
        }
        App::end();
    }

    private function registeSignalHandler($closure)
    {
        pcntl_signal(SIGTERM, $closure, false);
        pcntl_signal(SIGQUIT, $closure, false);
    }
}