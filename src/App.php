<?php
/**
 * Xaircraft PHP Framework version 1.1
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/11
 * Time: 11:11
 */

namespace Xaircraft;


use Xaircraft\Module\AppModuleLoader;
use Xaircraft\Module\AppModuleState;
use Xaircraft\Core\Runtime;
use Xaircraft\Exception\AppModuleException;
use Xaircraft\Web\WebAppModule;

class App extends Container
{
    private static $app;

    /**
     * @var array Base paths
     */
    private $paths;

    private $environment = array();

    private $appModules = array();

    public function run()
    {
        if (isset($this->appModules) && !empty($this->appModules)) {
            $currentModule = "";
            try {
                $state = new AppModuleState();
                foreach ($this->appModules as $item) {
                    $currentModule = $item;
                    /**
                     * @var $module \Xaircraft\Module\AppModule
                     */
                    $module = DI::get($item, array('state' => $state));
                    $module->handle();
                    $state = $module->state();
                    if ($state->stop) {
                        break;
                    }
                }
            } catch (\Exception $ex) {
                $this->onErrorAppModule(new AppModuleException($currentModule, $ex->getMessage(), $ex->getCode(), $ex));
            }
        }
    }

    public function bindPath($paths)
    {
        if (isset($paths)) {
            if (is_array($paths) && !empty($paths)) {
                $this->paths = $paths;
            } else if (is_readable($paths) && $paths !== '') {
                $this->paths = require_once $paths;
            }
        }
    }

    public static function instance()
    {
        if (!isset(self::$app)) {
            self::$app = new App();
            self::$app->initialize();
        }
        return self::$app;
    }

    public static function path($key)
    {
        return self::instance()->getPath($key);
    }

    public static function environment($key, $value = null)
    {
        if (isset($value)) {
            self::instance()->setEnvironment($key, $value);
        }
        return self::instance()->getEnvironment($key);
    }

    public static function module($module)
    {
        DI::bindSingleton($module, $module);
        self::instance()->appModules[] = $module;
    }

    private function initialize()
    {
        $this->initializeEnvironment();
        $this->initializeBaseModules();
    }

    private function getPath($key)
    {
        if (isset($this->paths) && isset($key) && array_key_exists($key, $this->paths)) {
            return $this->paths[$key];
        }
        return null;
    }

    private function initializeEnvironment()
    {
        if (!isset($this->environment) || empty($this->environment)) {
            $this->environment[Globals::ENV_FRAMEWORK] = 'Xaircraft';
            $this->environment[Globals::ENV_VERSION] = '1.1';
            $this->environment[Globals::ENV_MODE] = 'dev';
            $this->environment[Globals::ENV_HOST] = '';
            $this->environment[Globals::ENV_RUNTIME_MODE] = php_sapi_name();
            $this->environment[Globals::ENV_OS] = Runtime::getOS();
            $this->environment[Globals::ENV_OS_INFO] = php_uname();
        }
    }

    private function initializeBaseModules()
    {
        self::module(AppModuleLoader::class);
        self::module(WebAppModule::class);
    }

    private function getEnvironment($key)
    {
        if (isset($key) && array_key_exists($key, $this->environment)) {
            return $this->environment[$key];
        }
        return null;
    }

    private function setEnvironment($key, $value)
    {
        if (isset($key) && array_key_exists($key, $this->environment) && isset($value)) {
            if (array_key_exists($key, array(
                Globals::ENV_FRAMEWORK,
                Globals::ENV_OS,
                Globals::ENV_OS_INFO,
                Globals::ENV_RUNTIME_MODE,
                Globals::ENV_VERSION
            ))) {
                throw new \Exception("Can't set environment - $key", Globals::EXCEPTION_ERROR_ENVIRONMENT_SET_LIMIT);
            }

            if (Globals::ENV_MODE === $key && false === array_search($value, array(
                    Globals::MODE_DEV, Globals::MODE_PUB
                ))) {
                throw new \Exception("Mode must be " . Globals::MODE_DEV . " or " . Globals::MODE_PUB, Globals::EXCEPTION_ERROR_ENVIRONMENT);
            }
            $this->environment[$key] = $value;
        }
    }

    private function onErrorAppModule(AppModuleException $ex)
    {
        var_dump($ex);
    }
}