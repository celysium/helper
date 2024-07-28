<?php

namespace Celysium\Helper;

use Celysium\Helper\Middlewares\Mobile;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadMiddlewares();
    }

    private function loadMiddlewares()
    {
        app('router')->aliasMiddleware('mobile', Mobile::class);
    }

}
