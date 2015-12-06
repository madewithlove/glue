<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions\Glue;

use Madewithlove\Glue\Configuration\ConfigurationInterface;

class ConfigurationDefinition extends AbstractValuesDefinition
{
    /**
     * @var string
     */
    protected $key = 'config';

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    protected function getValues()
    {
        return $this->configuration->toArray();
    }
}
