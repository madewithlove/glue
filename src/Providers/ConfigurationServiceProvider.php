<?php

namespace Madewithlove\Glue\Providers;

use InvalidArgumentException;
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
        return array_merge(parent::getProvided(), [
            ConfigurationInterface::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValues()
    {
        $values = $this->container->get(ConfigurationInterface::class)->toArray();

        // Validate object
        $required = ['debug', 'paths'];
        $missing  = array_diff($required, array_keys($values));
        if ($missing) {
            throw new InvalidArgumentException('Missing configuration keys: '.implode(', ', $missing));
        }

        return $values;
    }
}
