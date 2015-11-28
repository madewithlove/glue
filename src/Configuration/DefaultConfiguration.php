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
use Madewithlove\Glue\Definitions\Console\PhinxDefinition;
use Madewithlove\Glue\Definitions\Console\SymfonyConsoleDefinition;
use Madewithlove\Glue\Definitions\DebugbarDefinition;
use Madewithlove\Glue\Definitions\EloquentDefinition;
use Madewithlove\Glue\Definitions\FlysystemDefinition;
use Madewithlove\Glue\Definitions\LeagueRouteDefinition;
use Madewithlove\Glue\Definitions\MonologDefinition;
use Madewithlove\Glue\Definitions\RelayDefinition;
use Madewithlove\Glue\Definitions\TacticianDefinition;
use Madewithlove\Glue\Definitions\Twig\TwigDefinition;
use Madewithlove\Glue\Definitions\Twig\UrlGeneratorDefinition;
use Madewithlove\Glue\Definitions\Twig\WebpackDefinition;
use Madewithlove\Glue\Definitions\ZendDiactorosDefinition;
use Madewithlove\Glue\Http\Middlewares\LeagueRouteMiddleware;
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
            'providers' => $this->configureProviders(),
            'middlewares' => $this->configureMiddlewares(),
            'definitions' => $this->configureDefinitionProviders(),
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
    public function configureDefinitionProviders()
    {
        $views = $this->getPath('views');

        $providers = [
            'assets' => new WebpackDefinition($this->getPath('assets')),
            'request' => new ZendDiactorosDefinition(),
            'bus' => new TacticianDefinition(),
            'pipeline' => new RelayDefinition($this->getMiddlewares()),
            'routing' => new LeagueRouteDefinition(),
            'db' => new EloquentDefinition([
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
            ]),
            'filesystem' => new FlysystemDefinition([
                'default' => 'local',
                'adapters' => [
                    'local' => new Local($this->getRootPath()),
                ],
            ]),
            'logging' => new MonologDefinition([
                'path' => $this->getPath('logs'),
                'filename' => date('Y-m-d').'.log',
            ]),
            'console' => new SymfonyConsoleDefinition([
                TinkerCommand::class,
            ]),
            'views' => new TwigDefinition([
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
            ]),
            'url' => new UrlGeneratorDefinition($this->namespace),
        ];

        if ($this->isDebug()) {
            $providers = array_merge($providers, [
                'debugbar' => new DebugbarDefinition(),
                'migrations' => new PhinxDefinition([
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
                ]),
            ]);
        }

        return $providers;
    }
}
