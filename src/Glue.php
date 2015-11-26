<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue;

use Acclimate\Container\ContainerAcclimator;
use Dotenv\Dotenv;
use Interop\Container\ContainerInterface;
use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Container\ContainerInterface as LeagueContainerInterface;
use League\Container\ReflectionContainer;
use League\Route\RouteCollection;
use Madewithlove\Glue\Configuration\AbstractConfiguration;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Madewithlove\Glue\Configuration\DefaultConfiguration;
use Madewithlove\Glue\Providers\ConfigurationServiceProvider;
use Madewithlove\Glue\Providers\PathsServiceProvider;
use Madewithlove\Glue\Traits\Configurable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\SapiEmitter;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * @mixin RouteCollection
 * @mixin AbstractConfiguration
 */
class Glue implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use Configurable;

    /**
     * @var string
     */
    const VERSION = '0.1.0';

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @param ConfigurationInterface|null $configuration
     * @param Container|null              $container
     */
    public function __construct(ConfigurationInterface $configuration = null, $container = null)
    {
        $this->container = $this->sanitizeContainer($container);

        // Setup configuration
        $this->setConfiguration($configuration ?: new DefaultConfiguration());

        // Load environment variables
        $path = $this->configuration->getRootPath() ?: getcwd();
        if (file_exists($path.'/.env')) {
            $dotenv = new Dotenv($path);
            $dotenv->load();

            // Re-set configuration after Dotenv for env dependant variables
            $this->setConfiguration($this->configuration);
        }

        // Bind routes callable
        $this->container->add('routes', function () {
            return $this->routes;
        });
    }

    /**
     * @param object|null $container
     *
     * @return Container
     */
    public function sanitizeContainer($container = null)
    {
        // Setup container
        $delegates       = [new ReflectionContainer()];
        $parentContainer = new Container();
        $parentContainer->share(ContainerInterface::class, $parentContainer);

        if ($container) {
            // Unify container to PSR11
            $acclimator = new ContainerAcclimator();
            $container  = $acclimator->acclimate($container);

            array_unshift($delegates, $container);
        }

        // Bind delegates
        foreach ($delegates as $delegate) {
            $parentContainer->delegate($delegate);
        }

        return $parentContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(LeagueContainerInterface $container)
    {
        // Set our own container as delegate
        $container->delegate($this->container);

        $this->container = $container;
    }

    /**
     * Delegate calls to whater Router is bound.
     *
     * @param string $name
     * @param array  $arguments
     */
    public function __call($name, array $arguments)
    {
        $this->boot();

        // Delegate to Configuration
        if (method_exists($this->configuration, $name) && !in_array($name, ['get'], true)) {
            return $this->configuration->$name(...$arguments);
        }

        // Delegate to Router
        if ($this->container->has('router')) {
            $this->routes[] = $this->container->get('router')->$name(...$arguments);
        }
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

        // Bind configuration
        $this->container->add(ConfigurationInterface::class, function () {
            return $this->getConfiguration();
        });

        // Register providers
        $this->container->addServiceProvider(ConfigurationServiceProvider::class);
        $this->container->addServiceProvider(PathsServiceProvider::class);
        foreach ($this->configuration->getProviders() as $provider) {
            $this->container->addServiceProvider($provider);
        }
    }

    /**
     * Run the application.
     */
    public function run()
    {
        $this->boot();

        // If we haven't defined any routes
        // then don't do anything
        if (!$this->routes) {
            return;
        }

        $request  = $this->container->get(ServerRequestInterface::class);
        $response = $this->container->get(ResponseInterface::class);
        $emitter  = $this->container->get(SapiEmitter::class);

        // Get response middleware pipine
        /** @var callable $pipeline */
        $pipeline = $this->container->get('pipeline');
        $response = $pipeline($request, $response);

        $emitter->emit($response);
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
