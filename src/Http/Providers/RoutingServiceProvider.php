<?php

namespace Madewithlove\Glue\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\Route;
use League\Route\RouteCollection;
use League\Route\Strategy\ParamStrategy;
use Madewithlove\Glue\Services\UrlGenerator;

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
        'router',
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
            $strategy = new ParamStrategy();
            $strategy->setContainer($this->container);

            $router = new RouteCollection($this->container);
            $router->setStrategy($strategy);

            return $router;
        });

        $this->container->add('router', function () {
            return $this->container->get(RouteCollection::class);
        });

        // Since RouteCollection doesn't have a getRoutes we collect the
        // Route instances ourselves and pass them to the UrlGenerator
        $this->container->share(UrlGenerator::class, function () {
            return new UrlGenerator(
                $this->container->get('config.namespace'),
                $this->container->get('routes')
            );
        });
    }
}
