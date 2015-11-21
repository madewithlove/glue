<?php
namespace Madewithlove\Nanoframework\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Symfony\Component\Console\Application;

class ConsoleServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
      Application::class,
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->container->share(Application::class, function () {
            $console  = new Application();

            // Register commands
            $commands = $this->container->get('config.commands');
            foreach ($commands as $command) {
                $console->add($this->container->get($command));
            }

            return $console;
        });
    }
}
