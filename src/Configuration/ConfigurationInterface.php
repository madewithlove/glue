<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Configuration;

use Illuminate\Contracts\Support\Arrayable;
use Interop\Container\Definition\DefinitionProviderInterface;

interface ConfigurationInterface extends Arrayable
{
    /**
     * @return bool
     */
    public function isDebug();

    /**
     * @param bool $debug
     *
     * @return $this
     */
    public function setDebug($debug);

    /**
     * Get the root path of the application.
     *
     * @return string
     */
    public function getRootPath();

    /**
     * Get all configured paths.
     *
     * @return array
     */
    public function getPaths();

    /**
     * Get a particular path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getPath($path);

    /**
     * Set the paths for the application.
     *
     * @param array $paths
     *
     * @return $this
     */
    public function setPaths(array $paths = []);

    /**
     * Get the providers to bind with glue.
     *
     * @return array
     */
    public function getProviders();

    /**
     * Set the providers to apply.
     *
     * @param array $providers
     *
     * @return $this
     */
    public function setProviders(array $providers = []);

    /**
     * Get the middlewares to apply.
     *
     * @return array
     */
    public function getMiddlewares();

    /**
     * Set the middlewares to apply.
     *
     * @param array $middlewares
     *
     * @return $this
     */
    public function setMiddlewares(array $middlewares = []);

    /**
     * Get the definition providers to register.
     *
     * @return DefinitionProviderInterface[]
     */
    public function getDefinitionProviders();

    /**
     * Get a definition provider in particular.
     *
     * @param string $provider
     *
     * @return DefinitionProviderInterface
     */
    public function getDefinitionProvider($provider);

    /**
     * Set the definition providers to register.
     *
     * @param DefinitionProviderInterface[] $providers
     *
     * @return $this
     */
    public function setDefinitionsProviders(array $providers = []);

    /**
     * Set a definition provider in particular.
     *
     * @param string                      $name
     * @param DefinitionProviderInterface $provider
     *
     * @return $this
     */
    public function setDefinitionProvider($name, $provider);
}
