<?php

namespace Madewithlove\Glue\Providers;

use Madewithlove\Glue\Configuration\ConfigurationInterface;

class ConfigurationServiceProvider extends AbstractValuesProvider
{
    /**
     * @var string
     */
    protected $key = 'config';

    /**
     * {@inheritdoc}
     */
    protected function getProvided()
    {
        return array_merge(parent::getProvided(), [ConfigurationInterface::class]);
    }

    /**
     * @return array
     */
    protected function getValues()
    {
        /* @var ConfigurationInterface $configuration */
        if (!$this->container->has('config')) {
            $configuration = $this->container->get(ConfigurationInterface::class);
            $this->container->add('config', $configuration->getConfiguration());
        }

        return $this->container->get('config');
    }
}
