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
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Container\ImmutableContainerAwareInterface;
use League\Container\ImmutableContainerAwareTrait;
use League\Tactician\Handler\Locator\HandlerLocator;
use Madewithlove\Glue\CommandBus\ContainerLocator;

class ContainerLocatorDefinition implements DefinitionProviderInterface, ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function getDefinitions()
    {
        $locator = new ObjectDefinition(ContainerLocator::class);
        $locator->addMethodCall('setContainer', $this->container);

        return [
            HandlerLocator::class => $locator,
        ];
    }
}
