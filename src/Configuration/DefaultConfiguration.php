<?php

namespace Madewithlove\Glue\Configuration;

use Franzl\Middleware\Whoops\Middleware as WhoopsMiddleware;
use Madewithlove\Glue\Console\Commands\TinkerCommand;
use Madewithlove\Glue\Console\PhinxServiceProvider;
use Madewithlove\Glue\Http\Middlewares\LeagueRouteMiddleware;
use Madewithlove\Glue\Http\Providers\RequestServiceProvider;
use Madewithlove\Glue\Http\Providers\RoutingServiceProvider;
use Madewithlove\Glue\Http\Providers\TwigServiceProvider;
use Madewithlove\Glue\Providers\CommandBusServiceProvider;
use Madewithlove\Glue\Providers\ConsoleServiceProvider;
use Madewithlove\Glue\Providers\DatabaseServiceProvider;
use Madewithlove\Glue\Providers\DebugbarServiceProvider;
use Madewithlove\Glue\Providers\LogsServiceProvider;
use Madewithlove\Glue\Providers\PathsServiceProvider;
use Psr7Middlewares\Middleware\DebugBar;
use Psr7Middlewares\Middleware\FormatNegotiator;

class DefaultConfiguration extends AbstractConfiguration
{
    /**
     * @return string[]
     */
    public function getProviders()
    {
        return [
            'request'    => RequestServiceProvider::class,
            'paths'      => PathsServiceProvider::class,
            'routing'    => RoutingServiceProvider::class,
            'twig'       => TwigServiceProvider::class,
            'db'         => DatabaseServiceProvider::class,
            'logs'       => LogsServiceProvider::class,
            'commandbus' => CommandBusServiceProvider::class,
            'migrations' => PhinxServiceProvider::class,
        ];
    }

    /**
     * @return string[]
     */
    public function getDebugProviders()
    {
        return [
            ConsoleServiceProvider::class,
            DebugbarServiceProvider::class,
        ];
    }

    /**
     * @return string[]
     */
    public function getPaths()
    {
        $rootPath = $this->container->get('paths.root');

        return [
            'builds'     => $rootPath.'/public/builds',
            'factories'  => $rootPath.'/resources/factories',
            'migrations' => $rootPath.'/resources/migrations',
            'views'      => $rootPath.'/resources/views',
            'cache'      => $rootPath.'/storage/cache',
            'logs'       => $rootPath.'/storage/logs',
        ];
    }

    /**
     * @return string[]
     */
    public function getMiddlewares()
    {
        switch (getenv('APP_ENV')) {
            case 'local':
                return [
                    FormatNegotiator::class,
                    DebugBar::class,
                    WhoopsMiddleware::class,
                    LeagueRouteMiddleware::class,
                ];

            default:
                return [
                    LeagueRouteMiddleware::class,
                ];
        }
    }

    /**
     * @return string[]
     */
    public function getConsoleCommands()
    {
        return [
            TinkerCommand::class,
        ];
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return getenv('APP_ENV') === 'local';
    }
}
