<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Definitions\Glue;

use Assembly\ParameterDefinition;
use Interop\Container\Definition\DefinitionProviderInterface;

abstract class AbstractValuesDefinition implements DefinitionProviderInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * {@inheritdoc}
     */
    public function getDefinitions()
    {
        $values = $this->getValues();
        $definitions = [];
        foreach ($values as $key => $value) {
            $definitions[$this->key.'.'.$key] = new ParameterDefinition($value);
        }

        return $definitions;
    }

    /**
     * @return array
     */
    abstract protected function getValues();
}
