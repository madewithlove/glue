<?php

namespace Madewithlove\Nanoframework\Providers;

use Madewithlove\Nanoframework\Configuration\ConfigurationInterface;

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
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->get(ConfigurationInterface::class);
        $configuration = $configuration->getConfiguration();

        return $configuration;
    }
}
