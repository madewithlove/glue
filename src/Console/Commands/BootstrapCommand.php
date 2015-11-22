<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Console\Commands;

use League\Flysystem\FilesystemInterface;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BootstrapCommand extends Command
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * BootstrapCommand constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param FilesystemInterface    $filesystem
     */
    public function __construct(ConfigurationInterface $configuration, FilesystemInterface $filesystem)
    {
        parent::__construct();

        $this->configuration = $configuration;
        $this->filesystem    = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('bootstrap')
             ->setDescription('Bootstrap the configured folder structure');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output = new SymfonyStyle($input, $output);

        // Scaffold folders
        $paths    = (array) $this->configuration->paths;
        $rootPath = $this->configuration->rootPath.DIRECTORY_SEPARATOR;
        foreach ($paths as $path) {
            $path = str_replace($rootPath, null, $path);
            if (!$this->filesystem->has($path)) {
                $this->filesystem->createDir($path);
                $this->created($output, $path);
            }
        }

        $files = [
            '.env' => 'APP_ENV=local',
        ];

        // Scaffold files
        foreach ($files as $path => $contents) {
            if (!$this->filesystem->has($path)) {
                $this->filesystem->put($path, $contents);
                $this->created($output, $path);
            }
        }
    }

    /**
     * @param OutputInterface $output
     * @param string          $path
     */
    protected function created(OutputInterface $output, $path)
    {
        $output->writeln('<info>✓</info> Created <comment>'.$path.'</comment>');
    }
}
