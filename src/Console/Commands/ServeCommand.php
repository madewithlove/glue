<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Console\Commands;

use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ServeCommand extends Command
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        return $this
            ->setName('serve')
            ->setDescription('Run a small web server')
            ->addOption('host', 'H', InputOption::VALUE_REQUIRED, 'The host to run on', 'localhost')
            ->addOption('port', 'P', InputOption::VALUE_REQUIRED, 'The port to run on', '8123');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $address = $input->getOption('host').':'.$input->getOption('port');
        $public = $this->configuration->getPath('web').DS.'index.php';

        $output->writeln('Starting webserver at http://'.$address);

        // Create process
        $process = new Process(sprintf('php -S %s %s', $address, $public));
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        /** @var ProcessHelper $processes */
        $processes = $this->getHelperSet()->get('process');
        $processes->mustRun($output, $process);
    }
}
