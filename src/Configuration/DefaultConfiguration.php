<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Configuration;

use Franzl\Middleware\Whoops\Middleware as WhoopsMiddleware;
use League\Flysystem\Adapter\Local;
use Madewithlove\Glue\Console\Commands\TinkerCommand;
use Madewithlove\Glue\Console\PhinxServiceProvider;
use Madewithlove\Glue\Console\SymfonyConsoleServiceProvider;
use Madewithlove\Glue\Definitions\FlysystemDefinition;
use Madewithlove\Glue\Definitions\MonologDefinition;
use Madewithlove\Glue\Http\Middlewares\LeagueRouteMiddleware;
use Madewithlove\Glue\Http\Providers\Assets\WebpackServiceProvider;
use Madewithlove\Glue\Http\Providers\LeagueRouteServiceProvider;
use Madewithlove\Glue\Http\Providers\RelayServiceProvider;
use Madewithlove\Glue\Http\Providers\RequestServiceProvider;
use Madewithlove\Glue\Http\Providers\TwigServiceProvider;
use Madewithlove\Glue\Http\Providers\UrlGeneratorServiceProvider;
use Madewithlove\Glue\Providers\CommandBusServiceProvider;
use Madewithlove\Glue\Providers\DebugbarServiceProvider;
use Madewithlove\Glue\Providers\EloquentServiceProvider;
use Madewithlove\Glue\Providers\FlysystemServiceProvider;
use Madewithlove\Glue\Providers\MonologServiceProvider;
use Madewithlove\Glue\Utils;
use Psr7Middlewares\Middleware\DebugBar;
use Psr7Middlewares\Middleware\FormatNegotiator;
use Twig_Extension_Debug;
use Twig_Loader_Array;
use Twig_Loader_Filesystem;

class DefaultConfiguration extends AbstractConfiguration
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->configure();

        parent::__construct($attributes);
    }

    /**
     * Configure with the container and environment variables.
     */
    public function configure()
    {
        // Reset debug mode from env variables now that they're available
        $debug = $this->debug !== null
            ? $this->debug
            : !getenv('APP_ENV') || getenv('APP_ENV') === 'local';

        $this->attributes = [
            'debug' => $debug,
            'rootPath' => $this->configureRootPath(),
            'namespace' => $this->configureNamespace(),
            'paths' => $this->configurePaths(),
            'commands' => $this->configureCommands(),
            'providers' => $this->configureProviders(),
            'middlewares' => $this->configureMiddlewares(),
            'packages' => $this->configurePackagesConfiguration(),
        ];
    }

    /**
     * @return string
     */
    protected function configureRootPath()
    {
        $folder = Utils::find('composer.json', getcwd());
        $folder = str_replace('composer.json', null, $folder);
        $folder = rtrim($folder, DS);

        return $folder;
    }

    /**
     * @return string|void
     */
    protected function configureNamespace()
    {
        $composer = $this->getRootPath().'/composer.json';
        if (!file_exists($composer)) {
            return;
        }

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
     * @return array
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
    public function configureCommands()
    {
        return [
            TinkerCommand::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function configureProviders()
    {
        $providers = [
            'commandbus' => CommandBusServiceProvider::class,
            'db' => EloquentServiceProvider::class,
            'filesystem' => FlysystemServiceProvider::class,
            'logs' => MonologServiceProvider::class,
            'request' => RequestServiceProvider::class,
            'routing' => LeagueRouteServiceProvider::class,
            'view' => TwigServiceProvider::class,
            'pipeline' => RelayServiceProvider::class,
            'url' => UrlGeneratorServiceProvider::class,
            'assets' => WebpackServiceProvider::class,
        ];

        if ($this->isDebug()) {
            $providers += [
                'console' => SymfonyConsoleServiceProvider::class,
                'migrations' => PhinxServiceProvider::class,
                'debugbar' => DebugbarServiceProvider::class,
            ];
        }

        return $providers;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureMiddlewares()
    {
        if ($this->isDebug()) {
            return [
                FormatNegotiator::class,
                WhoopsMiddleware::class,
                LeagueRouteMiddleware::class,
                DebugBar::class,
            ];
        }

        return [
            LeagueRouteMiddleware::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionProviders()
    {
        $views = $this->getPath('views');

        return [
            new FlysystemDefinition([
                'default' => 'local',
                'adapters' => [
                    'local' => new Local($this->getRootPath()),
                ],
            ]),
            new MonologDefinition([
                'path' => $this->getPath('logs'),
                'filename' => date('Y-m-d').'.log',
            ]),
        ];

        return [
            'phinx' => [
                'paths' => [
                    'migrations' => $this->getPath('migrations'),
                ],
                'environments' => [
                    'default_migration_table' => 'phinxlog',
                    'default_database' => 'default',
                    'default' => [
                        'adapter' => 'mysql',
                        'host' => getenv('DB_HOST'),
                        'name' => getenv('DB_DATABASE'),
                        'user' => getenv('DB_USERNAME'),
                        'pass' => getenv('DB_PASSWORD'),
                        'port' => 3306,
                        'charset' => 'utf8',
                    ],
                ],
            ],
            'eloquent' => [
                'connections' => [
                    'default' => [
                        'driver' => 'mysql',
                        'host' => getenv('DB_HOST'),
                        'database' => getenv('DB_DATABASE'),
                        'username' => getenv('DB_USERNAME'),
                        'password' => getenv('DB_PASSWORD'),
                        'charset' => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix' => '',
                    ],
                ],
            ],
            'twig' => [
                'loader' => is_dir($views) ? new Twig_Loader_Filesystem($views) : new Twig_Loader_Array([]),
                'environment' => [
                    'debug' => $this->isDebug(),
                    'auto_reload' => $this->isDebug(),
                    'strict_variables' => false,
                    'cache' => $this->getPath('cache').DS.'twig',
                ],
                'extensions' => array_filter([
                    $this->isDebug() ? new Twig_Extension_Debug() : null,
                ]),
            ],
        ];
    }
}
