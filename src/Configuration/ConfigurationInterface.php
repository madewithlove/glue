<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Configuration;

interface ConfigurationInterface
{
    /**
     * @return bool
     */
    public function isDebug();

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
     * @return self
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
     * @return self
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
     * @return self
     */
    public function setMiddlewares(array $middlewares = []);

    /**
     * @return array
     */
    public function toArray();
}
