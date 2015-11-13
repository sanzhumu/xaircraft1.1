<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 13:48
 */

namespace Xaircraft\Web;


use Xaircraft\App;
use Xaircraft\DI;
use Xaircraft\Exception\URLRouterException;
use Xaircraft\Globals;
use Xaircraft\Module\AppModule;
use Xaircraft\Router\Router;
use Xaircraft\Web\Http\Request;
use Xaircraft\Web\Http\Response;
use Xaircraft\Web\Mvc\Controller;

class WebAppModule extends AppModule
{
    /**
     * @var \Xaircraft\Router\Router
     */
    private $router;

    public function handle()
    {
        if (Globals::RUNTIME_MODE_APACHE2HANDLER !== App::environment(Globals::ENV_RUNTIME_MODE)) {
            return;
        }

        $this->router = Router::getInstance(App::path('routes'), App::path('filter'));

        $defaultRouterToken = App::environment(Globals::ROUTER_DEFAULT_TOKENS);
        if (isset($defaultRouterToken) && !empty($defaultRouterToken)) {
            $this->router->baseMappings['default']['default'] = $defaultRouterToken;
        } else {
            $defaultRouterToken = $this->router->baseMappings['default']['default'];
        }

        $this->router->registerMatchedHandler(function ($params) {
            DI::bindSingleton(Request::class, new Request($params));
            DI::bindSingleton(Response::class, new Response());
        });

        $this->router->registerDefaultMatchedHandler(function ($params) use ($defaultRouterToken) {
            $namespace = null;
            if (array_key_exists('namespace', $params)) {
                $namespace = $params['namespace'];
            }
            if (array_key_exists('controller', $params)) {
                $controller = $params['controller'];
            }
            if (array_key_exists('action', $params)) {
                $action = $params['action'];
            }
            if (!isset($controller)) {
                $controller = $defaultRouterToken['controller'];
            }
            if (!isset($action)) {
                $action = $defaultRouterToken['action'];
            }
            Controller::invoke($controller, $action, $namespace);
        });

        $this->router->missing(function () {
            throw new URLRouterException("URL Routing missing.");
        });

        $this->router->routing();
    }

    public function appStart()
    {
        // TODO: Implement appStart() method.
    }

    public function appEnd()
    {
        // TODO: Implement appEnd() method.
    }
}