<?php

namespace Madewithlove\Glue\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Symfony\Component\Console\Application;

class ConsoleServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        'console',
      Application::class,
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

            // Register commands
            $commands = $this->container->get('config.commands');
            foreach ($commands as $command) {
                $command = is_string($command) ? $this->container->get($command) : $command;
                $console->add($command);
            }

            return $console;
        });

        $this->container->add('console', function() {
           return $this->container->get(Application::class);
        });
    }
}
