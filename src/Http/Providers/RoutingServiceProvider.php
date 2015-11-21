<?php

namespace Madewithlove\Nanoframework\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\Route;
use League\Route\RouteCollection;
use Madewithlove\Nanoframework\Services\UrlGenerator;

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
        UrlGenerator::class,
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

        // Since RouteCollection doesn't have a getRoutes we collect the
        // Route instances ourselves and pass them to the UrlGenerator
        $this->container->share(UrlGenerator::class, function () {
            $this->container->get(RouteCollection::class);
            return new UrlGenerator($this->container->get('config.namespace'), $this->routes);
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
