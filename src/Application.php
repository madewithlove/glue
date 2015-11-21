<?php

namespace Madewithlove\Nanoframework;

use Dotenv\Dotenv;
use Interop\Container\ContainerInterface;
use League\Container\Container;
use League\Container\ReflectionContainer;
use League\Container\ServiceProvider\ServiceProviderInterface;
use Madewithlove\Nanoframework\Configuration\ConfigurationInterface;
use Madewithlove\Nanoframework\Configuration\DefaultConfiguration;
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
    protected $configuration = DefaultConfiguration::class;

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
    }

    /**
     * @param string $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Boot the application.
     */
    public function boot()
    {
        // Load dotenv files
        $dotenv = new Dotenv($this->rootPath);
        $dotenv->load();

        // Bind configuration
        $this->container->add('paths.root', $this->rootPath);
        $this->container->add(ConfigurationInterface::class, function () {
            $class = $this->configuration;

            return new $class($this->container);
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
