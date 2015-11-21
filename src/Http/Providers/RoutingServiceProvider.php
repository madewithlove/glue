<?php

namespace Madewithlove\Nanoframework\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\Route;
use League\Route\RouteCollection;

class RoutingServiceProvider extends AbstractServiceProvider
{
    /**
     * @var Route[]
     */
    protected $routes;

    /**
     * @var array
     */
    protected $provides = [
        RouteCollection::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(RouteCollection::class, function () {
            $router = new RouteCollection($this->container);
            $this->routes = $this->getRoutes($router);

            return $router;
        });
    }

    /**
     * @param RouteCollection $router
     *
     * @return array
     */
    protected function getRoutes(RouteCollection $router)
    {
        return [];
    }
}
