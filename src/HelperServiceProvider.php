<?php

namespace Celysium\Helper;

use Celysium\Helper\Middlewares\IranianMobile;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadMiddlewares();
    }

    private function loadMiddlewares()
    {
        $router = app('router');

        $router->aliasMiddleware('iranian-mobile', IranianMobile::class);
    }

}
