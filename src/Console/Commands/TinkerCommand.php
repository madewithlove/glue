<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Console\Commands;

use Interop\Container\ContainerInterface;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Psy\Shell;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TinkerCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * TinkerCommand constructor.
     *
     * @param $container
     * @param $shell
     */
    public function __construct(ContainerInterface $container, Shell $shell)
    {
        parent::__construct();

        $this->container = $container;
        $this->shell = $shell;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tinker')
            ->setDescription('Tinker with the application and its classes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->shell->setScopeVariables([
            'app' => $this->container,
            'config' => $this->container->get(ConfigurationInterface::class),
        ]);

        $this->shell->run();
    }
}
