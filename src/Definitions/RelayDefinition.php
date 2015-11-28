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
use Assembly\FactoryCallDefinition;
use Assembly\ObjectDefinition;
use Assembly\Reference;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Relay\Relay;
use Relay\RelayBuilder;

class RelayDefinition implements DefinitionProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $relayFactory = new ObjectDefinition(RelayBuilder::class, RelayBuilder::class);
        $relayFactory->setConstructorArguments(function ($callable) {
            return is_string($callable) ? $this->container->get($callable) : $callable;
        });

        $relay = new FactoryCallDefinition(Relay::class, new Reference(RelayBuilder::class), 'newInstance');
        $relay->setArguments($this->middlewares);

        return [
            RelayBuilder::class => $relayFactory,
            Relay::class => $relay,
            'pipeline' => new AliasDefinition('pipeline', Relay::class),
        ];
    }
}
