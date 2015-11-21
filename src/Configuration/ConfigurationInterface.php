<?php
namespace Madewithlove\Nanoframework\Configuration;

use League\Route\RouteCollection;

interface ConfigurationInterface
{
    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @return array
     */
    public function getProviders();

    /**
     * @return array
     */
    public function getDebugProviders();

    /**
     * @return array
     */
    public function getPaths();

    /**
     * @return array
     */
    public function getMiddlewares();

    /**
     * @return bool
     */
    public function isDebug();

    /**
     * @param RouteCollection $router
     *
     * @return RouteCollection
     */
    public function getRoutes(RouteCollection $router);
}
