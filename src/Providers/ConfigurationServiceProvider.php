<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

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
        return array_merge(parent::getProvided(), [
            ConfigurationInterface::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValues()
    {
        return $this->container->get(ConfigurationInterface::class)->toArray();
    }
}
