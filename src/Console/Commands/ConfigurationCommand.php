<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Console\Commands;

use League\Container\Container;
use League\Container\ImmutableContainerAwareInterface;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Madewithlove\Glue\Configuration\DefaultConfiguration;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConfigurationCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    protected $output;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * ConfigurationCommand constructor.
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
        $this->setName('config')
            ->setDescription('Prints out the current configuration')
            ->addOption('default', null, InputOption::VALUE_NONE, 'Dump the default configuration');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = new SymfonyStyle($input, $output);
        if ($input->getOption('default')) {
            $this->configuration = new DefaultConfiguration();
        }

        $this->printConfiguration();
        $this->printMiddlewares();
        $this->printServiceProviders();
    }

    /**
     * Print the configured options.
     */
    protected function printConfiguration()
    {
        $this->title('Configuration');

        $values = array_except($this->configuration->toArray(), ['providers', 'middlewares']);
        $values = array_dot($values);
        foreach ($values as $key => &$value) {
            $value = ['<comment>'.$key.'</comment>', $value];
        }

        $this->output->table(['Key', 'Value'], $values);
    }

    private function printMiddlewares()
    {
        if (!$this->configuration->getMiddlewares()) {
            return;
        }

        $this->title('Middlewares');
        $this->output->listing($this->configuration->getMiddlewares());
    }

    /**
     * Print the current service providers.
     */
    protected function printServiceProviders()
    {
        $this->title('Service providers');

        $rows = [];
        foreach ($this->configuration->getServiceProviders() as $provider) {
            if ($provider instanceof ImmutableContainerAwareInterface) {
                $provider->setContainer(new Container());
            }

            $parameters = [];
            $reflection = new ReflectionClass($provider);
            if ($reflection->getProperties()) {
                foreach ($reflection->getProperties() as $parameter) {
                    if ($parameter->getName() === 'container') {
                        continue;
                    }

                    // Extract parameter type
                    $doc = $parameter->getDocComment();
                    preg_match('/.*@(type|var) (.+)\n.*/', $doc, $type);
                    $type = $type[2];

                    $parameters[] = '<info>'.$parameter->getName().'</info>: '.$type;
                }
            }

            $services = array_keys($provider->getServices());
            foreach ($services as $key => $binding) {
                $services[$key] = is_string($binding) ? ($key + 1).'. <comment>'.$binding.'</comment>' : null;
            }

            $rows[] = [
                'provider' => '<comment>'.get_class($provider).'</comment>',
                'options' => implode(PHP_EOL, $parameters),
                'services' => implode(PHP_EOL, $services),
            ];

            $rows[] = new TableSeparator();
        }

        $this->output->table(['Service Provider', 'Options', 'Services'], array_slice($rows, 0, -1));
    }

    /**
     * Prints out a pretty title.
     *
     * @param string $title
     */
    protected function title($title)
    {
        $this->output->block($title, null, 'fg=black;bg=cyan', ' ', false);
    }
}
