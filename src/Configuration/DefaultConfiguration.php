<?php
namespace Madewithlove\Nanoframework\Configuration;

use Franzl\Middleware\Whoops\Middleware as WhoopsMiddleware;
use League\Route\RouteCollection;
use Madewithlove\Nanoframework\Middlewares\LeagueRouteMiddleware;
use Madewithlove\Nanoframework\Providers\PathsServiceProvider;
use Madewithlove\Nanoframework\Providers\RequestServiceProvider;
use Madewithlove\Nanoframework\Providers\RoutingServiceProvider;

class DefaultConfiguration extends AbstractConfiguration
{
    /**
     * @return array
     */
    public function getProviders()
    {
        return [
            RequestServiceProvider::class,
            PathsServiceProvider::class,
            RoutingServiceProvider::class,
        ];
    }

    /**
     * @return array
     */
    public function getDebugProviders()
    {
        return [

        ];
    }

    /**
     * @return array
     */
    public function getPaths()
    {
        $rootPath = $this->container->get('paths.root');

        return [
            'builds'    => $rootPath.'/public/builds',
            'factories' => $rootPath.'/resources/factories',
            'views'     => $rootPath.'/resources/views',
            'cache'     => $rootPath.'/storage/cache',
            'logs'      => $rootPath.'/storage/logs',
        ];
    }

    /**
     * @return array
     */
    public function getMiddlewares()
    {
        return [
            WhoopsMiddleware::class,
            LeagueRouteMiddleware::class,
        ];
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return getenv('APP_ENV') === 'local';
    }

    /**
     * @param RouteCollection $router
     *
     * @return RouteCollection
     */
    public function getRoutes(RouteCollection $router)
    {
        return $router;
    }
}
