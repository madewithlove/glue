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
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
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

    /**
     * @return array
     */
    protected function getProvided()
    {
        return array_keys($this->getValues());
    }
}
