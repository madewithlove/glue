<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Traits;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerInterface;
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
     * @return ConfigurationInterface
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

        // Bind container if needed
        if ($this->configuration instanceof ContainerAwareInterface) {
            $this->configuration->setContainer($this->getContainer());
        }

        // Boot configuration if asked to
        if (method_exists($this->configuration, 'configure')) {
            $this->configuration->configure();
        }
    }

    //////////////////////////////////////////////////////////////////////
    //////////////////////////// FLUENT SETTERS //////////////////////////
    //////////////////////////////////////////////////////////////////////

    /**
     * Modify something in the configuration.
     *
     * @param string|array $configuration
     * @param mixed|null   $value
     */
    public function configure($configuration, $value = null)
    {
        if ($value && is_string($configuration)) {
            $configuration = [$configuration => $value];
        }

        $class = get_class($this->configuration);
        $this->configuration = new $class($this->recursiveMerge($configuration));
    }

    /**
     * Merge two arrays recursively manually
     * because PHP is bad at it.
     *
     * @param array $configuration
     *
     * @return array
     */
    protected function recursiveMerge(array $configuration)
    {
        foreach ($this->configuration->toArray() as $key => $value) {
            $current = array_get($configuration, $key);

            if (is_array($value)) {
                $current = $current ?: [];
                $configuration[$key] = array_merge($value, $current);
            } else {
                $configuration[$key] = $current !== null ? $current : $value;
            }
        }

        return $configuration;
    }
}
