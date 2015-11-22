<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Configuration;

use Franzl\Middleware\Whoops\Middleware as WhoopsMiddleware;
use Madewithlove\Glue\Console\Commands\BootstrapCommand;
use Madewithlove\Glue\Console\Commands\TinkerCommand;
use Madewithlove\Glue\Console\ConsoleServiceProvider;
use Madewithlove\Glue\Console\PhinxServiceProvider;
use Madewithlove\Glue\Http\Middlewares\LeagueRouteMiddleware;
use Madewithlove\Glue\Http\Providers\Assets\WebpackServiceProvider;
use Madewithlove\Glue\Http\Providers\RequestServiceProvider;
use Madewithlove\Glue\Http\Providers\RoutingServiceProvider;
use Madewithlove\Glue\Http\Providers\TwigServiceProvider;
use Madewithlove\Glue\Http\Providers\UrlGeneratorServiceProvider;
use Madewithlove\Glue\Providers\CommandBusServiceProvider;
use Madewithlove\Glue\Providers\DatabaseServiceProvider;
use Madewithlove\Glue\Providers\DebugbarServiceProvider;
use Madewithlove\Glue\Providers\FilesystemServiceProvider;
use Madewithlove\Glue\Providers\LogsServiceProvider;
use Madewithlove\Glue\Providers\PathsServiceProvider;
use Madewithlove\Glue\Utils;
use Psr7Middlewares\Middleware\DebugBar;
use Psr7Middlewares\Middleware\FormatNegotiator;

class DefaultConfiguration extends AbstractConfiguration
{
    /**
     * DefaultConfiguration constructor.
     */
    public function configure()
    {
        $environment = getenv('APP_ENV');

        $this->debug = $environment ? $environment === 'local' : true;
        $this->rootPath = $this->configureRootPath();
        $this->namespace = $this->configureNamespace();
        $this->providers = $this->configureProviders();
        $this->paths = $this->configurePaths();
        $this->middlewares = $this->configureMiddlewares();
        $this->commands = $this->configureCommands();
    }

    /**
     * @return string
     */
    protected function configureRootPath()
    {
        return str_replace('composer.json', null, Utils::find('composer.json'));
    }

    /**
     * @return string
     */
    protected function configureNamespace()
    {
        $composer = $this->getRootPath().'/composer.json';
        $composer = file_get_contents($composer);
        $composer = json_decode($composer, true);

        $namespaces = array_get($composer, 'autoload.psr-4', []);
        $namespaces = $namespaces ?: array_get($composer, 'autoload.psr-0', []);
        if (!$namespaces) {
            return;
        }

        return trim(array_keys($namespaces)[0], '\\');
    }

    /**
     * @return string[]
     */
    public function configureProviders()
    {
        $providers = [
            'paths' => PathsServiceProvider::class,
            'commandbus' => CommandBusServiceProvider::class,
            'db' => DatabaseServiceProvider::class,
            'filesystem' => FilesystemServiceProvider::class,
            'logs' => LogsServiceProvider::class,
            'request' => RequestServiceProvider::class,
            'routing' => RoutingServiceProvider::class,
            'view' => TwigServiceProvider::class,
            'url' => UrlGeneratorServiceProvider::class,
            'assets' => WebpackServiceProvider::class,
        ];

        if ($this->debug) {
            $providers += [
                'console' => ConsoleServiceProvider::class,
                'migrations' => PhinxServiceProvider::class,
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
        $rootPath = $this->getRootPath();

        return [
            'assets' => $rootPath.'/public/builds',
            'web' => $rootPath.'/public',
            'migrations' => $rootPath.'/resources/migrations',
            'views' => $rootPath.'/resources/views',
            'cache' => $rootPath.'/storage/cache',
            'logs' => $rootPath.'/storage/logs',
        ];
    }

    /**
     * @return string[]
     */
    public function configureMiddlewares()
    {
        if ($this->debug) {
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
            BootstrapCommand::class,
            TinkerCommand::class,
        ];
    }
}
