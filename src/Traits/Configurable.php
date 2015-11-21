<?php
namespace Madewithlove\Glue\Traits;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerInterface;
use Madewithlove\Glue\Configuration\ArrayConfiguration;
use Madewithlove\Glue\Configuration\ConfigurationInterface;

trait Configurable
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @return ContainerInterface
     */
    abstract public function getContainer();

    /**
     * @return string
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param ConfigurationInterface $configuration
     */
    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        if ($this->configuration instanceof ContainerAwareInterface) {
            $this->configuration->setContainer($this->getContainer());
        }
    }

    //////////////////////////////////////////////////////////////////////
    //////////////////////////// FLUENT SETTERS //////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * Modify something in the configuration
     *
     * @param array $configuration
     * @param bool  $recursive
     */
    public function configure(array $configuration, $recursive = true)
    {
        $method = $recursive ? 'array_merge_recursive' : 'array_merge';

        $this->configuration = new ArrayConfiguration($method($this->configuration->toArray(), $configuration));
    }
}
