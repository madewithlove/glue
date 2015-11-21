<?php

namespace Madewithlove\Glue\Traits;

use League\Tactician\CommandBus;
use Madewithlove\Glue\CommandBus\CommandInterface;

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
}
