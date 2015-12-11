<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\CommandBus;

use Interop\Container\ContainerInterface;
use League\Container\ImmutableContainerAwareInterface;
use League\Container\ImmutableContainerAwareTrait;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator;

/**
 * An handler locator that can infere the handler's
 * name from the command and resolve it
 * from a container.
 */
class ContainerLocator implements HandlerLocator, ImmutableContainerAwareInterface
{
    use ImmutableContainerAwareTrait;

    /**
     * Retrieves the handler for a specified command.
     *
     * @param string $commandName
     *
     * @throws MissingHandlerException
     *
     * @return object
     */
    public function getHandlerForCommand($commandName)
    {
        $handler = preg_replace('/Command$/', 'Handler', $commandName);

        return $this->container->get($handler);
    }
}
