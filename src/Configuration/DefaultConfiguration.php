<?php

namespace Madewithlove\Nanoframework\Configuration;

use Franzl\Middleware\Whoops\Middleware as WhoopsMiddleware;
use Madewithlove\Nanoframework\Console\Commands\TinkerCommand;
use Madewithlove\Nanoframework\Console\PhinxServiceProvider;
use Madewithlove\Nanoframework\Http\Middlewares\LeagueRouteMiddleware;
use Madewithlove\Nanoframework\Http\Providers\RequestServiceProvider;
use Madewithlove\Nanoframework\Http\Providers\RoutingServiceProvider;
use Madewithlove\Nanoframework\Http\Providers\TwigServiceProvider;
use Madewithlove\Nanoframework\Providers\CommandBusServiceProvider;
use Madewithlove\Nanoframework\Providers\ConsoleServiceProvider;
use Madewithlove\Nanoframework\Providers\DatabaseServiceProvider;
use Madewithlove\Nanoframework\Providers\DebugbarServiceProvider;
use Madewithlove\Nanoframework\Providers\LogsServiceProvider;
use Madewithlove\Nanoframework\Providers\PathsServiceProvider;
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
