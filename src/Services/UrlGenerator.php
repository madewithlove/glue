<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Services;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use League\Route\Route;

/**
 * A disgusting URL generator for league/route.
 */
class UrlGenerator
{
    /**
     * @var Route[]
     */
    protected $routes;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * UrlGenerator constructor.
     *
     * @param string $namespace
     * @param array  $routes
     */
    public function __construct($namespace, array $routes)
    {
        $this->routes    = $routes;
        $this->namespace = $namespace;
    }

    /**
     * @param string       $name
     * @param string|array $parameters
     *
     * @return string
     */
    public function to($name, $parameters = [])
    {
        $action = $this->routeToCallable($name);
        foreach ($this->routes as $route) {
            $path = $route->getPath();
            if ($route->getCallable() !== $action) {
                continue;
            }

            return $this->replaceParametersInPath($path, $parameters);
        }

        throw new InvalidArgumentException(sprintf('Unable to generate URL for %s (%s)', $name, $action));
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function routeToCallable($name)
    {
        $callable = str_replace('.', 'Controller::', ucfirst($name));
        $callable = $this->namespace.'\Http\Controllers\\'.$callable;

        return $callable;
    }

    /**
     * @param string       $path
     * @param string|array $parameters
     *
     * @return string
     */
    protected function replaceParametersInPath($path, $parameters = [])
    {
        if (!$parameters) {
            return $path;
        }

        return preg_replace_callback('/{(.+)}/', function ($pattern) use ($parameters) {
            return is_string($parameters) ? $parameters : Arr::get($parameters, $pattern[1], $pattern[1]);
        }, $path);
    }
}
