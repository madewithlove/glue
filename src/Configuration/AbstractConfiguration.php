<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Configuration;

use Illuminate\Support\Fluent;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;

/**
 * @property $namespace   The namespace of your application
 * @property $rootPath    The path to the root of your application
 * @property $debug       Whether we're in debug mode or not
 * @property $providers   The providers to apply
 * @property $middlewares The middlewares to apply to the current route
 * @property $commands    The commands to register with the CLI
 * @property $paths       The paths in your application
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
    public function getRootPath()
    {
        return rtrim($this->rootPath, DS) ?: getcwd();
    }

    /**
     * {@inheritdoc}
     */
    public function getProviders()
    {
        return (array) $this->providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getMiddlewares()
    {
        return (array) $this->middlewares;
    }

    /**
     * Setup the configuration.
     */
    public function configure()
    {
        // ...
    }
}
