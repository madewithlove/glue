<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\ServiceProviders\Console;

use Interop\Container\ServiceProviderInterface;
use Phinx\Config\Config;
use Phinx\Console\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

class PhinxServiceProvider implements ServiceProviderInterface
{
    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @param array $configuration
     */
    public function __construct(array $configuration = [])
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getServices()
    {
        return [
            Application::class => [$this, 'withMigrateCommands'],
        ];
    }

    /**
     * @param ContainerInterface $container
     * @param callable|null      $getPrevious
     *
     * @return Application
     */
    public function withMigrateCommands(ContainerInterface $container, callable $getPrevious = null)
    {
        /** @var Application $application */
        $application = $getPrevious();
        $application->addCommands([
            $this->getCommand(new Command\Create()),
            $this->getCommand(new Command\Migrate()),
            $this->getCommand(new Command\Rollback()),
            $this->getCommand(new Command\Status()),
        ]);

        return $application;
    }

    /**
     * @param Command\AbstractCommand $command
     *
     * @return Command\AbstractCommand
     */
    protected function getCommand(Command\AbstractCommand $command)
    {
        $command->setName('migrate:'.$command->getName());
        $command->setConfig(new Config($this->configuration));

        return $command;
    }
}
