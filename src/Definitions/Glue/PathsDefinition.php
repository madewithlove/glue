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

class PathsDefinition extends AbstractValuesDefinition
{
    /**
     * @var string
     */
    protected $key = 'paths';

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * PathsDefinition constructor.
     *
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return array
     */
    protected function getValues()
    {
        return $this->configuration->getPaths();
    }
}
