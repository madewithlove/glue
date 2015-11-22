<?php

/*
 * This file is part of Glue
 *
 * (c) Madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Traits;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerInterface;
use Madewithlove\Glue\Configuration\Configuration;
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
     * @param null         $value
     */
    public function configure($configuration, $value = null)
    {
        if ($value && !is_array($configuration)) {
            $configuration = [$configuration => $value];
        }

        // Merge stuff manually cause PHP is bad at it
        foreach ($this->configuration->toArray() as $key => $value) {
            if (is_array($value)) {
                $configuration[$key] = array_merge($value, array_get($configuration, $key, []));
            } else {
                $configuration[$key] = array_get($configuration, $key) ?: $value;
            }
        }

        $this->configuration = new Configuration($configuration);
    }
}
