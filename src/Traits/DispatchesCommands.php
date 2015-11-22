<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Traits;

use League\Tactician\CommandBus;
use Madewithlove\Glue\CommandBus\CommandInterface;
use Psr\Http\Message\ServerRequestInterface;

trait DispatchesCommands
{
    /**
     * @return CommandBus
     */
    abstract protected function getCommandBus();

    /**
     * @param CommandInterface|string $command
     * @param array                   $parameters
     *
     * @return mixed
     */
    protected function dispatch($command, array $parameters = [])
    {
        // Create instance of command if needed
        if (is_string($command)) {
            $command = new $command();
            foreach ($parameters as $key => $value) {
                $command->{$key} = $value;
            }
        }

        return $this->getCommandBus()->handle($command);
    }

    /**
     * @param CommandInterface|string $command
     * @param ServerRequestInterface  $request
     */
    protected function dispatchFromRequest($command, ServerRequestInterface $request)
    {
        $this->dispatch($command, $request->getAttributes());
    }
}
