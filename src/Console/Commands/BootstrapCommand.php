<?php
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
        $this->setName('glue:bootstrap')
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

        // Scaffold folder structure
        $paths = (array) $this->configuration->paths;
        foreach ($paths as $path) {
            if (!$this->filesystem->has($path)) {
                $this->filesystem->createDir($path);
                $this->created($output, $path);
            }
        }

        // Create Dotenv file
        $dotenv = $this->configuration->rootPath.'/.env';
        if (!$this->filesystem->has($dotenv)) {
            $this->filesystem->put($dotenv, 'APP_ENV=local');
            $this->created($output, $dotenv);
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
