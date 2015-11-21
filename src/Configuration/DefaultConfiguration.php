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
     * DefaultConfiguration constructor.
     */
    public function __construct()
    {
        $this->debug       = getenv('APP_ENV') === 'local';
        $this->rootPath    = $this->configureRootPath();
        $this->namespace   = $this->configureNamespace();
        $this->providers   = $this->configureProviders();
        $this->paths       = $this->configurePaths();
        $this->middlewares = $this->configureMiddlewares();
        $this->commands    = $this->configureCommands();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'debug'       => getenv('APP_ENV') === 'local',
            'rootPath'    => $this->configureRootPath(),
            'namespace'   => $this->configureNamespace(),
            'providers'   => $this->configureProviders(),
            'paths'       => $this->configurePaths(),
            'middlewares' => $this->configureMiddlewares(),
            'commands'    => $this->configureCommands(),
        ];
    }

    /**
     * @return string
     */
    protected function configureRootPath()
    {
        $folder = getcwd();
        while (!file_exists($folder.'/composer.json')) {
            $folder .= '/..';
        }

        return realpath($folder);
    }

    /**
     * @return string
     */
    protected function configureNamespace()
    {
        $composer = $this->rootPath.'/composer.json';
        $composer = file_get_contents($composer);
        $composer = json_decode($composer, true);

        $namespaces = array_keys($composer['autoload']['psr-4']);
        $namespace  = trim($namespaces[0], '\\');

        return $namespace;
    }

    /**
     * @return string[]
     */
    public function configureProviders()
    {
        $providers = [
            'request'    => RequestServiceProvider::class,
            'paths'      => PathsServiceProvider::class,
            'routing'    => RoutingServiceProvider::class,
            'twig'       => TwigServiceProvider::class,
            'db'         => DatabaseServiceProvider::class,
            'logs'       => LogsServiceProvider::class,
            'commandbus' => CommandBusServiceProvider::class,
            'migrations' => PhinxServiceProvider::class,
        ];

        if ($this->isDebug()) {
            $providers += [
                'console'  => ConsoleServiceProvider::class,
                'debugbar' => DebugbarServiceProvider::class,
            ];
        }

        return $providers;
    }

    /**
     * @return string[]
     */
    public function configurePaths()
    {
        return [
            'assets'     => $this->rootPath.'/public/builds',
            'factories'  => $this->rootPath.'/resources/factories',
            'migrations' => $this->rootPath.'/resources/migrations',
            'views'      => $this->rootPath.'/resources/views',
            'cache'      => $this->rootPath.'/storage/cache',
            'logs'       => $this->rootPath.'/storage/logs',
        ];
    }

    /**
     * @return string[]
     */
    public function configureMiddlewares()
    {
        if ($this->isDebug()) {
            return [
                FormatNegotiator::class,
                DebugBar::class,
                WhoopsMiddleware::class,
                LeagueRouteMiddleware::class,
            ];
        }

        return [
            LeagueRouteMiddleware::class,
        ];
    }

    /**
     * @return string[]
     */
    public function configureCommands()
    {
        return [
            TinkerCommand::class,
        ];
    }
}
