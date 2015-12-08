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
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use Madewithlove\Glue\CommandBus\ContainerLocator;

class TacticianDefinition implements DefinitionProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getDefinitions()
    {
        $handler = new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            new ContainerLocator($this->container),
            new HandleInflector()
        );

        $bus = new ObjectDefinition(CommandBus::class);
        $bus->setConstructorArguments([$handler]);

        return [
            CommandBus::class => $bus,
            'bus' => new Reference(CommandBus::class),
        ];
    }
}
