<?php

/*
 * This file is part of Glue
 *
 * (c) madewithlove <heroes@madewithlove.be>
 *
 * For the full copyright and license information, please view the LICENSE
 */

namespace Madewithlove\Glue\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;

abstract class AbstractValuesProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $key;

    /**
     * {@inheritdoc}
     */
    public function provides($alias = null)
    {
        $this->provides = array_map(function ($key) {
            return $this->key.'.'.$key;
        }, $this->getProvided());

        return parent::provides($alias);
    }

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     */
    public function register()
    {
        $values = $this->getValues();
        foreach ($values as $key => $value) {
            $this->container->add($this->key.'.'.$key, $value);
        }
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
