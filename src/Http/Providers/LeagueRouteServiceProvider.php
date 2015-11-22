<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Http\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Route\RouteCollection;
use League\Route\Strategy\ParamStrategy;

class LeagueRouteServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        RouteCollection::class,
        'router',
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
    }
}
