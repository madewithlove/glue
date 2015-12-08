<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions\Console;

use Interop\Container\Definition\DefinitionProviderInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Madewithlove\Glue\Definitions\DefinitionTypes\ExtendDefinition;
use Phinx\Config\Config;
use Phinx\Console\Command;
use Symfony\Component\Console\Application;

class PhinxDefinition implements DefinitionProviderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

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
    public function getDefinitions()
    {
        $phinx = new ExtendDefinition(Application::class);
        $phinx->addMethodCall('addCommands', [
            $this->getCommand(new Command\Create()),
            $this->getCommand(new Command\Migrate()),
            $this->getCommand(new Command\Rollback()),
            $this->getCommand(new Command\Status()),
        ]);

        return [$phinx];
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
