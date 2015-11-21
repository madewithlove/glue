<?php

namespace Madewithlove\Glue;

use Dotenv\Dotenv;
use Interop\Container\ContainerInterface;
use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Container\ReflectionContainer;
use League\Container\ServiceProvider\ServiceProviderInterface;
use League\Route\RouteCollection;
use Madewithlove\Glue\Configuration\ArrayConfiguration;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Madewithlove\Glue\Configuration\DefaultConfiguration;
use Madewithlove\Glue\Providers\ConfigurationServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relay\RelayBuilder;
use Zend\Diactoros\Response\SapiEmitter;

/**
 * @mixin RouteCollection
 */
class Application implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @param ConfigurationInterface|null $configuration
     * @param Container|null              $container
     */
    public function __construct(ConfigurationInterface $configuration = null, Container $container = null)
    {
        // Setup container
        $this->container = $container ?: new Container();
        $this->container->delegate(new ReflectionContainer());
        $this->container->share(ContainerInterface::class, $this->container);

        // Setup configuration
        $this->configuration = $configuration ?: new DefaultConfiguration();
        $this->setConfiguration($this->configuration);

        // Bind routes callable
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
        $this->configuration = new ArrayConfiguration($configuration);
    }

    /**
     * @return string
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param ConfigurationInterface|array $configuration
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        $this->configuration->setContainer($this->container);
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
        if ($this->container->has(ConfigurationInterface::class)) {
            return;
        }

        // Load dotenv files
        $dotenv = new Dotenv($this->configuration->getRootPath());
        $dotenv->load();

        // Bind configuration
        $this->container->add(ConfigurationInterface::class, function () {
            return $this->configuration;
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

        return $this->container->get('console')->run();
    }
}
