<?php

namespace Madewithlove\Nanoframework;

use Dotenv\Dotenv;
use Interop\Container\ContainerInterface;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Container\ServiceProvider\ServiceProviderInterface;
use League\Route\RouteCollection;
use Madewithlove\Nanoframework\Configuration\ConfigurationInterface;
use Madewithlove\Nanoframework\Configuration\DefaultConfiguration;
use Madewithlove\Nanoframework\Providers\ConfigurationServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relay\RelayBuilder;
use Symfony\Component\Console\Application as Console;
use Zend\Diactoros\Response\SapiEmitter;

/**
 * @mixin RouteCollection
 */
class Application
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $configuration = DefaultConfiguration::class;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @var array
     */
    protected $routes = [];

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

        $this->container->add('routes', function () {
            return $this->routes;
        });
    }

    /**
     * Delegate calls to the Router.
     *
     * @param string $name
     * @param array  $arguments
     */
    public function __call($name, array $arguments)
    {
        $this->boot();

        $this->routes[] = call_user_func_array([$this->container->get('router'), $name], $arguments);
    }

    //////////////////////////////////////////////////////////////////////
    //////////////////////////// CONFIGURATION ///////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * @param array $configuration
     */
    public function configure(array $configuration)
    {
        $this->container->add('config', $configuration);
    }

    /**
     * @param string $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    //////////////////////////////////////////////////////////////////////
    ////////////////////////////// RUNTIME ///////////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * Boot the application.
     */
    public function boot()
    {
        // If already booted, cancel
        if ($this->container->has('paths.root')) {
            return;
        }

        // Load dotenv files
        $dotenv = new Dotenv($this->rootPath);
        $dotenv->load();

        // Bind configuration
        $this->container->add('paths.root', $this->rootPath);
        $this->container->add(ConfigurationInterface::class, function () {
            return $this->container->get($this->configuration);
        });

        // Register providers
        $this->container->addServiceProvider(ConfigurationServiceProvider::class);
        $providers = $this->container->get('config.providers');
        array_walk($providers, [$this->container, 'addServiceProvider']);

        /* @var ServiceProviderInterface $instance */
        // Boot providers that need it
        foreach ($providers as $provider) {
            $instance = new $provider();
            $instance->setContainer($this->container);
            if (method_exists($instance, 'boot')) {
                $instance->boot();
            }
        }
    }

    /**
     * Run the application.
     */
    public function run()
    {
        $this->boot();

        $request  = $this->container->get(ServerRequestInterface::class);
        $response = $this->container->get(ResponseInterface::class);

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

    /**
     * Run the console application.
     *
     * @return mixed
     */
    public function console()
    {
        $this->boot();

        return $this->container->get(Console::class)->run();
    }
}
