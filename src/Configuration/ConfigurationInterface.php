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
     * Get the providers to bind with glue.
     *
     * @return array
     */
    public function getProviders();

    /**
     * Get the middlewares to apply.
     *
     * @return array
     */
    public function getMiddlewares();

    /**
     * @return array
     */
    public function toArray();
}
