<?php
namespace Madewithlove\Nanoframework;

use Dotenv\Dotenv;
use Franzl\Middleware\Whoops\Middleware as WhoopsMiddleware;
use Interop\Container\ContainerInterface;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Madewithlove\Nanoframework\Configuration\DefaultConfiguration;
use Madewithlove\Nanoframework\Middlewares\LeagueRouteMiddleware;
use Madewithlove\Nanoframework\Providers\ConfigurationServiceProvider;
use Psr\Http\Message\ServerRequestInterface;
use Relay\RelayBuilder;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;

class Application
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @param string         $rootPath
     * @param Container|null $container
     */
    public function __construct($rootPath, Container $container = null)
    {
        $this->rootPath = $rootPath;

        // Setup container
        $this->container = $container ?: new Container();
        $this->container->delegate(new ReflectionContainer());
        $this->container->share(ContainerInterface::class, $this->container);

        // Load dotenv files
        $dotenv = new Dotenv($this->rootPath);
        $dotenv->load();

        // Bind configuration
        $this->container->add('paths.root', $this->rootPath);
        $this->container->addServiceProvider(ConfigurationServiceProvider::class);

        // Register providers
        array_walk($this->container->get('config.providers'), [$this->container, 'addServiceProvider']);
    }

    /**
     * Run the application.
     */
    public function run()
    {
        $request  = $this->container->get(ServerRequestInterface::class);
        $response = new Response();

        // Build Relay factory
        $builder = new RelayBuilder(function ($callable) {
            return is_string($callable) ? $this->container->get($callable) : $callable;
        });

        // Process middlewares
        $middlewares = $this->container->get('config.middlewares');
        $relay       = $builder->newInstance($middlewares);
        $response    = $relay($request, $response);

        (new SapiEmitter())->emit($response);
    }
}
