<?php
namespace Madewithlove\Nanoframework\Providers;

use Madewithlove\Nanoframework\Configuration\ConfigurationInterface;
use Madewithlove\Nanoframework\Configuration\DefaultConfiguration;

class ConfigurationServiceProvider extends AbstractValuesProvider
{
    /**
     * @var string
     */
    protected $key = 'config';

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        // Bind default configuration if none found
        if (!$this->container->has(ConfigurationInterface::class)) {
            $this->container->add(ConfigurationInterface::class, function () {
                return new DefaultConfiguration($this->container);
            });
        }

        parent::register();
    }

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
