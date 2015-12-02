<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions;

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
        return [
            StrategyInterface::class => $this->getStrategy(),
            RouteCollection::class => $this->getRouter(),
            'router' => new Reference(RouteCollection::class),
        ];
    }

    /**
     * @return ObjectDefinition
     */
    protected function getStrategy()
    {
        $strategy = new ObjectDefinition(ParamStrategy::class);
        $strategy->addMethodCall('setContainer', $this->container);

        return $strategy;
    }

    /**
     * @return ObjectDefinition
     */
    protected function getRouter()
    {
        $router = new ObjectDefinition(RouteCollection::class);
        $router->setConstructorArguments($this->container);
        $router->addMethodCall('setStrategy', new Reference(StrategyInterface::class));

        return $router;
    }
}
