<?php
namespace Madewithlove\Nanoframework\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\Route;
use League\Route\RouteCollection;
use Zend\Diactoros\Response;

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

            /** @var callable $routes */
            // Bind routes to Router
            $routes = $this->container->get('config.routes');
            $router = $routes($router);

            return $router;
        });
    }
}
