<?php

namespace Madewithlove\Glue\Configuration;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Symfony\Component\Console\Command\Command;

abstract class AbstractConfiguration implements ConfigurationInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * The namespace of your application
     *
     * @var string
     */
    public $namespace;

    /**
     * @var string
     */
    public $rootPath;

    /**
     * Whether we're in debug mode or not
     *
     * @var bool
     */
    public $debug;

    /**
     * The providers to apply
     *
     * @var string[]
     */
    public $providers;

    /**
     * The middlewares to apply to the current route
     *
     * @var string[]
     */
    public $middlewares;

    /**
     * The commands to register with the CLI
     *
     * @var string[]
     */
    public $commands;

    /**
     * The paths in your application
     *
     * @var string[]
     */
    public $paths;

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * @param string $rootPath
     */
    public function setRootPath($rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return string[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * @param string[] $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * @return string[]
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * @param string[] $middlewares
     */
    public function setMiddlewares(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * @return string[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param string[] $commands
     */
    public function setCommands(array $commands)
    {
        $this->commands = $commands;
    }

    /**
     * @return string[]
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @param string[] $paths
     */
    public function setPaths(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'commands'    => $this->commands,
            'namespace'   => $this->namespace,
            'debug'       => $this->debug,
            'providers'   => $this->providers,
            'middlewares' => $this->middlewares,
            'paths'       => $this->paths,
        ];
    }
}
