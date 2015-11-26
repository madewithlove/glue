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
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Phinx\Config\Config;
use Phinx\Console\Command;
use Symfony\Component\Console\Application;

class PhinxServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        // ...
    }

    /**
     * Boot the provider.
     */
    public function boot()
    {
        /** @var Application $console */
        $console = $this->container->get(Application::class);
        $console->addCommands([
            $this->getCommand(new Command\Create()),
            $this->getCommand(new Command\Migrate()),
            $this->getCommand(new Command\Rollback()),
            $this->getCommand(new Command\Status()),
        ]);
    }

    /**
     * @param Command\AbstractCommand $command
     *
     * @return Command\AbstractCommand
     */
    protected function getCommand(Command\AbstractCommand $command)
    {
        $configuration = $this->container->get(ConfigurationInterface::class);
        $configuration = new Config($configuration->getPackageConfiguration(__CLASS__));

        $command->setName('migrate:'.$command->getName());
        $command->setConfig($configuration);

        return $command;
    }
}
