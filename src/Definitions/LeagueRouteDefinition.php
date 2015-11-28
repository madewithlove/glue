<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions;

use Assembly\AliasDefinition;
use Assembly\ObjectDefinition;
use Assembly\Reference;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Route\RouteCollection;
use League\Route\Strategy\ParamStrategy;
use League\Route\Strategy\StrategyInterface;

class LeagueRouteDefinition implements DefinitionProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $strategy = new ObjectDefinition(StrategyInterface::class, ParamStrategy::class);
        $strategy->addMethodCall('setContainer', $this->container);

        $router = new ObjectDefinition(RouteCollection::class, RouteCollection::class);
        $router->addMethodCall('setStrategy', new Reference(StrategyInterface::class));

        return [
            StrategyInterface::class => $strategy,
            RouteCollection::class => $router,
            'router' => new AliasDefinition('router', RouteCollection::class),
        ];
    }
}
