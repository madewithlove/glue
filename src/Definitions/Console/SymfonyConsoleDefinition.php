<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions\Console;

use Assembly\AliasDefinition;
use Assembly\ObjectDefinition;
use Assembly\Reference;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Madewithlove\Glue\Glue;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class SymfonyConsoleDefinition implements DefinitionProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var Command[]|string[]
     */
    protected $commands;

    /**
     * @param string[]|Command[] $commands
     */
    public function __construct($commands = [])
    {
        $this->commands = $commands;
    }

    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        $console = new ObjectDefinition(Application::class, Application::class);
        $console->addMethodCall('setName', 'Glue');
        $console->addMethodCall('setVersion', Glue::VERSION);

        // Register commands
        foreach ($this->commands as $command) {
            $console->addMethodCall('add', new Reference($command));
        }

        return [
            Application::class => $console,
            'console' => new AliasDefinition('console', Application::class),
        ];
    }
}
