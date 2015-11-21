<?php
namespace Madewithlove\Glue\Console\Commands;

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
     * BootstrapCommand constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        parent::__construct();

        $this->configuration = $configuration;
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
            if (!is_dir($path)) {
                mkdir($path, 0644, true);
                $this->created($output, $path);
            }
        }

        // Create Dotenv file
        $dotenv = $this->configuration->rootPath.'/.env';
        file_put_contents($dotenv, 'APP_ENV=local');
        $this->created($output, $dotenv);
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
