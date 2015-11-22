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
use Madewithlove\Glue\Console\PhinxServiceProvider;
use Madewithlove\Glue\Http\Middlewares\LeagueRouteMiddleware;
use Madewithlove\Glue\Http\Providers\Assets\WebpackServiceProvider;
use Madewithlove\Glue\Http\Providers\RequestServiceProvider;
use Madewithlove\Glue\Http\Providers\RoutingServiceProvider;
use Madewithlove\Glue\Http\Providers\TwigServiceProvider;
use Madewithlove\Glue\Http\Providers\UrlGeneratorServiceProvider;
use Madewithlove\Glue\Providers\CommandBusServiceProvider;
use Madewithlove\Glue\Providers\ConsoleServiceProvider;
use Madewithlove\Glue\Providers\DatabaseServiceProvider;
use Madewithlove\Glue\Providers\DebugbarServiceProvider;
use Madewithlove\Glue\Providers\FilesystemServiceProvider;
use Madewithlove\Glue\Providers\LogsServiceProvider;
use Madewithlove\Glue\Providers\PathsServiceProvider;
use Psr7Middlewares\Middleware\DebugBar;
use Psr7Middlewares\Middleware\FormatNegotiator;

class DefaultConfiguration extends AbstractConfiguration
{
    /**
     * DefaultConfiguration constructor.
     */
    public function configure()
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

        $namespaces = array_keys(array_get($composer, 'autoload.psr-4', []));
        if (!$namespaces) {
            return;
        }

        return trim($namespaces[0], '\\');
    }

    /**
     * @return string[]
     */
    public function configureProviders()
    {
        $providers = [
            'paths'      => PathsServiceProvider::class,
            'commandbus' => CommandBusServiceProvider::class,
            'db'         => DatabaseServiceProvider::class,
            'filesystem' => FilesystemServiceProvider::class,
            'logs'       => LogsServiceProvider::class,
            'request'    => RequestServiceProvider::class,
            'routing'    => RoutingServiceProvider::class,
            'view'       => TwigServiceProvider::class,
            'url'        => UrlGeneratorServiceProvider::class,
            'assets'     => WebpackServiceProvider::class,
        ];

        if ($this->debug) {
            $providers += [
                'console'    => ConsoleServiceProvider::class,
                'migrations' => PhinxServiceProvider::class,
                'debugbar'   => DebugbarServiceProvider::class,
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
            'web'        => $this->rootPath.'/public',
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
