<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Console;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Madewithlove\Glue\Glue;
use Symfony\Component\Console\Application;

class SymfonyConsoleServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Application::class,
        'console',
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $this->container->share(Application::class, function () {
            $console = new Application();
            $console->setName('Glue');
            $console->setVersion(Glue::VERSION);

            // Register commands
            $this->registerCommands($console);

            return $console;
        });

        $this->container->add('console', function () {
            return $this->container->get(Application::class);
        });
    }

    /**
     * Register the configured commands with
     * the Symfony application.
     *
     * @param Application $console
     */
    protected function registerCommands(Application $console)
    {
        if (!$this->container->has('config.commands')) {
            return;
        }

        $commands = $this->container->get('config.commands');
        foreach ($commands as $command) {
            $command = is_string($command) ? $this->container->get($command) : $command;
            $console->add($command);
        }
    }
}
