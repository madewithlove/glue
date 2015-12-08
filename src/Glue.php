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
use League\Container\ContainerAwareTrait;
use League\Container\ReflectionContainer;
use League\Route\RouteCollection;
use Madewithlove\Definitions\ValuesDefinition;
use Madewithlove\Glue\Configuration\AbstractConfiguration;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Madewithlove\Glue\Configuration\DefaultConfiguration;
use Madewithlove\Glue\Definitions\Glue\ConfigurationDefinition;
use Madewithlove\Glue\Definitions\Glue\PathsDefinition;
use Madewithlove\Glue\Traits\Configurable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\SapiEmitter;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * @property Container $container
 * @mixin RouteCollection
 * @mixin AbstractConfiguration
 */
class Glue
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
        // Setup container configuration
        $this->setContainer($container);
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
     */
    public function setContainer($container = null)
    {
        // Setup container
        $delegates = [$this->container, new ReflectionContainer()];
        $parentContainer = new Container();
        $parentContainer->share(ContainerInterface::class, $parentContainer);

        if ($container) {
            // Unify container to PSR11
            $acclimator = new ContainerAcclimator();
            $container = $acclimator->acclimate($container);

            array_unshift($delegates, $container);
        }

        // Bind delegates
        $delegates = array_filter($delegates);
        foreach ($delegates as $delegate) {
            $parentContainer->delegate($delegate);
        }

        $this->container = $parentContainer;
    }

    /**
     * Delegate calls to the router and configuration is bound.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        // Delegate to Configuration
        if (method_exists($this->configuration, $name) && !in_array($name, ['get'], true)) {
            return $this->configuration->$name(...$arguments);
        }

        // Delegate to Router
        $this->boot();
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

        $this->registerProviders();
    }

    /**
     * Register the service providers with the container.
     */
    protected function registerProviders()
    {
        // Register core providers
        $this->container->addDefinitionProvider(new ValuesDefinition('paths', $this->configuration->getPaths()));
        $this->container->addDefinitionProvider(new ValuesDefinition('config', $this->configuration->toArray()));

        // Register definitions
        $definitionProviders = $this->configuration->getDefinitionProviders();
        foreach ($definitionProviders as &$definitionProvider) {
            $this->container->addDefinitionProvider($definitionProvider);
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

        $request = $this->container->get(ServerRequestInterface::class);
        $response = $this->container->get(ResponseInterface::class);
        $emitter = $this->container->get(SapiEmitter::class);

        // Get response middleware pipeline
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
