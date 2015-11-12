<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 13:48
 */

namespace Xaircraft\Web;


use Xaircraft\App;
use Xaircraft\Globals;
use Xaircraft\Module\AppModule;
use Xaircraft\Router\Router;

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

        var_dump($this->router);
    }
}