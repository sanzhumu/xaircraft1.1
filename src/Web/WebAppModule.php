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
use Xaircraft\Globals;
use Xaircraft\Module\AppModule;
use Xaircraft\Router\Router;
use Xaircraft\Web\Http\Request;
use Xaircraft\Web\Http\Response;

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
        }

        $this->router->registerDefaultMatchedHandler(function ($params) {
            DI::bindSingleton(Request::class, new Request($params));
            DI::bindSingleton(Response::class, new Response());
        });


    }
}