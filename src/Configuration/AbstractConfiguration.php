<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Configuration;

use Illuminate\Support\Fluent;
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

/**
 * @property string                        $namespace   The namespace of your application
 * @property string                        $rootPath    The path to the root of your application
 * @property bool                          $debug       Whether we're in debug mode or not
 * @property string[]                      $middlewares The middlewares to apply to the current route
 * @property array                         $paths       The paths in your application
 * @property DefinitionProviderInterface[] $definitions The definition providers
 */
abstract class AbstractConfiguration extends Fluent implements ConfigurationInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return $this->debug !== null ? $this->debug : true;
    }

    /**
     * {@inheritdoc}
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaths()
    {
        return (array) $this->paths;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($path)
    {
        return array_get($this->getPaths(), $path);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaths(array $paths = [])
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootPath()
    {
        return rtrim($this->rootPath, DS) ?: getcwd();
    }

    /**
     * {@inheritdoc}
     */
    public function getMiddlewares()
    {
        return (array) $this->middlewares;
    }

    /**
     * {@inheritdoc}
     */
    public function setMiddlewares(array $middlewares = [])
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionProviders()
    {
        return (array) $this->definitions;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefinitionProviders(array $providers = [])
    {
        $this->definitions = $providers;

        return $this;
    }

    /**
     * Get a definition provider in particular.
     *
     * @param string $provider
     *
     * @return DefinitionProviderInterface
     */
    public function getDefinitionProvider($provider)
    {
        return array_get($this->definitions, $provider);
    }

    /**
     * Set a definition provider in particular.
     *
     * @param string                      $name
     * @param DefinitionProviderInterface $provider
     *
     * @return $this
     */
    public function setDefinitionProvider($name, $provider)
    {
        $this->definitions[$name] = $provider;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        // ...
    }
}
