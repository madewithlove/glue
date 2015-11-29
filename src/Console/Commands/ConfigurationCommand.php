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
use League\Container\ContainerAwareInterface;
use Madewithlove\Glue\Configuration\ConfigurationInterface;
use Madewithlove\Glue\Configuration\DefaultConfiguration;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
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
        $this->printDefinitions();
    }

    /**
     * Print the configured options.
     */
    protected function printConfiguration()
    {
        $this->title('Configuration');

        $values = array_except($this->configuration->toArray(), ['definitions', 'middlewares']);
        $values = array_dot($values);
        foreach ($values as $key => &$value) {
            $value = [$key, $value];
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
     * Print the current definitions.
     */
    protected function printDefinitions()
    {
        $this->title('Definitions');

        $rows = [];
        foreach ($this->configuration->getDefinitionProviders() as $definition) {
            if ($definition instanceof ContainerAwareInterface) {
                $definition->setContainer(new Container());
            }

            $parameters = [];
            $reflection = new ReflectionClass($definition);
            if ($reflection->getProperties()) {
                foreach ($reflection->getProperties() as $parameter) {
                    if ($parameter->getName() === 'container') {
                        continue;
                    }

                    // Extract parameter type
                    $doc = $parameter->getDocComment();
                    preg_match('/.*@(type|var) (.+)\n.*/', $doc, $type);
                    $type = $type[2];

                    $parameters[] = $parameter->getName().': '.$type;
                }
            }

            $definitions = array_keys($definition->getDefinitions());
            foreach ($definitions as $key => $binding) {
                $definitions[$key] = is_string($binding) ? ($key + 1).'. '.$binding : null;
            }

            $rows[] = [
                'definition' => get_class($definition),
                'options' => implode(PHP_EOL, $parameters),
                'bindings' => implode(PHP_EOL, $definitions),
            ];
        }

        $this->output->table(['Definition', 'Options', 'Bindings'], $rows);
    }

    /**
     * Prints out a pretty title.
     *
     * @param string $title
     */
    protected function title($title)
    {
        $this->output->block($title, null, 'fg=black;bg=cyan', ' ', true);
    }
}
